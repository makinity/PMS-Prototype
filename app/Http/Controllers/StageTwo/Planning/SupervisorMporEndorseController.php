<?php

namespace App\Http\Controllers\StageTwo\Planning;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SupervisorMporEndorseController extends Controller
{
    private const SESSION_KEY = 'stage2_supervisor_mpor_records';

    public function endorse(Request $request, int $mpor)
    {
        $records = $request->session()->get(self::SESSION_KEY, []);
        if (!is_array($records) || empty($records)) {
            return redirect()->route('supervisor.mpor')
                ->with('error', 'No MPOR record found to endorse.');
        }

        $recordFound = false;

        foreach ($records as &$record) {
            if ((int) ($record['id'] ?? 0) !== $mpor) {
                continue;
            }

            $recordFound = true;
            if (($record['status'] ?? '') === 'endorsed') {
                return redirect()->route('supervisor.mpor')
                    ->with('info', 'MPOR is already endorsed.');
            }

            $record['status'] = 'endorsed';
            $record['endorsed_by'] = $request->user()?->id;
            $record['endorsed_at'] = now()->toDateTimeString();
        }
        unset($record);

        if (!$recordFound) {
            return redirect()->route('supervisor.mpor')
                ->with('error', 'No MPOR record found to endorse.');
        }

        $request->session()->put(self::SESSION_KEY, array_values($records));

        return redirect()->route('supervisor.mpor')
            ->with('success', 'MPOR endorsed and forwarded to the Department Head.');
    }
}

