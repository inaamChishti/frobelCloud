@extends('layouts.auth')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/libs/datatables/datatables.css') }}">
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('assets/libs/datatables/datatables.js') }}"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

@endsection

@section('content')
    <script type="text/javascript" src="//cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>

    <style>
        .button-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 20px;
        }

        .button-row {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .input-row {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .input-field {
            margin-right: 10px;
        }

        #studentsTable {
            margin: 0 auto;
        }
    </style>

    <div class="button-container  "
        style=" background-color: #fff;
        border-radius: 2px;
         box-shadow: 1px 1px 4px 6px	#b0516a;
          padding:30px;
        margin:auto;
          max-width:700px;
          ">
        <div class="button-row mt-2">
            <button class="btn btn-info mr-2"
            style="background-image: linear-gradient(to right, #0082e0 , #02d5df); !important"
            id="activeStudents">Active Students</button>
            <button class="btn btn-info"
            style="background-image: linear-gradient(to right, #0082e0 , #02d5df); !important"
            id="medicalStudents">Medical Condition</button>
        </div>

        <div class="input-row" style="display: inline-flex;">
            <input type="text" class="form-control input-field" id="family_id" placeholder="Enter text here">
            <button class="btn btn-info submit-button"
            style="background-image: linear-gradient(to right, #0082e0 , #02d5df); !important"
            id="submit">Submit</button>
        </div>

    </div>

    <!-- Create a table with an id attribute of "studentsTable" -->


    <div class="tableDiv"
        style="box-shadow: 1px 1px 4px 6px	#039dfe !important; background-color:white;margin-left:30px;margin-right:30px;margin-top:20px;margin-bottom:300px;padding:20px"
        style="position: relative;
  top: -200px;background-color: #f5f5f5;border-radius: 2px;">
        <table id="studentsTable" class="display" style=" margin: 0 auto;
  background-color: #f5f5f5;">
            <thead>
                <tr>
                    <th style="font-weight: bold; font-size:18px;">Family id</th>
                    <th style="font-weight: bold; font-size:18px;">Name</th>
                    <th style="font-weight: bold; font-size:18px;">Gender</th>
                    <th style="font-weight: bold; font-size:18px;">DOB</th>
                    <th style="font-weight: bold; font-size:18px;">Year in School</th>
                    <th style="font-weight: bold; font-size:18px;">Medical Condition</th>

                </tr>
            </thead>
            <tbody>
                <!-- Add table rows here -->
            </tbody>
        </table>
    </div>
    <script>
        $(document).ready(function() {
            $('#studentsTable').DataTable({
                columns: [{
                        title: 'Family id'
                    },
                    {
                        title: 'Name'
                    },
                    {
                        title: 'Gender'
                    },
                    {
                        title: 'DOB'
                    },
                    {
                        title: 'Year in School'
                    },
                    { title: 'Medical Condition' }


                ],
                paging: true,
            pageLength: 50, // Display 10 rows per page
            lengthMenu: [[75, 100, 200, -1], [75, 100, 200, "All"]]
            });
            $("#activeStudents").click(function() {
                swal.fire({
            title: 'Please wait system is loading data...',
            allowOutsideClick: false,
            showConfirmButton: false,
            onBeforeOpen: () => {
                swal.showLoading();
            }
        });
                $.ajax({
                    type: "get",
                    url: "{{ url('getReport') }}",
                    dataType: "json",
                    success: function(response) {

                        // Clear existing table rows
                        $('#studentsTable').DataTable().clear().draw();

                        // Append data to table
                        $.each(response.response, function(index, activeStudent) {
                            console.log('new');
                            console.log(activeStudent);
                            $.each(activeStudent.details, function(i, student) {
                                console.log('inam');
                                console.log(student);
                                $('#studentsTable').DataTable().row.add([
                                    student.admissionid,
                                    student.studentname,
                                    student.studentgender,
                                    student.studentdob,
                                    student.studentyearinschool,
                                    ''
                                ]);
                            });
                        });

                        // Close the $.each() function and draw the table
                        $('#studentsTable').DataTable().draw();
                        swal.close();
                    }


                });
            });

            // Initialize datatable
            //   $('#studentsTable').DataTable({
            //     columns: [
            //       { title: 'Family id' },
            //       { title: 'Name' },
            //       { title: 'Gender' },
            //       { title: 'DOB' },
            //       { title: 'Year in School' },
            //     ]
            //   });
            //  2nd  button code
            $("#medicalStudents").click(function() {

                swal.fire({
            title: 'Please wait system is loading data...',
            allowOutsideClick: false,
            showConfirmButton: false,
            onBeforeOpen: () => {
                swal.showLoading();
            }
        });

                $.ajax({
                    type: "get",
                    url: "{{ url('getMedicalReport') }}",
                    dataType: "json",
                    success: function(response) {
                        // Clear existing table rows
                        $('#studentsTable').DataTable().clear().draw();

                        // Append data to table
                        $.each(response.response, function(index, activeStudent) {
                            console.log(activeStudent.medicalcondition);


                                $('#studentsTable').DataTable().row.add([
                                    activeStudent.admissionid,
                                    activeStudent.studentname,
                                    activeStudent.studentgender,
                                    activeStudent.studentdob,
                                    activeStudent.studentyearinschool,
                                    activeStudent.medicalcondition,


                                ]);

                        });

                        // Close the $.each() function and draw the table
                        $('#studentsTable').DataTable().draw();
                        swal.close();
                    }


                });
            });

            // Initialize datatable
            //   $('#studentsTable').DataTable({
            //     columns: [
            //       { title: 'Family id' },
            //       { title: 'Name' },
            //       { title: 'Gender' },
            //       { title: 'DOB' },
            //       { title: 'Year in School' },
            //     ]
            //   });

            $("#submit").click(function() {
                var family_id = $('#family_id').val();
                $.ajax({
                    type: "get",
                    url: "{{ url('getfamilyReport') }}/" + family_id,
                    dataType: "json",
                    success: function(response) {
                        // Clear existing table rows
                        $('#studentsTable').DataTable().clear().draw();

                        // Append data to table
                        $.each(response.response, function(index, activeStudent) {
                            $.each(activeStudent.details, function(i, student) {
                                console.log(student.name);
                                $('#studentsTable').DataTable().row.add([
                                    student.admissionid,
                                    student.studentname,
                                    student.studentgender,
                                    student.studentdob,
                                    student.studentyearinschool,
                                ]);
                            });
                        });

                        // Close the $.each() function and draw the table
                        $('#studentsTable').DataTable().draw();
                    }
                });
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            var dataTable = $('#studentsTable').DataTable();

            // Hide the last column initially
            dataTable.column(-1).visible(false);

            // Active Students button click event
            $('#activeStudents').on('click', function() {
                // Hide the last column
                dataTable.column(-1).visible(false);
            });

            // Medical Condition button click event
            $('#medicalStudents').on('click', function() {
                // Show the last column
                dataTable.column(-1).visible(true);
            });
        });
        </script>
@endsection

@section('scripts')
@endsection
