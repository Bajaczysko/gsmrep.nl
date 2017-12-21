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

                <div class="col-md-5">

                </div>
                <div class="col-md-2">
                    <a href="#new-customer" class="btn btn-100 btn-info">New customer</a>
                </div>
            </div>

        </div>

        <br>
        <?php if($search_page): ?>
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
                    <td><a style="text-decoration: none;" href="/billing/link_customer/<?php echo $customer->id; ?>" class="btn-sm btn-primary">Select customer</a></td>
                </tr>
                <?php endforeach; ?>
                </tbody>

            <?php endif; ?>


        </table>


    </div>

    <?php echo $pagination['links']; ?>

        <?php else: ?>

            <?php echo form_open(current_url(), array('class' => 'form-horizontal')); ?>
                <fieldset>
                    <legend>Create a new customer</legend>
                    <div class="form-group">
                        <label for="inputEmail" class="col-lg-2 control-label">Full name</label>
                        <div class="col-lg-10">
                            <input class="form-control" name="full-name" required id="inputEmail" placeholder="Full name" type="text">
                        </div>
                    </div>
                                        <div class="form-group">
                        <label for="inputEmail" class="col-lg-2 control-label">Phone</label>
                        <div class="col-lg-10">
                            <input class="form-control" id="inputEmail" name="phone" placeholder="Phone" type="text">
                        </div>
                    </div>
                                        <div class="form-group">
                        <label for="inputEmail" class="col-lg-2 control-label">Email</label>
                        <div class="col-lg-10">
                            <input class="form-control" id="inputEmail" name="email" placeholder="Email" type="text">
                        </div>
                    </div>
                                                            <div class="form-group">
                        <label for="inputEmail" class="col-lg-2 control-label">IMEI</label>
                        <div class="col-lg-10">
                            <input class="form-control" id="inputEmail" name="imei" placeholder="iMEI nummer van toestel" type="text">
                        </div>
                    </div>
                                        <div class="form-group">
                        <label for="inputEmail" class="col-lg-2 control-label">Company</label>
                        <div class="col-lg-10">
                            <input class="form-control" id="inputEmail" name="company" placeholder="Company" type="text">
                        </div>
                    </div>
                        <div class="form-group">
      <label class="col-lg-2 control-label">Newsletter</label>
      <div class="col-lg-10">
        <div class="radio">
          <label>
            <input name="newsletter"  id="optionsRadios1" value="1" checked="" type="radio">
            Yes
          </label>
        </div>
        <div class="radio">
          <label>
            <input name="newsletter-no" id="optionsRadios2" value="0" type="radio">
            No
          </label>
        </div>
      </div>
    </div>

                    <input hidden name="new-customer-input" value="1"/>

                    <div class="form-group">
                        <div class="col-lg-10 col-lg-offset-2">
                            <button type="submit" name="new-customer" class="btn btn-primary">Create customer & Edit Bill</button>
                        </div>
                    </div>
                </fieldset>
            </form>


        <?php endif; ?>

    </div>
    </div>

</div>


