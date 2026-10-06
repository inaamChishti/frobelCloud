<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Crash Course Records Export</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #20439F;
            color: white;
            font-weight: bold;
        }
        .section-title {
            background-color: #f0f0f0;
            padding: 8px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <h2 style="text-align: center; color: #20439F;">Crash Course Records Export</h2>
    <p style="text-align: center;">Generated on: {{ date('d/m/Y H:i:s') }}</p>
    
    <!-- Registrations Section -->
    <div class="section-title">Registration Records</div>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Candidate Name</th>
                <th>Family ID</th>
                <th>Package Name</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @if($registrations && count($registrations) > 0)
                @foreach($registrations as $reg)
                    @php
                        $packageObj = collect($packages)->where('id', $reg->package_id)->first();
                    @endphp
                    <tr>
                        <td>{{ $reg->id }}</td>
                        <td>{{ $reg->candidate_name ?? 'N/A' }}</td>
                        <td>{{ $reg->family_id ?? 'N/A' }}</td>
                        <td>{{ $packageObj ? $packageObj->name : 'N/A' }}</td>
                        <td>{{ $reg->created_at ? date('d/m/Y', strtotime($reg->created_at)) : 'N/A' }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="5" style="text-align: center;">No registration records found</td>
                </tr>
            @endif
        </tbody>
    </table>
    
    <!-- Payments Section -->
    <div class="section-title">Payment Records</div>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Candidate Name</th>
                <th>Family ID</th>
                <th>Amount Paid</th>
                <th>Payment Method</th>
                <th>Payment Date</th>
            </tr>
        </thead>
        <tbody>
            @if($payments && count($payments) > 0)
                @foreach($payments as $payment)
                    <tr>
                        <td>{{ $payment->id }}</td>
                        <td>{{ $payment->candidate_name ?? 'N/A' }}</td>
                        <td>{{ $payment->family_id ?? 'N/A' }}</td>
                        <td>£{{ number_format($payment->amount_paid ?? 0, 2) }}</td>
                        <td>{{ $payment->payment_method ? ucfirst(str_replace('_', ' ', $payment->payment_method)) : 'N/A' }}</td>
                        <td>{{ $payment->payment_date ? date('d/m/Y', strtotime($payment->payment_date)) : 'N/A' }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="6" style="text-align: center;">No payment records found</td>
                </tr>
            @endif
        </tbody>
    </table>
    
    <!-- Attendance Section -->
    <div class="section-title">Attendance Records</div>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Candidate Name</th>
                <th>Family ID</th>
                <th>Subject</th>
                <th>Teacher</th>
                <th>Time Slot</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @if($attendanceRecords && count($attendanceRecords) > 0)
                @foreach($attendanceRecords as $attendance)
                    <tr>
                        <td>{{ $attendance->id }}</td>
                        <td>{{ $attendance->candidate_name ?? 'N/A' }}</td>
                        <td>{{ $attendance->family_id ?? 'N/A' }}</td>
                        <td>{{ $attendance->subject ?? 'N/A' }}</td>
                        <td>{{ $attendance->teacher ?? 'N/A' }}</td>
                        <td>{{ $attendance->timeslot ?? 'N/A' }}</td>
                        <td>{{ $attendance->created_at ? date('d/m/Y', strtotime($attendance->created_at)) : 'N/A' }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="7" style="text-align: center;">No attendance records found</td>
                </tr>
            @endif
        </tbody>
    </table>
</body>
</html>

