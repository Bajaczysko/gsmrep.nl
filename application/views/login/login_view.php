<div class="container jumbotron clearfix">


    <div class="row">
        <div class="col-md-6 col-md-offset-3">

            <?php if (! empty($message)) { ?>
            <div class="alert alert-dismissible alert-danger">
                <button type="button" class="close" data-dismiss="alert">X</button>
                <?php echo $message; ?>
            </div>
            <?php } ?>

            <?php echo form_open(current_url(), 'class="form-horizontal"');?>
                <fieldset>
                    <legend>Login to GRC Billingsystem</legend>
                    <div class="form-group">
                        <label for="inputEmail" class="col-lg-2 control-label">Username:</label>
                        <div class="col-lg-10">
                            <input name="login_identity" class="form-control" id="inputEmail" placeholder="Email" type="text">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputPassword" class="col-lg-2 control-label">Password:</label>
                        <div class="col-lg-10">
                            <input class="form-control" name="login_password" id="inputPassword" placeholder="Password" type="password">
                            <div class="checkbox">
                                <label>
                                    <input name="remember_me" type="checkbox"> Remember Credentials
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-10 col-lg-offset-2">
                            <input type="submit" name="login_user" id="submit" value="Submit" class="btn btn-primary"/>
                        </div>
                    </div>
                </fieldset>
            </form>


        </div>
    </div>




</div>

