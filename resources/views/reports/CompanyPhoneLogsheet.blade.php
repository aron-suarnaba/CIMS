<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>CIMS Company Logsheet</title>
    <style>
        @page {
            margin: 20px;
        }

        body {
            font-family: Arial, sans-serif;
            color: #333;
            font-size: 12px;
        }

        .container {
            width: 100%;
        }

        h1 {
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 20px;
        }

        /* Use Tables instead of Flex for the Header Section */
        .header-table {
            width: 100%;
            margin-bottom: 10px;
        }

        .header-table td {
            border: none;
            vertical-align: bottom;
            padding: 4px 0;
        }

        .label {
            white-space: nowrap;
            padding-right: 5px;
            text-align: right;
        }

        .line {
            border-bottom: 1px solid #000;
            width: 100%;
            display: inline-block;
            min-height: 14px;
            text-indent: 15px;
        }

        /* Checkbox styling for PDF */
        .checkbox-custom {
            font-family: DejaVu Sans, sans-serif;
            /* Required for checkbox symbols */
            font-size: 14px;
        }

        /* Table Styling */
        table.log-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 10px;
        }

        table.log-table th {
            background-color: #305496;
            color: white;
            font-size: 8px;
            padding: 5px 2px;
            border: 1px solid #000;
            text-transform: uppercase;
        }

        table.log-table td {
            border: 1px solid #000;
            height: 45px;
            padding: 0px 3px;
            font-size: 9px;
            text-align: center;
            vertical-align: middle;
        }

        .acc-cell {
            text-align: left !important;
            font-size: 8px !important;
            padding: 0px auto;
            gap: 0px;
        }

        .footer-section {
            margin-top: 20px;
            width: 100%;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Company Phone Log Sheet</h1>

        <table class="header-table">
            <tr>
                <td width="50%">
                    <table width="100%">
                        <tr>
                            <td class="label" width="80px">Brand/Model:</td>
                            <td><span class="line">{{ $phone->brand ?? '' }} {{ $phone->model ?? '' }}</span></td>
                        </tr>
                        <tr>
                            <td class="label">Serial Number:</td>
                            <td><span class="line">{{ $phone->serial_num ?? '' }}</span></td>
                        </tr>
                        <tr>
                            <td class="label">RAM/ROM:</td>
                            <td><span class="line">{{ $phone->ram ?? ''}} / {{ $phone->rom ?? '' }}</span></td>
                        </tr>
                        <tr>
                            <td class="label">IMEI 1/2:</td>
                            <td><span class="line">{{ $phone->imei_one ?? ''}}
                                    {{ $phone->imei_two ? '/ ' . $phone->imei_two : '' }}</span></td>
                        </tr>
                        <tr>
                            <td class="label">Sim No:</td>
                            <td><span class="line">{{ $phone->sim_no ?? ''}}</span></td>
                        </tr>
                        <tr>
                            <td class="label">Department:</td>
                            <td><span class="line">{{ $current->department ?? ''}}</span></td>
                        </tr>
                    </table>
                </td>
                <td width="5%"></td>
                <td width="45%">
                    <table width="100%">
                        <tr>
                            <td class="label">Date Received:</td>
                            <td><span class="line">{{ $date }}</span></td>
                        </tr>
                        <tr>
                            <td class="label">Device Name:</td>
                            <td><span class="line">{{ $phone->brand }}-{{ $phone->serial_num }}</span></td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-align: right; padding-top: 15px;">
                                <strong>Accessories:</strong>
                                <span class="checkbox-custom">☐</span> Charger &nbsp;
                                <span class="checkbox-custom">☐</span> Earphone
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <div style="margin: 10px 0;">
            <span class="checkbox-custom">☐</span> <strong>WITH CASHOUT</strong> &nbsp;&nbsp;
            <span class="checkbox-custom">☐</span> <strong>WITHOUT CASHOUT</strong>
        </div>

        <table class="log-table">
            <thead>
                <tr>
                    <th>Issued To</th>
                    <th>Date Issued</th>
                    <th>Issued By</th>
                    <th>Accessories</th>
                    <th>Ack. By IT</th>
                    <th>Ack. By Purch.</th>
                    <th>Returned By</th>
                    <th>Date Returned</th>
                    <th>Returned To</th>
                    <th>Accessories</th>
                    <th>Ack. By IT</th>
                    <th>Ack. By Purch.</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $current->issued_to ?? '' }}</td>
                    <td>{{ $current->date_issued ?? '' }}</td>
                    <td>{{ $current->issued_by ?? '' }}</td>
                    <td class="acc-cell">
                        <span class="checkbox-custom">☐</span> Charger<br>
                        <span class="checkbox-custom">☐</span> Earphone
                    </td>
                    <td></td>
                    <td></td>
                    <td>{{ $current->returned_by ?? '' }}</td>
                    <td>{{ $current->date_returned ?? '' }}</td>
                    <td></td>
                    <td class="acc-cell">
                        <span class="checkbox-custom">☐</span> Charger<br>
                        <span class="checkbox-custom">☐</span> Earphone
                    </td>
                    <td></td>
                    <td></td>
                </tr>
                @for ($i = 0; $i < 7; $i++)
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="acc-cell"><span class="checkbox-custom">☐</span> Charger <br> <span
                                class="checkbox-custom">☐</span> Earphone</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="acc-cell">
                            <span class="checkbox-custom">☐</span> Charger <br> <span class="checkbox-custom">☐</span>
                            Earphone
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                @endfor
            </tbody>
        </table>

        <table class="footer-section">
            <tr>
                <td class="label" width="60px">Remarks:</td>
                <td>{{ $phone->remarks ?? '' }}</td>
            </tr>
        </table>
    </div>
</body>

</html>
