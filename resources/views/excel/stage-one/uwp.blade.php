<html>
    <!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <title>UWP Export</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: top;
        }

        th {
            background: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }

        .header, .metadata {
            margin-top: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 16px;
        }

        .metadata td {
            border: none;
            padding: 3px;
            font-size: 11px;
        }

        .section {
            background: #ededed;
            font-weight: bold;
            text-align: left;
        }

        .standards-block {
            font-size: 10px;
            line-height: 1.2;
        }

        .standards-block strong {
            display: inline-block;
            width: 70px;
        }
    </style>
</head>
<body>

@php
function formatStandardsRows(array $standardsMap, string $indicator): array
{
    $rows = [];
    foreach (range(5, 1) as $rating) {
        $entry = $standardsMap[$indicator][$rating] ?? ['q' => [], 'e' => [], 't' => []];
        $mapped = [];
        foreach (['q' => 'Q', 'e' => 'E', 't' => 'T'] as $key => $label) {
            $values = array_filter($entry[$key] ?? [], fn ($value) => $value !== '' && $value !== null);
            if (count($values)) {
                $mapped[$label] = $values;
            }
        }
        $rows[$rating] = $mapped;
    }
    return $rows;
}
@endphp

<div class="header">
    <h1>UNIT WORK PLAN (UWP)</h1>
    <div>Period: January – June 2026</div>
</div>

<table class="metadata">
    <tr>
        <td><strong>Office / Unit:</strong> {{ $uwp['office'] }}</td>
        <td><strong>Supervisor:</strong> {{ $uwp['supervisor'] }}</td>
        <td><strong>Department Head:</strong> {{ $uwp['dept_head'] }}</td>
    </tr>
</table>

<table>
    <thead>
        <tr>
            <th style="width:25%;">PPA / MFO</th>
            <th style="width:30%;">Success Indicator</th>
            <th style="width:15%;">Allotted Budget</th>
            <th style="width:30%;">Standards per Success Indicator (Q/E/T)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="4" class="section">A. CORE FUNCTIONS (80%)</td>
        </tr>

        @foreach ($uwp['outputs'] as $row)
            @if (str_contains($row['function'], 'Core'))
                @php
                    $indicatorCount = count($row['success_indicators']);
                @endphp

                @foreach ($row['success_indicators'] as $index => $indicator)
                    @php
                        $standardsRows = formatStandardsRows($standardsMap, $indicator);
                    @endphp
                    <tr>
                        @if ($index === 0)
                            <td rowspan="{{ $indicatorCount }}">{{ $row['mfo'] }}</td>
                        @endif
                        <td>{{ $indicator }}</td>
                        <td></td>
                        <td class="standards-block">
                            @foreach ($standardsRows as $rating => $values)
                                <div style="margin-bottom:4px;">
                                    <strong>Rating {{ $rating }}:</strong>
                                    @foreach ($values as $label => $items)
                                        {{ $label }}: {{ implode(', ', $items) }}@if(!$loop->last) • @endif
                                    @endforeach
                                </div>
                            @endforeach
                        </td>
                    </tr>
                @endforeach
            @endif
        @endforeach

        <tr>
            <td colspan="4" class="section">B. SUPPORT FUNCTIONS (20%)</td>
        </tr>

        @foreach ($uwp['outputs'] as $row)
            @if (str_contains($row['function'], 'Support'))
                @php
                    $indicatorCount = count($row['success_indicators']);
                @endphp

                @foreach ($row['success_indicators'] as $index => $indicator)
                    @php
                        $standardsRows = formatStandardsRows($standardsMap, $indicator);
                    @endphp
                    <tr>
                        @if ($index === 0)
                            <td rowspan="{{ $indicatorCount }}">{{ $row['mfo'] }}</td>
                        @endif
                        <td>{{ $indicator }}</td>
                        <td></td>
                        <td class="standards-block">
                            @foreach ($standardsRows as $rating => $values)
                                <div style="margin-bottom:4px;">
                                    <strong>Rating {{ $rating }}:</strong>
                                    @foreach ($values as $label => $items)
                                        {{ $label }}: {{ implode(', ', $items) }}@if(!$loop->last) • @endif
                                    @endforeach
                                </div>
                            @endforeach
                        </td>
                    </tr>
                @endforeach
            @endif
        @endforeach
    </tbody>
</table>

</body>
</html>
