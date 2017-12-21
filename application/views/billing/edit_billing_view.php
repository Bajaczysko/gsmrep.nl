<style>
    .invoice-title h2, .invoice-title h3 {
        display: inline-block;
    }

    .table > tbody > tr > .no-line {
        border-top: none;
    }

    .table > thead > tr > .no-line {
        border-bottom: none;
    }

    .table > tbody > tr > .thick-line {
        border-top: 2px solid;
    }
</style>

<div class="container">
    <?php echo form_open(current_url()); ?>
    <div class="well well-sm">
        <div class="row">

            <div class="col-md-2">
                <?php if($bill->status != 'done'): ?>
                <a class="btn btn-primary btn-100" href="/billing/add_coupon/<?php echo $bill->id; ?>">Add coupon</a>
                <?php endif; ?>
            </div>
            <?php if($this->flexi_auth->is_admin()): ?>
            <div class="col-md-2">
                <button type="button" class="btn btn-success btn-100" data-toggle="modal" data-target="#Modal-changepayment">Change payment</button>
                </div>
            <?php else: ?>
            <div class="col-md-2">
                <?php if($bill->status != 'done'): ?>
                <a class="btn btn-primary btn-100" href="/billing/add_item/<?php echo $bill->id; ?>">Add item</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            <div class="col-md-2">
                <a class="btn btn-info btn-100" href="/billing/view_bill/<?php echo $bill->id; ?>">Print Bill</a>
            </div>
            <div class="col-md-2">
                <?php if($bill->status != 'done'): ?>
                <input value="Update Bill" type="submit" class="btn btn-warning btn-100" href="/billing/update"/>
                <?php endif; ?>
            </div>
            <div class="col-md-2">
                <?php if($bill->status != 'done'): ?>
                <button type="button" class="btn btn-success btn-100" data-toggle="modal" data-target="#Modal-confirm">Confirm Bill</button>
                <?php endif; ?>
            </div>
            <div class="col-md-2">
                <a class="btn btn-danger btn-100" href="/billing/remove/<?php echo $bill->id; ?>">Delete Bill</a>
            </div>
        </div>

    </div>

    <div class="well well-sm">
        <h4>Snel producten toevoegen</h4>
        <div class="row">

            <?php foreach($quick_products as $quick_product): ?>
            <div class="col-md-2">
                <?php if($bill->status != 'done'): ?>
                    <a class="btn btn-primary btn-100" href="/billing/add_quick_product/<?php echo $bill->id; ?>/<?php echo $quick_product->id; ?>"><?php echo $quick_product->button_name; ?></a>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>

        </div>

    </div>

    <div class="row">
        <div class="col-xs-12">
            <div class="row">
                <div class="col-xs-6">
                    <address>
                        <?php if($bill->customer_id == '0'): ?>
                        <strong>Billed To:</strong><br>
                        John Smith<br>
                        1234 Main<br>
                        Apt. 4B<br>
                        Springfield, ST 54321
                        <?php else: ?>
                            <strong>Billed To:</strong><br>
                            Not Defined
                        <?php endif; ?>
                    </address>
                </div>
                <div class="col-xs-6 text-right">
                    <address>
                        <?php if($bill->customer_id == '0'): ?>
                        <strong>Shipped To:</strong><br>
                        Jane Smith<br>
                        1234 Main<br>
                        Apt. 4B<br>
                        Springfield, ST 54321
                        <?php else: ?>
                            <strong>Shipped To:</strong><br>
                            Not defined
                        <?php endif; ?>
                    </address>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 text-right">
                    <address>
                        <strong>Order Date:</strong><br>
                        <?php echo date("d-m-Y", strtotime($bill->created_at)); ?><br><br>
                    </address>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><strong>Items</strong></h3>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-condensed">
                            <thead>
                            <tr>
                                <td><strong>Title</strong></td>
                                <td class="text-center"><strong>Price</strong></td>
                                <td class="text-center"><strong>Quantity</strong></td>
                                <td class="text-right"><strong>Totals</strong></td>
                                <td class="text-right"><strong>Options</strong></td>
                            </tr>

                            </thead>
                            <tbody>

                            <?php if($bill_items): ?>

                                <?php foreach($bill_items as $bill_item): ?>
                            <!-- foreach ($order->lineItems as $line) or some such thing here -->
                            <tr>
                                <td><?php echo $bill_item->name; ?></td>
                                <td class="text-center text-align-vertical">&euro; <?php echo $bill_item->price; ?></td>
                                <td class="text-center">

                                    <input class="form-control" type="number" value="<?php echo $bill_item->quantity; ?>" name="<?php echo $bill_item->id; ?>"/>

                                </td>
                                <td class="text-right">&euro; <?php echo $bill_item->price * $bill_item->quantity; ?></td>
                                <td class="text-right"><a href="/billing/delete_item/<?php echo $bill_item->id; ?>" class="btn-sm btn-warning">Delete</a> </td>
                            </tr>
                                <?php endforeach; ?>

                                <tr>
                                <td class="thick-line"></td>
                                <td class="thick-line"></td>
                                <td class="thick-line text-center"><strong>Subtotal</strong></td>
                                <td class="thick-line text-right">&euro; <?php echo round($bill->subtotal, 2) ?></td>
                            </tr>

                                <tr>
                                    <td class="no-line"></td>
                                    <td class="no-line"></td>
                                    <td class="no-line text-center"><strong>BTW @21%</strong></td>
                                    <td class="no-line text-right">&euro; <?php echo round($bill->total_tax, 2); ?></td>
                                </tr>

                            <tr>
                                <td class="no-line"></td>
                                <td class="no-line"></td>
                                <td class="no-line text-center"><strong>Total</strong></td>
                                <td class="no-line text-right">&euro; <?php echo $bill->total; ?></td>
                            </tr>

                            <?php else: ?>
                                <tr>
                                    <td class="text-center">No products in bill</td>
                                </tr>
                            <?php endif; ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <fieldset>

                    <legend>Edit customer data</legend>
                    <div class="form-group">
                        <label class="col-lg-2 control-label" for="inputEmail">Full name</label>
                        <div class="col-lg-10">
                            <input type="text" value="<?php echo $customer_information->name; ?>" placeholder="Full name" id="inputEmail" required="" name="full-name" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-2 control-label" for="inputEmail">Phone</label>
                        <div class="col-lg-10">
                            <input type="text" placeholder="Phone" value="<?php echo $customer_information->phone; ?>" name="phone" id="inputEmail" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-2 control-label" for="inputEmail">Email</label>
                        <div class="col-lg-10">
                            <input type="text" placeholder="Email" value="<?php echo $customer_information->email; ?>" name="email" id="inputEmail" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-2 control-label" for="inputEmail">IMEI</label>
                        <div class="col-lg-10">
                            <input type="text" value="<?php echo $customer_information->imei; ?>" placeholder="iMEI nummer van toestel" name="imei" id="inputEmail" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-2 control-label" for="inputEmail">Company</label>
                        <div class="col-lg-10">
                            <input type="text" value="<?php echo $customer_information->company; ?>" placeholder="Company" name="company" id="inputEmail" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-2 control-label">Newsletter</label>
                        <div class="col-lg-10">
                            <div class="radio">
                                <label>
                                    <input type="radio" <?php if($customer_information->newsletter == '1'): echo "checked"; endif; ?>  value="1" id="optionsRadios1" name="newsletter">
                                    Yes
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" <?php if($customer_information->newsletter == '0'): echo "checked"; endif; ?> value="0" id="optionsRadios2" name="newsletter-no">
                                    No
                                </label>
                            </div>
                        </div>
                    </div>

                </fieldset>

            <b>!Gelieve eerst de bon op te slaan om de wijzigingen van kracht te laten worden.!</b>

        </div>
    </div>
    <?php echo form_close(); ?>
    <div class="row"

