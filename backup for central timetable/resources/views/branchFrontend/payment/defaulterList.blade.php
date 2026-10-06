@extends('layouts.branchDashboardApp')

@section('content')
    <!-- Include External CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <div class="registration-container scroll-smooth">
        <div class="container">
            <!-- Header -->
            <div class="text-center mb-12">
                <h1 class="text-3xl font-bold text-[var(--primary-dark)] mb-3 tracking-tight sm:text-4xl">Defaulter List</h1>
            </div>

            <!-- Defaulter Records Table -->
            <div class="card form-section">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <div class="bg-[var(--primary)] p-2 rounded-lg mr-3 section-icon">
                            <i class="fas fa-table h-5 w-5 text-white text-lg"></i>
                        </div>
                        <h2 class="text-xl font-semibold text-[var(--primary-dark)] sm:text-2xl">Defaulter Records</h2>
                    </div>
                    <button type="button" class="btn-primary flex items-center" onclick="exportAll()">
                        <i class="fas fa-file-export mr-2"></i> Export All
                    </button>
                </div>
                <div class="table-responsive">
                    <span id="validationError" class="text-center text-[var(--error)] error_messages mb-3 d-block"></span>
                    <table id="defaulterTable" class="table w-full">
                        <thead>
                            <tr>
                                <th class="col-id">#</th>
                                <th class="col-name">Payment Family ID</th>
                                <th class="col-date">Last Payment Date</th>
                                <th class="col-date">Payment Expiry Date</th>
                                <th class="col-balance">Balance</th>
                                <th class="col-actions">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $isSuperAdmin = auth()->user()->email === 'superadmin@frobel.com';
                                $cutoffDate = \Carbon\Carbon::now()->subMonths(4);
                            @endphp
                            @foreach ($paymentRecords as $key => $record)
                                @php
                                    $lastPayDate = \Carbon\Carbon::parse($record->last_payment_date);
                                    $isOld = $lastPayDate->lt($cutoffDate);
                                @endphp
                                @if (!$isOld || $isSuperAdmin)
                                <tr>
                                    <td class="col-id">{{ $key + 1 }}</td>
                                    <td class="col-name">{{ $record->paymentfamilyid }}</td>
                                    <td class="col-date">{{ $record->last_payment_date }}</td>
                                    <td class="col-date">
    {{ \Carbon\Carbon::parse($record->payment_expiry_date)->format('d F Y') }}
</td>

                                    <td class="col-balance">{{ $record->balance }}</td>
                                    <td class="col-actions">
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn-primary btn-sm" onclick="exportRecord(this, {{ json_encode($record) }})">
                                                <i class="fas fa-file-export"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

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
            width: 8%;
            text-align: center;
        }

        .registration-container .table th.col-name,
        .registration-container .table td.col-name {
            width: 25%;
            white-space: normal;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .registration-container .table th.col-date,
        .registration-container .table td.col-date {
            width: 20%;
        }

        .registration-container .table th.col-balance,
        .registration-container .table td.col-balance {
            width: 15%;
        }

        .registration-container .table th.col-actions,
        .registration-container .table td.col-actions {
            width: 12%;
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

        .registration-container .error_messages {
            font-size: 1rem;
            font-weight: 500;
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
                width: 10%;
            }
            .registration-container .table th.col-name,
            .registration-container .table td.col-name {
                width: 20%;
            }
            .registration-container .table th.col-date,
            .registration-container .table td.col-date {
                width: 25%;
            }
            .registration-container .table th.col-balance,
            .registration-container .table td.col-balance {
                width: 20%;
            }
            .registration-container .table th.col-actions,
            .registration-container .table td.col-actions {
                width: 15%;
            }
        }
    </style>

    <script>
  $(document).ready(function() {
    $('#defaulterTable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'copy',
                text: '<i class="fas fa-copy"></i> Copy',
                className: 'btn btn-sm btn-primary',
                title: 'Defaulter List',
                exportOptions: { columns: ':not(:last-child)' }
            },
            {
                extend: 'csv',
                text: '<i class="fas fa-file-csv"></i> CSV',
                className: 'btn btn-sm btn-primary',
                title: 'Defaulter List',
                exportOptions: { columns: ':not(:last-child)' }
            },
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-sm btn-primary',
                title: 'Defaulter List',
                exportOptions: { columns: ':not(:last-child)' }
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'btn btn-sm btn-primary',
                title: 'Defaulter List',
                exportOptions: { columns: ':not(:last-child)' }
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Print',
                className: 'btn btn-sm btn-primary',
                title: 'Defaulter List',
                exportOptions: { columns: ':not(:last-child)' }
            }
        ],
        paging: false, // Disable pagination
        responsive: true,
        columnDefs: [{ targets: -1, orderable: false }],
        language: {
            emptyTable: "No defaulter records available"
        }
    });
});

        function exportRecord(button, record) {
            const row = button.closest('tr');
            const headerValues = Array.from(row.parentNode.previousElementSibling.querySelectorAll('th'))
                .map(header => header.textContent)
                .slice(0, -1); // Exclude Actions column
            const rowValues = Array.from(row.children)
                .map(cell => cell.textContent)
                .slice(0, -1); // Exclude Actions column
            const allValues = [headerValues, rowValues];

            const worksheet = XLSX.utils.aoa_to_sheet(allValues);
            const workbook = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(workbook, worksheet, 'Sheet 1');
            XLSX.writeFile(workbook, `defaulter_${record.paymentfamilyid}.xlsx`);
        }

        function exportAll() {
            const rows = Array.from(document.querySelectorAll('#defaulterTable tbody tr'));
            const headerValues = Array.from(document.querySelectorAll('#defaulterTable thead th'))
                .map(header => header.textContent)
                .slice(0, -1); // Exclude Actions column
            const allData = [headerValues];

            rows.forEach(row => {
                const rowValues = Array.from(row.children)
                    .map(cell => cell.textContent)
                    .slice(0, -1); // Exclude Actions column
                allData.push(rowValues);
            });

            const worksheet = XLSX.utils.aoa_to_sheet(allData);
            const workbook = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(workbook, worksheet, 'Sheet 1');
            XLSX.writeFile(workbook, 'defaulter_list_all.xlsx');
        }
    </script>
@endsection
