<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/signature_pad/4.1.5/signature_pad.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* General Styles */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #FFFFFF;
            color: #333333;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            /* Ensure body fits within iframe */
            height: 100%;
            overflow-x: hidden;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            /* Smooth scrolling on iOS */
        }

        .container {
            width: 90%;
            /* Adjusted for smaller screens */
            max-width: 1200px;
            margin: 0 auto;
            background-color: #FFFFFF;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            padding: 1rem;
            min-height: 100%;
            /* Ensure container takes full height */
            box-sizing: border-box;
        }

        /* Form Title Styles */
        h4 {
            color: #169F9F;
            font-weight: 700;
            margin-bottom: 24px;
            font-size: 1.5rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
        }

        h4.student-details {
            color: #767676;
        }

        h4.parent-details,
        h4.emergency-details,
        h4.consent-details {
            color: #575555;
        }

        h4::after {
            content: '';
            display: block;
            width: 60px;
            height: 4px;
            background-color: #169F9F;
            position: absolute;
            bottom: -10px;
            left: 0;
            border-radius: 2px;
        }

        p {
            color: #555;
            margin-bottom: 24px;
            font-size: 0.9rem;
            line-height: 1.8;
        }

        /* Table Styles */
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
            background-color: #FFFFFF;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            margin-top: 1rem;
            font-size: 0.85rem;
        }

        .table th,
        .table td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #F5F5F5;
        }

        .table th {
            background-color: #169F9F;
            color: #FFFFFF;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            font-size: 0.8rem;
        }

        .table td {
            vertical-align: middle;
        }

        /* Button Styles */
        .btn {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            max-width: 200px;
        }

        .btn-primary {
            background-color: #169F9F;
            color: white;
            border: 2px solid #169F9F;
            padding: 3px 6px;
            height: 28px;
        }

        .btn-primary:hover {
            background-color: #d43f5b;
            transform: translateY(-1px);
        }

        .btn-success {
            background-color: #169F9F;
            color: white;
        }

        .btn-success:hover {
            background-color: #d43f5b;
            transform: translateY(-1px);
        }

        .btn-danger {
            background-color: #E6F3FA;
            color: #333333;
        }

        .btn-danger:hover {
            background-color: #D1E7F2;
            transform: translateY(-1px);
        }

        .btn-submit {
            background-color: #169F9F;
            color: white;
            border: 2px solid #169F9F;
            margin-top: 1rem;
            margin-bottom: 3rem;
            padding: 6px 12px;
            height: 36px;
        }

        .btn-table-action {
            padding: 3px 6px;
            font-size: 0.7rem;
            border-radius: 4px;
            line-height: 1.2;
            max-width: 80px;
        }

        .btn-table-action.editStudent {
            background-color: #FFC107;
            color: #333333;
        }

        .btn-table-action.editStudent:hover {
            background-color: #E0A800;
            transform: translateY(-1px);
        }

        .btn-table-action.deleteStudent {
            background-color: #E6F3FA;
            color: #333333;
        }

        .btn-table-action.deleteStudent:hover {
            background-color: #D1E7F2;
            transform: translateY(-1px);
        }

        .btn-modal {
            padding: 5px 10px;
            font-size: 0.8rem;
            border-radius: 6px;
            height: 34px;
            max-width: 120px;
        }

        .btn-modal-close {
            background-color: #6C757D;
            color: white;
        }

        .btn-modal-close:hover {
            background-color: #5A6268;
            transform: translateY(-1px);
        }

        .btn-modal-save {
            background-color: #169F9F;
            color: white;
            border: 2px solid #169F9F;
        }

        .btn-modal-save:hover {
            background-color: #d43f5b;
            transform: translateY(-1px);
        }

        /* Form Input Styles */
        input[type="text"],
        input[type="date"],
        input[type="email"],
        input[type="tel"],
        textarea,
        select {
            width: 100%;
            max-width: 95%;
            padding: 6px;
            border: 2px solid #c9abab;
            border-radius: 0;
            font-size: 0.8rem;
            transition: all 0.3s ease;
            background-color: #FFFFFF;
            color: #333333;
            margin-bottom: 10px;
            height: 34px;
            box-sizing: border-box;
        }

        textarea {
            height: 82px;
        }

        input[type="text"]:focus,
        input[type="date"]:focus,
        input[type="email"]:focus,
        input[type="tel"]:focus,
        textarea:focus,
        select:focus {
            border-color: #169F9F;
            box-shadow: 0 0 0 3px rgba(103, 192, 234, 0.1);
            outline: none;
        }

        .square-border {
            border: 2px solid #c9abab;
            border-radius: 0;
            padding: 6px;
            transition: all 0.3s ease;
        }

        .square-border:focus {
            border-color: #169F9F;
            box-shadow: 0 0 0 3px rgba(103, 192, 234, 0.1);
        }

        .valid {
            border-color: #28a745 !important;
        }

        .invalid {
            border-color: #dc3545 !important;
        }

        /* Input Field Title Styles */
        .form-label {
            font-weight: 600;
            color: #169F9F;
            margin-bottom: 6px;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Modal Styles */
        .modal-content {
            border-radius: 12px;
            border: none;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            max-height: 80vh;
            /* Limit modal height */
            overflow-y: auto;
            /* Allow scrolling within modal */
            -webkit-overflow-scrolling: touch;
        }

        .modal-header {
            background-color: #FFFFFF;
            border-bottom: 2px solid #169F9F;
            padding: 12px;
        }

        .modal-title {
            font-weight: 700;
            color: #333333;
            font-size: 1.2rem;
        }

        .modal-body {
            padding: 12px;
            max-height: 60vh;
            /* Restrict modal body height */
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }

        .modal-footer {
            border-top: 2px solid #169F9F;
            padding: 12px;
        }

        #studentModal {
            /* Removed zoom property for better iPad compatibility */
            transform: scale(1);
        }

        /* Signature Pad Styles */
        #signature-pad {
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 0;
            width: 100%;
            height: 102px;
            touch-action: none;
            /* Improve touch handling on iPad */
        }

        #signature-pad.disabled {
            pointer-events: none;
            opacity: 0.5;
            cursor: not-allowed;
        }

        .signature-container {
            border: 1px solid #ccc;
            padding: 0.5rem;
            border-radius: 0.25rem;
            background: #fff;
            max-width: 95%;
        }

        /* Checkbox and Radio Styles */
        .form-check-input {
            margin-right: 6px;
        }

        .form-check-label {
            font-weight: 400;
            color: #555;
            font-size: 0.8rem;
        }

        /* Terms Modal Styles */
        #termsModal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            overflow-y: auto;
            /* Ensure modal scrolls if content overflows */
            -webkit-overflow-scrolling: touch;
        }

        #termsModal>div {
            background: white;
            width: 90%;
            max-width: 600px;
            border-radius: 12px;
            padding: 16px;
            position: relative;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            max-height: 80vh;
            /* Limit modal height */
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }

        #termsModal h2 {
            color: #169F9F;
            margin: 0;
            font-size: 1.5rem;
            font-weight: 700;
        }

        #termsModal ol {
            padding-left: 20px;
            font-size: 0.85rem;
        }

        #termsModal ol li {
            margin-bottom: 10px;
            color: #555;
        }

        #termsModal .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #ddd;
            padding-bottom: 8px;
        }

        #termsModal .close-button {
            background: red;
            color: white;
            border: none;
            padding: 3px 6px;
            cursor: pointer;
            font-size: 0.75rem;
            border-radius: 4px;
        }

        #termsModal .modal-content {
            max-height: 50vh;
            overflow-y: auto;
            padding-right: 8px;
            margin-top: 8px;
        }

        #termsModal .modal-footer {
            text-align: right;
            padding-top: 8px;
        }

        /* Terms Note Styles */
        .terms-note {
            background-color: #f8f9fa;
            border-left: 4px solid #169F9F;
            padding: 10px;
            margin-bottom: 15px;
            font-size: 0.9rem;
            color: #333333;
        }

        .terms-note strong {
            color: #169F9F;
        }

        /* Terms and Conditions Link */
        .terms-link {
            color: blue;
            text-decoration: underline;
            font-size: 0.8rem;
        }

        /* reCAPTCHA Policy */
        .recaptcha-policy {
            color: #6c757d;
            font-size: 0.75rem;
        }

        /* Responsive Styles for iPad and Smaller Devices */
        @media (max-width: 1024px) {
            body {
                font-size: 0.9rem;
                /* Slightly smaller font for iPad */
                overflow-x: hidden;
                /* Prevent horizontal scroll */
            }

            .container {
                width: 95%;
                padding: 0.5rem;
                margin: 0 auto;
            }

            .row {
                flex-direction: column;
            }

            .col-md-6,
            .col-md-12 {
                width: 100%;
            }

            .table th,
            .table td {
                padding: 6px;
                font-size: 0.75rem;
            }

            input[type="text"],
            input[type="date"],
            input[type="email"],
            input[type="tel"],
            textarea,
            select {
                max-width: 100%;
                height: 32px;
                font-size: 0.75rem;
            }

            textarea {
                height: 72px;
            }

            .btn {
                padding: 5px 10px;
                font-size: 0.75rem;
            }

            .btn-table-action {
                font-size: 0.65rem;
                padding: 2px 5px;
            }

            #termsModal>div {
                width: 95%;
                max-height: 85vh;
            }

            #studentModal {
                transform: scale(1);
                /* Ensure no zoom issues */
            }

            .modal-content {
                max-height: 85vh;
                /* Adjust modal height for iPad */
            }

            .modal-body {
                max-height: 65vh;
            }

            #signature-pad {
                height: 90px;
                /* Slightly smaller for iPad */
            }
        }

        /* Specific Media Query for iPad 10 (2360x1640, ~264 PPI) */
        @media only screen and (min-device-width: 820px) and (max-device-width: 1180px) and (-webkit-min-device-pixel-ratio: 2) {
            body {
                zoom: 1;
                /* Reset any zoom to avoid scaling issues */
                -webkit-text-size-adjust: 100%;
                /* Prevent font scaling issues */
                overflow: hidden;
                /* Prevent content mixing */
                height: 100vh;
                /* Full viewport height */
            }

            .container {
                width: 98%;
                max-height: 100vh;
                overflow-y: auto;
                /* Allow scrolling within container */
                padding-bottom: 2rem;
                /* Extra padding to prevent cutoff */
            }

            .modal-dialog {
                width: 95%;
                max-width: 800px;
                margin: 1rem auto;
            }

            #studentModal,
            #termsModal {
                top: 0;
                transform: none;
                /* Remove scaling */
                max-height: 90vh;
                overflow-y: auto;
            }

            #termsModal>div {
                width: 90%;
                max-height: 80vh;
            }

            .modal-content {
                max-height: 80vh;
                overflow-y: auto;
            }

            .modal-body {
                max-height: 60vh;
                padding: 0.5rem;
            }

            input,
            select,
            textarea {
                font-size: 0.8rem;
                /* Adjust font size for clarity */
                padding: 5px;
            }

            .btn {
                font-size: 0.7rem;
                padding: 4px 8px;
            }

            .btn-submit {
                margin-bottom: 4rem;
                /* Extra space to prevent cutoff */
            }

            #signature-pad {
                height: 80px;
                /* Adjusted for iPad */
                max-width: 100%;
            }

            /* Ensure no content mixing at the bottom */
            .mt-4,
            .mt-3 {
                margin-bottom: 1.5rem !important;
                /* Prevent overlap */
            }
        }
    </style>
