@extends('layouts.branchDashboardApp')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/libs/datatables/datatables.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0/css/bootstrap-select.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1>Books Record</h1>
                <p>View and manage all books inventory and transactions.</p>
            </div>
        </div>

        <!-- Books Table -->
        <div class="table-responsive mb-5 books-table-container">
            <table id="booksTable" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Opening Balance</th>
                        <th>Book Name</th>
                        <th>Book Code</th>
                        <th>Sold</th>
                        <th>Purchased</th>
                        <th>Available Books Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($books as $book)
                        <tr>
                            <td>{{ $book->opening_balance ?? '0' }}</td>
                            <td>{{ $book->name ?? 'N/A' }}</td>
                            <td>{{ $book->code ?? 'N/A' }}</td>
                            <td>{{ $book->sold ?? '0' }}</td>
                            <td>{{ $book->purchased ?? '0' }}</td>
                            <td>{{ $book->balance ?? '0' }}</td> <!-- Use balance instead of quantity -->
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Sales and Purchases Tables -->
        <div class="row mt-4">
            <div class="col-md-6 mb-5">
                <div class="table-responsive sales-table-container">
                    <div class="d-flex justify-content-center mb-3"> <!-- Changed to justify-content-center -->
                        <h3>Sales Record</h3>
                    </div>
                    <table id="salesTable" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Book Name</th>
                                <th>Quantity</th>
                                <th>Amount Received</th>
                                <th>Receipt</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sales as $sale)
                                @php
                                    $bookName = $books->where('id', $sale->book_id)->first()->name ?? 'Unknown';
                                @endphp
                                <tr>
                                    <td>{{ $bookName }}</td>
                                    <td>{{ $sale->quantity ?? '0' }}</td>
                                    <td>{{ $sale->amount_received ?? '0' }}</td>
                                    <td>
                                        @if($sale->receipt_path)
                                            <a href="{{ asset($sale->receipt_path) }}" target="_blank" class="text-primary">View</a>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($sale->created_at)->diffForHumans() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $sales->links() }}
                </div>
            </div>
            <div class="col-md-6 mb-5">
                <div class="table-responsive purchases-table-container">
                    <div class="d-flex justify-content-center mb-3"> <!-- Changed to justify-content-center -->
                        <h3>Purchases Record</h3>
                    </div>
                    <table id="purchasesTable" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Book Name</th>
                                <th>Quantity</th>
                                <th>Amount Paid</th>
                                <th>Receipt</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchases as $purchase)
                                @php
                                    $bookName = $books->where('id', $purchase->book_id)->first()->name ?? 'Unknown';
                                @endphp
                                <tr>
                                    <td>{{ $bookName }}</td>
                                    <td>{{ $purchase->quantity ?? '0' }}</td>
                                    <td>{{ $purchase->amount_paid ?? '0' }}</td>
                                    <td>
                                        @if($purchase->payment_receipt)
                                            <a href="{{ asset($purchase->payment_receipt) }}" target="_blank" class="text-primary">View</a>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($purchase->created_at)->diffForHumans() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $purchases->links() }}
                </div>
            </div>
        </div>
    </div>

    <style>
        .main-content {
            background: transparent;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.6s ease-in-out;
            border: 1px solid #2044A2;
        }

        .main-content h1 {
            font-size: 32px;
            font-weight: 700;
            color: #2044A2;
            margin-bottom: 15px;
        }

        .main-content h3 {
            font-size: 24px;
            font-weight: 600;
            color: #2044A2;
            text-align: center; /* Added to center the headings */
        }

        .main-content p {
            font-size: 18px;
            color: #2044A2;
            margin-bottom: 20px;
        }

        /* Table Containers */
        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 20px;
            padding: 10px;
        }

        /* Books Table Border */
        .books-table-container {
            border: 3px solid #2044A2; /* Blue border for Books table */
            box-shadow: 0 4px 12px rgba(103, 192, 234, 0.3);
        }

        /* Sales Table Border */
        .sales-table-container {
            border: 3px solid #2044A2; /* Green border for Sales table */
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
        }

        /* Purchases Table Border */
        .purchases-table-container {
            border: 3px solid #2044A2; /* Red border for Purchases table */
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
        }

        .table.table-striped.table-hover {
            --bs-table-bg: transparent;
            --bs-table-color: #2044A2;
            --bs-table-striped-bg: rgba(103, 192, 234, 0.1);
            --bs-table-hover-bg: rgba(103, 192, 234, 0.2);
            --bs-table-hover-color: #ffffff;
            color: var(--bs-table-color);
            background: var(--bs-table-bg);
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 0;
            width: 100%;
        }

        .table.table-striped.table-hover thead th {
            background: transparent;
            color: #2044A2;
            border-bottom: 2px solid #2044A2;
            font-weight: 600;
            padding: 8px; /* Decreased padding */
            text-align: left;
            font-size: 14px; /* Decreased font size */
        }

        .table.table-striped.table-hover tbody {
            background: transparent;
        }

        .table.table-striped.table-hover tbody tr {
            transition: all 0.3s ease;
        }

        .table.table-striped.table-hover tbody td {
            vertical-align: middle;
            border-color: #2044A2;
            padding: 8px; /* Decreased padding */
            color: #2044A2;
            font-size: 13px; /* Decreased font size */
            font-weight: 500;
        }

        .table.table-striped.table-hover tbody tr:hover {
            background: #2044A2 !important;
            transform: translateX(5px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .table.table-striped.table-hover tbody tr:hover td {
            color: #ffffff !important;
        }

        .table.table-striped.table-hover tbody tr:hover a {
            color: #ffffff !important;
        }

        .table.table-striped>tbody>tr:nth-of-type(odd)>* {
            --bs-table-color-type: #2044A2 !important;
            --bs-table-bg-type: rgba(103, 192, 234, 0.1) !important;
            color: var(--bs-table-color-type) !important;
            background-color: var(--bs-table-bg-type) !important;
        }

        .text-primary {
            color: #2044A2 !important;
        }

        .pagination .page-item .page-link {
            color: #2044A2;
            background-color: transparent;
            border: 1px solid #2044A2;
        }

        .pagination .page-item.active .page-link {
            background-color: #2044A2;
            border-color: #2044A2;
            color: white;
        }

        .pagination .page-item.disabled .page-link {
            color: #6c757d;
            background-color: transparent;
            border-color: #2044A2;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @media (max-width: 768px) {
            .table.table-striped.table-hover thead th,
            .table.table-striped.table-hover tbody td {
                font-size: 12px;
                padding: 6px;
            }

            .main-content h1 {
                font-size: 24px;
            }

            .main-content h3 {
                font-size: 20px;
            }

            .main-content p {
                font-size: 16px;
            }

            .row {
                flex-direction: column;
            }

            .col-md-6 {
                width: 100%;
            }
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('assets/libs/datatables/datatables.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0/dist/js/bootstrap-select.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#booksTable').DataTable({
                processing: false, // Set to false since data is already loaded
                serverSide: false,
                paging: false,
                searching: false,
                info: false,
                ordering: false
            });

            $('#salesTable').DataTable({
                processing: false,
                serverSide: false, // Set to false since data is paginated server-side
                searching: false,
                info: false,
                pageLength: 10, // Match the server-side pagination limit
                order: [[4, 'desc']]
            });

            $('#purchasesTable').DataTable({
                processing: false,
                serverSide: false,
                searching: false,
                info: false,
                pageLength: 10, // Match the server-side pagination limit
                order: [[4, 'desc']]
            });
        });
    </script>
@endsection
