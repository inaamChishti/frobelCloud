<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            color: #333;
            margin: 0;
            padding: 10mm;
        }
        .report-header {
            text-align: center;
            font-weight: bold;
            font-size: 16pt;
            color: #2048AC;
            margin-bottom: 10mm;
        }
        .report-info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8mm;
            font-size: 9pt;
        }
        .report-info-table td {
            border: 1px solid #2048AC;
            padding: 3mm;
        }
        .report-info-table .info-header {
            font-weight: bold;
            background: #AED6F1;
            color: #2048AC;
            width: 16.66%;
        }
        .report-info-table .info-content {
            background: #fff;
            width: 16.66%;
        }
        .subject-overview-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8mm;
            font-size: 8pt;
        }
        .subject-overview-table th,
        .subject-overview-table td {
            border: 1px solid #2048AC;
            padding: 2mm;
            text-align: left;
        }
        .subject-overview-table th {
            background-color: #e6f0ff;
            color: #2048AC;
            font-weight: 600;
            font-size: 7pt;
        }
        .subject-overview-table td {
            background-color: #fff;
        }
        .performance-section {
            margin-bottom: 10mm;
            page-break-inside: avoid;
        }
        .performance-title {
            color: #FF8C00;
            font-weight: bold;
            font-size: 12pt;
            margin-bottom: 5mm;
        }
        .subject-name {
            color: #28a745;
            font-weight: bold;
        }
        .performance-info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5mm;
            font-size: 9pt;
        }
        .performance-info-table td {
            border: 1px solid #2048AC;
            padding: 3mm;
        }
        .performance-info-table .info-header {
            font-weight: bold;
            background: #AED6F1;
            color: #2048AC;
            width: 30%;
        }
        .performance-info-table .info-content {
            background: #fff;
        }
        .test-records-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 3mm;
            font-size: 8pt;
        }
        .test-records-table th,
        .test-records-table td {
            border: 1px solid #2048AC;
            padding: 2mm;
            text-align: left;
        }
        .test-records-table th {
            background-color: #e6f0ff;
            color: #2048AC;
            font-weight: 600;
            font-size: 7pt;
        }
        .test-records-table td {
            background-color: #fff;
        }
        .flag-pass {
            color: #28a745;
            font-weight: bold;
        }
        .flag-fail {
            color: #dc3545;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="report-header">Progress Tracking Report</div>

    <!-- Header Info - Single Line Table -->
    <table class="report-info-table">
        <tr>
            <td class="info-header">Family ID</td>
            <td class="info-content">{{ $family_id ?? '-' }}</td>
            <td class="info-header">Full Name</td>
            <td class="info-content">{{ $student_name ?? '-' }}</td>
            <td class="info-header">School Year</td>
            <td class="info-content">{{ $school_year ?? '-' }}</td>
        </tr>
    </table>

    <!-- Subject Overview Table -->
    @if(isset($data['subject_overview']) && count($data['subject_overview']) > 0)
        <table class="subject-overview-table">
            <thead>
                <tr>
                    <th>Subject Name</th>
                    <th>Start Date</th>
                    <th>Tier</th>
                    <th>Session Booked</th>
                    <th>Session Attended</th>
                    <th>Attendance</th>
                    <th>Home Work</th>
                    <th>Behaviour</th>
                    <th>Performance</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['subject_overview'] as $subject)
                    <tr>
                        <td>{{ $subject['subject_name'] ?? '-' }}</td>
                        <td>{{ $subject['start_date'] ?? '-' }}</td>
                        <td>{{ $subject['tier'] ?? '-' }}</td>
                        <td>{{ $subject['sessions_booked'] ?? '-' }}</td>
                        <td>{{ $subject['sessions_attended'] ?? '-' }}</td>
                        <td>{{ $subject['attendance'] ?? '-' }}</td>
                        <td>{{ $subject['homework'] ?? '-' }}</td>
                        <td>{{ $subject['behaviour'] ?? '-' }}</td>
                        <td>{{ $subject['performance'] ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Performance Sections -->
    @if(isset($data['performance_sections']) && count($data['performance_sections']) > 0)
        @foreach($data['performance_sections'] as $section)
            <div class="performance-section">
                <div class="performance-title">Performance</div>

                <!-- Subject Info -->
                <table class="performance-info-table">
                    <tr>
                        <td class="info-header">Subject Name</td>
                        <td class="info-content"><span class="subject-name">{{ $section['subject_name'] ?? '-' }}</span></td>
                    </tr>
                    <tr>
                        <td class="info-header">Current Grade</td>
                        <td class="info-content">{{ $section['current_grade'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="info-header">Target Grade</td>
                        <td class="info-content">{{ $section['target_grade'] ?? '-' }}</td>
                    </tr>
                </table>

                <!-- Test Records Table -->
                <table class="test-records-table">
                    <thead>
                        <tr>
                            <th>Test Date</th>
                            <th>Book Name</th>
                            <th>Test No</th>
                            <th>Test Score</th>
                            <th>Flag</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($section['test_records']) && count($section['test_records']) > 0)
                            @foreach($section['test_records'] as $test)
                                <tr>
                                    <td>{{ $test['test_date'] ?? '-' }}</td>
                                    <td>{{ $test['book_name'] ?? '-' }}</td>
                                    <td>{{ $test['test_no'] ?? '-' }}</td>
                                    <td>{{ $test['test_score'] ?? '-' }}</td>
                                    <td>
                                        @php
                                            $score = str_replace('%', '', $test['test_score'] ?? '0');
                                            $scoreNum = floatval($score);
                                        @endphp
                                        @if($scoreNum >= 70)
                                            <span class="flag-pass">Pass</span>
                                        @else
                                            <span class="flag-fail">Fail</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" style="text-align: center;">No test records available</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        @endforeach
    @endif
</body>
</html>

