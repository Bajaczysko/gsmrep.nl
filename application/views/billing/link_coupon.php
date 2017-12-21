<div class="container">

    <?php $attributes = array('class' => 'form-horizontal'); ?>

    <?php echo form_open(current_url(), $attributes); ?>
    <fieldset>
        <legend>Choose which coupon you would like to add</legend>
        <div class="form-group">
            <label for="inputEmail" class="col-lg-2 control-label">Coupon</label>
            <div class="col-lg-10">
                <select class="form-control" name="coupon">
                    <?php foreach($coupons as $coupon): ?>

                        <option value="<?php echo $coupon->id ?>"><?php echo $coupon->name; ?></option>

                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <div class="col-lg-10 col-lg-offset-2">
                <button type="submit" class="btn btn-primary">Add coupon</button>
            </div>
        </div>

    </fieldset>
</form>
</div>