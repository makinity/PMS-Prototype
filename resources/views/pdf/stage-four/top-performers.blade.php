<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Top Performing Employee Report</title>
    <style>
        @page { size: legal landscape; margin: 12mm; }
        body { font-family: Arial, sans-serif; font-size: 10px; color: #000; }
        .header-table, .signature-table, .report-table { width: 100%; border-collapse: collapse; }
        .header-table td { border: none; vertical-align: top; }
        .center { text-align: center; }
        .right { text-align: right; }
        .report-table th, .report-table td { border: 1px solid #000; padding: 4px 5px; }
        .report-table th { text-align: center; font-weight: bold; }
        .small { font-size: 8px; }
        .signature-table td { border: none; text-align: center; padding-top: 18px; }
        .line { border-top: 1px solid #000; margin: 22px auto 0; width: 70%; }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td style="width:18%;">
                <div class="center small">OFFICIAL SEAL</div>
            </td>
            <td class="center" style="width:64%;">
                <div>Republic of the Philippines</div>
                <div>Province of Davao del Sur</div>
                <div style="font-size:18px; font-weight:bold; margin-top:6px;">Performance Management Team (PMT)</div>
                <div style="font-size:18px; font-weight:bold;">TOP PERFORMING EMPLOYEE REPORT</div>
                <div style="margin-top:4px;">For the Period of {{ $activePeriod?->name ?? '--' }}</div>
            </td>
            <td class="right small" style="width:18%;">THIS IS A SYSTEM GENERATED REPORT</td>
        </tr>
    </table>

    <table class="header-table" style="margin-top:14px;">
        <tr>
            <td style="width:20%; font-weight:bold;">Agency Name:</td>
            <td style="width:30%;">{{ $agencyName }}</td>
            <td style="width:20%;"></td>
            <td style="width:30%;"></td>
        </tr>
        <tr>
            <td style="font-weight:bold;">Address:</td>
            <td>{{ $address }}</td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td style="font-weight:bold;">Performance Semester:</td>
            <td>{{ $activePeriod?->name ?? '--' }}</td>
            <td></td>
            <td></td>
        </tr>
    </table>

    <table class="report-table" style="margin-top:12px;">
        <thead>
            <tr>
                <th colspan="10">DETAILS</th>
            </tr>
            <tr>
                <th rowspan="2" style="width:4%;">Rank</th>
                <th colspan="4">Employee Name</th>
                <th rowspan="2" style="width:16%;">Designation</th>
                <th rowspan="2" style="width:18%;">Office</th>
                <th colspan="2" style="width:12%;">Rating</th>
                <th rowspan="2" style="width:10%;">Remarks</th>
            </tr>
            <tr>
                <th style="width:11%;">Surname</th>
                <th style="width:11%;">Given Name</th>
                <th style="width:11%;">Middle Name</th>
                <th style="width:6%;">Name Ext.</th>
                <th style="width:6%;">Numerical</th>
                <th style="width:6%;">Adjective</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($topEmployees as $row)
                <tr>
                    <td class="center">{{ $row['rank'] }}</td>
                    <td>{{ $row['surname'] }}</td>
                    <td>{{ $row['given_name'] }}</td>
                    <td>{{ $row['middle_name'] }}</td>
                    <td class="center">{{ $row['name_extension'] }}</td>
                    <td>{{ $row['designation'] }}</td>
                    <td>{{ $row['office_name'] }}</td>
                    <td class="center">{{ number_format((float) $row['official_score'], 2) }}</td>
                    <td class="center">{{ $row['official_rating'] }}</td>
                    <td>{{ $row['remarks'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="center">No top performing employees identified for the active period.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="signature-table" style="margin-top:28px;">
        <tr>
            <td style="width:33%;">
                <strong>Prepared by:</strong>
                <div class="line"></div>
                <div>{{ $preparedBy }}</div>
            </td>
            <td style="width:33%;">
                <strong>Reviewed by:</strong>
                <div class="line"></div>
                <div>{{ $reviewedBy }}</div>
            </td>
            <td style="width:34%;">
                <strong>Approved by:</strong>
                <div class="line"></div>
                <div>{{ $approvedBy }}</div>
            </td>
        </tr>
    </table>
</body>
</html>
