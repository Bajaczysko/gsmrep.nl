<div class="container">

    <div class="row">

        <div class="col-md-12">

            <?php if($message): ?>
                <div class="alert alert-dismissible alert-info">
                    <button data-dismiss="alert" class="close" type="button">×</button>
                    <strong>Heads up!</strong> This <a class="alert-link" href="#">alert needs your attention</a>, but it's not super important.
                </div>
            <?php endif; ?>

            <div class="well well-sm">
                <div class="row">

                    <div class="col-md-3">

                        <?php echo form_open(current_url());	?>
                        <input  type="text" id="search" name="search_query" value="<?php echo set_value('search_customer',$search_query);?>" class="form-control"
                                title="This example searches for users by email, first name and last name."
                            />

                    </div>
                    <div class="col-md-2" style="padding-left: 0px; padding-right: 30px;">
                        <input type="submit" name="search_customer" value="Search" class="btn btn-primary"/>
                        <a href="/billing/manage" class="btn btn-default">Reset</a>

                        <?php echo form_close();?>

                    </div>

                    <div class="col-md-4">

                    </div>
                    <div class="col-md-3">
                        <a href="/" class="btn btn-info btn-100">New billing</a>
                    </div>
                </div>

            </div>

            <br>

            <table class="table table-striped table-hover ">
                <thead>
                <tr>
                    <th>BillingID</th>
                    <th>Status</th>
                    <th>Datum</th>
                    <th>In Store</th>
                    <th>Products</th>
                    <th>Customer</th>
                    <th>Total Price</th>
                    <th>Actions</th>
                </tr>
                </thead>

                <?php if($total_rows == '0'): ?>
                    <tbody>
                    <td>
                        No results found
                    </td>
                    </tbody>
                <?php else: ?>
                    <?php foreach($customers as $order): ?>
                        <tbody>
                        <tr>
                            <td><?php echo $order->billingid; ?></td>
                            <td><?php echo $order->status; ?></td>
                            <td><?php echo $order->created_at; ?></td>
                            <td><?php echo $order->store; ?></td>

                            <td>

                                <?php

                                foreach($this->report_model->load_products_from_bill($order->billingid) as $products):

                                    echo $products->name . "<BR>";

                                endforeach;

                                ?>

                            </td>

                            <td><?php echo $order->customer_name; ?></td>
                            <td><?php echo $order->total; ?></td>
                            <td>
                                <?php if($order->status != 'done'): ?>
                                <a style="text-decoration: none;" href="/billing/edit/<?php echo $order->billingid; ?>" class="btn-sm btn-primary">Edit</a>
                                <a style="text-decoration: none;" href="/billing/view_bill/<?php echo $order->billingid; ?>/offerte" class="btn-sm btn-primary">Offerte</a>

                                <?php endif; ?>

                                <?php if($order->status == 'done'): ?>

                                    <?php if($this->flexi_auth->is_admin()): ?>
                                        <a style="text-decoration: none;" href="/billing/remove/<?php echo $order->billingid; ?>" class="btn-sm btn-danger">DELETE</a>
                                    <?php endif; ?>

                                <a style="text-decoration: none;" href="/billing/view_bill/<?php echo $order->billingid; ?>" class="btn-sm btn-success">View Bill</a>
                                <a class="btn-sm btn-warning" style="text-decoration: none;" data-toggle="modal" data-target="#Modal-invoice-<?php echo $order->billingid; ?>">View Invoice</a>

                                <?php endif; ?>

                        </tr>


                        <!-- Modal -->
                        <div id="Modal-invoice-<?php echo $order->billingid; ?>" class="modal fade" role="dialog">
                            <div class="modal-dialog">

                                <!-- Modal content-->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title">Please select payment period</h4>
                                    </div>
                                    <div class="modal-body">

                                        <?php echo form_open("/billing/view_invoice/$order->billingid", array('class' => 'form')) ?>

                                        <label>Payment period (in days)</label>
                                        <input type="number" name="period" class="form-control">
                                        <br>
                                        <input type="submit" class="btn-100 btn-success btn" value="Generate invoice"/>

                                        <?php echo form_close(); ?>

                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    </div>
                                </div>

                            </div>
                        </div>

                    <?php endforeach; ?>
                    </tbody>

                <?php endif; ?>


            </table>

        </div>

    </div>

    <?php echo $pagination['links']; ?>

</div>








