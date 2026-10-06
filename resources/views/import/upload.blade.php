<!DOCTYPE html>
<html>
<head>
    <title>Import Database</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Import Database Tables</h1>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('import.upload') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="table">Select Table</label>
                <select name="table" class="form-control" required>
                    <option value="">Select a table</option>
                    <option value="access_permissions">Access Permissions</option>
                    <option value="activity_log">Activity Log</option>
                    <option value="admission">Admission</option>
                    <option value="assign_books">Assign Books</option>
                    <option value="attendance">Attendance</option>
                    <option value="books">Books</option>
                    <option value="consents">Consents</option>
                    <option value="deleted_payments">Deleted Payments</option>
                    <option value="email_job">Email Job</option>
                    <option value="exam_entry_form">Exam Entry Form</option>
                    <option value="exam_table">Exam Table</option>
                    <option value="family_comments">Family Comments</option>
                    <option value="feesection">Feesection</option>
                    <option value="general_timetables">General Timetables</option>
                    <option value="guardian">Guardian</option>
                    <option value="history">History</option>
                    <option value="iag_meetings">IAG Meetings</option>
                    <option value="ip_addresses">IP Addresses</option>
                    <option value="kin">Kin</option>
                    <option value="mails">Mails</option>
                    <option value="medical_condition">Medical Condition</option>
                    <option value="migrations">Migrations</option>
                    <option value="mock_results">Mock Results</option>
                    <option value="notes">Notes</option>
                    <option value="notifications">Notifications</option>
                    <option value="payment">Payment</option>
                    <option value="payment_comments">Payment Comments</option>
                    <option value="payment_log">Payment Log</option>
                    <option value="purchases">Purchases</option>
                    <option value="role">Role</option>
                    <option value="sales">Sales</option>
                    <option value="staff_attendances">Staff Attendances</option>
                    <option value="studentdata">Student Data</option>
                    <option value="student_requests">Student Requests</option>
                    <option value="student_tests">Student Tests</option>
                    <option value="subjects">Subjects</option>
                    <option value="teachers_subject">Teachers Subject</option>
                    <option value="teacher_comments">Teacher Comments</option>
                    <option value="timetable">Timetable</option>
                </select>
            </div>

            <div class="form-group">
                <label for="file">Upload Excel File</label>
                <input type="file" name="file" class="form-control" accept=".xlsx,.xls" required>
            </div>

            <button type="submit" class="btn btn-primary">Upload and Import</button>
        </form>
    </div>
</body>
</html>
