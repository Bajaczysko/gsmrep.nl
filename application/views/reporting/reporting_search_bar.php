<?php
/**
 * Created by PhpStorm.
 * User: Sebastiaan
 * Date: 20-06-16
 * Time: 21:57
 */

?>

<div class="container">

    <div class="row">

        <div class="col-md-12">

            <div class="well well-sm">
                <div class="row">

                    <?php echo form_open('/reporting/set_post'); ?>
                    <div class="col-md-2">
                        <input hidden value="<?php echo $this->uri->segment(3, 0); ?>" name="shop"/>
                        <input hidden value="<?php echo $this->uri->segment(2, 0); ?>" name="type"/>
                        <input class="form-control" id="datetimefrom" name="date-from" value="<?php echo $this->uri->segment(4, 0); ?>">
                    </div>
                    <div class="col-md-1">
                        <center style="margin-top: 12px;"><------></center>
                    </div>
                    <div class="col-md-2">
                        <input class="form-control" id="datetimeto" name="date-to" value="<?php echo $this->uri->segment(5, 0); ?>">
                    </div>
                    <div class="col-md-1">
                        <input type="submit" class="btn btn-danger btn-100" value="show" />
                    </div>
                    <?php echo form_close(); ?>



                    <div class="col-md-2">
                        <a class="btn btn-info btn-100" href="/reporting/set/<?php echo $this->uri->segment(3, 0); ?>/<?php echo $this->uri->segment(2, 0); ?>/today">Vandaag</a>
                    </div>
                    <div class="col-md-2">
                        <a class="btn btn-info btn-100" href="/reporting/set/<?php echo $this->uri->segment(3, 0); ?>/<?php echo $this->uri->segment(2, 0); ?>/this-week">Deze week</a>
                    </div>
                    <div class="col-md-2">
                        <a class="btn btn-info btn-100" href="/reporting/set/<?php echo $this->uri->segment(3, 0); ?>/<?php echo $this->uri->segment(2, 0); ?>/this-month">Deze maand</a>
                    </div>
                </div>

            </div>

        </div>

    </div>

</div>

