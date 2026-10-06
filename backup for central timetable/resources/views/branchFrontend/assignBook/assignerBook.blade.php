@extends('layouts.branchDashboardApp')

@section('content')
<style>
    /* Force Toastr success background color to green */
    #toast-container > .toast-success {
        background-color: #28a745 !important;
        color: #ffffff !important;
    }

    /* Force Toastr error background color to red */
    #toast-container > .toast-error {
        background-color: #dc3545 !important;
        color: #ffffff !important;
    }

    /* Center heading and improve container styling */
    .container {
        background-color: #ffffff;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        margin-top: 20px;
    }

    .heading-container {
        text-align: center;
        margin-bottom: 30px;
    }

    .heading-container h2 {
        color: #2045A5;
        font-size: 2rem;
        font-weight: 600;
    }

    .add-button, .back-button {
        display: inline-block;
        margin: 0 10px 20px;
        background-color: #2045A5;
        color: white;
        padding: 10px 20px;
    }
</style>

<div class="container">
    <div class="heading-container">
        <h2>Subject Book Assignments</h2>
    </div>
    <div style="text-align: center;">
        <button type="button" class="btn add-button" data-bs-toggle="modal" data-bs-target="#createModal">Add New Book</button>
        <a href="{{ url('assign-book') }}" class="btn back-button">Back</a>
    </div>

    <table class="table table-bordered" id="assignmentsTable">
        <thead style="background-color: #2045A5; color: white;">
            <tr>
                <th>Book Name</th>
                <th>Subject Name</th>
                <th>Price</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($assignments as $assignment)
                <tr data-id="{{ $assignment->id }}">
                    <td>{{ $assignment->book_name ?? 'N/A' }}</td>
                    <td>{{ $assignment->subject_name ?? 'N/A' }}</td>
                    <td>{{ $assignment->price ? '£' . number_format($assignment->price, 2) : 'N/A' }}</td>
                    <td>
                        <button type="button" class="btn btn-sm" style="background-color: #2045A5; color: white;"
                            data-bs-toggle="modal" data-bs-target="#editModal"
                            onclick="editAssignment({{ $assignment->id }}, '{{ $assignment->book_name }}', '{{ $assignment->subject_name }}', '{{ $assignment->price }}')">Edit</button>
                        <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="{{ $assignment->id }}">Delete</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModalLabel">Add New Assignment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createForm" action="{{ route('subjectBookAssignments.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="book_name" class="form-label">Book Name</label>
                        <input type="text" class="form-control" id="book_name" name="book_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="subject_id" class="form-label">Subject</label>
                        <select class="form-control" id="subject_id" name="subject_id" required>
                            <option value="">Select Subject</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}" data-name="{{ $subject->name }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">Price (£)</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="price" name="price" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn" style="background-color: #2045A5; color: white;">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Assignment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="mb-3">
                        <label for="edit_book_name" class="form-label">Book Name</label>
                        <input type="text" class="form-control" id="edit_book_name" name="book_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_subject_id" class="form-label">Subject</label>
                        <select class="form-control" id="edit_subject_id" name="subject_id" required>
                            <option value="">Select Subject</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}" data-name="{{ $subject->name }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_price" class="form-label">Price (£)</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="edit_price" name="price" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn" style="background-color: #2045A5; color: white;">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    // Configure toastr
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "3000",
        "success": {
            "backgroundColor": "#28a745"
        },
        "error": {
            "backgroundColor": "#dc3545"
        }
    };

    // Display success message if exists
    @if (session('success'))
        toastr.success('{{ session('success') }}');
    @endif

    function editAssignment(id, book_name, subject_name, price) {
        $('#edit_id').val(id);
        $('#edit_book_name').val(book_name);
        $('#edit_price').val(price);
        $('#editForm').attr('action', '/subjectBookAssignments/' + id);

        // Wait for modal to be fully shown before setting the select value
        $('#editModal').on('shown.bs.modal', function () {
            $('#edit_subject_id').find('option').removeAttr('selected');
            const $selectedOption = $('#edit_subject_id').find(`option[data-name="${subject_name}"]`);
            if ($selectedOption.length) {
                const subject_id = $selectedOption.val();
                $('#edit_subject_id').val(subject_id).trigger('change');
            } else {
                $('#edit_subject_id').val('').trigger('change');
                toastr.warning('Subject not found in the list.');
            }
            $(this).off('shown.bs.modal');
        });
    }

    $(document).on('click', '.delete-btn', function() {
        if (confirm('Are you sure you want to delete this assignment?')) {
            var id = $(this).data('id');
            $.ajax({
                url: '/subjectBookAssignments/' + id,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#assignmentsTable').find('tr[data-id="' + id + '"]').remove();
                    toastr.success('Assignment deleted successfully.');
                },
                error: function(xhr) {
                    toastr.error('Error deleting assignment.');
                }
            });
        }
    });
</script>
@endsection
