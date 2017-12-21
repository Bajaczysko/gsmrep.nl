<?php
/**
 * Created by PhpStorm.
 * User: Sebastiaan
 * Date: 23-06-16
 * Time: 23:42
 */

?>

<div class="container">

    <div class="col-md-12">

        <h4>Bonnen met product "<?php echo $this->report_model->base64url_decode($this->uri->segment(6, 0)); ?>"</h4>

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
            <?php if($reverse_bill_search): ?>
                <tbody>
                <?php foreach($reverse_bill_search as $reverse_bill_data): ?>
                    <tr>
                        <td><?php echo $reverse_bill_data->id; ?></td>
                        <td><?php echo $reverse_bill_data->created_at; ?></td>
                        <td><?php echo $reverse_bill_data->name; ?></td>

                        <td>

                            <?php

                            foreach($this->report_model->load_products_from_bill($reverse_bill_data->id) as $products):

                                echo $products->name . "<BR>";

                            endforeach;

                            ?>

                        </td>

                        <td><?php echo round($reverse_bill_data->total, 2); ?></td>
                        <td><a href="/reporting/edit_bill/<?php echo $reverse_bill_data->id; ?>" class="btn btn-xs btn-default">Show bill</a> </td>
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
