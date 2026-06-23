<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee Voucher - #{{ $payment->receipt_no ?? 'Receipt' }}</title>
    <style>
        @import url('https://googleapis.com');
        
        body {
            font-family: 'Inter', -apple-system, sans-serif;
            margin: 0;
            padding: 8px;
            background-color: #ffffff;
            color: #000000;
            font-size: 10px;
            line-height: 1.3;
        }

        /* 3-COLUMN SIDE-BY-SIDE GRID SLIPS */
        .voucher-container {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 12px;
            width: 100%;
            max-width: 1300px;
            margin: 0 auto;
        }

        .voucher-slip {
            border: 1px solid #000000;
            padding: 10px;
            background: #ffffff;
            box-sizing: border-box;
            position: relative;
        }

        /* LOGO & BRANDING ROW STRUCTURE */
        .brand-header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 4px;
        }
        .brand-logo-placeholder {
            width: 35px;
            height: 35px;
            background: #1e293b;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 800;
            font-size: 10px;
        }
        .inst-title { font-size: 13px; font-weight: 800; text-transform: uppercase; text-align: center; letter-spacing: 0.5px; margin: 0; }
        .trust-subtitle { font-size: 8px; text-transform: uppercase; text-align: center; font-weight: 700; color: #475569; margin: 0; text-decoration: underline; }
        
        .voucher-title-banner {
            text-align: center;
            font-size: 11px;
            font-weight: 800;
            margin: 6px 0;
            text-transform: uppercase;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 2px 0;
        }

        /* DATA ALIGNMENT MATRIX TABLES */
        .particulars-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        .particulars-table td {
            padding: 3px 2px;
            border-bottom: 1px dotted #cbd5e1;
            font-size: 10px;
        }
        .label-cell { font-weight: 700; color: #334155; width: 30%; }
        .val-cell { font-weight: 600; color: #000; }

        /* ARREARS & TUITION FEES BREAKDOWN TABLES */
        .fee-matrix-table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
        }
        .fee-matrix-table th {
            border: 1px solid #000;
            padding: 4px;
            font-weight: 700;
            text-align: left;
            font-size: 9px;
            text-transform: uppercase;
        }
        .fee-matrix-table td {
            border: 1px solid #000;
            padding: 5px 4px;
            font-size: 10px;
        }

        .highlight-banner-row {
            border: 1px solid #000;
            font-weight: 800;
            padding: 4px;
            margin: 4px 0;
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            text-transform: uppercase;
        }

        .text-amount-line {
            font-style: italic;
            font-weight: 600;
            font-size: 9px;
            margin: 4px 0;
            border-bottom: 1px solid #000;
            padding-bottom: 2px;
            text-transform: capitalize;
        }

        .bank-details-box {
            border: 1px solid #000;
            padding: 6px;
            font-size: 9px;
            margin-top: 8px;
            background: #f8fafc;
        }

        .legal-notice-text {
            font-size: 7.5px;
            color: #334155;
            text-align: justify;
            margin-top: 6px;
            line-height: 1.2;
        }

        .urdu-notice-text {
            direction: rtl;
            font-size: 9px;
            text-align: right;
            margin-top: 5px;
            line-height: 1.3;
            font-weight: 500;
        }

        /* VERIFICATION SIGNATURE SPACES */
        .signatures-matrix {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 30px;
            font-size: 8.5px;
        }
        .sig-entry {
            border-top: 1px solid #000;
            text-align: center;
            padding-top: 2px;
            font-weight: 600;
        }

        .slip-footer-tag {
            text-align: center;
            font-weight: 800;
            font-size: 9px;
            text-transform: uppercase;
            margin-top: 10px;
            letter-spacing: 0.5px;
        }

        /* BROWSER PRINT ENGINE SYSTEM MATRIX OVERRIDES */
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; margin: 0; background: #fff; }
            .voucher-container { gap: 8px; width: 100%; }
            .voucher-slip { height: 97vh; border: 1px solid #000 !important; }
            @page { size: landscape; margin: 0.4cm; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="background: #f8fafc; padding: 10px; border-bottom: 1px solid #cbd5e1; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; border-radius: 4px;">
        <div style="font-weight: 700; font-size: 12px;"> Fee Voucher Printing </div>
        <button onclick="window.print()" style="background: #04AA6D; color: white; border: none; padding: 6px 16px; font-weight: 700; border-radius: 4px; cursor: pointer; font-size: 11px;">Print Voucher</button>
    </div>

    <div class="voucher-container">
        @php
            $slipsConfig = [
                ['tag' => 'sm Copy'],
                ['tag' => 'Bank Copy'],
                ['tag' => 'Student Copy']
            ];

            // 🌟 REAL MATHEMATICS FIXED: Reads the real amount from the controller payment object dynamically
            $baseTuitionAmount = floatval($payment->amount);
            
            // Late fine calculation logic linked safely to your controller properties
            $lateFinePenalty = floatval($payment->flat_late_fine ?? 0.00);
            if ($lateFinePenalty <= 0 && \Carbon\Carbon::parse($payment->payment_date)->day > 10) {
                $lateFinePenalty = 500.00;
            }
            
            $totalAfterDueDate = $baseTuitionAmount + $lateFinePenalty;

            // 🌟 NATIVE CONVERSION MATRIX ENGINE: Transforms recorded digits into actual text components
            $wordFormatter = new \NumberFormatter('en', \NumberFormatter::SPELLOUT);
            
            $baseAmountInWords = "Rs. " . $wordFormatter->format($baseTuitionAmount) . " only";
            $lateAmountInWords = "Rs. " . $wordFormatter->format($totalAfterDueDate) . " only";
        @endphp

        @foreach($slipsConfig as $slip)
        <div class="voucher-slip">
            
            <div class="brand-header">
                <div class="brand-logo-placeholder">SM</div>
                <div>
                    <h1 class="inst-title">SCHOOL MATRIX</h1>
                    <p class="trust-subtitle">A PROJECT OF MALIK ABDULLAH</p>
                </div>
            </div>

            <div class="voucher-title-banner">
                Fee Voucher <br>
                <span style="font-size: 12px; font-weight: 800;">Voucher # {{ $payment->id ?? '13' }}</span>
            </div>

            <table class="particulars-table">
                <tr>
                    <td class="label-cell">Due Date</td>
                    <td class="val-cell" style="color: #b71c1c;">{{ \Carbon\Carbon::parse($payment->calculated_due_date ?? '2026-07-10')->format('d-M-Y') }}</td>
                </tr>
                <tr>
                    <td class="label-cell">Month</td>
                    <td class="val-cell">{{ \Carbon\Carbon::parse($payment->payment_date ?? '2026-06-17')->format('F Y') }}</td>
                </tr>
                <tr>
                    <td class="label-cell">Name:</td>
                    <td class="val-cell">{{ $payment->student_name ?? 'Muhammad Abdullah zafar' }}</td>
                </tr>
                <tr>
                    <td class="label-cell">F. Name:</td>
                    <td class="val-cell">Zaffar mehmood</td>
                </tr>
                <tr>
                    <td class="label-cell">Reg. #:</td>
                    <td class="val-cell" style="font-family: monospace; font-weight: 700;">{{ $payment->student_reg_no ?? 'AI-2000' }}</td>
                </tr>
            </table>

            <table class="fee-matrix-table">
                <thead>
                    <tr>
                        <th>Particulars</th>
                        <th style="text-align: right; width: 60px;">Rs.</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Arrears (Previous Balance)</td>
                        <td style="text-align: right; font-weight: 600;">0</td>
                    </tr>
                    <tr>
                        <td>Monthly Fee Portion</td>
                        <td style="text-align: right; font-weight: 600; color: #04AA6D;">{{ number_format($baseTuitionAmount, 0) }}</td>
                    </tr>
                </tbody>
            </table>
            <!-- DYNAMIC VALUES AND AUTOMATED TEXT STRINGS INSIGHTS -->
            <div class="highlight-banner-row">
                <span>Payable on or before Due Date:</span>
                <span>{{ number_format($baseTuitionAmount, 0) }}</span>
            </div>

            <!-- 🌟 FIXED WORKSPACE LOGIC: Outputs the exact words string matching your Rs. 20,000 transaction -->
            <div class="text-amount-line">
                {{ $baseAmountInWords }}
            </div>

            <div class="highlight-banner-row" style="margin-top: 6px;">
                <span>Payable After Due Date:</span>
                <span style="color: #b71c1c;">{{ number_format($totalAfterDueDate, 0) }}</span>
            </div>

            <!-- 🌟 FIXED WORKSPACE LOGIC: Outputs the exact words string matching your late fine transaction -->
            <div class="text-amount-line">
                {{ $lateAmountInWords }}
            </div>

            <div style="display: flex; justify-content: space-between; font-weight: bold; font-size: 9px; margin-top: 3px;">
                <span>563</span>
                <span>FBR Validation</span>
            </div>

            <div class="bank-details-box">
                <strong>Allied Bank Limited, Bosan Road, Multan (Br. 0255)</strong><br>
                <strong>(A/C No. 00100592586860042) MET Collection</strong>
            </div>

            <div class="legal-notice-text">
                Deposited dues will be non-Refundable/Non-Transferable. After online payments, deposit slip submit to accounts office. Main Campus 061-4745244. Cash should always be deposited at the respective counter and electronic computer generated receipt printed through flatbed printer on deposit slip/challan should be obtained before leaving the counter.
            </div>

            <div class="urdu-notice-text">
                اپنی رقوم کو ہمیشہ کاؤنٹر پر جمع کروائیں اور کمپیوٹر سے الیکٹرانک رسید حاصل کریں۔ کاؤنٹر چھوڑنے سے پہلے رسید چیک کر لیں کہ آپ کی حاصل کردہ رسید پر رقم درست درج ہے۔ بصورتِ دیگر بینک ذمہ دار نہ ہوگا۔
            </div>

            <div class="signatures-matrix">
                <div>Depositor<br>CNIC # ______________</div>
                <div>Signature:<br>Contact # ____________</div>
            </div>
            <div class="signatures-matrix" style="margin-top: 15px;">
                <div class="sig-entry">Authorize Signature</div>
                <div class="sig-entry">Authorize Signature</div>
            </div>

            <div style="margin-top: 12px; display: flex; justify-content: space-between; font-size: 9px; font-weight: 700;">
                <span>49</span>
                <span class="slip-footer-tag">{{ $slip['tag'] }}</span>
            </div>

        </div>
        @endforeach
    </div>

</body>
</html>
