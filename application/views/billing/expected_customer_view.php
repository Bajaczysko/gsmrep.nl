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
                        <a href="/billing/expected_customers" class="btn btn-default">Reset</a>

                        <?php echo form_close();?>

                    </div>

                    <div class="col-md-4">

                    </div>
                    <div class="col-md-3">
                        <input value="New planning" href="/billing/plan_new_customer" disabled class="btn btn-info btn-100"/>
                    </div>
                </div>

            </div>

            <br>

            <table class="table table-striped table-hover ">
                <thead>
                <tr>
                    <th>Expected Date</th>
                    <th>Expected Time</th>
                    <?php if($this->flexi_auth->is_admin()): ?>
                    <th>In store</th>
                    <?php else: ?>
                    <th>Phone</th>
                    <?php endif; ?>
                    <?php if($this->flexi_auth->is_admin()): ?>
                    <th>Source Website</th>
                    <?php endif; ?>
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
                            <td><?php echo $order->expected_date; ?></td>
                            <td><?php echo $order->expected_time; ?></td>
                            <?php if($this->flexi_auth->is_admin()): ?>
                            <td><?php echo $order->store; ?></td>
                            <?php else: ?>
                            <td><?php echo $this->billing_model->get_customer_phone($order->id); ?></td>
                            <?php endif; ?>
                            <?php if($this->flexi_auth->is_admin()): ?>
                            <td><?php echo $order->website; ?></td>
                            <?php endif; ?>
                            <td>

                                <?php foreach($this->billing_model->get_billing_lines_without_discount($order->id) as $product): ?>
                                    <?php echo $product->name; ?>
                                <?php endforeach; ?>

                            </td>
                            <td><?php echo $order->customer_name; ?></td>
                            <td><?php echo $order->total_price; ?></td>
                            <td><a style="text-decoration: none; width: 100%" href="/billing/expected_to_billing/<?php echo $order->id; ?>" class="btn-sm btn-primary">Make Bill</a></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>

                <?php endif; ?>


            </table>

        </div>

    </div>

    <?php echo $pagination['links']; ?>

</div>


