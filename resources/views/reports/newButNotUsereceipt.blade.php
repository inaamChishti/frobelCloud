<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8" />
  <title>Receipt</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 10px;
      font-size: 10px;
    }

    .receipt-section {
      border-bottom: 1px dashed #000;
      padding-bottom: 15px;
      margin-bottom: 15px;
    }

    .header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 5px;
    }

    .header-left img {
      height: 35px;
      width: 70px;
      vertical-align: middle;
    }

    .header-left p {
      margin: 2px 0;
      font-size: 7px;
      font-style: italic;
    }

    .header-right {
      text-align: right;
      margin-top: -5px;
    }

    .header-right h2 {
      margin: 0;
      font-size: 14px;
      font-weight: bold;
    }

    .header-right p {
      margin: 2px 0;
      font-size: 10px;
    }

    .student-details {
      margin-top: 5px;
      padding: 2px 0;
      width: 100%;
    }

    .student-details h4 {
      margin: 0 0 5px 0;
      text-decoration: underline;
      font-size: 12px;
    }

    .student-details p {
      margin: 2px 0;
      font-size: 10px;
    }

    .main-grid {
      display: grid;
      grid-template-columns: 1fr 1fr 1fr;
      margin-top: 10px;
      align-items: start;
    }

    .payment-method {
      padding: 5px;
      width: 80%;
      margin-bottom: 10px;
    }

    .payment-method h4 {
      margin: 0 0 5px 0;
      text-decoration: underline;
      font-size: 12px;
    }

    .payment-method span {
      display: inline;
      margin-right: 8px;
    }

    .fees-table {
      border-collapse: collapse;
      width: 60%;
      margin: 0 auto;
      font-size: 10px;
    }

    .fees-table td {
      border: 1px solid #ddd;
      padding: 4px;
    }

    .fees-table td:first-child {
      width: 60%;
    }

    .center-table {
      text-align: center;
    }

    .footer {
      text-align: center;
      margin-top: 10px;
    }

    .footer img {
      height: 70px;
      width: 140px;
      vertical-align: middle;
    }

    @media print {
      img {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
      }
    }
  </style>
</head>

<body>

  <!-- Office Copy -->
  <div class="receipt-section">
    <div class="header">
      <div class="header-left">
        <img src="https://frobelcloud.efrobel.com/FrobelEducationWhite.png" alt="Logo">
        <p><b>Formulas for Success</b></p>
      </div>
      <div class="header-right" style="margin-top: -6%;">
        <h2>RECEIPT (Office Copy)</h2>
        <p></p>
        <p></p>
        <p></p>
        <p></p>
        <p></p>
        <p></p>
        <p><strong>Receipt No:</strong> {{ $random }}</p>
        <p><strong>Date:</strong> {{ $date }}</p>
      </div>
    </div>

    <div class="student-details">
      <h4>Student Details</h4>
      <p><strong>Family ID:</strong> {{ $family_id }}</p>
      <p><strong>Student Name:</strong> {{ $student_names }}</p>
      <p><strong>Student Address:</strong> {{ $addressOfGuardian }}</p>
      <p><strong>City, County/Region, Postcode:</strong> {{ $city }}, {{ $state }}, {{ $postcode }}</p>
      <p><strong>Phone Number:</strong> {{ $mob }}</p>
    </div>

    <div class="main-grid">
      <div class="payment-method">
        <h4>Payment Method</h4>
        <span>Cash: <u>{{ @$cash }}</u></span>
        <span>Bank: <u>{{ @$bank }}</u></span>
        <span>Adjustment: <u>{{ @$adjustment }}</u></span>
      </div>

      <div class="center-table">
        <table class="fees-table">
          <tr>
            <td>Description</td>
            <td>Amount £</td>
          </tr>
          <tr>
            <td>Package Details</td>
            <td>{{ $feeAmount }}</td>
          </tr>
          <tr>
            <td>This Payment</td>
            <td>{{ $thisPayment }}</td>
          </tr>
          <tr>
            <td>Balance Due</td>
            <td>{{ $thisbalance }}</td>
          </tr>
        </table>
      </div>

      <div></div>
    </div>

    <div class="footer">
      <img src="https://frobelcloud.efrobel.com/contents.png" alt="Footer">
    </div>
  </div>

  <!-- Student Copy -->
  <div class="receipt-section">
    <div class="header">
      <div class="header-left" >
        <img src="https://frobelcloud.efrobel.com/FrobelEducationWhite.png" alt="Logo">
        <p><b>Formulas for Success</b></p>
      </div>
      <div class="header-right" style="margin-top:-4%;">
        <h2>RECEIPT (Student Copy)</h2>
        <p></p>
        <p></p>
        <p></p>
        <p></p>
        <p></p>
        <p></p>
        <p><strong>Receipt No:</strong> {{ $random }}</p>
        <p><strong>Date:</strong> {{ $date }}</p>
      </div>
    </div>

    <div class="student-details">
      <h4>Student Details</h4>
      <p><strong>Family ID:</strong> {{ $family_id }}</p>
      <p><strong>Student Name:</strong> {{ $student_names }}</p>
      <p><strong>Student Address:</strong> {{ $addressOfGuardian }}</p>
      <p><strong>City, County/Region, Postcode:</strong> {{ $city }}, {{ $state }}, {{ $postcode }}</p>
      <p><strong>Phone Number:</strong> {{ $mob }}</p>
    </div>

    <div class="main-grid">
      <div class="payment-method">
        <h4>Payment Method</h4>
        <span>Cash: <u>{{ @$cash }}</u></span>
        <span>Bank: <u>{{ @$bank }}</u></span>
        <span>Adjustment: <u>{{ @$adjustment }}</u></span>
      </div>

      <div class="center-table">
        <table class="fees-table">
          <tr>
            <td>Description</td>
            <td>Amount £</td>
          </tr>
          <tr>
            <td>Package Details</td>
            <td>{{ $feeAmount }}</td>
          </tr>
          <tr>
            <td>This Payment</td>
            <td>{{ $thisPayment }}</td>
          </tr>
          <tr>
            <td>Balance Due</td>
            <td>{{ $thisbalance }}</td>
          </tr>
        </table>
      </div>

      <div></div>
    </div>

    <div class="footer">
      <img src="https://frobelcloud.efrobel.com/contents.png" alt="Footer">
    </div>
  </div>

</body>
</html>
