<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin OPCR Report</title>
    <style>
        @page { size: legal landscape; margin: 12mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #000; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; table-layout: fixed; }
        th, td { border: 1px solid #000; padding: 5px; vertical-align: top; }
        th { background: #f3f4f6; text-align: center; }
        .header { text-align: center; margin-bottom: 8px; }
        .meta td { border: none; padding: 2px 4px; }
        .section { background: #e5e7eb; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h3 style="margin: 0;">ADMIN OPCR REPORT</h3>
        <div>Office: {{ $officeName }}</div>
        <div>Performance Period: {{ $periodLabel }}</div>
    </div>

    <table class="meta">
        <tr>
            <td style="width: 40%;"><strong>Office Head:</strong> {{ $officeHeadName }}</td>
            <td style="width: 40%;"><strong>Department Head:</strong> {{ $deptHeadName }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th style="width: 22%;">MFO / PPA</th>
                <th style="width: 34%;">Success Indicator</th>
                <th style="width: 14%;">Accountable</th>
                <th style="width: 30%;">Target / Standard</th>
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
                    <td>{{ $row['success_indicator'] }}</td>
                    <td>{{ $row['accountable'] }}</td>
                    <td>{{ $row['standard'] !== '' ? $row['standard'] : '—' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
