<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Unit Work Plan (Annex B)</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 20mm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
            table-layout: fixed;
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

        .header {
            text-align: center;
            margin-bottom: 14px;
        }

        .meta {
            margin-bottom: 10px;
            width: 100%;
        }

        .meta td {
            border: none;
            padding: 3px;
            font-size: 11px;
            vertical-align: top;
        }

        .section {
            font-weight: bold;
            text-align: left;
            background: #e6e6e6;
            padding: 6px;
        }

        ul {
            margin: 0;
            padding-left: 16px;
        }

        .center {
            text-align: center;
        }

        .standards-table {
            width: 100%;
            border-collapse: collapse;
            margin: -6px;
        }

        .standards-table th,
        .standards-table td {
            border: 1px solid #000;
            text-align: center;
            padding: 4px;
            height: 24px;
        }

        .standards-table th {
            background: #f2f2f2;
        }

        .standards-table tr:first-child th {
            border-bottom: 1px solid #000;
        }

        .standards-table td {
            border-top: none;
        }
    </style>
</head>
<body>

@php
function pdfSummarizeStandardSegment(array $values, int $limit = 26): string
{
    $text = implode('; ', array_filter($values, fn ($value) => $value !== null && $value !== ''));
    if ($text === '') {
        return '';
    }
    if (function_exists('mb_strlen') && mb_strlen($text) > $limit) {
        $text = mb_substr($text, 0, $limit - 1) . '…';
    } elseif (strlen($text) > $limit) {
        $text = substr($text, 0, $limit - 1) . '…';
    }
    return $text;
}

function pdfFormatStandardsCell(array $standard): string
{
    $parts = [];
    foreach (['q' => 'Q', 'e' => 'E', 't' => 'T'] as $key => $label) {
        $summary = pdfSummarizeStandardSegment($standard[$key] ?? [], 28);
        if ($summary === '') {
            continue;
        }
        $parts[] = "{$label}: {$summary}";
    }
    return implode(' | ', $parts);
}
@endphp

<div class="header">
    <h3>UNIT WORK PLAN (UWP)</h3>
    <p>{{ $uwp['period'] }}</p>
</div>

<table class="meta">
    <tr>
        <td width="50%"><strong>Office / Unit:</strong> {{ $uwp['office'] }}</td>
        <td width="50%"><strong>Supervisor:</strong> {{ $uwp['supervisor'] }}</td>
    </tr>
    <tr>
        <td><strong>Department Head:</strong> {{ $uwp['dept_head'] }}</td>
        <td></td>
    </tr>
</table>

<table>
    <thead>
        <tr>
            <th style="width:20%;">PPA / MFO</th>
            <th style="width:40%;">Success Indicators</th>
            <th style="width:15%;">Allotted Budget</th>
            <th style="width:25%;">
                <table class="standards-table">
                    <tr>
                        <th colspan="5">Standards per Success Indicator</th>
                    </tr>
                    <tr>
                        <th style="width:20%;">5</th>
                        <th style="width:20%;">4</th>
                        <th style="width:20%;">3</th>
                        <th style="width:20%;">2</th>
                        <th style="width:20%;">1</th>
                    </tr>
                </table>
            </th>
        </tr>
    </thead>
    <tbody>
        <!-- CORE FUNCTIONS -->
        <tr>
            <td colspan="4" class="section">A. CORE FUNCTIONS (80%)</td>
        </tr>

        @foreach ($uwp['outputs'] as $row)
            @if(str_contains($row['function'], 'Core'))
                @php
                    $indicatorCount = count($row['success_indicators']);
                @endphp

                @foreach ($row['success_indicators'] as $index => $indicator)
                <tr>
                    @if ($index === 0)
                        <td rowspan="{{ $indicatorCount }}" style="vertical-align:top;">{{ $row['mfo'] }}</td>
                    @endif
                    <td>{{ $indicator }}</td>
                    <td></td>
                    <td>
                        <table class="standards-table">
                            <tr>
                                @php
                                    $standards = $row['indicator_standards'][$indicator] ?? [];
                                @endphp
                                @foreach (range(5, 1) as $rating)
                                    @php
                                        $detail = $standards[$rating] ?? ['q' => [], 'e' => [], 't' => []];
                                        $summary = pdfFormatStandardsCell($detail);
                                    @endphp
                                    <td style="width:20%; font-size:9px; line-height:1.2; word-wrap:break-word;">
                                        {{ $summary ?: '—' }}
                                    </td>
                                @endforeach
                            </tr>
                        </table>
                    </td>
                </tr>
                @endforeach
            @endif
        @endforeach

        <!-- SUPPORT FUNCTIONS -->
        <tr>
            <td colspan="4" class="section">B. SUPPORT FUNCTIONS (20%)</td>
        </tr>

        @foreach ($uwp['outputs'] as $row)
            @if(str_contains($row['function'], 'Support'))
                @php
                    $indicatorCount = count($row['success_indicators']);
                @endphp

                @foreach ($row['success_indicators'] as $index => $indicator)
                <tr>
                    @if ($index === 0)
                        <td rowspan="{{ $indicatorCount }}" style="vertical-align:top;">{{ $row['mfo'] }}</td>
                    @endif
                    <td>{{ $indicator }}</td>
                    <td></td>
                    <td>
                        <table class="standards-table">
                            <tr>
                                @php
                                    $standards = $row['indicator_standards'][$indicator] ?? [];
                                @endphp
                                @foreach (range(5, 1) as $rating)
                                    @php
                                        $detail = $standards[$rating] ?? ['q' => [], 'e' => [], 't' => []];
                                        $summary = pdfFormatStandardsCell($detail);
                                    @endphp
                                    <td style="width:20%; font-size:9px; line-height:1.2; word-wrap:break-word;">
                                        {{ $summary ?: '—' }}
                                    </td>
                                @endforeach
                            </tr>
                        </table>
                    </td>
                </tr>
                @endforeach
            @endif
        @endforeach
    </tbody>
</table>

<br><br>

<p><strong>Prepared by:</strong> ____________________________</p>
<p><strong>Approved by (PMT):</strong> ____________________________</p>

</body>
</html>
