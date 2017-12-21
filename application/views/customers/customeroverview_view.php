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

                    <?php echo form_close();?>

                </div>

                <div class="col-md-4">

                </div>
                <div class="col-md-1">

                </div>
                <div class="col-md-2">
                    <a href="/customers/newcustomer" class="btn btn-info">New customer</a>
                </div>
            </div>

        </div>

        <br>

        <table class="table table-striped table-hover ">
            <thead>
            <tr>
                <th>CustomerID</th>
                <th>Name</th>
                <th>Company</th>
                <th>Email Address</th>
                <th>Phone</th>
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
                <?php foreach($customers as $customer): ?>
                <tbody>
                <tr>
                    <td><?php echo $customer->id; ?></td>
                    <td><?php echo $customer->name; ?></td>
                    <td><?php echo $customer->company; ?></td>
                    <td><?php echo $customer->email; ?></td>
                    <td><?php echo $customer->phone; ?></td>
                    <td><a style="text-decoration: none;" href="/customers/edit/<?php echo $customer->id; ?>" class="btn-sm btn-primary">Edit</a> <a style="text-decoration: none;" href="/customers/send_email/<?php echo $customer->id; ?>" class="btn-sm btn-primary">Send email</a> <a style="text-decoration: none;" href="/customers/create_bill/<?php echo $customer->id; ?>" class="btn-sm btn-primary">New Bill</a></td>
                </tr>
                <?php endforeach; ?>
                </tbody>

            <?php endif; ?>


        </table>

    </div>

    </div>

    <?php echo $pagination['links']; ?>

</div>