</head>

<body>
    <div class="container mt-4">
        <h4 style="color:#767676;">Student/s Details</h4>
        <table class="table mt-3">
            <thead>
                <tr>
                    <th>First Name</th>
                    <th>Date of Birth</th>
                    <th>Years in School</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="studentTableBody"></tbody>
        </table>
        <button class="btn btn-primary"
            style="background-color: #169F9F;border: 2px solid #169F9F;padding: 5px 10px;display: flex;align-items: center;justify-content: center;height: 30px;"
            data-bs-toggle="modal" data-bs-target="#studentModal">
            Add Student
        </button>
        {{-- @if (isset($_SERVER['HTTP_REFERER']))
    @php
        $ref = parse_url($_SERVER['HTTP_REFERER']);
        $domain = $ref['host'] ?? '';
    @endphp

    <p>Referrer Domain: {{ $domain }}</p>
@else
    <p>No Referrer</p>
@endif --}}

        <div class="mt-4">
            {{-- <div class="row">
                <div class="col-md-6">
                    <label class="form-label">Select Branch<span style="color:red;">*</span></label>
                    <select name="branch" class="form-control square-border" required>
                        <option value="" selected>Please Select...</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->branch_id }}">{{ $branch->branch_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div> --}}
            @if (isset($_SERVER['HTTP_REFERER']))
                @php
                    $ref = parse_url($_SERVER['HTTP_REFERER']);
                    $domain = $ref['host'] ?? '';
                @endphp
            @endif

            <div class="row">
                <div class="col-md-6">
                    <label class="form-label">Select Branch<span style="color:red;">*</span></label>
                    <select name="branch" class="form-control square-border" required>
                        <option value="" selected>Please Select...</option>

                        @if (!empty($domain) && $domain == 'hayes.frobeleducation.co.uk')
                            <option value="frobel_hayes_938">Hayes</option>
                        @else
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->branch_id }}">{{ $branch->branch_name }}</option>

                            @endforeach
                            {{-- <option value="frobel_hayes_938">Hayes</option> --}}
                        @endif
                    </select>
                </div>
            </div>


            <div class="row">
                <div class="col-md-6">
                    <label class="form-label">First Name<span style="color:red;">*</span></label>
                    <input type="text" name="parent1_first_name" placeholder="First Name"
                        class="form-control parent-input square-border" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="parent1_last_name" placeholder="Last Name"
                        class="form-control parent-input square-border" required>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-12">
                    <label class="form-label">Address / Street Address <span style="color:red;">*</span></label>
                    <input type="text" name="parent1_Address" class="form-control square-border parent-input"
                        placeholder="Address / Street Address">
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-12">
                    <label class="form-label">Address Line 2</label>
                    <input type="text" name="parent1_Address_line2" class="form-control square-border parent-input"
                        placeholder="Address Line 2">
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-6">
                    <label class="form-label">City</label>
                    <input type="text" name="parent1_city" class="form-control square-border parent-input"
                        placeholder="City">
                </div>
                <div class="col-md-6">
                    <label class="form-label">County / State / Region</label>
                    <input type="text" name="parent1_country_state_region"
                        class="form-control square-border parent-input" placeholder="County / State / Region">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3"><label class="form-label">ZIP / Postal Code</label><input type="text"
                        class="form-control square-border" name="parent1_zipCode"></div>
                <div class="col-md-6 mb-3"><label class="form-label">Country</label><select
                        class="form-control square-border" name="parent1_country">
                        <option>United Kingdom</option>
                    </select></div>
            </div>

            <div class="row mt-3">
                <div class="col-md-6">
                    <label class="form-label">Email<span style="color:red;">*</span></label>
                    <input type="text" name="parent1_email" class="form-control parent-input square-border"
                        placeholder="Email">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Mobile<span style="color:red;">*</span></label>
                    <input type="text" name="parent1_mobile" class="form-control parent-input square-border"
                        placeholder="Mobile">
                </div>
            </div>
        </div>

        <div class="mt-4">
            <h4 style="color:#575555;">Emergency Contact 1 Details</h4>
            <p>We are required to have 2 Emergency Contact details on record.</p>

            <div class="row">
                <div class="col-md-6">
                    <label class="form-label">First Name<span style="color:red;">*</span></label>
                    <input type="text" name="emergency_conatct1_first_name" placeholder="First Name"
                        class="form-control parent-input square-border" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="emergency_conatct1_last_name" placeholder="Last Name"
                        class="form-control parent-input square-border" required>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-12">
                    <label class="form-label">Address / Street Address <span style="color:red;">*</span></label>
                    <input type="text" name="emergency_conatct1_Address"
                        class="form-control parent-input square-border" placeholder="Address / Street Address">
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-12">
                    <label class="form-label">Address Line 2</label>
                    <input type="text" name="emergency_conatct1_Address_line2"
                        class="form-control square-border parent-input" placeholder="Address Line 2">
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-6">
                    <label class="form-label">City</label>
                    <input type="text" name="emergency_conatct1_city"
                        class="form-control square-border parent-input" placeholder="City">
                </div>
                <div class="col-md-6">
                    <label class="form-label">County / State / Region</label>
                    <input type="text" name="emergency_conatct1_country_state_region"
                        class="form-control square-border parent-input" placeholder="County / State / Region">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3"><label class="form-label">ZIP / Postal Code</label><input type="text"
                        class="form-control square-border" name="emergency_conatct1_zipCode"></div>
                <div class="col-md-6 mb-3"><label class="form-label">Country</label><select
                        class="form-control square-border" name="emergency_conatct1_country">
                        <option>United Kingdom</option>
                    </select></div>
            </div>

            <div class="row mt-3">
                <div class="col-md-6">
                    <label class="form-label">Email<span style="color:red;">*</span></label>
                    <input type="text" name="emergency_conatct1_email"
                        class="form-control square-border parent-input" placeholder="Email">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Mobile<span style="color:red;">*</span></label>
                    <input type="text" name="emergency_conatct1_mobile"
                        class="form-control square-border parent-input" placeholder="Mobile">
                </div>
            </div>
        </div>

        <div class="mt-4">
            <div class="row">
                <h4 style="color:#575555;">Consent <span style="color:red;">*</span></h4>
                <hr>
                <div class="col-md-6">
                    <label class="form-label">Parent / Guardian Name<span style="color:red;">*</span></label>
                    <input type="text" name="consent_1_first_name" placeholder="First Name"
                        class="form-control parent-input square-border" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="consent_1_last_name" placeholder="Last Name"
                        class="form-control parent-input square-border" required>
                </div>
            </div>

            <label>
                <input type="checkbox" name="consent_1_checkbox" value="no" disabled required>
                I have read and understand the terms and conditions and agree to be bound by them.
            </label>

            <div class="gfield_description gfield_consent_description">
                <a href="javascript:void(0);" onclick="openModal()" style="color: blue; text-decoration: underline;">
                    Tuition Centre Terms & Conditions
                </a>
            </div>

            <div class="row mt-3">
                <div class="col-md-6">
                    <label for="signature">Signature:</label>
                    <div class="border p-2 rounded" style="background: #fff;">
                        <canvas id="signature-pad" width="400" height="150"
                            style="border: 1px solid #ccc; width: 100%;" class="disabled"></canvas>
                    </div>
                    <button type="button" id="clear-signature" class="btn btn-danger btn-sm mt-2">Clear</button>
                    <input type="hidden" id="signature" name="consent_1signature">
                </div>
                <div class="col-md-6">
                    <label for="date">Date:</label>
                    <input type="date" id="date" name="consent_1date" class="form-control square-border">
                </div>
            </div>

            <div class="mb-3">
                <label for="how_did_you_hear" class="form-label">How did you hear about us? <span
                        class="text-primary">(Required)</span></label>
                <select name="how_did_you_hear" id="how_did_you_hear" class="form-select square-border" required>
                    <option value="" selected>Please Select...</option>
                    <option value="Social Media">Social Media</option>
                    <option value="Search Engine">Search Engine</option>
                    <option value="Leaflets">Leaflets</option>
                    <option value="Friends">Friends</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <div class="mt-3">
                <p class="text-muted small">
                    This site is protected by reCAPTCHA and the Google
                    <a href="https://policies.google.com/privacy" class="text-primary" target="_blank">Privacy
                        Policy</a> and
                    <a href="https://policies.google.com/terms" class="text-primary" target="_blank">Terms of
                        Service</a> apply.
                </p>
            </div>
        </div>

        <div id="termsModal"
            style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
            <div
                style="background: white; width: 60%; max-width: 800px; border-radius: 10px; padding: 20px; position: relative;">
                <div class="modal-header">
                    <h2 style="margin: 0;color: #169F9F !important;">Terms and Conditions for Frobel Tuition</h2>
                    <button onclick="closeModal()" class="close-button">X</button>
                </div>
                <div class="modal-content">
                    <div class="terms-note">
                        <p>You must check <strong>all terms and conditions</strong> to proceed.</p>
                    </div>
                    <ol type="1">
                        <li><input type="checkbox" name="term_1" class="term-checkbox"
                                data-text="Child care accounts are payable every four weeks, in advance, by card/direct debit/cheque/bank transfer/cash.">
                            Child care accounts are payable every four weeks, in advance, by card/direct
                            debit/cheque/bank transfer/cash.</li>
                        <li><input type="checkbox" name="term_2" class="term-checkbox"
                                data-text="Unless we are in breach of these terms and conditions, all booked sessions must be paid for regardless of student absence. No refunds are given for missed sessions, neither will the fee be carried forward. A session can be rescheduled, at no additional charge for planned absence, if the Admin Team has been informed as per our terms and conditions.">
                            Unless we are in breach of these terms and conditions, all booked sessions must be paid for
                            regardless of student absence. No refunds are given for missed sessions, neither will the
                            fee be carried forward. A session can be rescheduled, at no additional charge for planned
                            absence, if the Admin Team has been informed as per our terms and conditions.</li>
                        <li><input type="checkbox" name="term_3" class="term-checkbox"
                                data-text="I agree to pay the fees in advance by card/direct debit/cheque/bank transfer/cash.">
                            I agree to pay the fees in advance by card/direct debit/cheque/bank transfer/cash.</li>
                        <li><input type="checkbox" name="term_4" class="term-checkbox"
                                data-text="I will not bring my child into the centre if the child is sick and will inform the Admin Team about the sickness. I understand that I am required to inform the Centre at least 24 hours in advance, unless it is the case where this is not reasonable. I can then reschedule the missed lesson.">
                            I will not bring my child into the centre if the child is sick and will inform the Admin
                            Team about the sickness. I understand that I am required to inform the Centre at least 24
                            hours in advance, unless it is the case where this is not reasonable. I can then reschedule
                            the missed lesson.</li>
                        <li><input type="checkbox" name="term_5" class="term-checkbox"
                                data-text="For holidays and planned absence, parents/students must give the Centre 2 weeks’ written notice. During the holiday/planned absence, payments to the Centre will continue as per the agreed schedule, and compensation lessons will be arranged on the student’s return from holiday or leave. Where there is a need for emergency leave, and 2 weeks’ written notice is not reasonable, please contact the Admin Team to request the requirement for 2 weeks’ notice to be waived.">
                            For holidays and planned absence, parents/students must give the Centre 2 weeks’ written
                            notice. During the holiday/planned absence, payments to the Centre will continue as per the
                            agreed schedule, and compensation lessons will be arranged on the student’s return from
                            holiday or leave. Where there is a need for emergency leave, and 2 weeks’ written notice is
                            not reasonable, please contact the Admin Team to request the requirement for 2 weeks’ notice
                            to be waived.</li>
                        <li><input type="checkbox" name="term_6" class="term-checkbox"
                                data-text="If a parent/guardian decides to cancel tuition and the admission, two weeks’ written notice will be required. Fees continue to be payable during the 2-week notice of cancellation period. Fees continue to be payable until the Centre receives written notice of intention to cancel, even if a student ceases to attend.">
                            If a parent/guardian decides to cancel tuition and the admission, two weeks’ written notice
                            will be required. Fees continue to be payable during the 2-week notice of cancellation
                            period. Fees continue to be payable until the Centre receives written notice of intention to
                            cancel, even if a student ceases to attend.</li>
                        <li><input type="checkbox" name="term_7" class="term-checkbox"
                                data-text="If a parent/guardian decides to amend the number of hours of tuition purchased, as stated on the admission form, 2 weeks’ written notice of this change will be required. The original hours of tuition purchased will continue to be payable during the 2 weeks’ notice period.">
                            If a parent/guardian decides to amend the number of hours of tuition purchased, as stated on
                            the admission form, 2 weeks’ written notice of this change will be required. The original
                            hours of tuition purchased will continue to be payable during the 2 weeks’ notice period.
                        </li>
                        <li><input type="checkbox" name="term_8" class="term-checkbox"
                                data-text="There is a £50 refundable registration fee payable at the time of admission. This will not be refunded if the admission is cancelled before 6 months.">
                            There is a £50 refundable registration fee payable at the time of admission. This will not
                            be refunded if the admission is cancelled before 6 months.</li>
                        <li><input type="checkbox" name="term_9" class="term-checkbox"
                                data-text="Following the 2-week cancellation period, the deposit will be refunded. The admission is then cancelled with immediate effect.">
                            Following the 2-week cancellation period, the deposit will be refunded. The admission is
                            then cancelled with immediate effect.</li>
                        <li><input type="checkbox" name="term_10" class="term-checkbox"
                                data-text="No rescheduled sessions can be awarded once payment has ceased."> No
                            rescheduled sessions can be awarded once payment has ceased.</li>
                        <li><input type="checkbox" name="term_11" class="term-checkbox"
                                data-text="It is the responsibility of the parent/guardian to ensure the student arrives safely at the Centre for their sessions.">
                            It is the responsibility of the parent/guardian to ensure the student arrives safely at the
                            Centre for their sessions.</li>
                        <li><input type="checkbox" name="term_12" class="term-checkbox"
                                data-text="The Centre has the right to exclude a child at any time."> The Centre has
                            the right to exclude a child at any time.</li>
                    </ol>
                    <p>These terms and conditions represent the entire agreement between the parents and Saein Club
                        Barking.</p>
                    <p>Any other understanding, agreement, whether verbal or written, expressed or implied is excluded
                        to the fullest extent permitted by law. We reserve the right to amend these terms and conditions
                        any time and will inform our customers of any changes in writing.</p>
                </div>
                <div class="modal-footer">
                    <button onclick="closeModal()" class="btn btn-modal btn-modal-save">Save</button>
                </div>
            </div>
        </div>

        <button class="btn btn-success mt-3 mb-5" id="submitAll"
            style="background-color: #169F9F; color: white; border-color: #169F9F; display: none;">
            Submit
        </button>
    </div>

    <!-- Student Modal -->
    <div class="modal" id="studentModal" tabindex="-1" aria-labelledby="studentModalLabel" aria-hidden="true"
        style="zoom: 0.9;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="studentModalLabel">Student Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="studentForm">
                        <input type="hidden" id="editIndex">
                        <div class="row">
                            <div class="col-md-4 mb-3"><label class="form-label">First <span
                                        style="color:red;">*</span></label><input type="text"
                                    class="form-control square-border" name="firstName" required></div>
                            <div class="col-md-4 mb-3"><label class="form-label">Middle</label><input type="text"
                                    class="form-control square-border" name="middleName"></div>
                            <div class="col-md-4 mb-3"><label class="form-label">Last</label><input type="text"
                                    class="form-control square-border" name="lastName" required></div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3"><label class="form-label">DOB <span
                                        style="color:red;">*</span></label><input type="date"
                                    class="form-control square-border" name="dob" required></div>
                            <div class="col-md-4 mb-3"><label class="form-label">Gender <span
                                        style="color:red;">*</span></label><select class="form-control square-border"
                                    name="gender" required>
                                    <option>Male</option>
                                    <option>Female</option>
                                    <option>Other</option>
                                </select></div>
                            <div class="col-md-4 mb-3"><label class="form-label">Year in School <span
                                        style="color:red;">*</span></label><select class="form-control square-border"
                                    name="yearInSchool" required>
                                    <option>1</option>
                                    <option>2</option>
                                    <option>3</option>
                                    <option>4</option>
                                    <option>5</option>
                                    <option>6</option>
                                    <option>7</option>
                                    <option>8</option>
                                    <option>9</option>
                                    <option>10</option>
                                    <option>11</option>
                                    <option>12</option>
                                    <option>13</option>
                                </select></div>
                        </div>
                        <div class="mb-3"><label class="form-label">No of Tuition Hours Required (per week)</label>
                            <input type="number" class="form-control square-border" name="tuitionHours" required
                                min="1" max="3">
                        </div>
                        <div class="mb-3"><label class="form-label">Any Medical Conditions?<span
                                    style="color:red;">*</span></label>
                            <textarea class="form-control square-border" name="medicalConditions" required></textarea>
                        </div>
                        <div class="mb-3"><label class="form-label">Any Allergies? <span
                                    style="color:red;">*</span></label>
                            <textarea class="form-control square-border" name="allergies" required></textarea>
                        </div>
                        <div class="mb-3"><label class="form-label">Any SEND or Additional Needs? <span
                                    style="color:red;">*</span></label>
                            <textarea class="form-control square-border" name="additionalNeeds" required></textarea>
                        </div>
                        <h5>GP Information</h5>

