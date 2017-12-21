<!DOCTYPE html>
<html dir="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html;
charset=UTF-8">
    <title>GSM Reparatie Centrum</title>
</head>
<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
<div id="wrapper" dir="ltr"
     style="background-color: #f5f5f5; margin: 0; padding: 70px 0 70px 0; -webkit-text-size-adjust: none !important; width: 100%;">
    <table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
        <tr>
            <td align="center" valign="top">
                <div id="template_header_image">
                </div>
                <table border="0" cellpadding="0" cellspacing="0" width="600" id="template_container"
                       style="box-shadow: 0 1px 4px rgba(0,0,0,0.1) !important; background-color: #fdfdfd; border: 1px solid #dcdcdc; border-radius: 3px !important;">
                    <tr>
                        <td align="center" valign="top">
                            <!-- Header -->
                            <table border="0" cellpadding="0" cellspacing="0" width="600" id="template_header"
                                   style='background-color: #557da1; border-radius: 3px 3px 0 0 !important; color: #ffffff; border-bottom: 0; font-weight: bold; line-height: 100%; vertical-align: middle; font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;'>
                                <tr>
                                    <td id="header_wrapper" style="padding: 36px 48px; display: block;">
                                        <h1 style='color: #ffffff; font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif; font-size: 30px; font-weight: 300; line-height: 150%; margin: 0; text-align: left; text-shadow: 0 1px 0 #7797b4; -webkit-font-smoothing: antialiased;'>
                                            Uw bon overzicht</h1>
                                    </td>
                                </tr>
                            </table>
                            <!-- End Header -->
                        </td>
                    </tr>
                    <tr>
                        <td align="center" valign="top">
                            <!-- Body -->
                            <table border="0" cellpadding="0" cellspacing="0" width="600" id="template_body">
                                <tr>
                                    <td valign="top" id="body_content" style="background-color: #fdfdfd;">
                                        <!-- Content -->
                                        <table border="0" cellpadding="20" cellspacing="0" width="100%">
                                            <tr>
                                                <td valign="top" style="padding: 48px;">
                                                    <div id="body_content_inner"
                                                         style='color: #737373; font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif; font-size: 14px; line-height: 150%; text-align: left;'>

                                                        <p style="margin: 0 0 16px;">Recent bent u bij ons in de winkel
                                                            geweest en hebt een reparatie uit laten voeren, hieronder
                                                            mailen wij u het reparatie nummer en sturen wij u een
                                                            overzicht van de werkzaamheden die wij voor u verricht
                                                            hebben.</p>

                                                        <ul class="order_details bacs_details"></ul>
                                                        <h2 style='color: #557da1; display: block; font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif; font-size: 18px; font-weight: bold; line-height: 130%; margin: 16px 0 8px; text-align: left;'>
                                                            Overzicht van aangeschafte producten</h2>

                                                        <table class="td" cellspacing="0" cellpadding="6"
                                                               style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; color: #737373; border: 1px solid #e4e4e4;"
                                                               border="1">
                                                            <thead>
                                                            <tr>
                                                                <th class="td" scope="col"
                                                                    style="text-align: left; color: #737373; border: 1px solid #e4e4e4; padding: 12px;">
                                                                    Product
                                                                </th>
                                                                <th class="td" scope="col"
                                                                    style="text-align: left; color: #737373; border: 1px solid #e4e4e4; padding: 12px;">
                                                                    Quantity
                                                                </th>
                                                                <th class="td" scope="col"
                                                                    style="text-align: left; color: #737373; border: 1px solid #e4e4e4; padding: 12px;">
                                                                    Prijs
                                                                </th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>

                                                            <?php foreach($bill_items as $bill_item): ?>
                                                            <tr class="order_item">
                                                                <td class="td" style="text-align: left; vertical-align: middle; border: 1px solid #eee; font-family: 'Helvetica Neue', Helvetica,
Roboto, Arial, sans-serif; word-wrap: break-word; color: #737373; padding: 12px;"><?php echo $bill_item->name; ?>
                                                                </td>
                                                                <td class="td" style="text-align: left; vertical-align: middle; border: 1px solid #eee; font-family: 'Helvetica Neue', Helvetica,
