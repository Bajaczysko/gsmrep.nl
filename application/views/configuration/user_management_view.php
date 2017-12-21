<?php
/**
 * Created by PhpStorm.
 * User: Sebastiaan
 * Date: 05-03-16
 * Time: 12:55
 */

?>

<div class="container">

    <div class="row">
        <div class="col-lg-12">

            <div class="bs-component">
                <table class="table table-striped table-hover ">
                    <thead>
                    <tr>
                        <th>UserID</th>
                        <th><?php echo $this->language_model->translate('Username'); ?></th>
                        <th><?php echo $this->language_model->translate('Full name'); ?></th>
                        <th><?php echo $this->language_model->translate('Shop'); ?></th>
                        <th><?php echo $this->language_model->translate('Blocked'); ?></th>
                    </tr>
                    </thead>

                    <?php if(!empty($users)): ?>

                    <tbody>

                    <?php foreach ($users as $user): ?>

                    <tr>
                                        <a href="<?php echo $this->config->base_url().'system/update_user_account/'.$user[$this->flexi_auth->db_column('user_acc', 'id')];?>">

                        <td>1</td>
                        <td>Column content</td>
                        <td><?php echo $user['upro_first_name'];?> <?php echo $user['upro_last_name'];?></td>
                        <td>Column content</td>
                                        </a>

                    </tr>

                    <?php endforeach; ?>
                    </tbody>

                    <?php else: ?>

                    <tbody>
                    <tr style="background: none;">
                        <td><?php echo $this->language_model->translate('No results found'); ?></td>
                    </tr>
                    </tbody>

                    <?php endif; ?>

                </table>
                <div class="btn btn-primary btn-xs" id="source-button" style="display: none;">&lt; &gt;</div></div><!-- /example -->
        </div>
    </div>

</div>
