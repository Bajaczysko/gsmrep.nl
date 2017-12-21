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
                        <input type="submit" name="search_customer" value="Search" class="btn btn-default"/>

                        <?php echo form_close();?>

                    </div>

                    <div class="col-md-4">

                    </div>
                    <div class="col-md-1">

                    </div>
                    <div class="col-md-2">
                        <a href="/customers/newsmartgroup" class="btn btn-default">New SmartGroup</a>
                    </div>
                </div>

            </div>

            <br>

            <table class="table table-striped table-hover ">
                <thead>
                <tr>
                    <th>SmartGroupID</th>
                    <th>Name</th>
                    <th>Customers inside</th>
                    <th>Type</th>
                    <th>Actions</th>
                </tr>
                </thead>

                <tbody>
                <td>
                    No results found
                </td>
                </tbody>

            </table>

        </div>

    </div>

    <?php echo $pagination['links']; ?>

</div>


