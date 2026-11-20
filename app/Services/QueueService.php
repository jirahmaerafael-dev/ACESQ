<?php

namespace App\Services;

use App\Models\QueueRecord;
use App\Models\Window;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class QueueService
{
    public const GRACE_PERIOD_MINUTES = 5;

    public function issue(Window $window, string $studentName, string $studentSessionId): QueueRecord
    {
        return DB::transaction(function () use ($window, $studentName, $studentSessionId): QueueRecord {
            $lockedWindows = Window::query()
                ->orderBy('window_id')
                ->lockForUpdate()
                ->get();
            $lockedWindow = $lockedWindows->firstWhere('window_id', $window->getKey());

            abort_if($lockedWindow === null, 404);

            if (! $lockedWindow->is_active) {
                abort(404);
            }

            $queueDate = today()->toDateString();
            $normalizedName = Str::lower(Str::squish($studentName));
            $sessionHash = hash('sha256', $studentSessionId);
            $existingQueue = QueueRecord::query()
                ->whereDate('queue_date', $queueDate)
                ->whereIn('status', ['waiting', 'called', 'present'])
                ->where(function ($query) use ($normalizedName, $sessionHash): void {
                    $query->whereRaw('LOWER(student_name) = ?', [$normalizedName])
                        ->orWhere('student_session_hash', $sessionHash);
                })
                ->orderBy('queue_id')
                ->first();

            if ($existingQueue !== null) {
                return $existingQueue;
            }

            $queueNumber = (int) QueueRecord::query()
                ->where('window_id', $lockedWindow->window_id)
                ->whereDate('queue_date', $queueDate)
                ->max('queue_number') + 1;
            $queuePosition = (int) QueueRecord::query()
                ->where('window_id', $lockedWindow->window_id)
                ->whereDate('queue_date', $queueDate)
                ->where('status', 'waiting')
                ->max('queue_position') + 1;

            return $lockedWindow->queueRecords()->create([
                'student_name' => Str::squish($studentName),
                'ticket_prefix' => $lockedWindow->queue_prefix,
                'student_session_hash' => $sessionHash,
                'queue_date' => $queueDate,
                'queue_number' => $queueNumber,
                'queue_position' => $queuePosition,
                'status' => 'waiting',
                'missed_count' => 0,
                'terms_agreed' => true,
                'public_token' => (string) Str::uuid(),
            ]);
        });
    }

    public function callNext(Window $window): ?QueueRecord
    {
        $this->expireCalls($window->window_id);

        return DB::transaction(function () use ($window): ?QueueRecord {
            $lockedWindow = Window::query()
                ->lockForUpdate()
                ->findOrFail($window->getKey());
            $queueDate = today()->toDateString();

            $hasActiveCall = QueueRecord::query()
                ->where('window_id', $lockedWindow->window_id)
                ->whereDate('queue_date', $queueDate)
                ->whereIn('status', ['called', 'present'])
                ->exists();

            if ($hasActiveCall) {
                throw ValidationException::withMessages([
                    'queue' => 'Mark the current student present or completed before calling the next number.',
                ]);
            }

            $next = QueueRecord::query()
                ->where('window_id', $lockedWindow->window_id)
                ->whereDate('queue_date', $queueDate)
                ->where('status', 'waiting')
                ->orderBy('queue_position')
                ->lockForUpdate()
                ->first();

            if ($next === null) {
                return null;
            }

            $next->update([
                'status' => 'called',
                'called_at' => now(),
            ]);

            $this->normalizeWaitingPositions($lockedWindow->window_id, $queueDate);

            return $next->refresh();
        });
    }

    public function markPresent(QueueRecord $queueRecord): QueueRecord
    {
        $this->expireCalls($queueRecord->window_id);

        return DB::transaction(function () use ($queueRecord): QueueRecord {
            Window::query()->lockForUpdate()->findOrFail($queueRecord->window_id);
            $record = $this->findTodayRecord($queueRecord);

            if ($record->status !== 'called') {
                throw ValidationException::withMessages([
                    'queue' => 'Only a currently called student can be marked present.',
                ]);
            }

            $record->update(['status' => 'present']);

            return $record->refresh();
        });
    }

    public function complete(QueueRecord $queueRecord): QueueRecord
    {
        $this->expireCalls($queueRecord->window_id);

        return DB::transaction(function () use ($queueRecord): QueueRecord {
            Window::query()->lockForUpdate()->findOrFail($queueRecord->window_id);
            $record = $this->findTodayRecord($queueRecord);

            if ($record->status !== 'present') {
                throw ValidationException::withMessages([
                    'queue' => 'Mark the student present before completing the transaction.',
                ]);
            }

            $record->update(['status' => 'completed']);
            $this->normalizeWaitingPositions($record->window_id, today()->toDateString());

            return $record->refresh();
        });
    }

    public function expireCalls(?int $windowId = null): int
    {
        $expiredCount = 0;
        $windows = Window::query()
            ->when($windowId !== null, fn ($query) => $query->whereKey($windowId))
            ->get();

        foreach ($windows as $window) {
            $expiredCount += DB::transaction(function () use ($window): int {
                $lockedWindow = Window::query()
                    ->lockForUpdate()
                    ->find($window->getKey());

                if ($lockedWindow === null) {
                    return 0;
                }

                $expiredRecords = QueueRecord::query()
                    ->where('window_id', $lockedWindow->window_id)
                    ->whereDate('queue_date', today()->toDateString())
                    ->where('status', 'called')
                    ->whereNotNull('called_at')
                    ->where('called_at', '<=', now()->subMinutes(self::GRACE_PERIOD_MINUTES))
                    ->orderBy('called_at')
                    ->lockForUpdate()
                    ->get();

                foreach ($expiredRecords as $record) {
                    $this->recordMissedCall($record);
                }

                return $expiredRecords->count();
            });
        }

        return $expiredCount;
    }

    public function expireForWindow(Window $window): int
    {
        return $this->expireCalls($window->window_id);
    }

    private function findTodayRecord(QueueRecord $queueRecord): QueueRecord
    {
        return QueueRecord::query()
            ->whereKey($queueRecord->getKey())
            ->where('window_id', $queueRecord->window_id)
            ->whereDate('queue_date', today()->toDateString())
            ->lockForUpdate()
            ->firstOrFail();
    }

    private function recordMissedCall(QueueRecord $record): void
    {
        $callNumber = $record->missed_count + 1;
        $slotsPushed = match ($callNumber) {
            1 => 5,
            2 => 10,
            default => null,
        };

        $record->missed_count = min($callNumber, 3);

        if ($slotsPushed === null) {
            $record->status = 'forfeited';
            $record->save();
        } else {
            $record->status = 'waiting';
            $record->save();
            $this->pushBack($record, $slotsPushed);
        }

        $record->missedCalls()->create([
            'call_number' => $callNumber,
            'slots_pushed' => $slotsPushed,
            'missed_at' => now(),
        ]);
    }

    private function pushBack(QueueRecord $record, int $slots): void
    {
        $waiting = QueueRecord::query()
            ->where('window_id', $record->window_id)
            ->whereDate('queue_date', today()->toDateString())
            ->where('status', 'waiting')
            ->where('queue_id', '<>', $record->getKey())
            ->orderBy('queue_position')
            ->lockForUpdate()
            ->get()
            ->all();

        array_splice($waiting, min($slots, count($waiting)), 0, [$record]);

        foreach ($waiting as $index => $waitingRecord) {
            $waitingRecord->update(['queue_position' => $index + 1]);
        }
    }

    private function normalizeWaitingPositions(int $windowId, string $queueDate): void
    {
        $waitingRecords = QueueRecord::query()
            ->where('window_id', $windowId)
            ->whereDate('queue_date', $queueDate)
            ->where('status', 'waiting')
            ->orderBy('queue_position')
            ->orderBy('queue_id')
            ->lockForUpdate()
            ->get();

        foreach ($waitingRecords as $index => $record) {
            if ($record->queue_position !== $index + 1) {
                $record->update(['queue_position' => $index + 1]);
            }
        }
    }
}