</div>

<!-- Modal -->
<div id="Modal-confirm" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Please select payment method</h4>
            </div>
            <div class="modal-body">

                <div class="row">

                    <div class="col-md-4">

                        <center>
                            <a href="/billing/confirm/<?php echo $bill->id; ?>/pin">
                                <img width="100%" src="<?php echo $this->config->base_url('themes/default/images/credit-card-payment.png'); ?>"/>
                                Pinnen
                            </a>
                        </center>

                    </div>
                    <div class="col-md-4">
                        <center>
                            <a href="/billing/confirm/<?php echo $bill->id; ?>/contant">
                                <img width="100%" src="<?php echo $this->config->base_url('themes/default/images/Money.png'); ?>"/>
                                Contant
                            </a>
                        </center>
                    </div>
                    <div class="col-md-4">                        <center>
                            <a href="/billing/confirm/<?php echo $bill->id; ?>/factuur">
                                <img width="100%" src="<?php echo $this->config->base_url('themes/default/images/Accounting Bill.ico'); ?>"/>
                                Factuur
                            </a>
                        </center></div>

                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>

<!-- Modal to change payment method -->
<div id="Modal-changepayment" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Change method to</h4>
            </div>
            <div class="modal-body">

                <div class="row">

                    <div class="col-md-4">

                        <center>
                            <a href="/billing/confirm_change/<?php echo $bill->id; ?>/pin">
                                <img width="100%" src="<?php echo $this->config->base_url('themes/default/images/credit-card-payment.png'); ?>"/>
                                Pinnen
                            </a>
                        </center>

                    </div>
                    <div class="col-md-4">
                        <center>
                            <a href="/billing/confirm_change/<?php echo $bill->id; ?>/contant">
                                <img width="100%" src="<?php echo $this->config->base_url('themes/default/images/Money.png'); ?>"/>
                                Contant
                            </a>
                        </center>
                    </div>
                    <div class="col-md-4">                        <center>
                            <a href="/billing/confirm_change/<?php echo $bill->id; ?>/factuur">
                                <img width="100%" src="<?php echo $this->config->base_url('themes/default/images/Accounting Bill.ico'); ?>"/>
                                Factuur
                            </a>
                        </center></div>

                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>