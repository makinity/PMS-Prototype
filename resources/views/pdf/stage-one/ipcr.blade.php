<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Individual Performance Commitment and Review (IPCR)</title>

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
            font-size: 10px;
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
    <h3>INDIVIDUAL PERFORMANCE COMMITMENT AND REVIEW (IPCR)</h3>
    <p>Stage I – Performance Commitment</p>
    <p>{{ $ipcr['period'] }}</p>
</div>

<table class="meta">
    <tr>
        <td><strong>Employee:</strong> {{ $ipcr['employee_name'] }}</td>
        <td><strong>Position:</strong> {{ $ipcr['position'] }}</td>
    </tr>
    <tr>
        <td><strong>Office / Unit:</strong> {{ $ipcr['office'] }}</td>
        <td><strong>Immediate Supervisor:</strong> {{ $ipcr['supervisor'] }}</td>
    </tr>
    <tr>
        <td><strong>Department Head:</strong> {{ $ipcr['dept_head'] }}</td>
        <td><strong>Rating Period:</strong> {{ $ipcr['period'] }}</td>
    </tr>
</table>

<table>
    <thead>
        <tr>
            <th rowspan="2" style="width:16%;">Major Output / Activity</th>
            <th rowspan="2" style="width:18%;">Success Indicator</th>
            <th rowspan="2" style="width:8%;">Actual Accomplishments</th>
            <th colspan="4" style="width:8%;">Rating</th>
            <th colspan="5" style="width:10%;">Numerical Rating</th>
            <th rowspan="2" style="width:10%;">Remarks</th>
            <th rowspan="2" style="width:10%;">Timeline / Target</th>
            <th rowspan="2" style="width:5%;">Weight (%)</th>
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

        <!-- CORE FUNCTIONS -->
        <tr>
            <td colspan="15" style="font-weight:bold; text-align:left;">
                A. CORE FUNCTIONS (80%)
            </td>
        </tr>

        @foreach ($ipcr['core_functions'] as $row)
        <tr>
            <td>{{ $row['mfo'] }}</td>
            <td>{{ $row['target'] }}</td>

            <!-- Stage 1: intentionally blank -->
            <td></td>

            <!-- Ratings: Stage 1 blank -->
            <td></td><td></td><td></td><td></td>

            <!-- Numerical Rating: Stage 1 blank -->
            <td></td><td></td><td></td><td></td><td></td>

            <td></td>
            <td class="center">{{ $row['timeline'] }}</td>
            <td class="center">{{ $row['weight'] }}</td>
        </tr>
        @endforeach

        <!-- SUPPORT FUNCTIONS -->
        <tr>
            <td colspan="15" style="font-weight:bold; text-align:left;">
                C. SUPPORT FUNCTIONS (20%)
            </td>
        </tr>

        @foreach ($ipcr['support_functions'] as $row)
        <tr>
            <td>{{ $row['mfo'] }}</td>
            <td>{{ $row['target'] }}</td>

            <!-- Stage 1: intentionally blank -->
            <td></td>

            <!-- Ratings: Stage 1 blank -->
            <td></td><td></td><td></td><td></td>

            <!-- Numerical Rating: Stage 1 blank -->
            <td></td><td></td><td></td><td></td><td></td>

            <td></td>
            <td class="center">{{ $row['timeline'] }}</td>
            <td class="center">{{ $row['weight'] }}</td>
        </tr>
        @endforeach

    </tbody>
</table>

<br><br>

<table class="meta">
    <tr>
        <td class="center">
            Committed by:<br><br>
            ____________________________<br>
            {{ $ipcr['employee_name'] }}<br>
            Employee
        </td>
        <td class="center">
            Endorsed by:<br><br>
            ____________________________<br>
            {{ $ipcr['supervisor'] }}<br>
            Immediate Supervisor
        </td>
        <td class="center">
            Approved by:<br><br>
            ____________________________<br>
            {{ $ipcr['dept_head'] }}<br>
            Department Head
        </td>
    </tr>
</table>

</body>
</html>
