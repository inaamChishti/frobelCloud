@extends('layouts.branchDashboardApp')

@section('content')
    <!-- Include External CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <div class="registration-container scroll-smooth">
        <div class="container">
            <!-- Header -->
            <div class="text-center mb-12">
                <h1 class="text-3xl font-bold text-[var(--primary-dark)] mb-3 tracking-tight sm:text-4xl">Notes List</h1>
            </div>

            <!-- Notes Table -->
            <div class="card form-section">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <div class="bg-[var(--primary)] p-2 rounded-lg mr-3 section-icon">
                            <i class="fas fa-table h-5 w-5 text-white text-lg"></i>
                        </div>
                        <h2 class="text-xl font-semibold text-[var(--primary-dark)] sm:text-2xl">Notes Records</h2>
                    </div>
                    <button type="button" class="btn-primary flex items-center" data-bs-toggle="modal" data-bs-target="#addNoteModal">
                        <i class="fas fa-plus-circle mr-2"></i> Add New Note
                    </button>
                </div>
                <div class="table-responsive">
                    <table id="notesTable" class="table w-full">
                        <thead>
                            <tr>
                                <th class="col-id">Sr No</th>
                                <th class="col-ref-no">Ref No</th>
                                <th class="col-name">Name</th>
                                <th class="col-message">Message</th>
                                <th class="col-received-by">Received By</th>
                                <th class="col-message-for">Message For</th>
                                <th class="col-created-at">Created At</th>
                                <th class="col-actions">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($notes as $note)
                                <tr>
                                    <td class="col-id">{{ $loop->iteration }}</td>
                                    <td class="col-ref-no">{{ $note->ref_no ?? '' }}</td>
                                    <td class="col-name">{{ $note->name ?? '' }}</td>
                                    <td class="col-message">{{ \Str::limit($note->message ?? '', 20, '...') }}</td>
                                    <td class="col-received-by">{{ $note->received_by ?? '' }}</td>
                                    <td class="col-message-for">{{ $note->message_for ?? '' }}</td>
                                    <td class="col-created-at">{{ $note->created_at ? $note->created_at->diffForHumans() : '' }}</td>
                                    <td class="col-actions">
                                        <div class="btn-group" role="group">
                                            <form action="{{ url('users/note/delete/' . $note->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this note?')">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Add Note Modal -->
            <div class="modal fade" id="addNoteModal" tabindex="-1" aria-labelledby="addNoteModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addNoteModalLabel">Add New Note</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('user.note.store') }}" method="POST" id="noteForm">
                            @csrf
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="refNo" class="block text-sm font-medium text-[var(--text-dark)] mb-1">Ref No</label>
                                    <div class="relative">
                                        <i class="fas fa-hashtag absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                                        <input type="number" name="ref_no" id="refNo" class="input-field pl-10" placeholder="Ref No">
                                    </div>
                                    <div class="error-message" id="refNo_feedback">Please enter a valid reference number.</div>
                                    @error('ref_no')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="receivedBy" class="block text-sm font-medium text-[var(--text-dark)] mb-1">Received By</label>
                                    <div class="relative">
                                        <i class="fas fa-user absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                                        <input type="text" name="received_by" id="receivedBy" class="input-field pl-10" placeholder="Received By">
                                    </div>
                                    <div class="error-message" id="receivedBy_feedback">Please enter the receiver's name.</div>
                                    @error('received_by')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="msgFor" class="block text-sm font-medium text-[var(--text-dark)] mb-1">Message For</label>
                                    <div class="relative">
                                        <i class="fas fa-user absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                                        <input type="text" name="message_for" id="msgFor" class="input-field pl-10" placeholder="Message For">
                                    </div>
                                    <div class="error-message" id="msgFor_feedback">Please enter the recipient's name.</div>
                                    @error('message_for')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="msg" class="block text-sm font-medium text-[var(--text-dark)] mb-1">Message</label>
                                    <div class="relative">
                                        <i class="fas fa-comment absolute left-3 top-2.5 text-[var(--text-light)] text-sm"></i>
                                        <textarea name="message" id="msg" class="input-field pl-10" rows="3" placeholder="Enter your message"></textarea>
                                    </div>
                                    <div class="error-message" id="msg_feedback">Please enter a message.</div>
                                    @error('message')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn-primary flex items-center">
                                    <i class="fas fa-save mr-2"></i> Save Note
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <style>
        .registration-container {
            --primary: #2563eb;
            --primary-dark: #1e40af;
            --primary-light: #93c5fd;
            --secondary: #e0f2fe;
            --accent: #3b82f6;
            --text-dark: #111827;
            --text-light: #6b7280;
            --border: #bfdbfe;
            --background: #f1f5f9;
            --error: #ef4444;
            --error-light: #fee2e2;
        }

        .registration-container .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 1rem 3rem;
        }

        @media (max-width: 768px) {
            .registration-container .container {
                padding: 0.75rem 2rem;
            }
        }

        @media (max-width: 640px) {
            .registration-container .container {
                padding: 0.5rem 1.5rem;
            }
        }

        .registration-container .card {
            background: #ffffff;
            border-radius: 0.75rem;
            border: 1px solid var(--border);
            box-shadow: 0 8px 24px rgba(29, 78, 216, 0.1);
            padding: 1.25rem 1.5rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
            position: relative;
            margin: 0;
        }

        .registration-container .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(29, 78, 216, 0.15);
            border-color: var(--primary-light);
        }

        .registration-container .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, var(--primary), var(--primary-light));
            transition: height 0.3s ease;
        }

        .registration-container .card:hover::before {
            height: 5px;
        }

        .registration-container .input-field {
            border: 1px solid var(--border);
            border-radius: 0.5rem;
            padding: 0.5rem 0.8rem 0.5rem 2.5rem;
            background: #ffffff;
            transition: all 0.3s ease;
            font-size: 0.875rem;
            width: 100%;
            box-sizing: border-box;
            color: var(--text-dark);
        }

        .registration-container .input-field:focus,
        .registration-container .input-field:hover {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            outline: none;
        }

        .registration-container .input-field.error {
            border-color: var(--error);
            background-color: var(--error-light);
        }

        .registration-container .input-field.valid {
            border-color: #10b981;
            background-color: #ecfdf5;
        }

        .registration-container .input-field[rows] {
            padding: 0.5rem 0.8rem 0.5rem 2.5rem;
            height: auto;
            min-height: 4rem;
        }

        .registration-container .error-message {
            color: var(--error);
            font-size: 0.75rem;
            margin-top: 0.25rem;
            display: none;
        }

        .registration-container .error-message:not(:empty) {
            display: block;
        }

        .registration-container .btn-primary {
            background: linear-gradient(45deg, var(--primary-dark), var(--primary));
            border: none;
            border-radius: 0.5rem;
            padding: 0.5rem 1rem;
            color: white;
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            height: 2.5rem;
            display: flex;
            align-items: center;
        }

        .registration-container .btn-primary:hover {
            background: linear-gradient(45deg, var(--primary), var(--primary-light));
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(37, 99, 235, 0.2);
        }

        .registration-container .btn-danger {
            background: linear-gradient(45deg, #b91c1c, var(--error));
            border: none;
            border-radius: 0.5rem;
            padding: 0.5rem 1rem;
            color: white;
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            height: 2.5rem;
            display: flex;
            align-items: center;
        }

        .registration-container .btn-danger:hover {
            background: linear-gradient(45deg, var(--error), #fee2e2);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(239, 68, 68, 0.2);
        }

        .registration-container .btn-secondary {
            background: linear-gradient(45deg, var(--text-light), #9ca3af);
            border: none;
            border-radius: 0.5rem;
            padding: 0.5rem 1rem;
            color: white;
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            height: 2.5rem;
            display: flex;
            align-items: center;
        }

        .registration-container .btn-secondary:hover {
            background: linear-gradient(45deg, #9ca3af, #d1d5db);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(107, 114, 128, 0.2);
        }

        .registration-container .btn-sm {
            padding: 0.4rem 0.8rem;
            font-size: 0.75rem;
            height: 2rem;
        }

        .registration-container .table {
            --bs-table-bg: transparent;
            --bs-table-color: var(--text-dark);
            --bs-table-striped-bg: var(--secondary);
            --bs-table-striped-color: var(--text-dark);
            --bs-table-hover-bg: rgba(37, 99, 235, 0.1);
            --bs-table-hover-color: var(--text-dark);
            color: var(--bs-table-color);
            border-color: var(--border);
            border-radius: 0.5rem;
            margin-bottom: 0;
            table-layout: fixed;
        }

        .registration-container .table thead th {
            background: var(--secondary);
            color: var(--text-dark);
            border-bottom: 2px solid var(--border);
            font-weight: 600;
            padding: 0.75rem;
            text-align: left;
            vertical-align: middle;
        }

        .registration-container .table tbody td {
            vertical-align: middle;
            border-color: var(--border);
            padding: 0.75rem;
            color: var(--text-dark);
            font-size: 0.875rem;
            font-weight: 500;
        }

        .registration-container .table tbody tr:hover {
            background: var(--bs-table-hover-bg);
            transform: translateX(2px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .registration-container .table th.col-id,
        .registration-container .table td.col-id {
            width: 5%;
            text-align: center;
        }

        .registration-container .table th.col-ref-no,
        .registration-container .table td.col-ref-no {
            width: 10%;
        }

        .registration-container .table th.col-name,
        .registration-container .table td.col-name {
            width: 15%;
            white-space: normal;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .registration-container .table th.col-message,
        .registration-container .table td.col-message {
            width: 20%;
            white-space: normal;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .registration-container .table th.col-received-by,
        .registration-container .table td.col-received-by {
            width: 15%;
        }

        .registration-container .table th.col-message-for,
        .registration-container .table td.col-message-for {
            width: 15%;
        }

        .registration-container .table th.col-created-at,
        .registration-container .table td.col-created-at {
            width: 15%;
        }

        .registration-container .table th.col-actions,
        .registration-container .table td.col-actions {
            width: 10%;
            text-align: center;
        }

        .registration-container .section-icon {
            transition: transform 0.3s ease;
        }

        .registration-container .section-icon:hover {
            transform: scale(1.2);
        }

        .registration-container .scroll-smooth {
            scroll-behavior: smooth;
        }

        .registration-container .modal-content {
            border-radius: 0.75rem;
            border: 1px solid var(--border);
            box-shadow: 0 8px 24px rgba(29, 78, 216, 0.1);
        }

        .registration-container .modal-header {
            background: linear-gradient(45deg, var(--primary-dark), var(--primary));
            color: #ffffff;
            border-bottom: none;
            border-radius: 0.75rem 0.75rem 0 0;
            padding: 1rem 1.5rem;
        }

        .registration-container .modal-title {
            font-weight: 600;
            font-size: 1.25rem;
        }

        .registration-container .modal-footer {
            border-top: none;
            padding: 1rem 1.5rem;
        }

        /* Toastr custom styles */
        .toast-error {
            background-color: var(--error) !important;
            color: #ffffff !important;
            border: 2px solid #b91c1c !important;
            border-radius: 0.5rem !important;
            font-weight: 500 !important;
        }

        .toast-success {
            background-color: #10b981 !important;
            color: #ffffff !important;
            border: 2px solid #059669 !important;
            border-radius: 0.5rem !important;
            font-weight: 500 !important;
        }

        @media (max-width: 768px) {
            .registration-container .table thead th,
            .registration-container .table tbody td {
                font-size: 0.75rem;
                padding: 0.5rem;
            }
            .registration-container .btn-sm {
                padding: 0.3rem 0.6rem;
                font-size: 0.65rem;
                height: 1.8rem;
            }
            .registration-container .table th.col-id,
            .registration-container .table td.col-id {
                width: 8%;
            }
            .registration-container .table th.col-ref-no,
            .registration-container .table td.col-ref-no {
                width: 12%;
            }
            .registration-container .table th.col-name,
            .registration-container .table td.col-name {
                width: 15%;
            }
            .registration-container .table th.col-message,
            .registration-container .table td.col-message {
                width: 20%;
            }
            .registration-container .table th.col-received-by,
            .registration-container .table td.col-received-by {
                width: 15%;
            }
            .registration-container .table th.col-message-for,
            .registration-container .table td.col-message-for {
                width: 15%;
            }
            .registration-container .table th.col-created-at,
            .registration-container .table td.col-created-at {
                width: 15%;
            }
            .registration-container .table th.col-actions,
            .registration-container .table td.col-actions {
                width: 10%;
            }
            .registration-container .modal-title {
                font-size: 1.1rem;
            }
            .registration-container .modal-body {
                padding: 1rem;
            }
        }
    </style>

    <script>
        $(document).ready(function() {
            // Initialize Toastr options
            toastr.options = {
                closeButton: true,
                progressBar: true,
                positionClass: "toast-top-right",
                timeOut: 5000,
                extendedTimeOut: 1000,
                showEasing: "swing",
                hideEasing: "linear",
                showMethod: "fadeIn",
                hideMethod: "fadeOut"
            };

            // Initialize DataTable
            $('#notesTable').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'copy',
                        text: '<i class="fas fa-copy"></i> Copy',
                        className: 'btn btn-sm btn-primary',
                        title: 'Notes List',
                        exportOptions: { columns: ':not(:last-child)' }
                    },
                    {
                        extend: 'csv',
                        text: '<i class="fas fa-file-csv"></i> CSV',
                        className: 'btn btn-sm btn-primary',
                        title: 'Notes List',
                        exportOptions: { columns: ':not(:last-child)' }
                    },
                    {
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel"></i> Excel',
                        className: 'btn btn-sm btn-primary',
                        title: 'Notes List',
                        exportOptions: { columns: ':not(:last-child)' }
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fas fa-file-pdf"></i> PDF',
                        className: 'btn btn-sm btn-primary',
                        title: 'Notes List',
                        exportOptions: { columns: ':not(:last-child)' }
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> Print',
                        className: 'btn btn-sm btn-primary',
                        title: 'Notes List',
                        exportOptions: { columns: ':not(:last-child)' }
                    }
                ],
                pageLength: 10,
                responsive: true,
                columnDefs: [{ targets: -1, orderable: false }],
                language: {
                    emptyTable: "No notes available"
                },
                order: [[6, 'desc']] // Order by Created At
            });

            // Form validation
            $('#noteForm').on('submit', function(e) {
                e.preventDefault();
                const refNo = $('#refNo');
                const receivedBy = $('#receivedBy');
                const msgFor = $('#msgFor');
                const msg = $('#msg');
                let valid = true;

                if (!refNo.val() || isNaN(refNo.val())) {
                    refNo.addClass('error').removeClass('valid');
                    $('#refNo_feedback').show();
                    valid = false;
                } else {
                    refNo.addClass('valid').removeClass('error');
                    $('#refNo_feedback').hide();
                }

                if (!receivedBy.val()) {
                    receivedBy.addClass('error').removeClass('valid');
                    $('#receivedBy_feedback').show();
                    valid = false;
                } else {
                    receivedBy.addClass('valid').removeClass('error');
                    $('#receivedBy_feedback').hide();
                }

                if (!msgFor.val()) {
                    msgFor.addClass('error').removeClass('valid');
                    $('#msgFor_feedback').show();
                    valid = false;
                } else {
                    msgFor.addClass('valid').removeClass('error');
                    $('#msgFor_feedback').hide();
                }

                if (!msg.val()) {
                    msg.addClass('error').removeClass('valid');
                    $('#msg_feedback').show();
                    valid = false;
                } else {
                    msg.addClass('valid').removeClass('error');
                    $('#msg_feedback').hide();
                }

                if (valid) {
                    this.submit();
                } else {
                    toastr.error("Please fill all required fields.", "Error", { toastClass: "toast-error" });
                }
            });

            // Real-time validation
            $('#refNo').on('input', function() {
                const feedback = $('#refNo_feedback');
                if (this.value && !isNaN(this.value)) {
                    $(this).addClass('valid').removeClass('error');
                    feedback.hide();
                } else {
                    $(this).addClass('error').removeClass('valid');
                    feedback.show();
                }
            });

            $('#receivedBy, #msgFor, #msg').on('input', function() {
                const feedback = $(`#${this.id}_feedback`);
                if (this.value && this.value.trim() !== '') {
                    $(this).addClass('valid').removeClass('error');
                    feedback.hide();
                } else {
                    $(this).addClass('error').removeClass('valid');
                    feedback.show();
                }
            });

            // Toastr notifications for session messages
            @if (session('success'))
                toastr.success("{{ session('success') }}", "Success", { toastClass: "toast-success" });
            @endif

            @if (session('error'))
                toastr.error("{{ session('error') }}", "Error", { toastClass: "toast-error" });
            @endif
        });
    </script>
@endsection
