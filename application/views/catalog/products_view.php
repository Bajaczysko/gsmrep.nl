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
                        <input  type="text" id="search" name="search_query" value="<?php echo set_value('search_product',$search_query);?>" class="form-control"

                            />

                    </div>
                    <div class="col-md-1" style="padding-left: 0px; padding-right: 30px;">
                        <input type="submit" name="search_product" value="Search" class="btn btn-primary"/>

                        <?php echo form_close();?>

                    </div>

                    <div class="col-md-1">

                    </div>

                    <div class="col-md-4">
                        <?php echo form_open(current_url()); ?>
                        <select name="storeview" class="form-control" onchange="this.form.submit()">

                            <?php foreach($stores as $store): ?>
                            <option <?php if(!empty($this->session->userdata('store_view')) && $this->session->userdata('store_view') == $store->id): echo "selected"; endif; ?> value="<?php echo $store->id; ?>"><?php echo $store->name; ?></option>
                            <?php endforeach; ?>

                        </select>
                        <?php echo form_close();?>
                    </div>

                    <div class="col-md-1">

                    </div>
                    <div class="col-md-2">
                        <a href="/catalog/new_product" class="btn btn-info">New product</a>
                    </div>
                </div>

            </div>

            <br>

            <table class="table table-striped table-hover ">
                <thead>
                <tr>
                    <th>ProductID</th>
                    <th>SKU</th>
                    <th>Productname</th>
                    <th>Product Price</th>
                    <th>StoreView</th>
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
                    <?php foreach($products as $product): ?>
                        <tbody>
                        <tr>
                            <td><?php echo $product->entity_id; ?></td>
                            <td><?php echo $product->sku; ?></td>
                            <td><?php echo $this->woocommerce_model->get_attribute_by_sku('title', $current_store, $product->sku); ?></td>
                            <td><?php echo $this->woocommerce_model->get_attribute_by_sku('regular_price', $current_store, $product->sku); ?></td>
                            <td><?php echo $this->woocommerce_model->get_storename_by_id($current_store); ?></td>
                            <td><a style="text-decoration: none;" href="/catalog/edit_product/<?php echo $product->entity_id; ?>" class="btn-sm btn-primary">Edit</a>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>

                <?php endif; ?>


            </table>

        </div>

    </div>

    <?php echo $pagination['links']; ?>

</div>