<!-- Full Name and GP Phone in the same row (inline) -->
<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Full Name</label>
        <input type="text" class="form-control square-border" name="gpFirstName">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">GP Phone</label>
        <input type="text" class="form-control square-border" name="GPPhone">
    </div>
</div>

{{-- <div class="col-md-4 mb-3"><label class="form-label">Prefix</label><select
        class="form-control square-border" name="gpPrefix">
        <option>Dr.</option>
        <option>Mr.</option>
        <option>Ms.</option>
    </select></div> --}}
{{-- <div class="col-md-4 mb-3"><label class="form-label">Last Name</label><input
        type="text" class="form-control square-border" name="gpLastName"></div> --}}

{{-- <div class="mb-3"><label class="form-label">GP Address</label><input type="text"
        class="form-control square-border" name="gpAddress"></div>
<div class="mb-3"><label class="form-label">Address Line 2</label><input type="text"
        class="form-control square-border" name="gpAddressLineTwo"></div>
<div class="row"> --}}
    {{-- <div class="col-md-6 mb-3"><label class="form-label">City</label><input type="text"
            class="form-control square-border" name="city"></div>
    <div class="col-md-6 mb-3"><label class="form-label">County / State / Region</label><input
            type="text" class="form-control square-border" name="CountyStateRegion"></div>
</div>
<div class="row">
    <div class="col-md-6 mb-3"><label class="form-label">ZIP / Postal Code</label><input
            type="text" class="form-control square-border" name="zipCode"></div>
    <div class="col-md-6 mb-3"><label class="form-label">Country</label><select
            class="form-control square-border" name="country">
            <option>United Kingdom</option>
        </select></div>
</div>  --}}

