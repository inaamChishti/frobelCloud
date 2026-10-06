<style>
        /* Existing styles unchanged */
        .medical-row {
            background-color: #ffd6d6 !important;
        }

        tr.medical-row > td {
            background-color: #ffd6d6 !important;
        }

        tr.medical-row input.form-control,
        tr.medical-row select.form-control,
        tr.medical-row select.form-select {
            background-color: #ffd6d6 !important;
        }

        #calendar-container .form-control[id^="book-"] {
            width: 100%;
            min-width: 85px;
        }

        #calendar-container .dropdown-menu {
            top: auto !important;
        }

        .layout-footer {
            display: none;
        }

        #calendar-container {
            width: 100%;
            margin: 0;
        }

        #calendar-container .container {
            display: flex;
            justify-content: center;
            width: 100%;
        }

        #calendar-container .time-class-info {
            display: flex;
            flex-wrap: wrap;
            padding-bottom: 10px;
            width: 100%;
            gap: 15px;
        }

        #calendar-container .time-class-info>div {
            width: 350px;
            font-size: 14px;
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 5px;
            text-align: left;
            background-color: #CCFFCC;
            color: black;
            position: relative;
            flex: 0 0 calc(33.33% - 15px);
            box-sizing: border-box;
        }

        #calendar-container .time-class-info>div button {
            position: absolute;
            top: 4px;
            background: #FF5722;
            border: none;
            color: white;
            font-size: 12px;
            font-weight: bold;
            z-index: 10;
            outline: none;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0px 3px 4px rgba(0, 0, 0, 0.2);
        }

        #calendar-container .time-class-info>div .edit-btn {
            background: #4CAF50;
            left: 8px;
            top: 4px;
        }

        #calendar-container .time-class-info>div .close-btn {
            background: #FF5722;
            right: 8px;
        }

        #calendar-container .merged-info-container {
            margin-left: -11px;
            margin-top: 12px;
            line-height: 1.6;
            padding: 5px;
            transform: scale(0.75);
            transform-origin: top left;
            width: fit-content;
        }

        #calendar-container .merged-info-container span {
            display: inline-block;
            font-size: 16px;
            margin-right: 8px;
        }

        #calendar-container #time-slots-container {
            display: flex;
            flex-direction: column;
            min-height: 200px;
            padding-bottom: 20px;
            width: 100%;
            gap: 15px;
        }

        #calendar-container .time-slot {
            width: 100%;
            background-color: #007bff;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 5px;
            margin-top: 31px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            overflow-y: auto;
        }

        #calendar-container .section-heading {
            width: 100%;
            background-color: #0056b3;
            color: white;
            padding: 10px;
            text-align: center;
            border-radius: 5px;
            margin-top: 20px;
            font-size: 1.5rem;
            font-weight: bold;
        }

        #calendar-container .navigation-buttons {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            justify-content: space-between;
            width: 200px;
        }

        #calendar-container .navigation-buttons button {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        #calendar-container .navigation-buttons button:hover {
            background-color: #0056b3;
        }

        #calendar-container .form-group.row {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        #calendar-container .form-group.row>div {
            margin-right: 5px;
        }

        #calendar-container .form-group.row .col-md-2,
        #calendar-container .form-group.row .col-md-3,
        #calendar-container .form-group.row .col-md-1 {
            padding: 0 5px;
        }

        #calendar-container .search-container {
            position: relative;
        }

        #calendar-container #search-bar:focus {
            border-color: #007bff;
        }

        #calendar-container .highlight {
            background-color: #730c50 !important;
            transition: background-color 0.3s ease;
        }

        #calendar-container .confirm-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        #calendar-container .confirm-modal-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }

        #calendar-container .confirm-btn,
        #calendar-container .cancel-btn {
            padding: 10px 20px;
            margin: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        #calendar-container .confirm-btn {
            background-color: green;
            color: white;
        }

        #calendar-container .cancel-btn {
            background-color: red;
            color: white;
        }

        #calendar-container #class-modal {
            background-color: #fff;
            border: 3px solid #333;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 20px;
            width: 90%;
            max-width: 800px;
            max-height: 90vh;
            overflow-y: auto;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        #calendar-container #class-modal.show {
            opacity: 1;
        }

        #calendar-container #class-modal .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        #calendar-container #class-modal .modal-header h3 {
            margin: 0;
            font-size: 1.5rem;
            color: #333;
        }

        #calendar-container #class-modal .close-modal {
            background: transparent;
            border: none;
            font-size: 1.5rem;
            color: #333;
            cursor: pointer;
        }

        #calendar-container #class-modal .form-group {
            margin-bottom: 15px;
        }

        #calendar-container #class-modal .form-group label {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
            color: #333;
        }

        #calendar-container #class-modal .form-control,
        #calendar-container #class-modal select.form-control {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        #calendar-container #class-modal .form-control:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
        }

        #calendar-container #class-modal .table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        #calendar-container #class-modal .table th,
        #calendar-container #class-modal .table td {
            padding: 10px;
            text-align: left;
            border: 1px solid #dee2e6;
        }

        #calendar-container #class-modal .table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        #calendar-container #class-modal .table td input[type="checkbox"]+label span {
            width: 20px;
            height: 20px;
            border: 2px solid #ccc;
            border-radius: 4px;
            display: inline-block;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        #calendar-container #class-modal .btn-success {
            background-color: #28a745;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 16px;
            width: 100%;
            margin-top: 20px;
            transition: background-color 0.3s ease;
        }

        #calendar-container #class-modal .btn-success:hover {
            background-color: #218838;
        }

        /* Term break modal styles */
        #calendar-container #term-break-modal {
            background-color: #fff;
            border: 3px solid #333;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 20px;
            width: 90%;
            max-width: 500px;
            max-height: 58vh;
            overflow-y: auto;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        #calendar-container #term-break-modal.show {
            opacity: 1;
        }

        #calendar-container #term-break-modal .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        #calendar-container #term-break-modal .modal-header h3 {
            margin: 0;
            font-size: 1.5rem;
            color: #333;
        }

        #calendar-container #term-break-modal .form-group {
            margin-bottom: 15px;
        }

        #calendar-container #term-break-modal .form-group label {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
            color: #333;
        }

        #calendar-container #term-break-modal .form-control {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        #calendar-container #term-break-modal .form-control:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
        }

        #calendar-container #term-break-modal .btn-success {
            background-color: #28a745;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 16px;
            width: 100%;
            margin-top: 20px;
            transition: background-color 0.3s ease;
        }

        #calendar-container #term-break-modal .btn-success:hover {
            background-color: #218838;
        }

        /* New styles for term break button */
        #calendar-container .term-break-container {
            display: flex;
            justify-content: center;
            margin: 15px 0;
        }

        #calendar-container #add-term-break {
            background-color: #6f42c1;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        #calendar-container #add-term-break:hover {
            background-color: #5a32a3;
            transform: scale(1.05);
        }

        #calendar-container #add-term-break:active {
            transform: scale(0.95);
        }

        /* Select2 styles */
        .select2-container--default .select2-selection--single {
            height: 38px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px;
        }

        .select2-container--default .select2-selection--single:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
        }

        /* Custom Medical Popup - Professional Modern Design */
        .medical-custom-popup {
            position: fixed;
            z-index: 10000;
            display: none;
            pointer-events: none;
        }

        .medical-popup-container {
            font-size: 14px;
            border: 1px solid #e0e0e0;
            padding: 0;
            border-radius: 8px;
            width: 380px;
            text-align: left;
            background: #fafafa;
            color: #4a4a4a;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
            pointer-events: auto;
            overflow: hidden;
        }

        .medical-popup-section {
            display: block !important;
            width: 100% !important;
            margin-bottom: 0 !important;
            background-color: #fafafa;
            clear: both !important;
        }

        .medical-popup-section:last-child {
            margin-bottom: 0 !important;
        }

        .medical-popup-header {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            font-weight: 600;
            padding: 14px 18px;
            margin: 0;
            text-transform: none;
            letter-spacing: 0;
            border-bottom: 1px solid;
        }

        .medical-popup-header i {
            font-size: 16px;
            width: 22px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .medical-conditions-header {
            background: #f5f5f5;
            color: #6b6b6b !important;
            border-bottom-color: #e8e8e8;
        }

        .medical-conditions-header i {
            color: #999999 !important;
        }

        .allergies-header {
            background: #f5f5f5;
            color: #6b6b6b !important;
            border-bottom-color: #e8e8e8;
        }

        .allergies-header i {
            color: #999999 !important;
        }

        .medical-popup-row {
            display: block !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            background-color: #ffffff;
            transition: background-color 0.2s ease;
        }

        .medical-popup-row:hover {
            background-color: #f9f9f9;
        }

        .medical-conditions-section {
            display: block !important;
            margin-bottom: 0 !important;
            clear: both !important;
            border-bottom: 1px solid #e8e8e8;
        }

        .allergies-section {
            display: block !important;
            margin-top: 0 !important;
            margin-bottom: 0 !important;
            clear: both !important;
        }

        .medical-popup-content {
            display: block !important;
            font-weight: normal;
            font-size: 14px;
            padding: 14px 18px;
            border: none;
            text-align: left;
            white-space: normal !important;
            color: #555555 !important;
            background-color: #ffffff !important;
            word-wrap: break-word;
            word-break: break-word;
            width: 100% !important;
            box-sizing: border-box;
            line-height: 1.7;
            border-left: 3px solid;
        }

        .medical-conditions-content {
            border-left-color: #d0d0d0 !important;
            background: #ffffff !important;
        }

        .allergies-content {
            border-left-color: #d0d0d0 !important;
            background: #ffffff !important;
        }

        .medical-popup-content i {
            font-size: 14px;
            margin-right: 8px;
            display: inline-block;
        }

        .medical-popup-divider {
            display: block !important;
            margin: 0 !important;
            padding: 0 !important;
            border: 0 !important;
            border-top: 1px solid #e9ecef !important;
            background: transparent !important;
            height: 1px !important;
            width: 100% !important;
            clear: both !important;
        }

        /* Responsive adjustments for popup */
        @media (max-width: 768px) {
            .medical-popup-container {
                width: 90vw;
                max-width: 380px;
                min-width: 300px;
            }

            .medical-popup-header {
                padding: 14px 18px;
                font-size: 14px;
            }

            .medical-popup-header i {
                font-size: 16px;
                width: 22px;
            }

            .medical-popup-content {
                font-size: 13px;
                padding: 14px 18px;
                line-height: 1.6;
            }
        }

        #calendar-container #class-modal [id^="medical-icon-"] {
            transition: transform 0.2s ease, color 0.2s ease;
        }

        #calendar-container #class-modal [id^="medical-icon-"]:hover {
            transform: scale(1.2);
            color: #c82333 !important;
        }

        @media (max-width: 576px) {
            #calendar-container #class-modal,
            #calendar-container #term-break-modal {
                width: 95%;
                padding: 15px;
            }

            #calendar-container #class-modal .table th,
            #calendar-container #class-modal .table td {
                padding: 8px;
                font-size: 12px;
            }

            #calendar-container #add-term-break {
                padding: 10px 20px;
                font-size: 14px;
            }
        }
    </style>
