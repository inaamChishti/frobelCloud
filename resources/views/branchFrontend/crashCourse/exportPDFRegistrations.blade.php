<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Crash Course Registration Records Export</title>
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
    <h2 style="text-align: center; color: #20439F;">Crash Course Registration Records</h2>
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
                <th>Registration Date</th>
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
</body>
</html>

