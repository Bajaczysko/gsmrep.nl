<?php
/**
 * Created by PhpStorm.
 * User: Sebastiaan
 * Date: 16-06-16
 * Time: 21:09
 */

?>

<div class="container">
    <h4>Overzicht vandaag</h4>

    <div class="row">
        <div class="col-sm-6 col-lg-3">
            <div class="widget-simple text-center card-box bg-blue">
                <h3 class="text-white counter"><?php echo $period_total_bills; ?></h3>
                <p class="text-white">Totaal aantal gemaakte bonnen</p>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="widget-simple text-center card-box bg-sun">
                <h3 class="text-white counter">&euro; <?php echo $period_total_pin; ?></h3>
                <p class="text-white">Totaal betaald pin</p>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="widget-simple text-center card-box bg-darkblue">
                <h3 class="text-white counter"><span class="counter">&euro; <?php echo $period_total_contant; ?></span></h3>
                <p class="text-white">Totaal betaald contant</p>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="widget-simple text-center card-box bg-red">
                <h3 class="text-white counter">&euro; <?php echo $period_total_factuur; ?></h3>
                <p class="text-white">Totaal betaald op factuur</p>
            </div>
        </div>
    </div>

    </div>