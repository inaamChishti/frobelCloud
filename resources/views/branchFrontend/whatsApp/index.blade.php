@extends('layouts.branchDashboardApp')

@section('content')
<div class="container my-5">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white text-center">
            <h2 class="mb-0" style="color: #ffffff;">Send WhatsApp Message</h2>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('whatsapp.send') }}" method="POST">
                @csrf
                <div class="row g-4">
                    <!-- Student Selection (Multi-Select) -->
                    <div class="col-md-6">
                        <label for="student_id" class="form-label fw-bold" style="color: #214DB8;">Select Students</label>
                        <select class="form-select select2" id="student_id" name="student_id[]" multiple required>
                            <option value="">Select Students</option>
                            @foreach ($students as $student)
                                <option value="{{ $student->studentid }}"
                                        data-guardianmob="{{ $student->guardianmob }}"
                                        data-kinmob="{{ $student->kinmob }}"
                                        data-studentname="{{ $student->studentname }} {{ $student->studentsur }}"
                                        data-admissionid="{{ $student->admissionid }}"
                                        class="{{ $student->guardianmob || $student->kinmob ? 'has-number' : 'no-number' }}">
                                    {{ $student->studentname }} {{ $student->studentsur }} (ID: {{ $student->admissionid }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Contact Number Input with Tags -->
                    <div class="col-md-6">
                        <label for="contact_number" class="form-label fw-bold" style="color: #214DB8;">Contact Numbers</label>
                        <div class="tag-container" id="contact_number_tags"></div>
                        <input type="hidden" id="contact_number" name="contact_number" required>
                    </div>
                </div>

                <!-- Message Body -->
                <div class="mt-4">
                    <label for="message" class="form-label fw-bold" style="color: #214DB8;">Message</label>
                    <textarea class="form-control" id="message" name="message" rows="6" placeholder="Type your message here..." required></textarea>
                </div>

                <!-- Send Button -->
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary btn-lg shadow-sm">Send WhatsApp Message</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- jQuery (required for Select2) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize Select2 for student dropdown (multi-select)
    $('#student_id').select2({
        placeholder: "Select Students",
        allowClear: true,
        width: '100%',
        templateResult: function(data) {
            if (!data.element) {
                return data.text;
            }
            var $element = $(data.element);
            var $wrapper = $('<span></span>');
            $wrapper.text(data.text);
            if ($element.hasClass('has-number')) {
                $wrapper.css('color', '#28a745'); // Light green for students with numbers
            } else if ($element.hasClass('no-number')) {
                $wrapper.css('color', '#dc3545'); // Red for students with no numbers
            }
            return $wrapper;
        }
    });

    // Dynamic contact number population with tags
    $('#student_id').on('change', function() {
        const tagContainer = $('#contact_number_tags');
        const contactInput = $('#contact_number');
        // Get current numbers from hidden input to preserve user removals
        let currentNumbers = contactInput.val() ? contactInput.val().split(', ') : [];

        // Get all selected student options
        const selectedOptions = $(this).find('option:selected');
        let numbers = [];

        // Collect all numbers from selected students
        selectedOptions.each(function() {
            const guardianMob = $(this).data('guardianmob');
            const kinMob = $(this).data('kinmob');
            const studentName = $(this).data('studentname');
            const admissionId = $(this).data('admissionid');

            if (guardianMob && guardianMob.trim() !== '') {
                numbers.push({
                    id: guardianMob,
                    text: `${studentName} (ID: ${admissionId}) - Guardian: ${guardianMob}`
                });
            }
            if (kinMob && kinMob.trim() !== '') {
                numbers.push({
                    id: kinMob,
                    text: `${studentName} (ID: ${admissionId}) - Kin: ${kinMob}`
                });
            }
        });

        // Filter out numbers that were previously removed by the user
        numbers = numbers.filter(num => !currentNumbers.includes(num.id) || currentNumbers.includes(num.id));

        // Update tag container
        tagContainer.empty();
        numbers.forEach(function(number) {
            const tag = $('<span>', {
                class: 'tag',
                'data-number': number.id,
                text: number.text
            }).append(
                $('<span>', {
                    class: 'remove-tag',
                    text: '×',
                    click: function() {
                        $(this).parent().fadeOut(200, function() {
                            $(this).remove();
                            // Update hidden input
                            const remainingNumbers = tagContainer.find('.tag').map(function() {
                                return $(this).data('number');
                            }).get();
                            contactInput.val(remainingNumbers.join(', '));
                        });
                    }
                })
            );
            tagContainer.append(tag);
        });

        // Update hidden input with all numbers
        contactInput.val(numbers.map(num => num.id).join(', '));
    });
});
</script>

<style>
    /* Card and Form Styling */
    .card {
        border-radius: 10px;
        overflow: hidden;
    }
    .card-header {
        background-color: #214DB8;
        padding: 1.5rem;
    }
    .card-body {
        background-color: #ffffff;
    }
    .form-label {
        font-weight: 600;
        color: #214DB8;
    }

    /* Select2 Styling */
    .select2-container--default .select2-selection--multiple {
        border: 2px solid #214DB8;
        border-radius: 6px;
        min-height: 42px;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }
    .select2-container--default .select2-selection--multiple:focus {
        border-color: #1a3c8f;
        box-shadow: 0 0 8px rgba(33, 77, 184, 0.3);
    }
    .select2-container--default .select2-selection--multiple .select2-selection__rendered {
        color: #214DB8;
        line-height: 28px;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #214DB8;
        color: white;
        border: none;
        border-radius: 4px;
        padding: 2px 8px;
        font-size: 14px;
    }

    /* Textarea Styling */
    .form-control {
        border: 2px solid #214DB8;
        border-radius: 6px;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }
    .form-control:focus {
        border-color: #1a3c8f;
        box-shadow: 0 0 8px rgba(33, 77, 184, 0.3);
    }

    /* Tag Container Styling */
    .tag-container {
        border: 2px solid #214DB8;
        border-radius: 6px;
        padding: 8px;
        min-height: 42px;
        background-color: #f8f9fa;
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
    }
    .tag {
        background-color: #214DB8;
        color: white;
        padding: 4px 10px;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        font-size: 14px;
        max-width: calc(100% - 10px);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        transition: transform 0.2s ease;
    }
    .tag:hover {
        transform: scale(1.02);
    }
    .remove-tag {
        margin-left: 8px;
        cursor: pointer;
        font-weight: bold;
        font-size: 16px;
        transition: color 0.2s ease;
    }
    .remove-tag:hover {
        color: #ff4d4d;
    }

    /* Button Styling */
    .btn-primary {
        background-color: #214DB8;
        border: none;
        border-radius: 6px;
        padding: 10px 24px;
        font-size: 16px;
        font-weight: 600;
        transition: background-color 0.3s ease, transform 0.2s ease;
    }
    .btn-primary:hover {
        background-color: #1a3c8f;
        transform: translateY(-2px);
    }
    .btn-primary:active {
        transform: translateY(0);
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .card-body {
            padding: 1.5rem;
        }
        .btn-primary {
            width: 100%;
            padding: 12px;
        }
    }
</style>
@endsection
