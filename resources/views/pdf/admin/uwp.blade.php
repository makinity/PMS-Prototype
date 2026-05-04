<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin UWP Report</title>
    <style>
        @page { size: a4 landscape; margin: 12mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #000; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; table-layout: fixed; }
        th, td { border: 1px solid #000; padding: 5px; vertical-align: top; }
        th { background: #f3f4f6; text-align: center; }
        .header { text-align: center; margin-bottom: 8px; }
        .meta td { border: none; padding: 2px 4px; }
        .section { background: #e5e7eb; font-weight: bold; }
        .mono { font-size: 9px; line-height: 1.35; }
        ul { margin: 0; padding-left: 16px; }
        li { margin-bottom: 3px; }
    </style>
</head>
<body>
    <div class="header">
        <h3 style="margin: 0;">ADMIN UNIT WORK PLAN REPORT</h3>
        <div>Office: {{ $officeName }}</div>
        <div>Performance Period: {{ $periodLabel }}</div>
    </div>

    <table class="meta">
        <tr>
            <td style="width: 40%;"><strong>Supervisor:</strong> {{ $supervisorName }}</td>
            <td style="width: 40%;"><strong>Department Head:</strong> {{ $deptHeadName }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th style="width: 20%;">PPA / MFO</th>
                <th style="width: 28%;">Success Indicators</th>
                <th style="width: 12%;">Function</th>
                <th style="width: 40%;">Standards Summary</th>
            </tr>
        </thead>
        <tbody>
            @php $currentSection = null; @endphp
            @foreach ($outputs as $row)
                @if ($currentSection !== $row['function_type'])
                    @php $currentSection = $row['function_type']; @endphp
                    <tr>
                        <td colspan="4" class="section">
                            {{ $currentSection === 'core' ? 'Core Functions' : 'Support Functions' }}
                        </td>
                    </tr>
                @endif
                <tr>
                    <td>{{ $row['mfo'] }}</td>
                    <td>
                        <ul>
                            @foreach ($row['success_indicators'] as $indicator)
                                <li>{{ $indicator }}</li>
                            @endforeach
                        </ul>
                    </td>
                    <td>{{ $row['function'] }}</td>
                    <td class="mono">
                        @foreach ($row['success_indicators'] as $indicator)
                            <div style="margin-bottom: 8px;">
                                <strong>{{ $indicator }}</strong><br>
                                @php $ratings = $row['indicator_standards'][$indicator] ?? []; @endphp
                                @foreach ([5, 4, 3, 2, 1] as $rating)
                                    @php $detail = $ratings[$rating] ?? ['q' => [], 'e' => [], 't' => []]; @endphp
                                    <div style="margin-top: 3px;">
                                        <strong>{{ $rating }}:</strong>
                                        @php
                                            $parts = [];
                                            foreach (['q' => 'Q', 'e' => 'E', 't' => 'T'] as $key => $label) {
                                                $values = array_values(array_filter($detail[$key] ?? []));
                                                if ($values !== []) {
                                                    $parts[] = $label . ' ' . implode('; ', $values);
                                                }
                                            }
                                        @endphp
                                        {{ $parts !== [] ? implode(' | ', $parts) : '—' }}
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