Roboto, Arial, sans-serif; color: #737373; padding: 12px;"><?php echo $bill_item->quantity; ?>
                                                                </td>
                                                                <td class="td" style="text-align: left; vertical-align: middle; border: 1px solid #eee; font-family: 'Helvetica Neue', Helvetica,
Roboto, Arial, sans-serif; color: #737373; padding: 12px;"><span class="amount">&euro; <?php echo $bill_item->price; ?></span></td>
                                                            </tr>
                                                            <?php endforeach; ?>


                                                            </tbody>
                                                            <tfoot>
                                                            <tr>
                                                                <th class="td" scope="row" colspan="2"
                                                                    style="text-align: left; border-top-width: 4px; color: #737373; border: 1px solid #e4e4e4; padding: 12px;">
                                                                    Subtotaal:
                                                                </th>
                                                                <td class="td"
                                                                    style="text-align: left; border-top-width: 4px; color: #737373; border: 1px solid #e4e4e4; padding: 12px;">
                                                                    <span class="amount">&euro; <?php echo round($bill->subtotal, 2); ?></span></td>
                                                            </tr>

                                                            <tr>
                                                                <th class="td" scope="row" colspan="2"
                                                                    style="text-align: left; border-top-width: 4px; color: #737373; border: 1px solid #e4e4e4; padding: 12px;">
                                                                    BTW @21%:
                                                                </th>
                                                                <td class="td"
                                                                    style="text-align: left; border-top-width: 4px; color: #737373; border: 1px solid #e4e4e4; padding: 12px;">
                                                                    <span class="amount">&euro; <?php echo round($bill->total_tax, 2); ?></span></td>
                                                            </tr>


                                                            <tr>
                                                                <th class="td" scope="row" colspan="2"
                                                                    style="text-align: left; color: #737373; border: 1px solid #e4e4e4; padding: 12px;">
                                                                    Betaalmethode:
                                                                </th>
                                                                <td class="td"
                                                                    style="text-align: left; color: #737373; border: 1px solid #e4e4e4; padding: 12px;">
                                                                    <?php echo $bill->payment_method; ?>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th class="td" scope="row" colspan="2"
                                                                    style="text-align: left; color: #737373; border: 1px solid #e4e4e4; padding: 12px;">
                                                                    Totaal:
                                                                </th>
                                                                <td class="td"
                                                                    style="text-align: left; color: #737373; border: 1px solid #e4e4e4; padding: 12px;">
                                                                    <span class="amount">&euro; <?php echo $bill->total; ?></span></td>
                                                            </tr>
                                                            </tfoot>
                                                        </table>
                                                        <br>
                                                        <a style="margin: 0 0 16px;">
                                                            Als u tevreden bent over uw reparatie zouden we u willen
                                                            vragen een recensie over de reparatie achter te laten op <a
                                                                href="https://www.klantenvertellen.nl/enquete/gsmreparatiecentrum.nl">https://www.klantenvertellen.nl/enquete/gsmreparatiecentrum.nl</a>
                                                            De enquete duurt maar 1 minuut om in te vullen!
                                                            </p>

                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                        <!-- End Content -->
                                    </td>
                                </tr>
                            </table>
                            <!-- End Body -->
                        </td>
                    </tr>
                    <tr>
                        <td align="center" valign="top">
                            <!-- Footer -->
                            <table border="0" cellpadding="10" cellspacing="0" width="600" id="template_footer">
                                <tr>
                                    <td valign="top" style="padding: 0; -webkit-border-radius: 6px;">
                                        <table border="0" cellpadding="10" cellspacing="0" width="100%">
                                            <tr>
                                                <td colspan="2" valign="middle" id="credit"
                                                    style="padding: 0 48px 48px 48px; -webkit-border-radius: 6px; border: 0; color: #99b1c7; font-family: Arial; font-size: 12px; line-height: 125%; text-align: center;">
                                                    <p>GSM
                                                        Reparatie Centrum</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            <!-- End Footer -->
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>
</body>
</html>

