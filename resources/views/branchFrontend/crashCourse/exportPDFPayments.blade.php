<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Crash Course Payment History Export</title>
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
    <h2 style="text-align: center; color: #20439F;">Crash Course Payment History</h2>
    <p style="text-align: center;">Generated on: {{ date('d/m/Y H:i:s') }}</p>
    
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
</body>
</html>

