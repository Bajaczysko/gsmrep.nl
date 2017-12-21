<body style="font-size: 10px; position: relative; font-style: font-family: 'Dosis';">
<div id="factuur-header">
    <div id="logo">
        <img src="<?php echo $this->config->base_url('themes/default/images/GRC_factuur_header.png'); ?>"
             style="width:100%;">
    </div>
</div>
<div id="factuur-body">
        <div id="customer-header">
            <div style="width: 30%; float:left; margin-left: 10%; margin-top: 50px; display: inline;" id="facuur-info">
                <table style="color: #CE2127; width: 100%;">
                    <tbody>
                    <tr>
                        <td><strong>DATUM</strong></td>
                        <?php if($company_information->completed_at != '0000:00:00 00:00:00'): ?>
                        <td><?php echo date("d / m / Y", strtotime($company_information->completed_at)); ?></td>
                        <?php else: ?>
                        <td>Nog niet bevestigd</td>
                        <?php endif; ?>
                    </tr>
                    <tr>
                        <td><strong>FACTUUR NR.</strong></td>
                        <td><?php echo $company_information->id ?>/ <?php echo date("d / m / Y"); ?> /<?php echo rand(1000, 1500); ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div style="width: 30%; float:right; text-align: right; display: inline;" id="customer-info">
                <table style="float: right; padding-right: 10%; padding-top: 50px;">
                    <tbody>
                    <?php if($name = $customer_information->name): ?>
                        <tr style="color: #CE2127;">
                            <td>T.N.V. <?php echo $name; ?></td>
                        </tr>
                    <?php endif; ?>
                    <?php if($company = $customer_information->company): ?>
                        <tr>
                            <td><?php echo $company; ?></td>
                        </tr>
                    <?php endif; ?>
                    <?php if($phone = $customer_information->phone): ?>
                        <tr>
                            <td><?php echo $phone; ?></td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <div style="width: 80%; float: left; margin-left: 10%; margin-top: 50px; position: relative;"
         id="factuur-producten">
        <table style="width: 100%; text-align: left;">
            <tbody>
            <tr style="text-align: left;">
                <th style="text-align: left;"><strong>Product / dienst</strong></th>
                <th style="text-align: left;">Aantal</th>
                <th style="text-align: left;">Prijs</th>
                <th style="text-align: left;">Excl. BTW</th>
            </tr>

            <?php foreach($bill_items as $billing_item): ?>
                <tr style="vertical-align:middle !important;" class="bill-row">
                    <td><?php echo $billing_item->name; ?></td>
                    <td><?php echo $billing_item->quantity; ?></td>
                    <td>&euro; <?php echo $billing_item->price; ?></td>
                    <td>&euro; <?php echo round(($billing_item->price / 121) * 100, 2); ?></td>

                </tr>
            <?php endforeach; ?>

            </tbody>
        </table>
        <div style="width:150px; float: right; margin-top: 100px;" id="factuur-totaal">
        <table style="float: right;">
                <tbody>
                <tr>
                    <th></th>
                    <th><strong>TOTAAL</strong></th>
                </tr>
                <tr>
                </tr>
                <tr>
                    <td>Exclusief</td>
                    <td>&euro; <?php echo round($company_information->subtotal ,2); ?></td>
                </tr>
                <tr>
                    <td>BTW 21%</td>
                    <td>&euro; <?php echo round($company_information->total_tax ,2); ?></td>
                </tr>
                <tr style="height: 10px !important;">
                    <td colspan="2"></td>
                </tr>
                <tr>
                    <td><strong>Inclusief BTW</strong></td>
                    <td><strong>&euro; <?php echo round($company_information->total ,2); ?></strong></td>
                </tr>

                </tbody>
            </table>
        </div>
    </div>
</div>
<div style="color:#CE2127; position: absolute; bottom: 120px; font-size: 16px; left: 50px;" id="betaaltermijn">
    <?php
$dagen = $this->uri->segment(4, 0);
$date = strtotime("+$dagen day");
$uiterste_betalen = date('d-m-Y', $date);
    ?>
    <strong>UITERSTE BETAALDATUM : <?php echo $uiterste_betalen; ?></strong>
</div>
<div
    style="width: 100%; height: 100px; background-color: #CE2127; float: left; padding-top: 10px; position: absolute; bottom:0;"
    id="footer">
    <table style="width: 80%; margin: 0 auto; color: #FFF; font-size: 15px; height: 100px;">
        <tbody>
        <tr>
            <td><?php echo strtoupper($company_information->email); ?></td>
            <td><?php echo strtoupper($company_information->address); ?></td>
        </tr>
        <tr>
            <td><?php echo strtoupper($company_information->url); ?></td>
            <td><?php echo strtoupper($company_information->zip); ?></td>
        </tr>
        </tbody>
    </table>
</div>


</body>


