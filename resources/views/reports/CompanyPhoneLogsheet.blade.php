<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Company Phone Logsheet</title>
    <style>
        @page { margin: 20px; }
        body { font-family: "Times New Roman", Times, serif; font-size: 10px; margin: 0; padding: 0; color: #000; }
        .container { width: 100%; }

        /* Centered Header */
        .header-section { text-align: center; margin-bottom: 10px; }
        .company-name { font-style: italic; font-weight: bold; font-size: 14px; margin-bottom: 2px; }
        .report-title { font-weight: bold; font-size: 16px; text-transform: uppercase; }

        /* Fixed Info Header Table */
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .info-table td { vertical-align: top; padding: 0; border: none; }

        /* Internal labels and lines */
        .field-row { margin-bottom: 4px; display: table; width: 100%; }
        .label {
            display: table-cell;
            white-space: nowrap;
            width: 1%;
            padding-right: 5px;
            min-width: 70px; /* Forces symmetrical starting point for underlines */
        }
        .line-fill { display: table-cell; border-bottom: 1px solid black; padding-left: 5px; height: 14px; vertical-align: bottom; }

        /* Centering the cashout section */
        .cashout-container {
            text-align: center;
            margin-top: 12px;
            width: 100%;
        }

        /* The Main Log Area */
        .log-wrapper { width: 100%; display: table; border-collapse: collapse; table-layout: fixed; }
        .log-column { display: table-cell; width: 49%; vertical-align: top; }
        .spacer-column { display: table-cell; width: 2%; }

        table.log-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        table.log-table th { border: 1px solid black; background: #fff; font-size: 8px; padding: 4px 1px; font-weight: bold; }
        table.log-table td { border: 1px solid black; height: 32px; text-align: center; font-size: 9px; padding: 1px; }

        .acc-cell { text-align: left !important; font-size: 8px !important; padding-left: 4px !important; line-height: 1.1; }
        .checkbox-custom { font-family: DejaVu Sans, sans-serif; font-size: 10px; }

        .remarks-section { margin-top: 10px; }
        .remarks-line { border-bottom: 1px solid black; width: 100%; height: 18px; margin-top: 2px; }
        .footer-code { position: fixed; bottom: 10px; left: 0; font-weight: bold; font-style: italic; font-size: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-section">
            <div class="company-name">Printwell, Inc.</div>
            <div class="report-title">COMPANY PHONE LOGSHEET</div>
        </div>

        <table class="info-table">
            <tr>
                <td width="45%">
                    <div class="field-row"><span class="label">Brand/Model:</span><span class="line-fill">{{ $phone->brand }} {{ $phone->model }}</span></div>
                    <div class="field-row"><span class="label">Serial Number:</span><span class="line-fill">{{ $phone->serial_num }}</span></div>
                    <div class="field-row"><span class="label">RAM/ROM:</span><span class="line-fill">{{ $phone->ram }}GB / {{ $phone->rom }}GB</span></div>
                    <div class="field-row"><span class="label">IMEI 1/2:</span><span class="line-fill">{{ $phone->imei_one }} / {{ $phone->imei_two }}</span></div>
                    <div class="field-row"><span class="label">Sim No.:</span><span class="line-fill">{{ $phone->sim_no }}</span></div>
                    <div class="field-row"><span class="label">Department:</span><span class="line-fill">{{ $transactions[count($transactions) - 1]->department }}</span></div>

                    <div class="cashout-container">
                        <span class="checkbox-custom">☐</span> With Cashout &nbsp;&nbsp;&nbsp;
                        <span class="checkbox-custom">☐</span> Without cashout
                    </div>
                </td>

                <td width="10%"></td>

                <td width="45%">
                    <div class="field-row"><span class="label">Date Received:</span><span class="line-fill">{{ $date }}</span></div>
                    <div class="field-row"><span class="label">Device name:</span><span class="line-fill"></span></div>
                    <div class="field-row">
                        <span class="label">Accessories:</span>
                        <span class="line-fill" style="border-bottom: none;">
                            <span class="checkbox-custom">☐</span> Charger &nbsp;&nbsp;
                            <span class="checkbox-custom">☐</span> Earphone
                        </span>
                    </div>
                </td>
            </tr>
        </table>

        <div class="log-wrapper">
            <div class="log-column">
                <table class="log-table">
                    <thead>
                        <tr><th colspan="4">ISSUANCE</th><th colspan="2">ACKNOWLEDGEMENT</th></tr>
                        <tr>
                            <th width="26%">ISSUED TO</th>
                            <th width="14%">DATE</th>
                            <th width="20%">ISSUED BY</th>
                            <th width="16%">ACCESSORIES</th>
                            <th width="12%">IT</th>
                            <th width="12%">PROCUREMENT</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $trx)
                        <tr>
                            <td>{{ $trx->issued_to }}</td>
                            <td>{{ $trx->date_issued ? $trx->date_issued->format('m/d/y') : '' }}</td>
                            <td>{{ $trx->issued_by }}</td>
                            <td class="acc-cell">
                                <span class="checkbox-custom">☐</span> Charger<br><span class="checkbox-custom">☐</span> Earphone
                            </td>
                            <td></td>
                            <td></td>
                        </tr>
                        @endforeach
                        @for ($i = count($transactions); $i < 11; $i++)
                        <tr><td></td><td></td><td></td><td class="acc-cell"><span class="checkbox-custom">☐</span> Charger<br><span class="checkbox-custom">☐</span> Earphone</td><td></td><td></td></tr>
                        @endfor
                    </tbody>
                </table>
            </div>

            <div class="spacer-column"></div>

            <div class="log-column">
                <table class="log-table">
                    <thead>
                        <tr><th colspan="3">RETURN</th><th colspan="3">ACKNOWLEDGEMENT</th></tr>
                        <tr>
                            <th width="26%">RETURNED BY</th>
                            <th width="14%">DATE</th>
                            <th width="20%">RETURNED TO</th>
                            <th width="16%">ACCESSORIES</th>
                            <th width="12%">IT</th>
                            <th width="12%">PROCUREMENT</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $trx)
                        <tr>
                            <td>{{ $trx->return->returned_by ?? '' }}</td>
                            <td>{{ ($trx->return && $trx->return->date_returned) ? $trx->return->date_returned->format('m/d/y') : '' }}</td>
                            <td>{{ $trx->return->returned_to ?? '' }}</td>
                            <td class="acc-cell">
                                <span class="checkbox-custom">☐</span> Charger<br><span class="checkbox-custom">☐</span> Earphone
                            </td>
                            <td></td>
                            <td></td>
                        </tr>
                        @endforeach
                        @for ($i = count($transactions); $i < 11; $i++)
                        <tr><td></td><td></td><td></td><td class="acc-cell"><span class="checkbox-custom">☐</span> Charger<br><span class="checkbox-custom">☐</span> Earphone</td><td></td><td></td></tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>

        <div class="remarks-section">
            <strong>Remarks:</strong>
            <div class="remarks-line"></div>
            <div class="remarks-line"></div>
            <div class="remarks-line"></div>
        </div>

        <div class="footer-code">IT-F-16/23-00</div>
    </div>
</body>
</html>
