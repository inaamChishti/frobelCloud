@extends('layouts.branchDashboardApp')

@section('content')

    <style>
        .swal2-container {
            z-index: 9999 !important;
        }

        .form-container {
            background-color: #ffffff;
            border: 2px solid #214DB6;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin: 50px auto;
        }

        .form-title {
            color: #fff;
            text-align: center;
            margin-bottom: 30px;
            font-size: 26px;
            font-weight: 700;
        }

        .form-group {
            margin-bottom: 25px;
        }

        label {
            font-weight: 500;
        }

        .form-control {
            border-radius: 8px;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .btn-submit {
            background-color: #214DB6;
            border: none;
            color: #fff;
            padding: 12px 20px;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
            width: 100%;
            font-size: 18px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .btn-submit:hover {
            background-color: #214DB6;
            transform: translateY(-2px);
        }

        .btn-submit:active {
            transform: translateY(1px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        /* Modal styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    padding-top: 60px;
    background-color: transparent; /* Remove the fade effect */
}

        .modal-content {
            margin: 5% auto;
            padding: 20px;
            border-radius: 5px;
            width: 80%;
            max-width: 400px;
            background-color: white;
        }

        @media print {

            /* Hide buttons and other non-essential elements */
            .btn-print,
            .btn-email {
                display: none;
            }

            /* Remove borders and outlines from elements like buttons and table cells during printing */
            .mock-result-table th,
            .mock-result-table td {
                border: none;
                /* Remove border */
                outline: none;
                /* Remove outline */
            }

            .btn {
                border: none;
                /* Remove any border on buttons */
                outline: none;
                /* Remove any outline on buttons */
            }

            /* Zoom out the print content */
            .print {
                transform: scale(0.8);
                /* Adjust the scale as needed */
                transform-origin: top center;
                width: 100%;
            }

            /* Style the print content to make sure it looks good when printed */
            .form-container {
                font-size: 18px;
                text-align: left;
                border: none;
                /* Remove border for print */
            }

            /* Other custom styles for the print layout */
        }
    </style>



    <div class="container">
        <div class="form-container">
            <h2 class="form-title">Mock Test Report</h2>
            @if (session('success'))
                <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                <script>
                    Swal.fire({
                        title: 'Success!',
                        text: "{{ session('success') }}",
                        icon: 'success',
                        showLoaderOnConfirm: true,
                        allowOutsideClick: false,
                        onBeforeOpen: () => {
                            Swal.showLoading();
                        }
                    });
                </script>
            @endif
            <form action="{{ url('mock/test/report') }}" method="GET">
                @csrf
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="family_id">Family ID:</label>
                        <input type="text" id="family_id" name="family_id" class="form-control">
                        @error('family_id')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group col-md-6">
                        <label for="name">Student Name:</label>
                        <select id="name" name="name" class="form-control">
                            <option value="">Select a student</option>
                        </select>
                    </div>

                </div>

                <button type="submit" class="btn-submit">Generate report</button>
            </form>
        </div>


        @if ($results && $results->count() > 0)
            <div class="form-container print" style="text-align: center; margin-top: 30px;">
                <img src="{{ asset('img/datesheetLogo.png') }}" alt="Datesheet Logo"
                    style="max-width: 150px; margin-bottom: -30px;    margin-left: 38%;">
                <h2 class="form-title" style="font-size: 24px; margin-top: 10px;">Mock Exam Result</h2>

                <div class="mock-result-card"
                    style="display: flex; flex-direction: column; align-items: center; margin-top: 20px;">
                    <!-- Left Section for Personal Details -->
                    <div class="left-side"
                        style="width: 80%; font-size: 16px; color: #333; text-align: left; margin-bottom: 20px;">
                        <p><strong>Family ID:</strong> {{ @$results->first()->family_id }}</p>
                        <p><strong>Student Name:</strong> {{ @$results->first()->name }}
                            @if (!empty($is_flagged))
                                <span title="Flagged Student" style="color:red;font-size:14px;margin-left:3px;">&#x1F6A9;</span>
                            @endif
                        </p>
                        <p><strong>Qualification:</strong> {{ @$results->first()->qualifications ?? 'N/A' }}</p>
                        <p><strong>Report Type:</strong> Mock Exam Results</p>
                    </div>

                    <!-- Loop over the results to display each subject -->
                    @foreach ($results as $index => $result)
                        <div class="right-side" style="width: 80%; margin-bottom: 20px;">
                            <table class="mock-result-table"
                                style="width: 100%; border-collapse: collapse; margin-top: 10px; text-align: center; border: 1px solid #ddd;">
                                <thead>
                                    <tr>
                                        <th style="padding: 10px; background-color: #f2f2f2; font-weight: bold; color:black;">Subject
                                        </th>
                                        <th style="padding: 10px; background-color: #f2f2f2; font-weight: bold;color:black;">Percentage
                                        </th>
                                        <th style="padding: 10px; background-color: #f2f2f2; font-weight: bold;color:black;">Grade</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="padding: 10px; background-color: #ffffff;color:black;">{{ @$result->subject }}</td>
                                        <td style="padding: 10px; background-color: #ffffff;color:black;">
                                            {{ @$result->percentage ?? 'N/A' }}%</td>
                                        <td style="padding: 10px; background-color: #ffffff;color:black;">
                                            {{ @$result->fine_grade ?? 'N/A' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="left-side"
                            style="width: 80%; font-size: 16px; color: #333; text-align: left; margin-top: 20px;">
                            <p><strong>Step 1:</strong> {{ @$results->first()->step_1 ?? 'N/A' }}</p>
                            <p><strong>Step 2:</strong> {{ @$results->first()->step_2 ?? 'N/A' }}</p>
                            <p><strong>Step 3:</strong> {{ @$results->first()->step_3 ?? 'N/A' }}</p>
                        </div>

                        <!-- Add a small margin to separate each set of subject-table -->
                        @if ($index < $results->count() - 1)
                            <hr style="width: 80%; border-top: 1px solid #ddd; margin-top: 20px;">
                        @endif
                    @endforeach

                    <!-- Display the step information once after all results -->


                    <!-- Additional Information -->
                    <div class="left-side" style="margin-top: 5px; font-size: 14px; color: #333;">
                        <p>On average, Frobel GCSE students attend 63 hours of tuition to progress one full GCSE grade.</p>
                        <p><strong class="">*Progress rates may vary based on the learning attitude and ability of the
                                individual student.*</strong></p>
                    </div>

                    <!-- Action Buttons -->
                    <div styssle="margin-top: 20px;">
                        <button class="btn btn-print"
                            style="padding: 10px 20px; background-color: #2196F3; color: white; border: none; border-radius: 5px; cursor: pointer; margin-right: 10px;"
                            onclick="printOnly();">Print</button>
                        <button class="btn btn-email"
                            style="padding: 10px 20px; background-color: #FF5722; color: white; border: none; border-radius: 5px; cursor: pointer;"
                            id="openModalBtn">Email</button>
                    </div>
                </div>
            </div>
        @endif







        <div id="emailModal" style="display: none;" class="modal">
            <div class="modal-content" style="background-color: white; padding: 20px; border-radius: 5px;">
                <span id="closeModal"
                    style="cursor: pointer; position: absolute; top: 10px; right: 10px; font-size: 20px;">&times;</span>
                <h3>Enter your email</h3>
                <input type="email" id="emailInput" placeholder="Enter email"
                    style="width: 100%; padding: 10px; margin-bottom: 10px;" value="{{ @$student_email }}">
                <button id="sendEmailBtn" class="btn btn-send"
                    style="padding: 10px 20px; background-color: #4CAF50; color: white; border: none; border-radius: 5px; cursor: pointer;">Send</button>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


        <script>
            $("#openModalBtn").click(function() {
                $("#emailModal").fadeIn();
            });

            $("#closeModal").click(function() {
                $("#emailModal").fadeOut();
            });

            $(window).click(function(event) {
                if ($(event.target).is("#emailModal")) {
                    $("#emailModal").fadeOut();
                }
            });

            $("#sendEmailBtn").click(function() {
                var email = $("#emailInput").val();
                var name = "{{ $name }}"; // Laravel Blade variable 'name'
                var familyId = "{{ $family_id }}"; // Laravel Blade variable 'family_id'

                if (email && name && familyId) {
                    var printData = $(".print").html();

                    // Show Swal loading message
                    Swal.fire({
                        title: 'Sending...',
                        text: 'Please wait while we send your email.',
                        imageUrl: '/path/to/loading.gif', // Optional: Add a loading GIF
                        imageWidth: 50,
                        imageHeight: 50,
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: "{{ url('send-mock-email') }}",
                        method: "POST",
                        data: {
                            email: email,
                            name: name,
                            family_id: familyId,
                            print_data: printData,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            // Show success message
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Email sent successfully!',
                                showConfirmButton: true
                            });
                            $("#emailModal").fadeOut();
                        },
                        error: function(xhr, status, error) {
                            // Show failure message
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Failed to send email. Please try again.',
                                showConfirmButton: true
                            });
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Missing Information',
                        text: 'Please enter a valid email, name, and family ID.',
                        showConfirmButton: true
                    });
                }
            });
        </script>



        <script>
            function printOnly() {
                // Add a class to apply zoom effect before printing
                var printContent = document.querySelector('.print');
                printContent.classList.add('zoom-out');

                // Hide everything else except the .print div
                var originalContent = document.body.innerHTML;
                document.body.innerHTML = printContent.outerHTML;

                // Trigger the print dialog
                window.print();
                window.location.reload();

                // Restore the original content after printing
                document.body.innerHTML = originalContent;

                // Remove the zoom-out class after printing to avoid affecting other prints
                printContent.classList.remove('zoom-out');
            }
        </script>




        <script>
            $(document).ready(function() {
                $('#family_id').on('focusout', function() {
                    const familyId = $('#family_id').val();

                    if (familyId.trim() !== "") {
                        Swal.fire({
                            title: 'Please wait...',
                            text: 'Fetching data...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.ajax({
                            url: "{{ url('get/family/rec') }}",
                            type: "GET",
                            data: {
                                family_id: familyId,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.close();

                                if (response.length > 0) {
                                    $('#name').find('option:not(:first)').remove();

                                    $.each(response, function(index, item) {
                                        const fullName = (typeof item === 'object' && item !== null) ? item.name : item;
                                        const isFlagged = (typeof item === 'object' && item !== null) ? item.is_flag == 1 : false;
                                        const flagIcon = isFlagged ? ' &#x1F6A9;' : '';
                                        $('#name').append('<option value="' + fullName + '">' + fullName + flagIcon + '</option>');
                                    });


                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Wrong Family ID',
                                        text: 'No records found for this family ID.'
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.close();
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'An error occurred while fetching data. Please try again later.'
                                });
                                console.error(xhr.responseText);
                            }
                        });
                    }
                });
            });
        </script>
    @endsection
