<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Clinic Report</title>
    <style>
        body { font-family: 'Helvetica', Arial, sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #2d3748; padding-bottom: 10px; }
        .header h1 { font-size: 24px; color: #1e3a8a; margin: 0; text-transform: uppercase; letter-spacing: 1px; }
        .header p { margin: 5px 0 0 0; color: #718096; font-size: 14px; }
        
        .meta-info { margin-bottom: 20px; font-size: 13px; font-weight: bold; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 40px; }
        th { background-color: #f7fafc; color: #4a5568; font-weight: bold; text-transform: uppercase; font-size: 11px; padding: 10px; text-align: left; border-bottom: 2px solid #e2e8f0; }
        td { padding: 10px; border-bottom: 1px solid #edf2f7; vertical-align: top; }
        tr:nth-child(even) td { background-color: #f8fafc; }
        
        .status { font-weight: bold; font-size: 11px; text-transform: uppercase; }
        
        .footer { margin-top: 50px; text-align: right; page-break-inside: avoid; }
        .signature-line { display: inline-block; width: 250px; border-bottom: 1px solid #000; margin-bottom: 5px; }
        .signature-title { display: block; font-weight: bold; font-size: 13px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Doc Jay's Veterinary Clinic</h1>
        <p>Appointment Report</p>
    </div>

    <div class="meta-info">
        Date Range: {{ $dateRange }}<br>
        Generated On: {{ $generatedDate }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Time</th>
                <th>Patient Name</th>
                <th>Pet Name</th>
                <th>Service</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $row)
            <tr>
                <td>{{ \Carbon\Carbon::parse($row->appointmentdate)->format('M d, Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($row->appointmenttime)->format('h:i A') }}</td>
                <td>{{ $row->patient_name }}</td>
                <td>{{ $row->pet_name }}</td>
                <td>{{ $row->service_name }}</td>
                <td class="status">{{ $row->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <span class="signature-line"></span>
        <span class="signature-title">Authorized Clinic Signature</span>
    </div>

</body>
</html>