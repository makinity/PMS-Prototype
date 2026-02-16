<?php

namespace App\Http\Controllers\StageTwo\Planning;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SupervisorMporEndorseController extends Controller
{
    private const SESSION_KEY = 'stage2_supervisor_mpor_records';

    public function approve(Request $request, int $mpor)
    {
        $records = $request->session()->get(self::SESSION_KEY, []);
        if (!is_array($records) || empty($records)) {
            return redirect()->route('supervisor.mpor.index')
                ->with('error', 'No MPOR record found to approve.');
        }

        $recordFound = false;

        foreach ($records as &$record) {
            if ((int) ($record['id'] ?? 0) !== $mpor) {
                continue;
            }

            $recordFound = true;
            $status = strtolower((string) ($record['status'] ?? 'submitted'));

            if ($status === 'endorsed') {
                return redirect()->route('supervisor.mpor.index', ['status' => 'endorsed'])
                    ->with('info', 'MPOR already endorsed.');
            }

            if ($status === 'approved') {
                return redirect()->route('supervisor.mpor.index', ['status' => 'approved'])
                    ->with('info', 'MPOR already approved.');
            }

            if ($status !== 'submitted') {
                return redirect()->route('supervisor.mpor.index', ['status' => 'submitted'])
                    ->with('info', 'MPOR must be submitted before approval.');
            }

            $record['status'] = 'approved';
            $record['approved_by'] = $request->user()?->id;
            $record['approved_at'] = now()->toDateTimeString();
        }
        unset($record);

        if (!$recordFound) {
            return redirect()->route('supervisor.mpor.index')
                ->with('error', 'No MPOR record found to approve.');
        }

        $request->session()->put(self::SESSION_KEY, array_values($records));

        return redirect()->route('supervisor.mpor.index', ['status' => 'approved'])
            ->with('success', 'MPOR approved. You may now endorse it to Dept Head.');
    }

    public function endorse(Request $request, int $mpor)
    {
        $records = $request->session()->get(self::SESSION_KEY, []);
        if (!is_array($records) || empty($records)) {
            return redirect()->route('supervisor.mpor.index')
                ->with('error', 'No MPOR record found to endorse.');
        }

        $recordFound = false;

        foreach ($records as &$record) {
            if ((int) ($record['id'] ?? 0) !== $mpor) {
                continue;
            }

            $recordFound = true;
            $status = strtolower((string) ($record['status'] ?? 'submitted'));

            if ($status === 'endorsed') {
                return redirect()->route('supervisor.mpor.index', ['status' => 'endorsed'])
                    ->with('info', 'MPOR already endorsed.');
            }

            if ($status !== 'approved') {
                return redirect()->back()
                    ->with('info', 'MPOR must be approved first before endorsement.');
            }

            $record['status'] = 'endorsed';
            $record['endorsed_by'] = $request->user()?->id;
            $record['endorsed_at'] = now()->toDateTimeString();
        }
        unset($record);

        if (!$recordFound) {
            return redirect()->route('supervisor.mpor.index')
                ->with('error', 'No MPOR record found to endorse.');
        }

        $request->session()->put(self::SESSION_KEY, array_values($records));

        return redirect()->route('supervisor.mpor.index', ['status' => 'endorsed'])
            ->with('success', 'MPOR endorsed and forwarded to the Department Head.');
    }
}
