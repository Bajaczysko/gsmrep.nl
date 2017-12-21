    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
    <div
    style="font-family:Cambria !important; font-size:8px !important; margin-top:5px !important; font-weight:bold !important; "
    id="bill">
    <div style="font-size:12.5px !important;" id="bill_header">



        <p style="text-align: center;">

                </br>
                <img width="170px" src="<?php echo $this->config->base_url(); ?>themes/default/images/logo/<?php echo $company_information->logo; ?>"/>
                </br>

            <?php echo $company_information->name . "<br>"; ?><?php echo $company_information->address . " " . $company_information->zip . "<br>"; ?> <?php echo $company_information->phone . ' ' . $company_information->email . "<br>"; ?><?php echo $company_information->url; ?>
        </p></div>
    <table style="font-family:Cambria !important; font-size:12.5px !important;">
        <tbody>
        <?php if($name = $customer_information->name): ?>
        <tr>
            <td>Naam:</td>
            <td><?php echo $name; ?></td>
        </tr>
        <?php endif; ?>
        <?php if($company = $customer_information->company): ?>
        <tr>
            <td>bedrijfsnaam:</td>
            <td><?php echo $company; ?></td>
        </tr>
        <?php endif; ?>
        <?php if($email = $customer_information->email): ?>
            <tr>
                <td>Email adres:</td>
                <td><?php echo $email; ?></td>
            </tr>
        <?php endif; ?>
        <?php if($phone = $customer_information->phone): ?>
        <tr>
            <td>Telefoonnummer:</td>
            <td><?php echo $phone; ?></td>
        </tr>
        <?php endif; ?>
        <?php if($imei = $customer_information->imei): ?>
        <tr>
            <td>IMEI:</td>
            <td><?php echo $imei; ?></td>
        </tr>
        <?php endif; ?>
        <?php if($payment_method = $bill->payment_method): ?>
            <tr>
                <td>Betaalmethode:</td>
                <td><?php echo $payment_method; ?></td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
    <table>
        <tbody>
        <tr style="font-size:12.5px !important; text-align:left !important;">
            <th>AANT</th>
            <th>OMSCHRIJVING</th>
            <th>TOTAAL</th>
        </tr>

        <?php foreach($bill_items as $billing_item): ?>
        <tr style="vertical-align:middle !important; font-size:11.5px !important;" class="bill-row">
            <td class="count"><?php echo $billing_item->quantity; ?></td>
            <td class="discripton"><?php echo $billing_item->name; ?></td>
            <td style="text-align:right !important; font-size:12px;" class="discripton">&euro; <?php echo $billing_item->price; ?></td>
        </tr>
        <?php endforeach; ?>

        <tr></tr>
        <tr style="font-size:12px !important; text-align:left !important;">
            <td></td>
            <td>TOTAAL</td>
            <td style="text-align:right !important">&euro; <?php echo round($company_information->total ,2); ?></td>
        </tr>
        <tr></tr>
        <tr></tr>
        <tr style="vertical-align:middle !important; font-size:10px !important;">
            <td></td>
            <td>BTW 21.00% over &euro; <?php echo round($company_information->total ,2); ?></td>
            <td style="text-align:right !important" class="discripton">&euro; <?php echo round($company_information->total_tax ,2); ?></td>
        </tr>
        </tbody>
    </table>

        <?php if($company_information->completed_at != '0000-00-00 00:00:00'): ?>
        <div class="time" style="vertical-align:middle !important; font-size:10px !important; margin-top:5px !important;">
        <?php echo date("d-m-Y H:i", strtotime($company_information->completed_at)); ?>
        </div>
        <?php elseif($this->uri->segment(4, 0) === 'offerte'): ?>
            <b>OFFERTE (<?php echo date("d-m-Y H:i"); ?>)</b></br>
        <?php else: ?>
        <div class="time" style="vertical-align:middle !important; font-size:10px !important; margin-top:5px !important;">
        NOG NIET BEVESTIGD (<?php echo date("d-m-Y H:i"); ?>)
        </div>
        <?php endif; ?>
    Handtekening klant voor goede ontvangst <br><br><br><br><br>________________________________________________________
    <br>

    <div style="font-size:11.5px !important; margin-top:5px !important;" id="bill_footer">

        <p
            style="text-align: center; margin-bottom: 2px;"><br>



            <br>Wij geven 6 maanden garantie op de vervangen onderdelen. Let op dat Val- / druk- / stootschade (strepen/vlekken in de LCD of scheuren in het glas) en vloeistofschade niet onder de garantie vallen. Tevens zijn wij niet aansprakelijk voor verder schade aan uw toestel na reparatie. <br>
            <?php if ($kvk = $company_information->kvk_nummer) {
                    echo "KVK: " . $kvk;
                } ?> <br>
                <?php if ($btw = $company_information->btw_nummer) {
                    echo "BTW Nr. : " . $btw . "<br>";
                } ?> 

            <br>

            <?php echo $this->barcode_model->get_barcode_code($company_information->barcode_id); ?><br>
            <center><?php echo $company_information->barcode_id; ?></center>

        </p>

</div>

        <?php // print_r($bill); ?>