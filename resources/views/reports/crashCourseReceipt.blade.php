<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <title>Crash Course Payment Receipt</title>

    <style>
        body {
            font-family: Arial, sans-serif;
        }

        #printableArea {
            margin: 20px;
        }

        .section {
            display: inline-block;
            width: 100%;
            text-align: center;
        }

        .logo-container {
            margin: 10px 0;
        }

        img.logo {
            height: 70px;
        }

        hr {
            margin: 0;
            padding: 0;
            border-top: 1px dashed;
        }

        .content-container {
            margin-left: 20px;
        }

        h1 {
            margin-bottom: -60px;
        }

        h3 {
            padding-bottom: 10px;
        }

        h5 span {
            margin-left: 10%;
        }

        h5 u {
            text-decoration: underline;
        }

        h5 {
            margin: 0;
        }

        table {
            width: 100%;
            margin-top: 10px;
            border-collapse: collapse;
        }

        td {
            padding: 5px;
            border: 1px solid #ddd;
        }

        .td_t {
            width: 70%;
        }
    </style>
</head>

<body>
    <div id="printableArea" class="printSection">
        <div class="section">
            <h1 style="margin-top: 5px;">CRASH COURSE PAYMENT RECEIPT (Office Copy)</h1>
            <div class="logo-container" style="margin-top: 70px;">
                <div class="logo">
                    {{-- <img src="https://frobelschoolsystemnew.frobel.co.uk/img/header.jpg" width="200px" height="60px"
                        alt="Frobel Logo"> --}}
                    {{-- <h3>Frobel Learning</h3> --}}
                </div>
            </div>
        </div>
        <div class="content-container" style="margin-top: 20px;">
            <h5 style="margin: 3px; display: inline-block;">&nbsp; Date: <u><span id="date_f" style="margin: 10px;">{{ $date }}</span></u></h5>
            <span style="display: inline-block;">Receipt No: <u><span id="receipt_p" style="margin: 10px;">{{$receipt_no}}</span></u></span></h5>
            <h5 style="margin-top: 10px;margin: 10px;">Candidate Name: <u><span id="candidate_name" style="margin: 10px;">{{$candidate_name}}</span></u></h5>
            <h5 style="margin-top: 10px;margin: 10px;">Family ID: <u><span id="family_id" style="margin: 10px;">{{$family_id}}</span></u></h5>
            <h5 style="margin-top: 10px;margin: 10px;">Package Name: <u><span id="package_name" style="margin: 10px;">{{$package_name}}</span></u></h5>
            <h5 style="margin-top: 10px;margin: 10px;">Amount Paid: <u><span id="amount_paid" style="margin: 10px;">{{$amount_paid}}</span></u></h5>
            <h5 style="margin-top: 10px;margin: 10px;">Amount in Words: <u><span id="amount_words" style="margin: 10px;">{{$amount_in_words}}</span></u></h5>
            <h5 style="margin-top: 10px;margin: 10px;">Payment Method: <u><span id="payment_method" style="margin: 10px;">{{$payment_method}}</span></u></h5>
            <h5 style="margin-top: 10px;">&nbsp;&nbsp;Received By: &nbsp; &nbsp;{{$received_by}}</h5>
        </div>
        <div class="section" style="margin-top: 40px;">
            <h1>CRASH COURSE PAYMENT RECEIPT (Student Copy)</h1>
            <div class="logo-container" style="margin-top: 70px;">
                <div class="logo">
                    {{-- <img src="https://frobelschoolsystemnew.frobel.co.uk/img/header.jpg" width="200px" height="60px"
                        alt="Frobel Logo"> --}}
                </div>
            </div>
        </div>
        <div class="content-container" style="margin-top: 20px;">
            <h5 style="margin: 3px; display: inline-block;">&nbsp; Date: <u><span id="date_f" style="margin: 10px;">{{ $date }}</span></u></h5>
            <span style="display: inline-block;">Receipt No: <u><span id="receipt_p" style="margin: 10px;">{{$receipt_no}}</span></u></span></h5>
            <h5 style="margin-top: 10px;margin: 10px;">Candidate Name: <u><span id="candidate_name" style="margin: 10px;">{{$candidate_name}}</span></u></h5>
            <h5 style="margin-top: 10px;margin: 10px;">Family ID: <u><span id="family_id" style="margin: 10px;">{{$family_id}}</span></u></h5>
            <h5 style="margin-top: 10px;margin: 10px;">Package Name: <u><span id="package_name" style="margin: 10px;">{{$package_name}}</span></u></h5>
            <h5 style="margin-top: 10px;margin: 10px;">Amount Paid: <u><span id="amount_paid" style="margin: 10px;">{{$amount_paid}}</span></u></h5>
            <h5 style="margin-top: 10px;margin: 10px;">Amount in Words: <u><span id="amount_words" style="margin: 10px;">{{$amount_in_words}}</span></u></h5>
            <h5 style="margin-top: 10px;margin: 10px;">Payment Method: <u><span id="payment_method" style="margin: 10px;">{{$payment_method}}</span></u></h5>
            <h5 style="margin-top: 10px;">&nbsp;&nbsp;Received By: &nbsp; &nbsp;{{$received_by}}</h5>
        </div>
    </div>
</body>

</html>

