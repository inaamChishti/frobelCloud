<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <title>Receipt</title>

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
            <h1 style="margin-top: 5px;">RECEIPT (Office Copy)</h1>
            <hr>
            <div class="logo-container" style="margin-top: 70px;">
                <div class="logo">
                    {{-- <img src="https://frobelschoolsystemnew.frobel.co.uk/img/header.jpg" width="200px" height="60px"
                        alt="Frobel Logo"> --}}
                        {{-- <h3>Frobel Learning</h3> --}}
                </div>
            </div>
        </div>
        <div class="content-container"  style="margin-top: 20px;">
           <h5 style="margin: 3px; display: inline-block;">&nbsp; Date: <u><span id="date_f" style="margin: 10px;">{{ $date }}</span></u></h5>
 <span style="display: inline-block;">Receipt No: <u><span id="receipt_p" style="margin: 10px;">{{$random}}</span></u></span></h5>
    <h5 style="margin-top: 10px;margin: 10px;">Received From: <u><span id="received_from" style="margin: 10px;">{{$receivedfrom}}</span></u></h5>
    <h5 style="margin-top: 10px;margin: 10px;">Amount: <u><span id="amount_f" style="margin: 10px;">{{$paid}}</span></u></h5>
    <h5 style="margin-top: 10px;margin: 10px;">From: <u><span id="p_from_p" style="margin: 10px;">{{$from}}</span></u> <span style="margin: 10px;">To: <u><span id="p_to_p" style="margin: 10px;">{{$to}}</span></u></span></h5>
    <h5 style="margin-top: 10px;">&nbsp;&nbsp;Received By: &nbsp; &nbsp;{{$receivedto}}</h5>
    <h5 style="margin-top: 10px;margin: 10px;">
        <span>Cash:- <u><span id="cash_p" style="margin: 10px;"> {{@$cash}}</span></u></span><span style="margin: 10px;">Bank:- <u><span id="bank_p" style="margin: 10px;">{{@$bank}}</span></u></span><span style="margin: 10px;">Adjustment:- <u><span
                id="adjustment_p" style="margin: 10px;">
                {{@$adjustment}}
            </span></u></span>
        </h5>
                <table>
                    <tr>
                        <td class="td_t">Fees Amount</td>
                        <td><span id="package_p_s">{{$feeAmount}}</span></td>
                    </tr>
                    <tr>
                        <td class="td_t">This Payment</td>
                        <td><span id="payment_p_s">{{$thisPayment}}</span></td>
                    </tr>
                    <tr>
                        <td class="td_t">Balance Due</td>
                        <td><span id="balance_p_s">{{$thisbalance}}</span></td>
                    </tr>
                </table>
        </div>
        <hr style="margin-top: 40px;">
        <div class="section">
            <h1>RECEIPT (Student Copy)</h1>
            <hr>
            <div class="logo-container" style="margin-top: 70px;">
                <div class="logo">
                    {{-- <img src="https://frobelschoolsystemnew.frobel.co.uk/img/header.jpg" width="200px" height="60px"
                        alt="Frobel Logo"> --}}

                </div>
            </div>

        </div>
        <div class="content-container" style="margin-top: 20px;">
            <h5 style="margin: 3px; display: inline-block;">&nbsp; Date: <u><span id="date_f" style="margin: 10px;">{{ $date }}</span></u></h5>
 <span style="display: inline-block;">Receipt No: <u><span id="receipt_p" style="margin: 10px;">{{$random}}</span></u></span></h5>
    <h5 style="margin-top: 10px;margin: 10px;">Received From: <u><span id="received_from" style="margin: 10px;">{{$receivedfrom}}</span></u></h5>
    <h5 style="margin-top: 10px;margin: 10px;">Amount: <u><span id="amount_f" style="margin: 10px;">{{$paid}}</span></u></h5>
    <h5 style="margin-top: 10px;margin: 10px;">From: <u><span id="p_from_p" style="margin: 10px;">{{$from}}</span></u> <span style="margin: 10px;">To: <u><span id="p_to_p" style="margin: 10px;">{{$to}}</span></u></span></h5>
    <h5 style="margin-top: 10px;">&nbsp;&nbsp;Received By: &nbsp; &nbsp;{{$receivedto}}</h5>
    <h5 style="margin-top: 10px;margin: 10px;">
        <span>Cash:- <u><span id="cash_p" style="margin: 10px;"> {{@$cash}}</span></u></span><span style="margin: 10px;">Bank:- <u><span id="bank_p" style="margin: 10px;">{{@$bank}}</span></u></span><span style="margin: 10px;">Adjustment:- <u><span
                id="adjustment_p" style="margin: 10px;">
                {{@$adjustment}}
            </span></u></span>
        </h5>
            <table>
                <tr>
                    <td class="td_t">Fees Amount</td>
                    <td><span id="package_p_s">{{$feeAmount}}</span></td>
                </tr>
                <tr>
                    <td class="td_t">This Payment</td>
                    <td><span id="payment_p_s">{{$thisPayment}}</span></td>
                </tr>
                <tr>
                    <td class="td_t">Balance Due</td>
                    <td><span id="balance_p_s">{{$thisbalance}}</span></td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>
