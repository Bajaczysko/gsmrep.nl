<header
<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/"><img height="100%" src="<?php echo $this->config->base_url('themes/default/images/logo.png'); ?>" </a>
        </div>


        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">



            <ul class="nav navbar-nav">
                <li><a href="/">Home</a></li>
                <?php if ($this->flexi_auth->is_logged_in()): ?>


                    <?php if(($this->flexi_auth->is_privileged('3')) || ($this->flexi_auth->is_privileged('4')) || ($this->flexi_auth->is_privileged('5'))): ?>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Billing <span class="caret"></span></a>
                            <ul class="dropdown-menu" role="menu">

                        <?php if(($this->flexi_auth->is_privileged('3')) || ($this->flexi_auth->is_privileged('4')) || ($this->flexi_auth->is_privileged('5'))): ?>

                            <li><a href="/"><?php echo $this->language_model->translate('New bill'); ?></a></li>

                        <?php endif; ?>

                        <?php if(($this->flexi_auth->is_privileged('4')) || ($this->flexi_auth->is_privileged('5'))): ?>

                            <li><a href="/billing/manage"><?php echo $this->language_model->translate('Manage billings'); ?></a></li>
                            <li><a href="/billing/expected_customers"><?php echo $this->language_model->translate('Expected Customers'); ?></a></li>
                            <li><a href="/billing/daily_check"><?php echo $this->language_model->translate('Daily billing closure'); ?></a></li>


                        <?php endif; ?>

                            </ul>
                        </li>
                    <?php endif; ?>


                    <?php if(($this->flexi_auth->is_privileged('1')) || ($this->flexi_auth->is_privileged('2'))): ?>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Catalog <span class="caret"></span></a>
                            <ul class="dropdown-menu" role="menu">

                                <li><a href="/catalog/products"><?php echo $this->language_model->translate('Manage products') ?></a></li>
                                <li><a href="/catalog/categories"><?php echo $this->language_model->translate('Manage categories') ?></a></li>
                                <li><a href="/catalog/attributes"><?php echo $this->language_model->translate('Attributes') ?></a></li>
                                <li><a href="/media/index"><?php echo $this->language_model->translate('Media') ?></a></li>


                            </ul>
                        </li>
                    <?php endif; ?>


                    <?php if(($this->flexi_auth->is_privileged('6')) || ($this->flexi_auth->is_privileged('7')) || ($this->flexi_auth->is_privileged('8')) || ($this->flexi_auth->is_privileged('14')) || ($this->flexi_auth->is_privileged('15'))): ?>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Customers <span class="caret"></span></a>
                            <ul class="dropdown-menu" role="menu">

                                <?php if(($this->flexi_auth->is_privileged('6')) || ($this->flexi_auth->is_privileged('7')) || ($this->flexi_auth->is_privileged('8'))): ?>

                                    <li><a href="/customers/newcustomer"><?php echo $this->language_model->translate('New customer') ?></a></li>

                                <?php endif; ?>

                                <?php if(($this->flexi_auth->is_privileged('7')) || ($this->flexi_auth->is_privileged('8'))): ?>

                                    <li><a href="/customers/customeroverview"><?php echo $this->language_model->translate('Manage customers') ?></a></li>

                                <?php endif; ?>

                                <?php if(($this->flexi_auth->is_privileged('14')) || ($this->flexi_auth->is_privileged('15'))): ?>

                                    <li><a href="/customers/smartgroups"><?php echo $this->language_model->translate('Smart groups') ?></a></li>

                                <?php endif; ?>

                            </ul>
                        </li>
                    <?php endif; ?>

                    <?php if(($this->flexi_auth->is_privileged('9')) || ($this->flexi_auth->is_privileged('10'))): ?>


                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Reporting <span class="caret"></span></a>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="/reporting/manage"><?php echo $this->language_model->translate('Manage billings'); ?></a></li>

                                <?php if(($this->flexi_auth->is_privileged('9')) || ($this->flexi_auth->is_privileged('10'))): ?>

                                    <?php foreach($this->report_model->load_shops() as $shop): ?>

                                        <li class="dropdown-submenu">
                                            <a tabindex="-1" href="#"><?php echo $shop->name; ?></a>
                                            <ul class="dropdown-menu">
                                                <li><a href="/reporting/turnover/<?php echo $shop->id; ?>/<?php echo url_title(date('Y-m-d', strtotime('-7 days'))); ?>/<?php echo url_title(date('Y-m-d')); ?>">Turnover</a></li>
                                            </ul>
                                        </li>

                                    <?php endforeach; ?>


                                <?php endif; ?>

                            </ul>
                        </li>
                    <?php endif; ?>

                    <?php if(($this->flexi_auth->is_privileged('11')) || ($this->flexi_auth->is_privileged('12')) || ($this->flexi_auth->is_privileged('13'))): ?>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><?php echo $this->lang->line('System'); ?> <span class="caret"></span></a>
                            <ul class="dropdown-menu" role="menu">

                                <?php if(($this->flexi_auth->is_privileged('12')) || ($this->flexi_auth->is_privileged('13'))): ?>

                                    <li><a href="/system/user_management"><?php echo $this->lang->line('User management'); ?></a></li>

                                <?php endif; ?>

                                <?php if($this->flexi_auth->is_privileged('11')): ?>

                                    <li><a href="/system/configuration"><?php echo $this->lang->line('Configuration'); ?></a></li>

                                <?php endif; ?>

                            </ul>
                        </li>
                    <?php endif; ?>

                <?php endif; ?>
            </ul>

            <?php if ($this->flexi_auth->is_logged_in()): ?>
            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <span class="glyphicon glyphicon-user"></span>
                        <strong><?php echo $this->flexi_auth->get_user_identity(); ?></strong>
                        <span class="glyphicon glyphicon-chevron-down"></span>
                    </a>
                    <ul class="dropdown-menu">

                        <li class="divider"></li>
                        <li>
                            <div class="navbar-login navbar-login-session">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <p>
                                            <a href="/auth/logout" class="btn btn-danger btn-block">Uitloggen</a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
            <?php endif; ?>


        </div>
    </div>
</nav>
</header>
