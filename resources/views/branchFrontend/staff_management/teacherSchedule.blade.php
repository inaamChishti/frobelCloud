@extends('layouts.branchDashboardApp')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/libs/datatables/datatables.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endsection

@section('content')
<div class="main-content" style="zoom:0.9;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1>Staff Scheduler</h1>
            <p>Mark and manage staff availability for this branch.</p>
        </div>
    </div>

    <div class="teacher-grid" id="teacher-container">
        <!-- Teachers load here -->
    </div>
</div>

<style>
    .teacher-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        grid-gap: 20px;
        padding: 10px 0;
    }

    .teacher-tile {
        border-radius: 12px;
        padding: 20px 15px;
        text-align: center;
        cursor: pointer;
        background: white;
        border: 2px solid #20439F;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .teacher-tile h3 {
        font-size: 17px;
        color: #20439F;
        margin: 0 0 8px 0;
        font-weight: 600;
        line-height: 1.2;
    }

    .teacher-tile p {
        font-size: 13px;
        color: #20439F;
        margin: 0;
        opacity: 0.9;
    }

    /* Available = GREEN */
    .teacher-tile.available {
        border-color: #28a745;
        background: rgba(40, 167, 69, 0.08);
        box-shadow: 0 0 15px rgba(40, 167, 69, 0.3);
    }

    /* Not Available = RED */
    .teacher-tile.unavailable {
        border-color: #dc3545;
        background: rgba(220, 53, 69, 0.08);
        box-shadow: 0 0 15px rgba(220, 53, 69, 0.3);
    }

    .teacher-tile:hover {
        transform: translateY(-5px) scale(1.05);
        box-shadow: 0 10px 25px rgba(0,0,0,0.2) !important;
    }

    .teacher-tile.selected {
        transform: scale(1.15);
        z-index: 10;
    }

    @media (max-width: 768px) {
        .teacher-grid {
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            grid-gap: 15px;
        }
        .teacher-tile h3 { font-size: 15px; }
        .teacher-tile p { font-size: 11px; }
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function () {
        const container = $('#teacher-container');

        function loadTeachers() {
            $.get("{{ url('teacher/getTeachers') }}", function(data) {
                container.empty();

                data.forEach(teacher => {
                    const isAvailable = teacher.is_available?.toString().toLowerCase() === 'yes';
                    const statusClass = isAvailable ? 'available' : 'unavailable';

                    const tile = `
                        <div class="teacher-tile ${statusClass}" data-teacher-id="${teacher.id}">
                            <h3>${teacher.teacher_name}</h3>
                            <p>${teacher.subject}</p>
                        </div>
                    `;
                    container.append(tile);
                });

                attachClickEvents();
            }).fail(function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Failed to load teachers',
                    confirmButtonText: 'OK',
                    allowOutsideClick: false,
                    allowEscapeKey: false
                });
            });
        }

        function attachClickEvents() {
            $('.teacher-tile').off('click').on('click', function() {
                const $tile = $(this);
                if ($tile.hasClass('selected')) return;

                const teacherId = $tile.data('teacher-id');
                $tile.addClass('selected');

                $.post("{{ url('teacher/markAttendance') }}", {
                    teacher_id: teacherId,
                    _token: "{{ csrf_token() }}"
                })
                .done(function(res) {
                    if (res.success) {
                        // SUCCESS → Auto-close after 3 sec
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: res.message,
                            timer: 3000,
                            timerProgressBar: true,
                            showConfirmButton: false
                        });

                        // Update tile instantly
                        $tile.removeClass('available unavailable selected')
                             .addClass(res.new_status === 'yes' ? 'available' : 'unavailable');

                        // Pop animation
                        $tile.css('transform', 'scale(1.3)')
                             .delay(300)
                             .queue(function(next) {
                                 $(this).css('transform', '');
                                 next();
                             });

                        setTimeout(loadTeachers, 800);
                    }
                    else if (res.conflict) {
                        $tile.removeClass('selected');

                        // CONFLICT → Stays until user clicks OK
                        Swal.fire({
                            icon: 'error',
                            title: 'Conflict!',
                            html: `<strong>${res.message}</strong>`,
                            confirmButtonText: 'Got it',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            customClass: {
                                popup: 'swal-border-red'
                            },
                            buttonsStyling: false,
                            confirmButtonColor: '#dc3545'
                        });
                    }
                    else {
                        $tile.removeClass('selected');
                        Swal.fire({
                            icon: 'warning',
                            title: 'Failed!',
                            text: res.message || 'Operation failed',
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .fail(function() {
                    $tile.removeClass('selected');
                    Swal.fire({
                        icon: 'error',
                        title: 'Server Error!',
                        text: 'Please try again later',
                        confirmButtonText: 'OK',
                        allowOutsideClick: false
                    });
                });
            });
        }

        loadTeachers();

        // Optional auto-refresh every 30 seconds
        // setInterval(loadTeachers, 30000);
    });
</script>

<!-- Red border for conflict alert -->
<style>
    .swal-border-red {
        border: 5px solid #dc3545 !important;
    }
</style>
@endsection
