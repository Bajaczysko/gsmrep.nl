<div class="container">
    <h4>Periode overzicht (Werkelijke periode <?php echo $range_first_date['DATE(created_at)']; ?> // <?php echo $range_last_date['DATE(created_at)']; ?>)</h4>
    <div class="row">
        <div class="col-sm-6 col-lg-3">
            <div class="widget-simple text-center card-box bg-success">
                <h3 class="text-white counter">&euro; <?php echo $period_turnover_subtotal; ?></h3>
                <p class="text-white">Totale omzet (EX BTW)</p>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="widget-simple text-center card-box bg-warning">
                <h3 class="text-white counter">&euro; <?php echo $period_turnover_total_tax; ?></h3>
                <p class="text-white">Totaal BTW</p>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="widget-simple text-center card-box bg-pink">
                <h3 class="text-white counter">&euro; <span class="counter"><?php echo $period_turnover_total; ?></span></h3>
                <p class="text-white">Totale omzet (Incl BTW)</p>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="widget-simple text-center card-box bg-purple">
                <h3 class="text-white counter"><?php echo $total_products; ?></h3>
                <p class="text-white">Totaal verkochte producten</p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6 col-lg-3">
            <div class="widget-simple text-center card-box bg-blue">
                <h3 class="text-white counter"><?php echo $period_total_bills; ?></h3>
                <p class="text-white">Totaal aantal gemaakte bonnen</p>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="widget-simple text-center card-box bg-sun">
                <h3 class="text-white counter">&euro; <?php echo $period_total_pin; ?></h3>
                <p class="text-white">Totaal betaald pin</p>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="widget-simple text-center card-box bg-darkblue">
                <h3 class="text-white counter"><span class="counter">&euro; <?php echo $period_total_contant; ?></span></h3>
                <p class="text-white">Totaal betaald contant</p>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="widget-simple text-center card-box bg-red">
                <h3 class="text-white counter">&euro; <?php echo $period_total_factuur; ?></h3>
                <p class="text-white">Totaal betaald op factuur</p>
            </div>
        </div>
    </div>

<div class="row">

    <div class="col-md-12">

        <h4>Omzet per betaalmethode gesorteed op orders</h4>

        <table id="turnover_products_pay_method" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
            <tr>
                <th>BillingID</th>
                <th>Klant</th>
                <th>Betaald met PIN</th>
                <th>Betaald contant</th>
                <th>Betaald met factuur</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <th>BillingID</th>
                <th>Klant</th>
                <th>Betaald met PIN</th>
                <th>Betaald contant</th>
                <th>Betaald met factuur</th>
                <th>Actions</th>
            </tr>
            </tr>
            </tfoot>
            <?php if($accepted_bills): ?>
                <tbody>
                <?php foreach($accepted_bills as $accepted_bill): ?>
                    <tr>
                        <td><?php echo $accepted_bill->orderID; ?></td>
                        <td><?php echo $accepted_bill->customer_name; ?></td>

                        <?php if($accepted_bill->payment_method == 'pin'): ?>
                        <td><?php echo $accepted_bill->total; ?></td>
                        <td>-</td>
                        <td>-</td>
                        <?php endif; ?>

                        <?php if($accepted_bill->payment_method == 'contant'): ?>
                            <td>-</td>
                            <td><?php echo $accepted_bill->total; ?></td>
                            <td>-</td>
                        <?php endif; ?>

                        <?php if($accepted_bill->payment_method == 'factuur'): ?>
                            <td>-</td>
                            <td>-</td>
                            <td><?php echo $accepted_bill->total; ?></td>
                        <?php endif; ?>

                        <td><a href="/reporting/edit_bill/<?php echo $accepted_bill->orderID; ?>" class="btn btn-xs btn-default">Show bill</a> </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            <?php else: ?>
                <tbody>
                <td>No results</td>
                </tbody>
            <?php endif; ?>
        </table>

    </div>

    <div class="col-md-12">

        <h4>Omzet per product in deze periode</h4>

        <table id="turnover_products" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
            <tr>
                <th>Product</th>
                <th>Totaal verkocht</th>
                <th>Totale omzet (zonder BTW)</th>
                <th>Totale omzet (met BTW)</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <th>Product</th>
                <th>Totaal verkocht</th>
                <th>Totale omzet (zonder BTW)</th>
                <th>Totale omzet (met BTW)</th>
                <th>Actions</th>
            </tr>
            </tfoot>
            <?php if($turnover_products): ?>
                <tbody>
                <?php foreach($turnover_products as $turnover_product): ?>
                    <tr>
                        <td><?php echo $turnover_product->name; ?></td>
                        <td><?php echo $turnover_product->counter; ?></td>
                        <td><?php echo round($turnover_product->subtotal, 2); ?></td>
                        <td><?php echo round($turnover_product->price, 2); ?></td>
                        <td><a href="/reporting/order_lines/<?php echo $this->uri->segment(3, 0) ?>/<?php echo $this->uri->segment(4, 0) ?>/<?php echo $this->uri->segment(5, 0) ?>/<?php echo $this->report_model->base64url_encode($turnover_product->name); ?>" class="btn btn-xs btn-default">Show all bills</a> </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            <?php else: ?>
                <tbody>
                <td>No results</td>
                </tbody>
            <?php endif; ?>
        </table>

    </div>

    <div class="col-md-12">

        <h4>Bonnen niet bevestigd</h4>

        <table id="uncompleted_bills" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
            <tr>
                <th>BillingID</th>
                <th>Datum aangemaakt</th>
                <th>Klant</th>
                <th>Product</th>
                <th>Bedrag</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <th>BillingID</th>
                <th>Datum aangemaakt</th>
                <th>Klant</th>
                <th>Product</th>
                <th>Bedrag</th>
                <th>Actions</th>
            </tr>
            </tfoot>
            <?php if($draft_bills): ?>
                <tbody>
                <?php foreach($draft_bills as $draft_bill): ?>
                    <tr>
                        <td><?php echo $draft_bill->id; ?></td>
                        <td><?php echo $draft_bill->created_at; ?></td>
                        <td><?php echo $draft_bill->name; ?></td>

                        <td>

                            <?php

                            foreach($this->report_model->load_products_from_bill($draft_bill->id) as $products):

                                echo $products->name . "<BR>";

                            endforeach;

                            ?>

                        </td>

                        <td><?php echo round($draft_bill->total, 2); ?></td>
                        <td><a href="/reporting/edit_bill/<?php echo $draft_bill->id; ?>" class="btn btn-xs btn-default">Show bill</a> </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            <?php else: ?>
                <tbody>
                <td>No results</td>
                </tbody>
            <?php endif; ?>
        </table>

    </div>

    <div class="col-md-12">

        <h4>Afwezigheid afspraak</h4>

        <table id="customer_no_show" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
            <tr>
                <th>Datum afspraak</th>
                <th>Klant</th>
                <th>Product</th>
                <th>Telefoon</th>
                <th>Mail</th>
                <th>Bedrag</th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <th>Datum afspraak</th>
                <th>Klant</th>
                <th>Product</th>
                <th>Telefoon</th>
                <th>Mail</th>
                <th>Bedrag</th>
            </tr>
            </tfoot>
            <?php if($customers_no_shows): ?>
                <tbody>
                <?php foreach($customers_no_shows as $customers_no_show): ?>
                    <tr>
                        <td><?php echo $customers_no_show->id; ?></td>
                        <td><?php echo $customers_no_show->name; ?></td>

                        <td>

                            <?php

                            foreach($this->report_model->load_products_from_bill($customers_no_show->id) as $products):

                                echo $products->name . "<BR>";

                            endforeach;

                            ?>

                        </td>
                        <td><?php echo $customers_no_show->phone; ?></td>
                        <td><?php echo $customers_no_show->email; ?></td>

                        <td><?php echo round($customers_no_show->total, 2); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            <?php else: ?>
                <tbody>
                <td>No results</td>
                </tbody>
            <?php endif; ?>
        </table>

    </div>


    <div class="col-md-12">

        <h4>Verwijderde bonnen in deze periode</h4>

            <table id="removed_bills" class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th>BillingID</th>
                    <th>Date created</th>
                    <th>Date finished</th>
                    <th>Customer</th>
                    <th>Product</th>
                    <th>Betaald met</th>
                    <th>Total price</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <th>BillingID</th>
                    <th>Date created</th>
                    <th>Date finished</th>
                    <th>Customer</th>
                    <th>Product</th>
                    <th>Betaald met</th>
                    <th>Total price</th>
                    <th>Actions</th>
                </tr>
                </tfoot>
                <?php if($removed_bills): ?>
                <tbody>
                    <?php foreach($removed_bills as $removed_bill): ?>
                <tr>
                    <td><?php echo $removed_bill->orderID; ?></td>
                    <td><?php echo $this->report_model->date_to_human($removed_bill->created_at); ?></td>
                    <td><?php echo $this->report_model->date_to_human($removed_bill->completed_at); ?></td>
                    <td><?php echo $removed_bill->customer_name; ?></td>
                    <td>

                        <?php

                        foreach($this->report_model->load_products_from_bill($removed_bill->orderID) as $products):

                            echo $products->name . "<BR>";

                        endforeach;

                        ?>

                    </td>
                    <td><?php echo $removed_bill->payment_method; ?></td>
                    <td><?php echo $removed_bill->total; ?></td>
                    <td><a href="/reporting/edit_bill/<?php echo $removed_bill->orderID; ?>" class="btn btn-xs btn-default">Open bill</a> </td>
                </tr>
                    <?php endforeach; ?>
                </tbody>
                <?php else: ?>
                    <tbody>
                    <td>No results</td>
                    </tbody>
                <?php endif; ?>
            </table>




    </div>

</div>

</div>

<script>
$(document).ready(function() {
$('#removed_bills').DataTable();
} );

$(document).ready(function() {
    $('#turnover_products').DataTable(
        {
            "footerCallback": function ( row, data, start, end, display ) {
                var api = this.api(), data;

                // Remove the formatting to get integer data for summation
                var intVal = function ( i ) {
                    return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                        typeof i === 'number' ?
                            i : 0;
                };

                // Total over all pages
                total = api
                    .column( 2 )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );

                // Total over this page
                pageTotal = api
                    .column( 2, { page: 'current'} )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );

                // Total over all pages
                total1 = api
                    .column( 3 )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );

                // Total over this page
                pageTotal1 = api
                    .column( 3, { page: 'current'} )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );

                // Total over all pages
                total_product_count = api
                    .column( 1 )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );

                // Total over this page
                pageTotal_product_count = api
                    .column( 1, { page: 'current'} )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );

                var new_number_omzet = Math.round(pageTotal).toFixed(2);
                var new_number_omzet_totaal = Math.round(total).toFixed(2);
                var new_number_omzet1 = Math.round(pageTotal1).toFixed(2);
                var new_number_omzet_totaal1 = Math.round(total1).toFixed(2);
                var new_total_product_count = Math.round(total_product_count).toFixed(2);
                var new_pageTotal_product_count = Math.round(pageTotal_product_count).toFixed(2);


                // Update footer
                $( api.column( 2 ).footer() ).html(
                    '€ '+ new_number_omzet +' ( € '+ new_number_omzet_totaal +' totale omzet)'
                );
                // Update footer
                $( api.column( 3 ).footer() ).html(
                    '€ '+ new_number_omzet1 +' ( € '+ new_number_omzet_totaal1 +' totale omzet)'
                );
                $( api.column( 1 ).footer() ).html(
                    new_pageTotal_product_count +' ( '+ new_total_product_count +' totaal verkocht)'
                );
            }
        }
    );
} );

$(document).ready(function() {
    $('#turnover_products_pay_method').DataTable();
} );
$(document).ready(function() {
    $('#uncompleted_bills').DataTable();
} );
$(document).ready(function() {
    $('#customer_no_show').DataTable();
} );
</script>







