<?php

namespace App\Http\Controllers;

use App\Models\QueueRecord;
use App\Models\Window;
use App\Services\QueueService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QueueController extends Controller
{
    public function index(): View
    {
        $windows = Window::query()
            ->where('is_active', true)
            ->orderBy('window_id')
            ->get();

        return view('layouts.public', compact('windows'));
    }

    public function terms(Window $window): View
    {
        abort_unless($window->is_active, 404);

        return view('queue-terms', compact('window'));
    }

    public function issue(Request $request, Window $window, QueueService $queueService): RedirectResponse
    {
        $validated = $request->validate([
            'student_name' => ['required', 'string', 'max:255'],
            'terms_agreed' => ['required', 'accepted'],
        ]);

        $record = $queueService->issue(
            $window,
            $validated['student_name'],
            $request->session()->getId()
        );

        $redirect = redirect()->route('public.queue.show', $record->public_token);

        if (! $record->wasRecentlyCreated) {
            $redirect->with('notice', 'You already have an active queue. Continue using this ticket until your service is complete.');
        }

        return $redirect;
    }

    public function show(string $token, QueueService $queueService): View
    {
        $record = $this->findByToken($token);
        $queueService->expireForWindow($record->window);
        $record->refresh()->load('window', 'missedCalls');
        $status = $this->statusPayload($record);

        return view('queue-board', compact('record', 'status'));
    }

    public function status(string $token, QueueService $queueService): JsonResponse
    {
        $record = $this->findByToken($token);
        $queueService->expireForWindow($record->window);
        $record->refresh()->load('window', 'missedCalls');

        return response()->json($this->statusPayload($record))
            ->header('Cache-Control', 'no-store, private');
    }

    public function cancel(Request $request, string $token, QueueService $queueService): RedirectResponse
    {
        $record = $this->findByToken($token);
        $studentSessionHash = hash('sha256', $request->session()->getId());

        abort_unless(hash_equals($record->student_session_hash ?? '', $studentSessionHash), 404);

        $request->validate([
            'confirm_cancel' => ['required', 'accepted'],
        ]);

        $queueService->cancel($record);

        return redirect()
            ->route('public.queue.show', $token)
            ->with('notice', 'Your ticket has been cancelled.');
    }

    private function findByToken(string $token): QueueRecord
    {
        return QueueRecord::query()
            ->where('public_token', $token)
            ->firstOrFail();
    }

    /**
     * @return array{
     *     queue_number: int,
     *     ticket_number: string,
     *     window_name: string,
     *     status: string,
     *     position: int|null,
     *     waiting_count: int,
     *     serving_number: int|null,
     *     serving_ticket_number: string|null,
     *     estimated_wait_minutes: int|null,
     *     countdown_seconds: int|null,
     *     missed_count: int,
     *     active_today: bool
     * }
     */
    private function statusPayload(QueueRecord $record): array
    {
        $activeToday = $record->queue_date->toDateString() === today()->toDateString();
        $waiting = collect();
        $serving = null;

        if ($activeToday) {
            $waiting = QueueRecord::query()
                ->where('window_id', $record->window_id)
                ->whereDate('queue_date', today()->toDateString())
                ->where('status', 'waiting')
                ->orderBy('queue_position')
                ->get(['queue_id', 'queue_number']);
            $serving = QueueRecord::query()
                ->where('window_id', $record->window_id)
                ->whereDate('queue_date', today()->toDateString())
                ->whereIn('status', ['called', 'present'])
                ->orderBy('called_at')
                ->with('window')
                ->first();
        }

        $position = null;
        if ($activeToday && $record->status === 'waiting') {
            $index = $waiting->search(
                fn (QueueRecord $waitingRecord): bool => $waitingRecord->queue_id === $record->queue_id
            );
            $position = $index === false ? null : $index + 1;
        }

        $countdownSeconds = null;
        if ($record->status === 'called' && $record->called_at !== null && $activeToday) {
            $deadline = $record->called_at->copy()->addMinutes(QueueService::GRACE_PERIOD_MINUTES);
            $countdownSeconds = max(0, $deadline->getTimestamp() - now()->getTimestamp());
        }

        $estimatedWait = null;
        if ($position !== null) {
            $estimatedWait = max(0, ($position - 1) * QueueService::GRACE_PERIOD_MINUTES)
                + ($serving === null ? 0 : QueueService::GRACE_PERIOD_MINUTES);
        }

        return [
            'queue_number' => $record->queue_number,
            'ticket_number' => $record->displayNumber(),
            'window_name' => $record->window->window_name,
            'status' => $record->status,
            'position' => $position,
            'waiting_count' => $waiting->count(),
            'serving_number' => $serving?->queue_number,
            'serving_ticket_number' => $serving?->displayNumber(),
            'estimated_wait_minutes' => $estimatedWait,
            'countdown_seconds' => $countdownSeconds,
            'missed_count' => $record->missed_count,
            'active_today' => $activeToday,
        ];
    }
}
