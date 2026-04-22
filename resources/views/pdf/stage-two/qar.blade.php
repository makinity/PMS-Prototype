<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Office Quarterly Accomplishment Report - LBAC Form No. 3</title>
    <style>
        @page { size: Legal landscape; margin: 14mm; }
        body { font-family: Arial, sans-serif; font-size: 11px; color: #000; }
        h1, h2, h3, h4 { margin: 4px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #000; padding: 6px 5px; vertical-align: top; text-align: left; }
        .center { text-align: center; }
        .no-border { border: none; }
        .no-border td { border: none; padding: 2px 4px; }
        .small { font-size: 10px; }
        .bullet { margin: 0; padding-left: 16px; }
        .signature { margin-top: 16px; }
    </style>
</head>
<body>
    <div class="center">
        <div><strong>Office Quarterly Accomplishment Report</strong></div>
        <div><strong>LBAC Form No. 3</strong></div>
        <h3 style="margin-top:6px;">QUARTERLY PHYSICAL REPORT OF OPERATIONS</h3>
        <div style="margin-top:4px;">For the Quarter Ending: <u>{{ $quarterEndingLabel }}</u></div>
        <div>Department / Office: <u>{{ $officeName }}</u></div>
        <div style="margin-top:4px;">Performance Period: {{ $periodName }} ({{ $periodRange }})</div>
    </div>

    <table style="margin-top:12px;">
        <thead>
            <tr>
                <th style="width: 10%;">PPA Code</th>
                <th style="width: 22%;">MFO / PPA</th>
                <th style="width: 24%;">Performance Indicator</th>
                <th style="width: 14%;">Target Output</th>
                <th style="width: 10%;">Actual Performance</th>
                <th style="width: 10%;">Variance as of {{ $quarterEndingLabel }}</th>
                <th style="width: 10%;">Remarks</th>
            </tr>
        </thead>
        <tbody>
            @forelse($qarHeader->rows as $row)
                <tr>
                    <td>{{ $row->ppa_code }}</td>
                    <td>{{ $row->mfo_title }}</td>
                    <td>{!! nl2br(e($row->indicator_text)) !!}</td>
                    <td>{{ $row->target_timeline }}</td>
                    <td class="center">
                        {{ (int) round((float) $row->actual_performance) }}
                    </td>
                    <td class="center">&mdash;</td>
                    <td>{{ $row->remarks }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="center">
                        No QAR rows found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="signature">
        <table class="no-border">
            <tr>
                <td style="width:50%; text-align:center;">
                    Prepared by:<br><br>
                    __________________________<br>
                    Department Head<br>
                    Date: ____________________
                </td>
                <td style="width:50%; text-align:center;">
                    <br><br>
                    __________________________<br>
                    Local Planning and Development Coordinator<br>
                    Date: ____________________
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
