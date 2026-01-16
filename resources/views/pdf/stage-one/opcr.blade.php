<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Office Performance Commitment and Review (OPCR)</title>

    <style>
        @page {
            size: A4 landscape;
            margin: 20mm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #000;
        }

        h3 {
            margin: 0;
            font-size: 14px;
        }

        .header {
            text-align: center;
            margin-bottom: 14px;
        }

        .meta {
            width: 100%;
            margin-bottom: 10px;
        }

        .meta td {
            border: none;
            padding: 4px;
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #000;
            padding: 4px;
            vertical-align: middle;
        }

        th {
            text-align: center;
            font-weight: bold;
            background: #f2f2f2;
        }

        .center {
            text-align: center;
        }

        .signature {
            width: 100%;
            margin-top: 30px;
        }

        .signature td {
            border: none;
            padding-top: 25px;
            font-size: 11px;
        }
    </style>
</head>
<body>

<!-- HEADER -->
<div class="header">
    <h3>OFFICE PERFORMANCE COMMITMENT AND REVIEW (OPCR)</h3>
    <p>{{ $opcr['period'] }}</p>
</div>

<!-- META INFORMATION -->
<table class="meta">
    <tr>
        <td><strong>Office / Unit:</strong> {{ $opcr['office'] }}</td>
        <td><strong>Office Head:</strong> {{ $opcr['office_head'] }}</td>
    </tr>
    <tr>
        <td><strong>Department Head:</strong> {{ $opcr['dept_head'] }}</td>
        <td></td>
    </tr>
</table>

<!-- OPCR TABLE -->
<table>
    <thead>
        <tr>
            <th rowspan="2" style="width:14%;">MFOs / PPAs</th>
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
            <th>Q</th>
            <th>E</th>
            <th>T</th>
            <th>A</th>
            <th>5</th>
            <th>4</th>
            <th>3</th>
            <th>2</th>
            <th>1</th>
        </tr>
    </thead>

    <tbody>
        @php
            $currentFunction = null;
        @endphp

        @foreach ($opcr['outputs'] as $row)

            @if ($currentFunction !== $row['function_type'])
                @php $currentFunction = $row['function_type']; @endphp
                <tr>
                    <td colspan="15"
                        style="font-weight:bold; background:#f5f5f5; text-align:left;">
                        {{ $row['function_type'] === 'core'
                            ? 'A. CORE FUNCTIONS (80%)'
                            : 'C. SUPPORT FUNCTIONS (20%)' }}
                    </td>
                </tr>
            @endif

            <tr>
                <!-- STAGE 1 PLANNING DATA -->
                <td>{{ $row['mfo'] }}</td>
                <td>{{ $row['success_indicator'] }}</td>
                <td class="center">{{ $row['budget'] }}</td>
                <td class="center">{{ $row['accountable'] }}</td>

                <!-- FUTURE STAGES -->
                <td></td>

                <!-- Rating -->
                <td></td>
                <td></td>
                <td></td>
                <td></td>

                <!-- Numerical Rating -->
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>

                <!-- Remarks -->
                <td></td>

                <!-- Standards -->
                <td>{{ $row['standard'] }}</td>
            </tr>

        @endforeach

    </tbody>
</table>

<!-- SIGNATURE BLOCK -->
<table class="signature">
    <tr>
        <td>
            Prepared by:<br><br>
            ___________________________<br>
            {{ $opcr['office_head'] }}<br>
            Office Head
        </td>
        <td>
            Approved by:<br><br>
            ___________________________<br>
            Department Head
        </td>
        <td>
            ___________________________<br>
            Governor
        </td>
    </tr>
</table>

</body>
</html>
