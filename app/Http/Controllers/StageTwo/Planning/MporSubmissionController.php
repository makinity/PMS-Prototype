<?php

namespace App\Http\Controllers\StageTwo\Planning;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MporSubmissionController extends Controller
{
    public function submit(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            abort(403, 'Unauthorized.');
        }

        $monthYear = (string) $request->input('month_year', 'January 2026');
        $statusKey = $this->statusSessionKey((int) $user->id, $monthYear);

        $currentStatus = (string) data_get($request->session()->get($statusKey), 'status', '');
        if (in_array($currentStatus, ['submitted', 'endorsed'], true)) {
            return redirect()->route('employee.mpor')
                ->with('info', 'MPOR is already submitted.');
        }

        $request->session()->put($statusKey, [
            'status' => 'submitted',
            'month_year' => $monthYear,
            'submitted_at' => now()->toDateTimeString(),
        ]);

        return redirect()->route('employee.mpor')
            ->with('success', 'MPOR submitted successfully. It is now locked and forwarded for review.');
    }

    private function statusSessionKey(int $userId, string $monthYear): string
    {
        return 'mpor_status_' . $userId . '_' . Str::slug($monthYear, '_');
    }
}

