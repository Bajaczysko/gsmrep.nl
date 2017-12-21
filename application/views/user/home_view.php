<?php
/**
 * Created by PhpStorm.
 * User: Sebastiaan
 * Date: 09-01-16
 * Time: 17:28
 */

?>
<script src="http://cdn.jsdelivr.net/typeahead.js/0.9.3/typeahead.min.js"></script>

<script>
    $(document).ready(function(){
        $("#search").typeahead({
            name : 'sear',
            remote: {
                url : '/jquery_controller/search_product/%QUERY'
            }

        });
    });
</script>

<div class="container clearfix">

    <h2><?php echo $this->language_model->translate('Hi, to make a new bill please select one of the following options:'); ?></h2>


    <div class="row">

        <div class="col-md-4">

            <h3><?php echo $this->language_model->translate('Select on category'); ?></h3>

            <?php echo form_open(current_url()) ?>
            <div class="row">
                <div class="col-xs-12">
                    <div class="form-group">
                        <select class="selectpicker form-control" name="merk" onChange="showToestel(this);">
                            <option value=""><?php echo $this->language_model->translate('Select brand'); ?></option>

                            <?php foreach ($merken->result() as $merk) { ?>
                                <option value="<?php echo $merk->category_id; ?>"><?php echo $merk->name; ?></option>
                            <?php } ?>

                        </select>
                    </div>
                </div>
            </div>

            <!-- This will hold state dropdown -->
            <div id="output1"></div>

            <!-- This will hold city dropdown -->
            <div id="output2"></div>
        </div>
        <div class="col-md-4">

            <h3><?php echo $this->language_model->translate('Search on product') ?></h3>

            <div class="form-group">
            <input type="text" name="search_product" placeholder="Search" id="search" class="form-control">
            </div>

        </div>
        <div class="col-md-4">
            <h3><?php echo $this->language_model->translate('Create custom billing') ?></h3>

            <form>
                <div class="form-group">
                    <input type="text" name="manual_product" placeholder="Productname" id="focusedInput" class="form-control">
                </div>
                <div class="form-group">
                    <input type="number" name="manual_product_qty" placeholder="QTY" id="focusedInput" class="form-control">
                </div>
                <div class="form-group">
                    <input type="number" name="manual_product_price" placeholder="Price" id="focusedInput" class="form-control">
                </div>
                <div class="form-group">
                    <input type="submit" value="Bon aanmaken" name="manual_create" class="btn btn-success btn-100">
                </div>

                <?php echo form_close(); ?>


                <div class="btn btn-primary btn-xs" id="source-button" style="display: none;">&lt; &gt;</div>

            </form>


        </div>

    </div>

</div>


<script>
$(function() {
    $('.selectpicker').selectpicker();
    
});
function showToestel(sel) {
        var country_id = sel.options[sel.selectedIndex].value;
        $("#output1").html( "" );
        $("#output2").html( "" );
        if (country_id.length > 0 ) {

            $("#manual").html( "" );
            $("#change-bon-box").hide();
            $.ajax({
                type: "POST",
                url: "/jquery_controller/get_toestel",
                data: "merk_id="+country_id,
                cache: false,
                beforeSend: function () {
                    $('#output1').html('<img src="loader.gif" alt="" width="24" height="24">');
                },
                success: function(html) {
                    $("#output1").html( html );
                }
            });
        }
    }
</script>

<script>
    function showPart(sel) {
        var state_id = sel.options[sel.selectedIndex].value;
        if (state_id.length > 0 ) {
            $("#change-bon-box").hide();
            $.ajax({
                type: "POST",
                url: "/jquery_controller/get_reparatie",
                data: "reparatie_id="+state_id,
                cache: false,
                beforeSend: function () {
                    $('#output2').html('<img src="loader.gif" alt="" width="24" height="24">');
                },
                success: function(html) {
                    $("#output2").html( html );
                }
            });
        }

    }
</script>