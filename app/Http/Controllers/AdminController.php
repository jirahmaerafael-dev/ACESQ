<?php

namespace App\Http\Controllers;

use App\Models\MissedCall;
use App\Models\QueueRecord;
use App\Models\StaffUser;
use App\Models\Window;
use App\Services\QueueService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(QueueService $queueService): View
    {
        $queueService->expireCalls();

        $todayRecords = QueueRecord::query()->whereDate('queue_date', today()->toDateString());
        $windowCount = Window::query()->count();
        $openWindowCount = Window::query()->where('is_active', true)->count();
        $staffCount = StaffUser::query()->count();
        $ticketCount = (clone $todayRecords)->count();
        $waitingCount = (clone $todayRecords)->where('status', 'waiting')->count();
        $servingCount = (clone $todayRecords)->whereIn('status', ['called', 'present'])->count();
        $recentRecords = (clone $todayRecords)
            ->with('window')
            ->orderByDesc('created_at')
            ->orderByDesc('queue_id')
            ->limit(8)
            ->get();

        return view('admin.index', compact(
            'windowCount',
            'openWindowCount',
            'staffCount',
            'ticketCount',
            'waitingCount',
            'servingCount',
            'recentRecords'
        ));
    }

    public function windows(): View
    {
        $windows = Window::query()
            ->withCount('staffUsers')
            ->orderBy('window_id')
            ->get();

        return view('admin.windows', compact('windows'));
    }

    public function staff(): View
    {
        $windows = Window::query()
            ->orderBy('window_id')
            ->get();
        $staff = StaffUser::query()
            ->with('window')
            ->orderBy('full_name')
            ->get();

        return view('admin.staff', compact('windows', 'staff'));
    }

    public function queues(QueueService $queueService): View
    {
        $queueService->expireCalls();

        $records = QueueRecord::query()
            ->whereDate('queue_date', today()->toDateString());
        $records = $records
            ->with('window', 'missedCalls')
            ->orderBy('window_id')
            ->orderBy('queue_position')
            ->get();

        return view('admin.queues', compact('records'));
    }

    public function missedCalls(QueueService $queueService): View
    {
        $queueService->expireCalls();

        $missedCalls = MissedCall::query()
            ->whereHas('queueRecord', function ($query): void {
                $query->whereDate('queue_date', today()->toDateString());
            })
            ->with('queueRecord.window')
            ->orderByDesc('missed_at')
            ->get();

        return view('admin.missed-calls', compact('missedCalls'));
    }

    public function analytics(Request $request): View
    {
        $validated = $request->validate([
            'date' => ['nullable', 'date_format:Y-m-d'],
            'window_id' => ['nullable', 'integer', Rule::exists('windows', 'window_id')],
        ]);
        $selectedDate = $validated['date'] ?? today()->toDateString();
        $selectedWindowId = $validated['window_id'] ?? null;

        $windows = Window::query()
            ->withCount([
                'queueRecords as tickets_count' => function (Builder $query) use ($selectedDate): void {
                    $query->whereDate('queue_date', $selectedDate);
                },
            ])
            ->orderByDesc('tickets_count')
            ->orderBy('window_id')
            ->get();
        $maximumTicketCount = max(1, (int) $windows->max('tickets_count'));
        $windowStats = $windows->map(function (Window $window) use ($maximumTicketCount): array {
            $ticketCount = (int) $window->tickets_count;

            return [
                'window_name' => $window->window_name,
                'queue_prefix' => $window->queue_prefix,
                'ticket_count' => $ticketCount,
                'percentage' => (int) round($ticketCount / $maximumTicketCount * 100),
            ];
        });
        $busiestWindow = $windowStats->first(
            fn (array $windowStat): bool => $windowStat['ticket_count'] > 0
        );

        $historyQuery = QueueRecord::query()
            ->whereDate('queue_date', $selectedDate)
            ->when(
                $selectedWindowId !== null,
                fn (Builder $query): Builder => $query->where('window_id', $selectedWindowId)
            );
        $ticketCount = (clone $historyQuery)->count();
        $waitingCount = (clone $historyQuery)->where('status', 'waiting')->count();
        $servingCount = (clone $historyQuery)->whereIn('status', ['called', 'present'])->count();
        $closedCount = (clone $historyQuery)->whereIn('status', ['completed', 'forfeited'])->count();
        $records = (clone $historyQuery)
            ->with('window', 'missedCalls')
            ->orderByDesc('created_at')
            ->orderByDesc('queue_id')
            ->paginate(25)
            ->withQueryString();
        $filterWindows = Window::query()->orderBy('window_id')->get();

        return view('admin.analytics', compact(
            'selectedDate',
            'selectedWindowId',
            'windowStats',
            'busiestWindow',
            'ticketCount',
            'waitingCount',
            'servingCount',
            'closedCount',
            'records',
            'filterWindows'
        ));
    }

    public function storeWindow(Request $request): RedirectResponse
    {
        $request->merge([
            'queue_prefix' => Str::upper(trim((string) $request->input('queue_prefix'))),
        ]);
        $validated = $request->validate([
            'window_name' => ['required', 'string', 'max:255', 'unique:windows,window_name'],
            'queue_prefix' => ['required', 'string', 'regex:/\A[A-Z0-9]{1,8}\z/', 'unique:windows,queue_prefix'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        Window::query()->create([
            'window_name' => $validated['window_name'],
            'queue_prefix' => $validated['queue_prefix'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.windows.index')->with('success', 'Window created.');
    }

    public function updateWindow(Request $request, Window $window): RedirectResponse
    {
        $request->merge([
            'queue_prefix' => Str::upper(trim((string) $request->input('queue_prefix'))),
        ]);
        $validated = $request->validate([
            'window_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('windows', 'window_name')->ignore($window->window_id, 'window_id'),
            ],
            'queue_prefix' => [
                'required',
                'string',
                'regex:/\A[A-Z0-9]{1,8}\z/',
                Rule::unique('windows', 'queue_prefix')->ignore($window->window_id, 'window_id'),
            ],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $window->update([
            'window_name' => $validated['window_name'],
            'queue_prefix' => $validated['queue_prefix'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.windows.index')->with('success', 'Window updated.');
    }

    public function deleteWindow(Window $window): RedirectResponse
    {
        if ($window->queueRecords()->exists() || $window->staffUsers()->exists()) {
            throw ValidationException::withMessages([
                'window' => 'A window with queue history or assigned staff cannot be deleted. Deactivate it instead.',
            ]);
        }

        $window->delete();

        return redirect()->route('admin.windows.index')->with('success', 'Window deleted.');
    }

    public function storeStaff(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:100', 'unique:staff_users,username'],
            'role' => ['required', Rule::in(['staff', 'admin'])],
            'window_id' => ['nullable', 'required_if:role,staff', 'exists:windows,window_id'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        StaffUser::query()->create([
            'full_name' => $validated['full_name'],
            'username' => $validated['username'],
            'role' => $validated['role'],
            'window_id' => $validated['role'] === 'staff' ? $validated['window_id'] : null,
            'password_hash' => $validated['password'],
        ]);

        return redirect()->route('admin.staff.index')->with('success', 'Staff account created.');
    }

    public function updateStaff(Request $request, StaffUser $staffUser): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'username' => [
                'required',
                'string',
                'max:100',
                Rule::unique('staff_users', 'username')->ignore($staffUser->staff_id, 'staff_id'),
            ],
            'role' => ['required', Rule::in(['staff', 'admin'])],
            'window_id' => ['nullable', 'required_if:role,staff', 'exists:windows,window_id'],
        ]);
        $windowId = $validated['window_id'] ?? null;

        if (
            (int) $staffUser->staff_id === (int) auth()->id()
            && ($validated['role'] !== 'admin' || $windowId !== null)
        ) {
            throw ValidationException::withMessages([
                'staff' => 'You cannot change your own administrator role or window assignment.',
            ]);
        }

        $staffUser->update([
            'full_name' => $validated['full_name'],
            'username' => $validated['username'],
            'role' => $validated['role'],
            'window_id' => $validated['role'] === 'staff' ? $windowId : null,
        ]);

        return redirect()->route('admin.staff.index')->with('success', 'Staff account updated.');
    }

    public function resetStaffPassword(Request $request, StaffUser $staffUser): RedirectResponse
    {
        $validated = $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $staffUser->update(['password_hash' => $validated['password']]);

        return redirect()->route('admin.staff.index')->with('success', 'Password reset.');
    }

    public function deleteStaff(StaffUser $staffUser): RedirectResponse
    {
        if ((int) $staffUser->staff_id === (int) auth()->id()) {
            throw ValidationException::withMessages([
                'staff' => 'You cannot delete the account currently signed in.',
            ]);
        }

        $staffUser->delete();

        return redirect()->route('admin.staff.index')->with('success', 'Staff account deleted.');
    }
}
