<?php

namespace Tests\Feature;

use App\Models\QueueRecord;
use App\Models\StaffUser;
use App\Models\Window;
use App\Services\QueueService;
use Database\Seeders\WindowSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class QueueWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private Window $registrar;

    private Window $cashier;

    protected function setUp(): void
    {
        parent::setUp();

        $this->registrar = Window::query()->create([
            'window_name' => 'Registrar',
            'queue_prefix' => 'R',
            'is_active' => true,
        ]);
        $this->cashier = Window::query()->create([
            'window_name' => 'Cashier',
            'queue_prefix' => 'C',
            'is_active' => true,
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_students_only_see_active_windows(): void
    {
        $closedWindow = Window::query()->create([
            'window_name' => 'Closed window',
            'is_active' => false,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Registrar')
            ->assertSee('Cashier')
            ->assertDontSee('Closed window');

        $this->get(route('public.queue.terms', $closedWindow))->assertNotFound();
        $this->post(route('public.queue.issue', $closedWindow), [
            'student_name' => 'Student',
            'terms_agreed' => '1',
        ])
            ->assertNotFound();
    }

    public function test_default_windows_are_seeded_with_their_ticket_prefixes(): void
    {
        $this->seed(WindowSeeder::class);

        $this->assertDatabaseHas('windows', [
            'window_name' => 'Window 3',
            'queue_prefix' => 'W3',
        ]);
    }

    public function test_a_queue_number_is_issued_only_after_terms_are_accepted_and_resets_per_window(): void
    {
        $this->get(route('public.queue.terms', $this->registrar))
            ->assertOk()
            ->assertSee('five-minute grace period');

        $this->post(route('public.queue.issue', $this->registrar), [])
            ->assertSessionHasErrors('terms_agreed');

        $first = $this->issueTicket($this->registrar);
        $second = $this->issueTicket($this->registrar);
        $cashierTicket = $this->issueTicket($this->cashier);

        $this->assertSame(1, $first->queue_number);
        $this->assertSame(2, $second->queue_number);
        $this->assertSame(1, $cashierTicket->queue_number);
        $this->assertTrue($first->terms_agreed);
        $this->assertSame('waiting', $first->status);
    }

    public function test_students_must_enter_their_name_before_receiving_a_queue_number(): void
    {
        $this->get(route('public.queue.terms', $this->registrar))
            ->assertOk()
            ->assertSee('Student name');

        $this->post(route('public.queue.issue', $this->registrar), ['terms_agreed' => '1'])
            ->assertSessionHasErrors(['student_name' => 'The student name field is required.']);

        $this->assertDatabaseCount('queue_records', 0);
    }

    public function test_a_student_with_an_active_queue_is_sent_to_the_existing_ticket(): void
    {
        $ticket = $this->issueTicket($this->registrar, 'Alex Rivera');

        $this->post(route('public.queue.issue', $this->cashier), [
            'student_name' => ' alex rivera ',
            'terms_agreed' => '1',
        ])
            ->assertRedirect(route('public.queue.show', $ticket->public_token))
            ->assertSessionHas('notice', 'You already have an active queue. Continue using this ticket until your service is complete.');

        $this->assertDatabaseCount('queue_records', 1);
    }

    public function test_an_active_queue_is_reused_for_the_same_browser_even_if_the_name_changes(): void
    {
        $queueService = app(QueueService::class);
        $ticket = $queueService->issue($this->registrar, 'Alex Rivera', 'student-session');
        $existingTicket = $queueService->issue($this->cashier, 'Sam Santos', 'student-session');

        $this->assertSame($ticket->queue_id, $existingTicket->queue_id);
        $this->assertDatabaseCount('queue_records', 1);
    }

    public function test_a_student_can_get_another_queue_after_the_current_queue_is_completed(): void
    {
        $firstTicket = $this->issueTicket($this->registrar, 'Alex Rivera');
        $queueService = app(QueueService::class);
        $queueService->callNext($this->registrar);
        $queueService->markPresent($firstTicket);
        $queueService->complete($firstTicket);

        $nextTicket = $this->issueTicket($this->cashier, 'alex rivera');

        $this->assertSame(2, QueueRecord::query()->count());
        $this->assertSame(1, $nextTicket->queue_number);
        $this->assertSame('C001', $nextTicket->displayNumber());
    }

    public function test_an_issued_ticket_keeps_its_prefix_if_the_window_prefix_changes(): void
    {
        $ticket = $this->issueTicket($this->registrar, 'Alex Rivera');
        $this->registrar->update(['queue_prefix' => 'REG']);

        $this->assertSame('R001', $ticket->displayNumber());
    }

    public function test_ticket_status_is_public_and_returns_live_queue_information(): void
    {
        $ticket = $this->issueTicket($this->registrar);

        $this->get(route('public.queue.show', $ticket->public_token))
            ->assertOk()
            ->assertSee('Your queue ticket');

        $this->getJson(route('public.queue.status', $ticket->public_token))
            ->assertOk()
            ->assertJsonPath('queue_number', $ticket->queue_number)
            ->assertJsonPath('ticket_number', 'R001')
            ->assertJsonPath('window_name', 'Registrar')
            ->assertJsonPath('status', 'waiting')
            ->assertJsonPath('position', 1)
            ->assertJsonPath('active_today', true);
    }

    public function test_a_student_can_cancel_their_ticket_and_rejoin_the_queue(): void
    {
        $ticket = $this->issueTicketInCurrentBrowser($this->registrar, 'Alex Rivera');
        $nextTicket = $this->createQueueRecord($this->registrar, today()->toDateString(), 2, 'waiting');

        $this->get(route('public.queue.show', $ticket->public_token))
            ->assertOk()
            ->assertSee('Cancel my ticket')
            ->assertSee('I confirm that I no longer need this queue ticket.');

        $this->post(route('public.queue.cancel', $ticket->public_token), [
            'confirm_cancel' => '1',
        ])
            ->assertRedirect(route('public.queue.show', $ticket->public_token))
            ->assertSessionHas('notice', 'Your ticket has been cancelled.');

        $this->assertSame('cancelled', $ticket->fresh()->status);
        $this->assertSame(1, $nextTicket->fresh()->queue_position);
        $this->assertSame('cancelled', $this->get(route('public.queue.show', $ticket->public_token))
            ->viewData('record')->status);

        $this->post(route('public.queue.issue', $this->registrar), [
            'student_name' => 'Alex Rivera',
            'terms_agreed' => '1',
        ])->assertRedirect();

        $newTicket = QueueRecord::query()
            ->where('student_name', 'Alex Rivera')
            ->where('status', 'waiting')
            ->firstOrFail();

        $this->assertNotSame($ticket->queue_id, $newTicket->queue_id);
        $this->assertSame(2, $newTicket->queue_position);
    }

    public function test_cancellation_requires_explicit_confirmation(): void
    {
        $ticket = $this->issueTicketInCurrentBrowser($this->registrar);

        $this->post(route('public.queue.cancel', $ticket->public_token))
            ->assertSessionHasErrors('confirm_cancel');

        $this->assertSame('waiting', $ticket->fresh()->status);
    }

    public function test_a_student_can_cancel_their_private_ticket_from_another_browser_session(): void
    {
        $ticket = app(QueueService::class)->issue($this->registrar, 'Alex Rivera', 'another-browser');

        $this->post(route('public.queue.cancel', $ticket->public_token), [
            'confirm_cancel' => '1',
        ])->assertRedirect(route('public.queue.show', $ticket->public_token));

        $this->assertSame('cancelled', $ticket->fresh()->status);
    }

    public function test_a_student_cannot_cancel_a_ticket_after_staff_marks_them_present(): void
    {
        $ticket = $this->issueTicketInCurrentBrowser($this->registrar);
        $queueService = app(QueueService::class);
        $queueService->callNext($this->registrar);
        $queueService->markPresent($ticket);

        $this->post(route('public.queue.cancel', $ticket->public_token), [
            'confirm_cancel' => '1',
        ])->assertSessionHasErrors('queue');

        $this->assertSame('present', $ticket->fresh()->status);
    }

    public function test_a_student_can_cancel_a_ticket_that_has_been_called(): void
    {
        $ticket = $this->issueTicketInCurrentBrowser($this->registrar);
        app(QueueService::class)->callNext($this->registrar);

        $this->post(route('public.queue.cancel', $ticket->public_token), [
            'confirm_cancel' => '1',
        ])->assertRedirect(route('public.queue.show', $ticket->public_token));

        $this->assertSame('cancelled', $ticket->fresh()->status);
    }

    public function test_a_student_cannot_cancel_a_ticket_from_a_previous_day(): void
    {
        $ticket = $this->issueTicketInCurrentBrowser($this->registrar);
        $ticket->update(['queue_date' => today()->subDay()]);

        $this->post(route('public.queue.cancel', $ticket->public_token), [
            'confirm_cancel' => '1',
        ])->assertSessionHasErrors('queue');

        $this->assertSame('waiting', $ticket->fresh()->status);
    }

    public function test_queue_numbers_and_missed_counts_start_over_on_the_next_day(): void
    {
        $time = Carbon::now();
        Carbon::setTestNow($time);
        $firstDayTicket = $this->issueTicket($this->registrar);
        $queueService = app(QueueService::class);
        $queueService->callNext($this->registrar);

        Carbon::setTestNow($time->copy()->addMinutes(5));
        $queueService->expireForWindow($this->registrar);
        $this->assertSame(1, $firstDayTicket->fresh()->missed_count);

        Carbon::setTestNow($time->copy()->addDay());
        $tomorrowTicket = $this->issueTicket($this->registrar);

        $this->assertSame(1, $tomorrowTicket->queue_number);
        $this->assertSame(0, $tomorrowTicket->missed_count);
        $this->assertNotSame(
            $firstDayTicket->queue_date->toDateString(),
            $tomorrowTicket->queue_date->toDateString()
        );
    }

    public function test_staff_can_manage_only_their_assigned_window(): void
    {
        $registrarTicket = $this->issueTicket($this->registrar);
        $cashierTicket = $this->issueTicket($this->cashier);
        $staff = $this->createStaff($this->registrar);

        $this->actingAs($staff)
            ->post(route('staff.call-next'))
            ->assertRedirect(route('staff.dashboard'));

        $this->assertSame('called', $registrarTicket->fresh()->status);
        $this->assertSame('waiting', $cashierTicket->fresh()->status);

        $this->actingAs($staff)
            ->post(route('staff.present', $cashierTicket))
            ->assertNotFound();
    }

    public function test_roles_cannot_open_the_other_role_dashboard_or_actions(): void
    {
        $staff = $this->createStaff($this->registrar);
        $admin = StaffUser::query()->create([
            'full_name' => 'System Admin',
            'username' => 'admin',
            'password_hash' => Hash::make('secret-password'),
            'role' => 'admin',
            'window_id' => null,
        ]);

        $this->actingAs($staff)->get(route('admin.dashboard'))->assertForbidden();
        $this->actingAs($staff)->get(route('admin.analytics.index'))->assertForbidden();
        $this->actingAs($admin)->get(route('staff.dashboard'))->assertForbidden();
        $this->actingAs($admin)->post(route('staff.call-next'))->assertForbidden();
    }

    public function test_login_uses_username_and_redirects_admin_to_the_admin_panel(): void
    {
        StaffUser::query()->create([
            'full_name' => 'System Admin',
            'username' => 'admin',
            'password_hash' => Hash::make('secret-password'),
            'role' => 'admin',
            'window_id' => null,
        ]);

        $this->post(route('login.submit'), [
            'username' => 'admin',
            'password' => 'secret-password',
        ])->assertRedirect(route('admin.dashboard'));
    }

    public function test_missed_calls_apply_five_and_ten_slot_pushbacks_before_forfeiture(): void
    {
        $ticket = $this->issueTicket($this->registrar);
        for ($number = 2; $number <= 18; $number++) {
            $this->issueTicket($this->registrar);
        }

        $queueService = app(QueueService::class);
        $time = Carbon::now();
        Carbon::setTestNow($time);

        $queueService->callNext($this->registrar);
        $time = $time->copy()->addMinutes(5);
        Carbon::setTestNow($time);
        $queueService->expireForWindow($this->registrar);
        $this->assertSame(6, $ticket->fresh()->queue_position);

        for ($number = 2; $number <= 6; $number++) {
            $called = $queueService->callNext($this->registrar);
            $this->assertNotNull($called);
            $this->assertSame($number, $called->queue_number);
            $queueService->markPresent($called);
            $queueService->complete($called);
        }

        $calledAgain = $queueService->callNext($this->registrar);
        $this->assertNotNull($calledAgain);
        $this->assertSame($ticket->queue_id, $calledAgain->queue_id);
        $time = $time->copy()->addMinutes(5);
        Carbon::setTestNow($time);
        $queueService->expireForWindow($this->registrar);
        $this->assertSame(11, $ticket->fresh()->queue_position);

        for ($number = 7; $number <= 16; $number++) {
            $called = $queueService->callNext($this->registrar);
            $this->assertNotNull($called);
            $this->assertSame($number, $called->queue_number);
            $queueService->markPresent($called);
            $queueService->complete($called);
        }

        $calledThirdTime = $queueService->callNext($this->registrar);
        $this->assertNotNull($calledThirdTime);
        $this->assertSame($ticket->queue_id, $calledThirdTime->queue_id);
        $time = $time->copy()->addMinutes(5);
        Carbon::setTestNow($time);
        $queueService->expireForWindow($this->registrar);

        $ticket->refresh();
        $this->assertSame('forfeited', $ticket->status);
        $this->assertSame(3, $ticket->missed_count);
        $this->assertSame([5, 10, null], $ticket->missedCalls()->pluck('slots_pushed')->all());
    }

    public function test_staff_and_admin_dashboards_render_their_separate_panels(): void
    {
        $staff = $this->createStaff($this->registrar);
        $admin = StaffUser::query()->create([
            'full_name' => 'System Admin',
            'username' => 'admin',
            'password_hash' => Hash::make('secret-password'),
            'role' => 'admin',
            'window_id' => null,
        ]);

        $staffResponse = $this->actingAs($staff)
            ->get(route('staff.dashboard'))
            ->assertOk()
            ->assertSee('Waiting list')
            ->assertSee('Staff queue control')
            ->assertSee('Are you sure you want to sign out?');

        $this->assertSame(
            1,
            substr_count($staffResponse->getContent(), 'onsubmit="return confirm(\'Are you sure you want to sign out?\')"')
        );

        $adminResponse = $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Admin dashboard')
            ->assertSee('Latest tickets today')
            ->assertSee('admin-queue-sidebar')
            ->assertSee('<svg', false)
            ->assertSee('Are you sure you want to sign out?');

        $this->assertSame(
            2,
            substr_count($adminResponse->getContent(), 'onsubmit="return confirm(\'Are you sure you want to sign out?\')"')
        );
    }

    public function test_admin_dashboard_shows_the_overview_without_management_forms(): void
    {
        $this->actingAs($this->createAdmin())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Admin dashboard')
            ->assertDontSee('Add a service window')
            ->assertDontSee('Create staff or admin account');
    }

    public function test_service_windows_menu_opens_a_separate_management_page(): void
    {
        $this->actingAs($this->createAdmin())
            ->get(route('admin.windows.index'))
            ->assertOk()
            ->assertSee('Add a service window')
            ->assertDontSee('Latest tickets today')
            ->assertDontSee('Create staff or admin account');
    }

    public function test_staff_accounts_menu_opens_a_separate_management_page(): void
    {
        $this->actingAs($this->createAdmin())
            ->get(route('admin.staff.index'))
            ->assertOk()
            ->assertSee('Create staff or admin account')
            ->assertDontSee('Add a service window')
            ->assertDontSee('Latest tickets today');
    }

    public function test_queues_menu_opens_a_separate_monitoring_page(): void
    {
        $this->actingAs($this->createAdmin())
            ->get(route('admin.queues.index'))
            ->assertOk()
            ->assertSee('Queue tickets')
            ->assertDontSee('Create staff or admin account')
            ->assertDontSee('Latest tickets today');
    }

    public function test_missed_calls_menu_opens_a_separate_history_page(): void
    {
        $this->actingAs($this->createAdmin())
            ->get(route('admin.missed-calls.index'))
            ->assertOk()
            ->assertSee('Today’s missed-call history')
            ->assertDontSee('Create staff or admin account')
            ->assertDontSee('Latest tickets today');
    }

    public function test_admin_can_filter_queue_history_and_compare_window_volume(): void
    {
        $this->travelTo(Carbon::parse('2026-03-10 09:00:00'));
        $this->createQueueRecord($this->registrar, '2026-03-10', 1, 'waiting');
        $this->createQueueRecord($this->registrar, '2026-03-10', 2, 'completed');
        $this->createQueueRecord($this->cashier, '2026-03-10', 1, 'called');
        $this->createQueueRecord($this->registrar, '2026-03-09', 3, 'completed');
        $this->assertDatabaseCount('queue_records', 4);

        $response = $this->actingAs($this->createAdmin())
            ->get(route('admin.analytics.index', [
                'date' => '2026-03-10',
                'window_id' => $this->registrar->window_id,
            ]));

        $response->assertOk()
            ->assertViewHas('ticketCount', 2)
            ->assertViewHas('waitingCount', 1)
            ->assertViewHas('servingCount', 0)
            ->assertViewHas('closedCount', 1)
            ->assertViewHas('busiestWindow', fn (array $window): bool => $window['window_name'] === 'Registrar'
                && $window['ticket_count'] === 2)
            ->assertViewHas('windowStats', fn ($stats): bool => $stats->pluck('ticket_count', 'window_name')->all() === [
                'Registrar' => 2,
                'Cashier' => 1,
            ])
            ->assertSee('Tickets in selection')
            ->assertSee('Tickets per window')
            ->assertSee('R001')
            ->assertSee('R002')
            ->assertDontSee('C001')
            ->assertDontSee('R003');
    }

    public function test_admin_analytics_rejects_invalid_date_and_window_filters(): void
    {
        $response = $this->actingAs($this->createAdmin())
            ->get(route('admin.analytics.index', [
                'date' => 'March 10, 2026',
                'window_id' => 999,
            ]));

        $response->assertInvalid([
            'date' => 'The date field must match the format Y-m-d.',
            'window_id' => 'The selected window id is invalid.',
        ]);
    }

    public function test_admin_can_create_a_staff_account_with_a_hashed_password(): void
    {
        $admin = StaffUser::query()->create([
            'full_name' => 'System Admin',
            'username' => 'admin',
            'password_hash' => Hash::make('secret-password'),
            'role' => 'admin',
            'window_id' => null,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.staff.store'), [
                'full_name' => 'Cashier Staff',
                'username' => 'cashier-staff',
                'role' => 'staff',
                'window_id' => $this->cashier->window_id,
                'password' => 'another-secret',
                'password_confirmation' => 'another-secret',
            ])
            ->assertRedirect(route('admin.staff.index'));

        $staff = StaffUser::query()->where('username', 'cashier-staff')->firstOrFail();
        $this->assertSame($this->cashier->window_id, $staff->window_id);
        $this->assertTrue(Hash::check('another-secret', $staff->password_hash));
        $this->assertNotSame('another-secret', $staff->password_hash);
    }

    public function test_admin_must_assign_a_unique_prefix_to_each_window(): void
    {
        $admin = StaffUser::query()->create([
            'full_name' => 'System Admin',
            'username' => 'admin',
            'password_hash' => Hash::make('secret-password'),
            'role' => 'admin',
            'window_id' => null,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.windows.store'), [
                'window_name' => 'Records',
                'queue_prefix' => 'r',
                'is_active' => '1',
            ])
            ->assertSessionHasErrors('queue_prefix');

        $this->assertDatabaseMissing('windows', ['window_name' => 'Records']);

        $this->actingAs($admin)
            ->post(route('admin.windows.store'), [
                'window_name' => 'Records',
                'queue_prefix' => 'REC',
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.windows.index'));

        $this->assertDatabaseHas('windows', [
            'window_name' => 'Records',
            'queue_prefix' => 'REC',
        ]);
    }

    public function test_mark_present_and_complete_follow_the_staff_queue_flow(): void
    {
        $ticket = $this->issueTicket($this->registrar);
        $staff = $this->createStaff($this->registrar);

        $this->actingAs($staff)->post(route('staff.call-next'));
        $this->post(route('staff.present', $ticket))->assertRedirect(route('staff.dashboard'));
        $this->assertSame('present', $ticket->fresh()->status);

        $this->post(route('staff.complete', $ticket))->assertRedirect(route('staff.dashboard'));
        $this->assertSame('completed', $ticket->fresh()->status);
    }

    private function issueTicket(Window $window, ?string $studentName = null): QueueRecord
    {
        $studentName ??= 'Student '.(QueueRecord::query()->count() + 1);

        $this->post(route('public.queue.issue', $window), [
            'student_name' => $studentName,
            'terms_agreed' => '1',
        ])
            ->assertRedirect();

        return QueueRecord::query()->latest('queue_id')->firstOrFail();
    }

    private function issueTicketInCurrentBrowser(Window $window, ?string $studentName = null): QueueRecord
    {
        $studentName ??= 'Student '.(QueueRecord::query()->count() + 1);

        $response = $this->post(route('public.queue.issue', $window), [
            'student_name' => $studentName,
            'terms_agreed' => '1',
        ]);
        $response->assertRedirect();

        $sessionCookie = $response->getCookie(config('session.cookie'));
        if ($sessionCookie !== null) {
            $this->withCookie(config('session.cookie'), $sessionCookie->getValue());
        }

        return QueueRecord::query()->latest('queue_id')->firstOrFail();
    }

    private function createQueueRecord(Window $window, string $queueDate, int $queueNumber, string $status): QueueRecord
    {
        return QueueRecord::query()->create([
            'window_id' => $window->window_id,
            'student_name' => 'Student '.$window->queue_prefix.$queueNumber,
            'ticket_prefix' => $window->queue_prefix,
            'queue_date' => $queueDate,
            'queue_number' => $queueNumber,
            'queue_position' => $queueNumber,
            'status' => $status,
            'missed_count' => 0,
            'terms_agreed' => true,
            'public_token' => (string) Str::uuid(),
        ]);
    }

    private function createStaff(Window $window): StaffUser
    {
        return StaffUser::query()->create([
            'full_name' => 'Window Staff',
            'username' => 'staff-'.$window->window_id,
            'password_hash' => Hash::make('secret-password'),
            'role' => 'staff',
            'window_id' => $window->window_id,
        ]);
    }

    private function createAdmin(): StaffUser
    {
        return StaffUser::query()->create([
            'full_name' => 'System Admin',
            'username' => 'admin',
            'password_hash' => Hash::make('secret-password'),
            'role' => 'admin',
            'window_id' => null,
        ]);
    }
}