<div class="mb-3">
    <label class="form-label">I/We give consent to the centre to seek medical treatment or
        advice, for my child in the case of an emergency. <span style="color:red;">*</span></label>
    <div class="form-check"><input class="form-check-input" type="radio"
            name="medicalConsent" value="yes" required><label
            class="form-check-label">Yes</label></div>
    <div class="form-check"><input class="form-check-input" type="radio"
            name="medicalConsent" value="no"><label class="form-check-label">No</label>
    </div>
</div>

<div class="mb-3">
    <label class="form-label">I give consent for my child’s photograph/video to be
        taken/uploaded for use on:</label>
    <div class="form-check"><input class="form-check-input" type="checkbox"
            name="photoConsent" value="website"><label class="form-check-label">Frobel
            Education’s website</label></div>
    <div class="form-check"><input class="form-check-input" type="checkbox"
            name="photoConsent" value="socialMedia"><label class="form-check-label">Frobel
            Education’s social media accounts</label></div>
    <div class="form-check"><input class="form-check-input" type="checkbox"
            name="photoConsent" value="marketing"><label class="form-check-label">Frobel
            Education’s prospectus and marketing material</label></div>
</div>

<div class="mb-3">
    <label class="form-label">I give my child permission to leave the centre alone.
        (Required)</label>
    <div class="form-check"><input class="form-check-input" type="radio" name="leaveAlone"
            value="yes" required><label class="form-check-label">Yes</label></div>
    <div class="form-check"><input class="form-check-input" type="radio" name="leaveAlone"
            value="no"><label class="form-check-label">No</label></div>
