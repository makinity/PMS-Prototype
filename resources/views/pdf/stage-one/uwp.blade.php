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
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        th, td {
            border: 1px solid #000;
            padding: 4px;
            vertical-align: middle;
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

        .meta td {
            border: none;
            padding: 3px;
            font-size: 11px;
        }

        .center {
            text-align: center;
        }
    </style>
</head>
<body>

<div class="header">
    <h3>UNIT WORK PLAN (UWP)</h3>
    <p>{{ $uwp['period'] }}</p>
</div>

<table class="meta">
    <tr>
        <td><strong>Office / Unit:</strong> {{ $uwp['office'] }}</td>
        <td><strong>Supervisor:</strong> {{ $uwp['supervisor'] }}</td>
    </tr>
    <tr>
        <td><strong>Department Head:</strong> {{ $uwp['dept_head'] }}</td>
        <td></td>
    </tr>
</table>

<table>
    <thead>
        <tr>
            <th rowspan="2" style="width:14%;">PPA / MFO</th>
            <th rowspan="2" style="width:18%;">Success Indicators</th>
            <th rowspan="2" style="width:7%;">Allotted Budget</th>
            <th rowspan="2" style="width:10%;">Division / Individual Accountable</th>
            <th rowspan="2" style="width:12%;">6 Months Summary of Accomplishment</th>
            <th colspan="4" style="width:8%;">Rating</th>
            <th colspan="5" style="width:10%;">Numerical Rating</th>
            <th rowspan="2" style="width:10%;">Remarks</th>
            <th rowspan="2" style="width:11%;">Standards per Success Indicator</th>
        </tr>
        <tr>
            <th style="width:2%;">Q</th>
            <th style="width:2%;">E</th>
            <th style="width:2%;">T</th>
            <th style="width:2%;">A</th>
            <th style="width:2%;">5</th>
            <th style="width:2%;">4</th>
            <th style="width:2%;">3</th>
            <th style="width:2%;">2</th>
            <th style="width:2%;">1</th>
        </tr>
    </thead>

    <tbody>

    <!-- CORE FUNCTIONS HEADER -->
    <tr>
        <td colspan="16" style="font-weight:bold; text-align:left;">
            A. CORE FUNCTIONS (80%)
        </td>
    </tr>

    @foreach ($uwp['outputs'] as $row)
        @if(str_contains($row['function'], 'Core'))
        <tr>
            <td>{{ $row['mfo'] }}</td>
            <td>
                <ul style="margin:0; padding-left:14px;">
                    @foreach ($row['success_indicators'] as $indicator)
                        <li>{{ $indicator }}</li>
                    @endforeach
                </ul>
            </td>
            <td class="center">{{ $row['budget'] ?? '' }}</td>
            <td class="center">{{ $row['accountable'] ?? '' }}</td>

            <!-- Stage 1: intentionally blank -->
            <td></td>

            <td></td><td></td><td></td><td></td>
            <td></td><td></td><td></td><td></td><td></td>

            <td></td>
            <td>{{ $row['standard'] ?? '' }}</td>
        </tr>
        @endif
    @endforeach

    <!-- SUPPORT FUNCTIONS HEADER -->
    <tr>
        <td colspan="16" style="font-weight:bold; text-align:left;">
            C. SUPPORT FUNCTIONS (20%)
        </td>
    </tr>

    @foreach ($uwp['outputs'] as $row)
        @if(str_contains($row['function'], 'Support'))
        <tr>
            <td>{{ $row['mfo'] }}</td>
            <td>
                <ul style="margin:0; padding-left:14px;">
                    @foreach ($row['success_indicators'] as $indicator)
                        <li>{{ $indicator }}</li>
                    @endforeach
                </ul>
            </td>
            <td class="center">{{ $row['budget'] ?? '' }}</td>
            <td class="center">{{ $row['accountable'] ?? '' }}</td>

            <!-- Stage 1: intentionally blank -->
            <td></td>

            <td></td><td></td><td></td><td></td>
            <td></td><td></td><td></td><td></td><td></td>

            <td></td>
            <td>{{ $row['standard'] ?? '' }}</td>
        </tr>
        @endif
    @endforeach

</tbody>

</table>

<br><br>

<p><strong>Prepared by:</strong> ____________________________</p>
<p><strong>Approved by (PMT):</strong> ____________________________</p>

</body>
</html>
