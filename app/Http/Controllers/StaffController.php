<?php

namespace App\Http\Controllers;

use App\Models\MissedCall;
use App\Models\QueueRecord;
use App\Models\Window;
use App\Services\QueueService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StaffController extends Controller
{
    public function index(QueueService $queueService): View
    {
        $window = auth()->user()->window;
        abort_unless($window instanceof Window, 403, 'This staff account has no assigned window.');
        $queueService->expireForWindow($window);

        $todayRecords = QueueRecord::query()
            ->where('window_id', $window->window_id)
            ->whereDate('queue_date', today()->toDateString());
        $waiting = (clone $todayRecords)
            ->with('missedCalls', 'window')
            ->where('status', 'waiting')
            ->orderBy('queue_position')
            ->get();
        $current = (clone $todayRecords)
            ->with('missedCalls', 'window')
            ->whereIn('status', ['called', 'present'])
            ->orderBy('called_at')
            ->first();
        $missedCalls = MissedCall::query()
            ->whereHas('queueRecord', function ($query) use ($window): void {
                $query->where('window_id', $window->window_id)
                    ->whereDate('queue_date', today()->toDateString());
            })
            ->with('queueRecord.window')
            ->orderByDesc('missed_at')
            ->get();

        return view('staff', compact('window', 'waiting', 'current', 'missedCalls'));
    }

    public function callNext(QueueService $queueService): RedirectResponse
    {
        $window = auth()->user()->window;
        abort_unless($window instanceof Window, 403);
        $called = $queueService->callNext($window);

        return redirect()
            ->route('staff.dashboard')
            ->with($called ? 'success' : 'error', $called
                ? 'Ticket '.$called->displayNumber().' has been called.'
                : 'There are no waiting students in this window.');
    }

    public function markPresent(QueueRecord $queueRecord, QueueService $queueService): RedirectResponse
    {
        $this->ensureAssignedWindowOwns($queueRecord);
        $queueService->markPresent($queueRecord);

        return redirect()->route('staff.dashboard')->with('success', 'Student marked present.');
    }

    public function complete(QueueRecord $queueRecord, QueueService $queueService): RedirectResponse
    {
        $this->ensureAssignedWindowOwns($queueRecord);
        $queueService->complete($queueRecord);

        return redirect()->route('staff.dashboard')->with('success', 'Transaction completed.');
    }

    private function ensureAssignedWindowOwns(QueueRecord $queueRecord): void
    {
        abort_unless(
            (int) auth()->user()->window_id === $queueRecord->window_id,
            404
        );
    }
}
