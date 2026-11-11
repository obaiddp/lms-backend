<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice - {{ $course->title ?? 'Course' }}</title>
    <style>
        body {
            font-family: dejavusans, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .invoice-box {
            width: 100%;
            border: 1px solid #ddd;
            padding: 20px;
        }
        h2, h3, h4 {
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        .header h2 {
            text-transform: uppercase;
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        table th, table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }
        .text-right {
            text-align: right;
        }
        .copy-type {
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        .footer {
            margin-top: 30px;
            font-size: 11px;
            text-align: center;
            border-top: 1px solid #000;
            padding-top: 10px;
        }
    </style>
</head>
<body>

    <div class="invoice-box">

        {{-- Copy Type Header --}}
        <div class="copy-type">
            {{ $copy_type ?? 'STUDENT COPY' }}
        </div>

        {{-- Institute Header --}}
        <div class="header">
         
        </div>

        {{-- Student & Course Info --}}
        <table>
            <tr>
                <th>Student Name</th>
                <td>{{ $student->name ?? '' }}</td>
                <th>Student ID</th>
                <td>{{ $student->id ?? '' }}</td>
            </tr>
            <tr>
                <th>Course</th>
                <td>{{ $course->title ?? '' }}</td>
                <th>Date</th>
                <td>{{ now()->format('d-m-Y') }}</td>
            </tr>
        </table>

        {{-- Fee Breakdown --}}
        <h4 style="margin-top: 20px;">Fee Details</h4>
        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="text-right">Original Price</th>
                    <th class="text-right">Discount</th>
                    <th class="text-right">Discount Type</th>
                    <th class="text-right">Net Price</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Course Fee</td>
                    <td class="text-right">{{ number_format($data['course_price'] ?? 0, 2) }}</td>
                    <td class="text-right">{{ number_format($data['discount_course_price'] ?? 0, 2) }}</td>
                    <td class="text-right">
                        {{ ($data['discount_type_course_price'] ?? 0) == 0 ? 'Amount' : 'Percentage' }}
                    </td>
                    <td class="text-right">{{ number_format($data['net_course_price'] ?? 0, 2) }}</td>
                </tr>
                <tr>
                    <td>Admission Fee</td>
                    <td class="text-right">{{ number_format($data['admission_fee'] ?? 0, 2) }}</td>
                    <td class="text-right">{{ number_format($data['discount_admission_fee'] ?? 0, 2) }}</td>
                    <td class="text-right">
                        {{ ($data['discount_type_admission_fee'] ?? 0) == 0 ? 'Amount' : 'Percentage' }}
                    </td>
                    <td class="text-right">{{ number_format($data['net_admission_fee'] ?? 0, 2) }}</td>
                </tr>
                <tr>
                    <th colspan="4" class="text-right">Total Payable</th>
                    <th class="text-right">{{ number_format($data['net_price'] ?? 0, 2) }}</th>
                </tr>
            </tbody>
        </table>

        {{-- Footer --}}
        <div class="footer">
            <p>Generated electronically on {{ now()->format('d-m-Y H:i') }} — no signature required.</p>
            <p>This invoice is valid for all purposes related to enrollment and payment submission.</p>
        </div>
    </div>

</body>
</html>