@extends('layouts.branchDashboardApp')

@section('content')
 <div class="registration-container scroll-smooth">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6 sm:mb-8">
                <div>
                   <h1 class="text-2xl sm:text-3xl font-bold text-[var(--primary-dark)] mb-2 tracking-tight lg:text-4xl">
                        Archived Students</h1>
                    <p class="text-base sm:text-lg text-[var(--text-light)]">View and manage all student requests in the
                        system.</p>
                </div>
            </div>

            <!-- Table Card -->
            <div
                class="card bg-white rounded-xl shadow-lg border border-[var(--border)] p-4 sm:p-6 hover:shadow-xl transition-all duration-300">
                <div class="table-responsive">
                    <table id="studentRequestsTable" class="table table-hover w-full">
                        <thead>
                            <tr>
                                <th>Full Name</th>
                                <th>DOB</th>
                                <th>Year in School</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($studentRequests as $request)
                                @php
                                    $data = json_decode($request->base64_data, true);
                                    $firstName = $data['firstName'][0] ?? 'N/A';
                                    $middleName = $data['middleName'][0] ?? '';
                                    $lastName = $data['lastName'][0] ?? 'N/A';
                                    $fullName = trim($firstName . ' ' . $middleName . ' ' . $lastName);
                                    $dobRaw = $data['dob'][0] ?? 'N/A';
                                    $dob = $dobRaw !== 'N/A' ? \Carbon\Carbon::parse($dobRaw)->format('d/m/Y') : 'N/A';
                                    $yearInSchool = $data['yearInSchool'][0] ?? 'N/A';
                                @endphp
                                <tr>
                                    <td>{{ $fullName ?: 'N/A' }}</td>
                                    <td>{{ $dob }}</td>
                                    <td>{{ $yearInSchool }}</td>
                                    <td>
                                        <span
                                            class="badge {{ $request->is_approved == 0 ? 'bg-red-500' : 'bg-green-500' }} text-white px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm">
                                            {{ $request->is_approved == 0 ? 'Pending' : 'Approved' }}
                                        </span>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($request->created_at)->format('d M Y') }}</td>
                                    <td>
                                        <div class="btn-group flex space-x-1 sm:space-x-2">
                                            @if ($request->is_approved == 0)
                                                <a href="{{ url('review-request/' . $request->id) }}"
                                                    class="btn-primary btn-sm flex items-center space-x-1">
                                                    <i class="fas fa-eye text-xs sm:text-sm"></i><span
                                                        class="text-xs sm:text-sm">View</span>
                                                </a>
                                            @endif
                                            <button class="btn-primary btn-sm flex items-center space-x-1 comment-btn"
                                                data-id="{{ $request->id }}">
                                                <i class="fas fa-comment text-xs sm:text-sm"></i><span
                                                    class="text-xs sm:text-sm">Comment</span>
                                            </button>
                                           <a href="{{ url('undo-archive/' . $request->id) }}"
                                               class="btn-primary btn-sm flex items-center space-x-1 undo-btn"
                                               data-id="{{ $request->id }}">
                                                <i class="fas fa-undo text-xs sm:text-sm"></i><span class="text-xs sm:text-sm">Undo</span>
                                            </a>
                                        </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Improved Comment Modal -->
    <div id="commentModal"
        class="modal fixed inset-0 flex items-center justify-center p-2 sm:p-4 hidden transition-all duration-300 z-[1002]">
        <div
            class="modal-content bg-white rounded-2xl shadow-2xl w-full max-w-md sm:max-w-2xl transform transition-all duration-300 scale-95">
            <!-- Modal Header -->
            <div class="flex justify-between items-center p-4 sm:p-5 border-b border-gray-200 bg-gray-50 rounded-t-2xl">
                <h3 class="text-lg sm:text-xl font-semibold text-gray-800">Add or View Comments</h3>
                <button
                    class="close-modal text-gray-500 hover:text-gray-700 text-xl sm:text-2xl focus:outline-none transition-colors duration-200">&times;</button>
            </div>

            <!-- Modal Body -->
            <div class="p-4 sm:p-6 grid gap-4 sm:gap-6">
                <!-- Comment Form -->
                <div class="grid gap-3 sm:gap-4">
                    <textarea id="commentInput"
                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-y text-gray-700 placeholder-gray-400 text-sm sm:text-base"
                        rows="3" placeholder="Write your comment here..."></textarea>
                    <input type="hidden" id="studentRequestId">
                    <input type="hidden" id="editCommentId">
                    <div class="flex justify-end space-x-2 sm:space-x-3">
                        <button id="submitComment"
                            class="btn-primary-prominent px-3 sm:px-4 py-1.5 sm:py-2 rounded-lg flex items-center space-x-1 sm:space-x-2 bg-blue-600 hover:bg-blue-700 text-white transition-colors duration-200 text-sm sm:text-base">
                            <i class="fas fa-paper-plane"></i><span>Submit Comment</span>
                        </button>
                        <button id="updateComment"
                            class="btn-primary-prominent px-3 sm:px-4 py-1.5 sm:py-2 rounded-lg flex items-center space-x-1 sm:space-x-2 bg-blue-600 hover:bg-blue-700 text-white hidden transition-colors duration-200 text-sm sm:text-base">
                            <i class="fas fa-edit"></i><span>Update Comment</span>
                        </button>
                    </div>
                </div>

                <!-- Comments List -->
                <div id="commentsList" class="space-y-3 sm:space-y-4 max-h-64 sm:max-h-72 overflow-y-auto pr-2">
                    <!-- Comments will be loaded here -->
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="p-4 sm:p-5 border-t border-gray-200 bg-gray-50 rounded-b-2xl flex justify-end">
                {{-- <button class="close-modal btn-close px-3 sm:px-4 py-1.5 sm:py-2 rounded-lg flex items-center space-x-1 sm:space-x-2 bg-gray-500 hover:bg-gray-600 text-white transition-colors duration-200 text-sm sm:text-base">
                <i class="fas fa-times"></i><span>Close</span>
            </button> --}}
            </div>
        </div>
    </div>

    <!-- Include Dependencies -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <style>
        /* CSS Variables */
        :root {
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

        /* Container */
        .registration-container .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0.5rem 1rem;
        }

        /* Card */
        .registration-container .card {
            position: relative;
            overflow: hidden;
        }

        .registration-container .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--primary-light));
            transition: height 0.3s ease;
        }

        .registration-container .card:hover::before {
            height: 6px;
        }

        /* Table */
        .registration-container .table-responsive {
            border-radius: 0.5rem;
            overflow-x: auto;
        }

        .registration-container .table thead th {
            background: var(--secondary);
            color: var(--primary-dark);
            font-weight: 600;
            font-size: 0.75rem;
            padding: 0.5rem;
            border-bottom: 2px solid var(--border);
            text-align: left;
            white-space: nowrap;
        }

        .registration-container .table tbody td {
            vertical-align: middle;
            padding: 0.5rem;
            color: var(--text-dark);
            font-size: 0.75rem;
            font-weight: 500;
            border-top: 1px solid var(--border);
            white-space: nowrap;
        }

        .registration-container .table tbody tr:hover {
            background: var(--secondary);
            transform: translateX(3px);
            box-shadow: 0 2px 8px rgba(29, 78, 216, 0.1);
        }

        /* Buttons */
        .registration-container .btn-primary {
            background: linear-gradient(45deg, var(--primary-dark), var(--primary));
            border: none;
            border-radius: 0.5rem;
            padding: 0.5rem;
            color: white;
            font-weight: 500;
            font-size: 0.75rem;
            transition: all 0.3s ease;
            height: 2rem;
        }

        .registration-container .btn-primary:hover {
            background: linear-gradient(45deg, var(--primary), var(--primary-light));
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
        }

        .registration-container .btn-primary:active {
            transform: translateY(0);
            scale: 0.98;
        }

        /* Modal Styles */
        .modal-content {
            animation: slideIn 0.3s ease-out;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .comment-item {
            background: #f9fafb;
            border-radius: 0.5rem;
            padding: 0.75rem;
            transition: background 0.2s ease;
            border: 1px solid var(--border);
        }

        .comment-item:hover {
            background: #f1f5f9;
        }

        .comment-actions {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .comment-actions button {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
            transition: all 0.2s ease;
        }

        .comment-actions .edit-comment {
            color: var(--primary);
            background: rgba(37, 99, 235, 0.1);
        }

        .comment-actions .edit-comment:hover {
            background: rgba(37, 99, 235, 0.2);
            color: var(--primary-dark);
        }

        .comment-actions .delete-comment {
            color: var(--error);
            background: rgba(239, 68, 68, 0.1);
        }

        .comment-actions .delete-comment:hover {
            background: rgba(239, 68, 68, 0.2);
            color: #dc2626;
        }

        /* Animation for modal */
        @keyframes slideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .registration-container .container {
                padding: 0.5rem;
            }

            .registration-container .table thead th,
            .registration-container .table tbody td {
                font-size: 0.7rem;
                padding: 0.4rem;
            }

            .registration-container .btn-primary {
                padding: 0.4rem 0.8rem;
                font-size: 0.7rem;
                height: 1.8rem;
            }

            .registration-container .btn-group {
                flex-direction: column;
                space-x-0;
                gap: 0.5rem;
            }

            .registration-container .badge {
                font-size: 0.7rem;
                padding: 0.3rem 0.6rem;
            }
        }

        @media (max-width: 640px) {
            .registration-container .container {
                padding: 0.25rem;
            }

            .registration-container .card {
                padding: 0.5rem;
            }

            .modal-content {
                max-width: 95vw;
            }

            .modal-content .p-4,
            .modal-content .p-5 {
                padding: 0.75rem;
            }

            .modal-content .text-xl {
                font-size: 1rem;
            }

            .modal-content .text-sm {
                font-size: 0.875rem;
            }

            .modal-content textarea {
                font-size: 0.875rem;
                rows: 2;
            }

            .modal-content .btn-primary-prominent,
            .modal-content .btn-close {
                font-size: 0.875rem;
                padding: 0.5rem 1rem;
            }

            .comment-item {
                padding: 0.5rem;
            }

            .comment-actions button {
                font-size: 0.7rem;
                padding: 0.2rem 0.5rem;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize DataTable with Responsive Extension
            $('#studentRequestsTable').DataTable({
                paging: true,
                searching: true,
                ordering: false,
                info: true,
                lengthChange: false,
                pageLength: 10,
                responsive: true
            });

            // Toastify notifications for session messages
            @if (session('success'))
                Toastify({
                    text: "{{ session('success') }}",
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#10b981",
                    stopOnFocus: true,
                }).showToast();
            @endif

            @if (session('error'))
                Toastify({
                    text: "{{ session('error') }}",
                    duration: 5000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#ef4444",
                    stopOnFocus: true,
                }).showToast();
            @endif
        });

        $(document).ready(function() {
            // Prevent background interaction when modal is open
            $(document).on('click', '.modal', function(e) {
                if ($(e.target).hasClass('modal')) {
                    $('#commentModal').addClass('hidden');
                }
            });

            // Comment button click event
            $(document).on('click', '.comment-btn', function(e) {
                e.preventDefault();
                const studentRequestId = $(this).data('id');
                $('#studentRequestId').val(studentRequestId);
                $('#commentInput').val('');
                $('#editCommentId').val('');
                $('#submitComment').show();
                $('#updateComment').hide();
                $('#commentModal').removeClass('hidden');
                loadComments(studentRequestId);
            });

            // Close modal
            $(document).on('click', '.close-modal', function() {
                $('#commentModal').addClass('hidden');
            });

            // Submit comment
            $('#submitComment').click(function() {
                const comment = $('#commentInput').val().trim();
                const studentRequestId = $('#studentRequestId').val();

                if (!comment) {
                    showToast('Comment cannot be empty', 'error');
                    return;
                }

                $.ajax({
                    url: '{{ route('Comment.request-comments.store') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        student_request_id: studentRequestId,
                        comment: comment
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#commentInput').val('');
                            loadComments(studentRequestId);
                            showToast('Comment added successfully', 'success');
                        }
                    },
                    error: function(xhr) {
                        showToast(xhr.responseJSON?.message || 'Error adding comment', 'error');
                    }
                });
            });

            // Update comment
            $('#updateComment').click(function() {
                const comment = $('#commentInput').val().trim();
                const commentId = $('#editCommentId').val();
                const studentRequestId = $('#studentRequestId').val();

                if (!comment) {
                    showToast('Comment cannot be empty', 'error');
                    return;
                }

                $.ajax({
                    url: '{{ route('Comment.request-comments.update', ':id') }}'.replace(':id',
                        commentId),
                    method: 'PUT',
                    data: {
                        _token: '{{ csrf_token() }}',
                        comment: comment
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#commentInput').val('');
                            $('#editCommentId').val('');
                            $('#submitComment').show();
                            $('#updateComment').hide();
                            loadComments(studentRequestId);
                            showToast('Comment updated successfully', 'success');
                        }
                    },
                    error: function(xhr) {
                        showToast(xhr.responseJSON?.message || 'Error updating comment',
                            'error');
                    }
                });
            });

            // Load comments
            function loadComments(studentRequestId) {
                $.ajax({
                    url: '{{ route('Comment.request-comments.index', ':id') }}'.replace(':id',
                        studentRequestId),
                    method: 'GET',
                    success: function(comments) {
                        let html = '';
                        if (comments.length === 0) {
                            html =
                                '<p class="text-gray-500 text-center text-sm sm:text-base">No comments yet.</p>';
                        } else {
                            comments.forEach(comment => {
                                const canEditDelete = comment.user_id == {{ Auth::id() }};
                                const formattedDate = new Date(comment.created_at)
                                    .toLocaleDateString('en-GB', {
                                        day: '2-digit',
                                        month: '2-digit',
                                        year: 'numeric'
                                    });
                                html += `
                                <div class="comment-item">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <strong class="text-gray-800 text-sm sm:text-base">${comment.name}</strong>
                                            <span class="text-xs sm:text-sm text-gray-500 block">${formattedDate}</span>
                                        </div>
                                        ${canEditDelete ? `
                                                <div class="comment-actions">
                                                    <button class="edit-comment" data-id="${comment.id}" data-comment="${comment.comment.replace(/"/g, '&quot;')}">
                                                        <i class="fas fa-edit mr-1"></i> Edit
                                                    </button>
                                                    <button class="delete-comment" data-id="${comment.id}">
                                                        <i class="fas fa-trash-alt mr-1"></i> Delete
                                                    </button>
                                                </div>
                                            ` : ''}
                                    </div>
                                    <p class="mt-2 text-gray-700 text-sm sm:text-base">${comment.comment}</p>
                                </div>
                            `;
                            });
                        }
                        $('#commentsList').html(html);
                    },
                    error: function(xhr) {
                        showToast('Error loading comments', 'error');
                    }
                });
            }

            // Edit comment
            $(document).on('click', '.edit-comment', function() {
                const commentId = $(this).data('id');
                const commentText = $(this).data('comment');
                $('#commentInput').val(commentText);
                $('#editCommentId').val(commentId);
                $('#submitComment').hide();
                $('#updateComment').show();
            });

            // Delete comment
            $(document).on('click', '.delete-comment', function() {
                const commentId = $(this).data('id');
                const studentRequestId = $('#studentRequestId').val();

                if (confirm('Are you sure you want to delete this comment?')) {
                    $.ajax({
                        url: '{{ route('Comment.request-comments.destroy', ':id') }}'.replace(
                            ':id', commentId),
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                loadComments(studentRequestId);
                                showToast('Comment deleted successfully', 'success');
                            }
                        },
                        error: function(xhr) {
                            showToast(xhr.responseJSON?.message || 'Error deleting comment',
                                'error');
                        }
                    });
                }
            });

            // Toast notification
            function showToast(message, type) {
                Toastify({
                    text: message,
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: type === 'success' ? "#10b981" : "#ef4444",
                    stopOnFocus: true,
                }).showToast();
            }
        });





    </script>
<script>
    $(document).ready(function() {
        // Toast notification function (minimal version for archive)
        function showToast(message, type) {
            Toastify({
                text: message,
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: type === 'success' ? "#10b981" : "#ef4444",
                stopOnFocus: true,
            }).showToast();
        }

        // Archive button click event
        $(document).on('click', '.undo-btn', function(e) {
                e.preventDefault();
                const studentRequestId = $(this).data('id');
                const undoUrl = $(this).attr('href');

                if (confirm('Are you sure you want to undo archiving this request?')) {
                    $.ajax({
                        url: undoUrl,
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: studentRequestId
                        },
                        success: function(response) {
                            if (response.success) {
                                showToast('Request unarchived successfully', 'success');
                                setTimeout(() => {
                                    location.reload(); // Reload the page after toast
                                }, 1500);
                            }
                        },
                        error: function(xhr) {
                            showToast(xhr.responseJSON?.message || 'Error unarchiving request', 'error');
                        }
                    });
                }
            });
    });
</script>


@endsection