</div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveStudent">Save Student</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const canvas = document.getElementById("signature-pad");
        const signaturePad = new SignaturePad(canvas);
        const signatureInput = document.getElementById("signature");
        const clearButton = document.getElementById("clear-signature");
        const submitButton = document.getElementById("submitAll");
        const consentCheckbox = document.querySelector('input[name="consent_1_checkbox"]');
        const termCheckboxes = document.querySelectorAll('.term-checkbox');

        // Initially disable signature pad, submit button, and consent checkbox
        canvas.classList.add('disabled');
        submitButton.style.display = 'none';
        consentCheckbox.disabled = true;

        function clearSignature() {
            signaturePad.clear();
            signatureInput.value = "";
        }

        function saveSignature() {
            if (!signaturePad.isEmpty()) {
            signatureInput.value = signaturePad.toDataURL();
            }
        }

        function toggleSignaturePad() {
            if (consentCheckbox.checked) {
                canvas.classList.remove('disabled');
                submitButton.style.display = 'inline-flex';
            } else {
                canvas.classList.add('disabled');
                submitButton.style.display = 'none';
                signaturePad.clear();
                signatureInput.value = "";
            }
        }

        function checkAllTerms() {
            const allChecked = Array.from(termCheckboxes).every(checkbox => checkbox.checked);
            consentCheckbox.disabled = !allChecked;
            consentCheckbox.checked = allChecked;
            toggleSignaturePad();
        }

        // Add event listeners to term checkboxes
        termCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', checkAllTerms);
        });

        // Check terms and conditions before allowing signature
        canvas.addEventListener('mousedown', (e) => {
            if (!consentCheckbox.checked) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Terms and Conditions',
                    text: 'Please read and agree to all Terms and Conditions before providing your signature.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#B66EDE'
                });
            }
        });

        canvas.addEventListener('touchstart', (e) => {
            if (!consentCheckbox.checked) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Terms and Conditions',
                    text: 'Please read and agree to all Terms and Conditions before providing your signature.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#B66EDE'
                });
            }
        });

        consentCheckbox.addEventListener('change', toggleSignaturePad);
        clearButton.addEventListener('click', clearSignature);
        canvas.addEventListener("mouseup", saveSignature);
        canvas.addEventListener("touchend", saveSignature);

        document.addEventListener("mousedown", (event) => {
            if (!canvas.contains(event.target) && !clearButton.contains(event.target)) {
                saveSignature();
            }
        });

        document.addEventListener("touchstart", (event) => {
            if (!canvas.contains(event.target) && !clearButton.contains(event.target)) {
                saveSignature();
            }
        });

        // Real-time validation for required fields
        const requiredFields = document.querySelectorAll('input[required], select[required]');
        requiredFields.forEach(field => {
            field.addEventListener('input', function() {
                if (this.value.trim() !== '' && !(this.tagName === 'SELECT' && this.value === '')) {
                    this.classList.remove('invalid');
                    this.classList.add('valid');
                } else {
                    this.classList.remove('valid');
                    this.classList.add('invalid');
                }
            });
            field.addEventListener('change', function() {
                if (this.value.trim() !== '' && !(this.tagName === 'SELECT' && this.value === '')) {
                    this.classList.remove('invalid');
                    this.classList.add('valid');
                } else {
                    this.classList.remove('valid');
                    this.classList.add('invalid');
                }
            });
        });
    });

    function openModal() {
        document.getElementById('termsModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('termsModal').style.display = 'none';
    }

    $(document).ready(function() {
        $('input[name="consent_1_checkbox"]').on('change', function() {
            $(this).val(this.checked ? 'yes' : 'no');
            console.log(this.name + ":", $(this).val());
        });

        let students = [];
        let editIndex = -1;

        const renderTable = () => {
            $('#studentTableBody').empty().append(
                students.map((student, index) => `
                    <tr>
                        <td>${student.firstName}</td>
                        <td>${new Date(student.dob).toLocaleDateString('en-GB')}</td>
                        <td>${student.yearInSchool}</td>
                        <td>
                            <button class="btn btn-warning btn-sm editStudent" data-index="${index}" data-bs-toggle="modal" data-bs-target="#studentModal">Edit</button>
                            <button class="btn btn-danger btn-sm deleteStudent" data-index="${index}">Delete</button>
                        </td>
                    </tr>
                `).join('')
            );
        };

        $('#studentModal').on('show.bs.modal', (e) => {
            const index = $(e.relatedTarget).data('index');
            if (index !== undefined) {
                editIndex = index;
                const student = students[index];
                $('#studentModalLabel').text('Edit Student');
                $('#editIndex').val(index);
                Object.keys(student).forEach(key => $(`[name="${key}"]`).val(student[key]));
                $('input[name="medicalConsent"][value="' + student.medicalConsent + '"]').prop("checked", true);
                $('input[name="photoConsent"]').prop("checked", false);
                student.photoConsent.forEach(val => $(`input[name="photoConsent"][value="${val}"]`).prop("checked", true));
                $('input[name="leaveAlone"][value="' + student.leaveAlone + '"]').prop("checked", true);
            } else {
                editIndex = -1;
                $('#studentModalLabel').text('Add Student');
                $('#studentForm')[0].reset();
                $('#editIndex').val("");
            }
        });

        $('#saveStudent').click(() => {
            const student = {};
            $('#studentForm').serializeArray().forEach(field => student[field.name] = field.value);
            student.photoConsent = $('input[name="photoConsent"]:checked').map((i, el) => el.value).get();
            if (editIndex === -1) {
                students.push(student);
            } else {
                students[editIndex] = student;
            }
            $('#studentModal').modal('hide');
            renderTable();
        });

        $(document).on('click', '.editStudent', (e) => $('#studentModal').modal('show').attr('data-edit-index', $(e.target).data('index')));
        $(document).on('click', '.deleteStudent', (e) => {
            students.splice($(e.target).data('index'), 1);
            renderTable();
        });

        // ===============================================
        // MAIN SUBMIT BUTTON WITH FULL VALIDATION (A to Z)
        // ===============================================
        let isSubmitting = false;
        $('#submitAll').click(() => {
            // Prevent multiple submissions
            if (isSubmitting) {
                return false;
            }

            if (students.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'No Students',
                    text: 'Please add at least one student before submitting.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#B66EDE'
                });
                return;
            }

            // === 1. VALIDATE EACH STUDENT'S REQUIRED FIELDS ===
            const studentRequired = [
                { name: 'firstName', label: 'First Name' },
                { name: 'gender', label: 'Gender' },
                { name: 'yearInSchool', label: 'Year in School' },
                { name: 'medicalConditions', label: 'Any Medical Conditions?' },
                { name: 'allergies', label: 'Any Allergies?' },
                { name: 'additionalNeeds', label: 'Any SEND or Additional Needs?' },
                { name: 'medicalConsent', label: 'Medical Treatment Consent' }
            ];

            const studentErrors = [];
            students.forEach((student, idx) => {
                studentRequired.forEach(field => {
                    let value = student[field.name];
                    if (field.name === 'medicalConsent') {
                        if (!value || value === '') {
                            studentErrors.push(`${field.label} (Student ${idx + 1})`);
                        }
                    } else {
                        if (!value || value.trim() === '') {
                            studentErrors.push(`${field.label} (Student ${idx + 1})`);
                        }
                    }
                });
            });

            // === 2. COLLECT PARENT / EMERGENCY / CONSENT DATA ===
            const parentData = {
                parent1_first_name: $('input[name="parent1_first_name"]').val() || null,
                parent1_last_name: $('input[name="parent1_last_name"]').val() || null,
                parent1_Address: $('input[name="parent1_Address"]').val() || null,
                parent1_Address_line2: $('input[name="parent1_Address_line2"]').val() || null,
                parent1_country_state_region: $('input[name="parent1_country_state_region"]').val() || null,
                parent1_city: $('input[name="parent1_city"]').val() || null,
                parent1_email: $('input[name="parent1_email"]').val() || null,
                parent1_mobile: $('input[name="parent1_mobile"]').val() || null,
                parent1_zipCode: $('input[name="parent1_zipCode"]').val() || null,
                parent1_country: $('select[name="parent1_country"]').val() || null,
                branch: $('select[name="branch"]').val() || null,

                emergency_conatct1_first_name: $('input[name="emergency_conatct1_first_name"]').val() || null,
                emergency_conatct1_last_name: $('input[name="emergency_conatct1_last_name"]').val() || null,
                emergency_conatct1_Address: $('input[name="emergency_conatct1_Address"]').val() || null,
                emergency_conatct1_Address_line2: $('input[name="emergency_conatct1_Address_line2"]').val() || null,
                emergency_conatct1_country_state_region: $('input[name="emergency_conatct1_country_state_region"]').val() || null,
                emergency_conatct1_city: $('input[name="emergency_conatct1_city"]').val() || null,
                emergency_conatct1_email: $('input[name="emergency_conatct1_email"]').val() || null,
                emergency_conatct1_mobile: $('input[name="emergency_conatct1_mobile"]').val() || null,
                emergency_conatct1_zipCode: $('input[name="emergency_conatct1_zipCode"]').val() || null,
                emergency_conatct1_country: $('select[name="emergency_conatct1_country"]').val() || null,

                consent_1date: $('input[name="consent_1date"]').val() || null,
                consent_1signature: $('input[name="consent_1signature"]').val() || null,
                consent_1_checkbox: $('input[name="consent_1_checkbox"]').val() || null,
                consent_1_last_name: $('input[name="consent_1_last_name"]').val() || null,
                consent_1_first_name: $('input[name="consent_1_first_name"]').val() || null,

                how_did_you_hear: $('select[name="how_did_you_hear"]').val() || null,

                term_1: $('input[name="term_1"]').is(':checked') ? $('input[name="term_1"]').data('text') : '',
                term_2: $('input[name="term_2"]').is(':checked') ? $('input[name="term_2"]').data('text') : '',
                term_3: $('input[name="term_3"]').is(':checked') ? $('input[name="term_3"]').data('text') : '',
                term_4: $('input[name="term_4"]').is(':checked') ? $('input[name="term_4"]').data('text') : '',
                term_5: $('input[name="term_5"]').is(':checked') ? $('input[name="term_5"]').data('text') : '',
                term_6: $('input[name="term_6"]').is(':checked') ? $('input[name="term_6"]').data('text') : '',
                term_7: $('input[name="term_7"]').is(':checked') ? $('input[name="term_7"]').data('text') : '',
                term_8: $('input[name="term_8"]').is(':checked') ? $('input[name="term_8"]').data('text') : '',
                term_9: $('input[name="term_9"]').is(':checked') ? $('input[name="term_9"]').data('text') : '',
                term_10: $('input[name="term_10"]').is(':checked') ? $('input[name="term_10"]').data('text') : '',
                term_11: $('input[name="term_11"]').is(':checked') ? $('input[name="term_11"]').data('text') : '',
                term_12: $('input[name="term_12"]').is(':checked') ? $('input[name="term_12"]').data('text') : '',
            };

            // === 3. VALIDATE PARENT REQUIRED FIELDS ===
            const requiredFields = [
                { name: 'branch', label: 'Branch' },
                { name: 'parent1_first_name', label: 'Parent/Guardian 1 First Name' },
                { name: 'parent1_last_name', label: 'Parent/Guardian 1 Last Name' },
                { name: 'parent1_Address', label: 'Parent/Guardian 1 Address' },
                { name: 'parent1_email', label: 'Parent/Guardian 1 Email' },
                { name: 'parent1_mobile', label: 'Parent/Guardian 1 Mobile' },
                { name: 'emergency_conatct1_first_name', label: 'Emergency Contact 1 First Name' },
                { name: 'emergency_conatct1_last_name', label: 'Emergency Contact 1 Last Name' },
                { name: 'emergency_conatct1_Address', label: 'Emergency Contact 1 Address' },
                { name: 'emergency_conatct1_email', label: 'Emergency Contact 1 Email' },
                { name: 'emergency_conatct1_mobile', label: 'Emergency Contact 1 Mobile' },
                { name: 'consent_1_first_name', label: 'Consent First Name' },
                { name: 'consent_1_last_name', label: 'Consent Last Name' },
                { name: 'consent_1_checkbox', label: 'Consent Checkbox' },
                { name: 'consent_1signature', label: 'Signature' },
                { name: 'how_did_you_hear', label: 'How Did You Hear About Us' }
            ];

            const missingFields = [];
            requiredFields.forEach(field => {
                const element = $(`[name="${field.name}"]`);
                const value = parentData[field.name];
                if (!value || value === '' || (element.is('select') && value === '')) {
                    missingFields.push(field.label);
                    element.addClass('invalid').removeClass('valid');
                } else {
                    element.addClass('valid').removeClass('invalid');
                }
            });

            // === 4. VALIDATE TERMS CHECKBOXES ===
            const uncheckedTerms = [];
            for (let i = 1; i <= 12; i++) {
                if (parentData[`term_${i}`] === '') {
                    uncheckedTerms.push(`Term ${i}`);
                }
            }

            // === 5. COMBINE ALL ERRORS ===
            const allErrors = [...missingFields, ...studentErrors, ...uncheckedTerms.map(t => t + ' (Terms)')];

            // === 6. SHOW ERRORS IN 2-COLUMN GRID ===
            if (allErrors.length > 0) {
                const chunks = [];
                for (let i = 0; i < allErrors.length; i += 2) {
                    chunks.push(allErrors.slice(i, i + 2));
                }

                const gridHtml = chunks.map(pair =>
                    `<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 6px; font-size: 0.9rem;">
                        <div>${pair[0] || ''}</div>
                        <div>${pair[1] || ''}</div>
                    </div>`
                ).join('');

                Swal.fire({
                    icon: 'error',
                    title: 'Missing Required Fields or Terms',
                    html: `<div style="max-width: 100%; text-align: left; padding: 0 10px;">${gridHtml}</div>`,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#B66EDE',
                    width: '38em'
                });
                return;
            }

            // === 7. TRANSFORM STUDENT DATA FOR SUBMISSION ===
            const transformedData = {};
            ['firstName', 'middleName', 'lastName', 'dob', 'gender', 'yearInSchool', 'tuitionHours',
                'medicalConditions', 'allergies', 'additionalNeeds', 'gpPrefix', 'gpFirstName',
                'gpLastName', 'gpAddress', 'gpAddressLineTwo', 'CountyStateRegion', 'city', 'zipCode',
                'country', 'GPPhone', 'medicalConsent', 'photoConsent', 'leaveAlone'
            ].forEach(field => transformedData[field] = students.map(student => student[field] || null));

            const finalData = {
                ...transformedData,
                ...parentData,
                _token: '{{ csrf_token() }}'
            };

            // === 8. SUBMIT TO SERVER ===
            isSubmitting = true;
            $('#submitAll').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Submitting...');
            
            Swal.fire({
                title: "Please Wait...",
                text: "Processing your request...",
                icon: "info",
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: "{{ url('/send-student') }}",
                method: 'POST',
                data: finalData,
                success: () => {
                    Swal.fire({
                        title: "Success!",
                        text: "Thank you for submitting the form. Our admissions team will contact you shortly.",
                        icon: "success",
                        confirmButtonText: "OK"
                    }).then(() => {
                        location.reload();
                    });
                },
                error: (xhr, status, error) => {
                    isSubmitting = false;
                    $('#submitAll').prop('disabled', false).html('Submit All');
                    Swal.fire({
                        title: "Error!",
                        text: "Something went wrong: " + error,
                        icon: "error",
                        confirmButtonText: "OK"
                    });
                }
            });
        });

        renderTable();
    });
</script>








</body>

</html>
