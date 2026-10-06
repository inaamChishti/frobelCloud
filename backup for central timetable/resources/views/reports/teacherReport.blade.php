@extends('layouts.auth')

@section('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.1.1/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="{{ asset('assets/libs/datatables/datatables.css') }}">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="{{ asset('assets/css/custom-style.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<style>
    #dateRangeInputs label { margin-right: 10px; display: inline-block; }
    #dateRangeInputs input { display: inline-block; width: auto; }
    #dateRangeInputs .col-md-6 select { width: 75%; }
</style>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card" style="background-color: white; box-shadow: 1px 1px 4px 6px #b0516a;">
                <div class="card-body">
                    <h4 class="card-title">Your Report Title</h4>
                    <form action="{{url('/teacher/report')}}" method="GET">
                        @csrf
                        <div class="form-group">
                            <label for="reportType">Staff Report:</label>
                            <select class="form-control" name="report_type" id="reportType">
                                <option value="" selected disabled>Choose an option</option>
                                <option value="staffReport">Staff Report</option>
                                <option value="staffSession">Staff session</option>
                            </select>
                        </div>

                        <div class="form-group row" id="dateRangeInputs">
                            <div class="col-md-6">
                                <label for="fromDate">From:</label>
                                <input type="text" id="fromDate" name="fromDate" class="form-control datepicker"
                                    placeholder="dd/mm/yyyy" required value="{{ old('fromDate') }}">
                            </div>
                            <div class="col-md-6">
                                <label for="toDate">To:</label>
                                <input type="text" id="toDate" name="toDate" class="form-control datepicker"
                                    placeholder="dd/mm/yyyy" required value="{{ old('toDate') }}">
                            </div>
                            <div class="col-md-6 mt-2 d-flex align-items-center">
                                <label class="mr-2" for="dropdown3">Staff: &nbsp;</label>
                                <select class="form-control" style="width:75%;" id="dropdown3" name="teacher_name">
                                    <option value="" selected disabled>Choose an option</option>
                                    @foreach ($teacherNames as $teacherName)
                                        <option value="{{ $teacherName }}" {{ old('teacher_name') == $teacherName ? 'selected' : '' }}>
                                            {{ $teacherName }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mt-2 d-flex align-items-center">
                                <button type="submit" class="btn btn-primary ml-2" style="margin-left: 2.1rem !important; height: 35px" onclick="submitForm()">Show</button>
                            </div>
                        </div>

                        <!-- Your form content -->
                    </form>
                </div>
            </div>

            @if(!empty($staffReport))
            <div class="card mt-3">
                <div class="card-datatable table-responsive">
                    <table id="example" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">Staff</th>
                                <th class="text-center">Subject</th>
                                <th class="text-center">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(@$staffReport as $staff)
                            <tr>
                                <td class="text-center">{{@$staff->teacher_name}}</td>
                                <td class="text-center">{{@$staff->subject}}</td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($staff->date)->format('d F Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <button id="exportPdfBtn" class="btn btn-primary">Export to PDF</button>
                </div>
            </div>
            @endif

            @if($condition)
            <div class="card mt-3">
                <div class="card-datatable table-responsive">
                    <table id="example" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">Staff</th>
                                <th class="text-center">Total Lectures</th>
                                <th class="text-center">Attended Lectures</th>
                            </tr>
                        </thead>
                        <tbody>

                            <tr>
                                <td class="text-center">{{@$teacher}}</td>
                                <td class="text-center">{{@$totalTeacherCount}}</td>
                                <td class="text-center">{{@$attendence}}</td>
                            </tr>

                        </tbody>
                    </table>
                    <button id="exportPdfBtn" class="btn btn-primary">Export to PDF</button>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        flatpickr(".datepicker", {
            dateFormat: "d/m/Y",
            locale: { firstDayOfWeek: 1 },
            defaultDate: "today"
        });
    });

    $(document).ready(function() {
        $("#dateRangeInputs").hide();

        $("#reportType").change(function() {
            $("#dateRangeInputs").toggle($(this).val() === "staffReport" || $(this).val() === "staffSession");
        });

        $('#example').DataTable({
            order: [[1, "asc"]],
            paging: true,
            searching: true,
            info: true,
            responsive: true,
        });

        $("#dropdown3").select2();
    });

    $(document).ready(function() {
        // Function to export the table to PDF
        function exportToPDF() {
            var element = document.getElementById('example');
            html2pdf(element, {
                margin: 10,
                filename: 'table.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
            });
        }

        // Bind the click event to the export button
        $('#exportPdfBtn').on('click', function() {
            exportToPDF();
        });
    });
</script>
@endsection
