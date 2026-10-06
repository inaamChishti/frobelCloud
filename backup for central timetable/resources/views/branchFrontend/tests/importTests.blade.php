@extends('layouts.branchDashboardApp')

@section('content')
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.0.1/min/dropzone.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.2.0/min/dropzone.min.js"></script>

<style>
    .student-test-container {
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

    .student-test-container .container-fluid {
        max-width: 1200px;
        margin: 0 auto;
        padding: 1rem 3rem;
    }

    .student-test-container .card {
        background: #ffffff;
        border-radius: 0.75rem;
        border: 1px solid var(--border);
        box-shadow: 0 8px 24px rgba(29, 78, 216, 0.1);
        padding: 1.25rem 1.5rem;
        transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
        position: relative;
        margin: 0;
    }

    .student-test-container .card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 3px;
        background: linear-gradient(90deg, var(--primary), var(--primary-light));
        transition: height 0.3s ease;
    }

    .student-test-container .card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 32px rgba(29, 78, 216, 0.15);
        border-color: var(--primary-light);
    }

    .student-test-container .card:hover::before {
        height: 5px;
    }

    .student-test-container .alert {
        border-radius: 0.5rem;
        padding: 0.75rem 1.25rem;
        margin-bottom: 1rem;
        border: 1px solid transparent;
    }

    .student-test-container .alert-success {
        background-color: #d1fae5;
        border-color: #a7f3d0;
        color: #065f46;
    }

    .student-test-container .alert-danger {
        background-color: var(--error-light);
        border-color: #fca5a5;
        color: var(--error);
    }

    .student-test-container .btn-primary {
        background: linear-gradient(45deg, var(--primary-dark), var(--primary));
        border: none;
        border-radius: 0.5rem;
        padding: 0.5rem 1rem;
        color: white;
        font-weight: 500;
        font-size: 0.875rem;
        transition: all 0.3s ease;
        height: 2.5rem;
        display: inline-flex;
        align-items: center;
    }

    .student-test-container .btn-primary:hover {
        background: linear-gradient(45deg, var(--primary), var(--primary-light));
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(37, 99, 235, 0.2);
        scale: 1.05;
    }

    .student-test-container h4 {
        color: var(--primary-dark);
    }

    .student-test-container .text-muted {
        color: var(--text-light);
    }

    .student-test-container .form-control {
        border: 1px solid var(--border);
        border-radius: 0.5rem;
        padding: 0.5rem 0.75rem;
        transition: all 0.3s ease;
    }

    .student-test-container .form-control:focus {
        border-color: var(--primary-light);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
    }

    .student-test-container .text-danger {
        color: var(--error);
    }

    .student-test-container .dropzone {
        border: 2px dashed var(--border);
        border-radius: 0.5rem;
        padding: 1.5rem;
        background: var(--background);
        transition: all 0.3s ease;
    }

    .student-test-container .dropzone:hover {
        border-color: var(--primary-light);
        background: var(--secondary);
    }

    .student-test-container .dz-message {
        color: var(--text-light);
        font-size: 1rem;
    }
</style>

<div class="student-test-container container-fluid flex-grow-1 container-p-y">
    <h4 class="font-weight-bold py-3 mb-4">
        <span class="text-muted font-weight-light">Student Tests /</span> Add Records
    </h4>

    <!-- alerts -->
    @if (Session::has('alert-success'))
    <div class="alert alert-success alert-dismissible">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <strong>Success!</strong> {{ Session::get('alert-success') }}.
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

    <!-- DataTable within card -->
    <div class="card">
        <h5 class="card-header" style="color: var(--primary-dark);">Upload sheet</h5>

        <div class="container-fluid mt-3">
            <div class="mb-3" id="dropzoneForm">
                <label class="form-label">Choose Excel</label>
                <div class="dropzone" id="myDropzone"></div>
                <small class="text-danger">Only these formats will be accepted. (xls, csv, xlsx)</small>
                <br>
            </div>
        </div>
    </div>
</div>

<script>
    // Dropzone configuration
    Dropzone.autoDiscover = false;

    var myDropzone = new Dropzone("#myDropzone", {
        url: "{{ route('student-test.import') }}",
        method: "post",
        paramName: "file",
        maxFilesize: 5, // Set your desired max file size in MB
        acceptedFiles: ".xls, .csv, .xlsx",
        addRemoveLinks: true,
        dictRemoveFile: "Remove",
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        init: function () {
            this.on("success", function (file, response) {
                // Handle success, e.g., show a success message
                console.log(response.message);

                // Check if all files are uploaded successfully
                if (myDropzone.getQueuedFiles().length === 0 && myDropzone.getUploadingFiles().length === 0) {
                    // Reload the page after a short delay
                    setTimeout(function () {
                        location.reload();
                    }, 1000);
                }
            });

            this.on("error", function (file, errorMessage) {
                // Handle errors, e.g., show an error message
                console.error(errorMessage);
            });
        }
    });
</script>
@endsection
