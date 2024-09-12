<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title><?= $title ?> </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="<?= lang('login_note') ?>" name="description" />
    <meta content="<?= $this->app_settings->app_name ?>" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" data-fancy href="<?= $assets ?>images/logo/<?= $this->app_settings->favicon ?>">

    <!-- third party css -->
    <link data-fancy href="<?= $assets ?>css/vendor/jquery-jvectormap-1.2.2.css" rel="stylesheet" type="text/css" />
    <link data-fancy href="<?= $assets ?>css/vendor/dataTables.bootstrap4.css" rel="stylesheet" type="text/css" />
    <link data-fancy href="<?= $assets ?>css/vendor/responsive.bootstrap4.css" rel="stylesheet" type="text/css" />
    <link data-fancy href="<?= $assets ?>css/vendor/buttons.bootstrap4.css" rel="stylesheet" type="text/css" />
    <link data-fancy href="<?= $assets ?>css/vendor/select.bootstrap4.css" rel="stylesheet" type="text/css" />
    <!-- Summernote css -->
    <link data-fancy href="<?= $assets ?>css/vendor/summernote-bs4.css" rel="stylesheet" type="text/css" />
    <!-- third party css end -->
    <style>
        .bg-gen {
            background-image: url('<?= base_url("assets/images/waves.png") ?>');
            background-repeat: no-repeat;
        }
    </style>
    <!-- App css -->
    <link data-fancy href="<?= $assets ?>css/icons.min.css" rel="stylesheet" type="text/css" />
    <link data-fancy href="<?= $assets ?>css/app-modern.min.css" rel="stylesheet" type="text/css" id="light-style" />
    <link data-fancy href="<?= $assets ?>css/app-modern-dark.min.css" rel="stylesheet" type="text/css" id="dark-style" />
</head>

<body class="loading" data-layout-config='{"leftSideBarTheme":"light","layoutBoxed":false, "leftSidebarCondensed":<?= $this->user->side_bar ?>, "leftSidebarScrollable":false,"darkMode":<?= $this->user->theme ?>, "showRightSidebarOnStart": false}'>

    <!-- Begin page -->
    <div class="wrapper">
        <!-- ========== Left Sidebar Start ========== -->
        <div class="left-side-menu ">
            <!-- LOGO -->
            <a data-fancy href="<?= base_url('dashboard') ?>" class="logo text-center logo-light">
                <span class="logo-lg">
                    <img src="<?= $assets ?>images/logo/<?= $this->app_settings->logo ?>" alt="" height="36">
                    <!-- <b class="font-weight-bolder font-18"><?= $this->app_settings->app_name ?></b> -->
                </span>
                <span class="logo-sm">
                    <img src="<?= $assets ?>images/logo/<?= $this->app_settings->logo ?>" alt="" height="36">
                </span>
            </a>

            <!-- LOGO -->
            <a data-fancy href="<?= base_url('dashboard') ?>" class="logo text-center logo-dark">
                <span class="logo-lg">
                    <img src="<?= $assets ?>images/logo/<?= $this->app_settings->logo ?>" alt="" height="36">
                    <!-- <b class="font-weight-bolder font-18"><?= $this->app_settings->app_name ?></b> -->
                </span>
                <span class="logo-sm">
                    <img src="<?= $assets ?>images/logo/<?= $this->app_settings->logo ?>" alt="" height="36">
                </span>
            </a>

            <div class="h-100" id="left-side-menu-container" data-simplebar>

                <!--- Sidemenu -->
                <ul class="metismenu side-nav">
                    <li class="side-nav-item">
                        <a data-fancy href="<?= base_url('dashboard') ?>" class="side-nav-link">
                            <i class="uil-home-alt"></i>
                            <span> <?= lang('dashboard') ?> </span>
                        </a>
                    </li>
                    <li class="side-nav-title side-nav-item text-primary">Application</li>
                    <li class="side-nav-item">
                        <a href="javascript: void(0);" class="side-nav-link">
                            <i class="uil-briefcase"></i>
                            <span class="badge badge-primary float-right">4</span>
                            <span> <?= lang('registration') ?> </span>
                        </a>
                        <ul class="side-nav-second-level" aria-expanded="false">
                            <li>
                                <a data-fancy href="<?= base_url('registration') ?>"><?= lang('members') ?></a>
                            </li>
                            <li>
                                <a data-fancy href="<?= base_url('registration/add_member') ?>"><?= lang('add_member') ?></a>
                            </li>
                            <li>
                                <a data-fancy href="<?= base_url('memberexit') ?>"><?= lang('exit_request') ?></a>
                            </li>
                            <li>
                                <a data-fancy href="<?= base_url('memberexit/exited_members') ?>"><?= lang('exited_members') ?></a>
                            </li>
                        </ul>
                    </li>
                    <li class="side-nav-item">
                        <a href="javascript: void(0);" class="side-nav-link">
                            <i class="uil-briefcase"></i>
                            <span class="badge badge-primary float-right">3</span>
                            <span> <?= lang('savings') ?> </span>
                        </a>
                        <ul class="side-nav-second-level" aria-expanded="false">
                            <li>
                                <a data-fancy href="<?= base_url('savings') ?>"><?= lang('savings') ?></a>
                            </li>
                            <li>
                                <a data-fancy href="<?= base_url('savings/add') ?>"><?= lang('add_single_saving') ?></a>
                            </li>
                            <li>
                                <a data-fancy href="<?= base_url('savings/add_batch') ?>"><?= lang('add_batch_saving') ?></a>
                            </li>
                        </ul>
                    </li>
                    <li class="side-nav-item">
                        <a href="javascript: void(0);" class="side-nav-link">
                            <i class=" uil-moneybag"></i>
                            <span class="badge badge-primary float-right">8</span>
                            <span> <?= lang('loan') ?> </span>
                        </a>
                        <ul class="side-nav-second-level" aria-expanded="false">
                            <li>
                                <a data-fancy href="<?= base_url('loan/add') ?>"><?= lang('add_single_loan') ?></a>
                            </li>
                            <li>
                                <a data-fancy href="<?= base_url('loan') ?>"><?= lang('requested_loans') ?></a>
                            </li>
                            <li>
                                <a data-fancy href="<?= base_url('loan/approved_loans') ?>"><?= lang('approved_loans') ?></a>
                            </li>
                            <li>
                                <a data-fancy href="<?= base_url('loan/disbursed_loans') ?>"><?= lang('disbursed_loans') ?></a>
                            </li>
                            <li>
                                <a data-fancy href="<?= base_url('loan/finished_loans') ?>"><?= lang('finished_loans') ?></a>
                            </li>
                            <li>
                                <a data-fancy href="<?= base_url('loanrepayment') ?>"><?= lang('loan_repayment') ?></a>
                            </li>
                            <li>
                                <a data-fancy href="<?= base_url('loanrepayment/due_payment') ?>"><?= lang('due_payment') ?></a>
                            </li>
                            <li>
                                <a data-fancy href="<?= base_url('loanrepayment/add_batch') ?>"><?= lang('add_batch_repayment') ?></a>
                            </li>
                        </ul>
                    </li>
                    <li class="side-nav-item">
                        <a href="javascript: void(0);" class="side-nav-link">
                            <i class="uil-gift"></i>
                            <span class="badge badge-primary float-right">7</span>
                            <span> <?= lang('credit_sales') ?> </span>
                        </a>
                        <ul class="side-nav-second-level" aria-expanded="false">
                            <li>
                                <a data-fancy href="<?= base_url('creditsales/order_product') ?>"><?= lang('order') . ' ' . lang('credit_sales') ?></a>
                            </li>
                            <li>
                                <a data-fancy href="<?= base_url('creditsales') ?>"><?= lang('requested_credit_sales') ?></a>
                            </li>
                            <li>
                                <a data-fancy href="<?= base_url('creditsales/approved') ?>"><?= lang('approved_credit_sales') ?></a>
                            </li>
                            <li>
                                <a data-fancy href="<?= base_url('creditsales/supplied') ?>"><?= lang('supplied_products') ?></a>
                            </li>
                            <ul class="side-nav-second-level" aria-expanded="false">
                                <li class="side-nav-item">
                                    <a href="javascript: void(0);" aria-expanded="false"><?= lang('ordered_products') ?>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul class="side-nav-third-level" aria-expanded="false">
                                        <li>
                                            <a data-fancy href="<?= base_url('products/ordered_products') ?>"><?= lang('in_app') ?></a>
                                        </li>
                                        <li>
                                            <a data-fancy href="<?= base_url('products/market_hub') ?>"><?= lang('market_hub') ?></a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                            <li>
                                <a data-fancy href="<?= base_url('creditsalesrepayment') ?>"><?= lang('repayment') ?></a>
                            </li>
                            <li>
                                <a data-fancy href="<?= base_url('creditsalesrepayment/add_batch') ?>"><?= lang('add_batch_repayment') ?></a>
                            </li>
                        </ul>
                    </li>
                    <li class="side-nav-item">
                        <a href="javascript: void(0);" class="side-nav-link">
                            <i class="uil-money-withdraw"></i>
                            <span class="badge badge-primary float-right">3</span>
                            <span> <?= lang('withdrawal') ?> </span>
                        </a>
                        <ul class="side-nav-second-level" aria-expanded="false">
                            <li>
                                <a data-fancy href="<?= base_url('withdrawal') ?>"><?= lang('withdrawals') ?></a>
                            </li>
                            <li>
                                <a data-fancy href="<?= base_url('withdrawal/add') ?>"><?= lang('add_withdrawal') ?></a>
                            </li>
                            <li>
                                <a data-fancy href="<?= base_url('withdrawal/pending_withdrawal') ?>"><?= lang('pending_withdrawal') ?></a>
                            </li>
                        </ul>
                    </li>
                    <li class="side-nav-item">
                        <a href="javascript: void(0);" class="side-nav-link">
                            <i class="uil-calcualtor"></i>
                            <span class="badge badge-primary float-right">1</span>
                            <span> <?= lang('dividend') ?> </span>
                        </a>
                        <ul class="side-nav-second-level" aria-expanded="false">
                            <li>
                                <a data-fancy href="<?= base_url('dividend') ?>"><?= lang('generate') . ' ' . lang('dividend') ?></a>
                            </li>
                        </ul>
                    </li>
                    <li class="side-nav-item">
                        <a data-fancy href="<?= base_url('wallet') ?>" class="side-nav-link">
                            <i class="uil-wallet"></i>
                            <span> <?= lang('wallet_trans') ?></span>
                        </a>
                    </li>
                    <li class="side-nav-item">
                        <a href="javascript: void(0);" class="side-nav-link">
                            <i class="uil-comment-alt-message"></i>
                            <span class="badge badge-primary float-right">2</span>
                            <span> <?= lang('communication') ?></span>
                        </a>
                        <ul class="side-nav-second-level" aria-expanded="false">
                            <li>
                                <a data-fancy href="<?= base_url('communication') ?>"><?= lang('broadcast') ?></a>
                            </li>
                            <li>
                                <a data-fancy href="<?= base_url('communication/sms_report') ?>"><?= lang('sms_report') ?></a>
                            </li>
                        </ul>
                    </li>
                    <li class="side-nav-item">
                        <a data-fancy href="<?= base_url('statement') ?>" class="side-nav-link">
                            <i class="uil-book-alt"></i>
                            <span> <?= lang('statement') ?></span>
                        </a>
                    </li>
                    <li class="side-nav-item">
                        <a data-fancy href="<?= base_url('investment') ?>" class="side-nav-link">
                            <i class="  uil-wallet"></i>
                            <span> <?= lang('investment') ?></span>
                        </a>
                    </li>
                    <li class="side-nav-item">
                        <a href="javascript: void(0);" class="side-nav-link">
                            <i class="uil-book"></i>
                            <span class="badge badge-primary float-right">5</span>
                            <span> <?= lang('accounting') ?></span>
                        </a>
                        <ul class="side-nav-second-level" aria-expanded="false">
                            <li>
                                <a data-fancy href="<?= base_url('coa') ?>"><?= lang('coa') ?></a>
                            </li>
                            <li>
                                <a data-fancy href="<?= base_url('accounting') ?>"><?= lang('ledger_entry') ?></a>
                            </li>
                            <li>
                                <a data-fancy href="<?= base_url('accounting/journal') ?>"><?= lang('journals') ?></a>
                            </li>
                            <li>
                                <a data-fancy href="<?= base_url('accounting/books') ?>"><?= lang('books') ?></a>
                            </li>
                            <li>
                                <a data-fancy href="<?= base_url('accounting/auto_postings') ?>"><?= lang('auto_posting') ?></a>
                            </li>
                        </ul>
                    </li>

                    <li class="side-nav-item">
                        <a href="javascript: void(0);" class="side-nav-link">
                            <i class="uil-user-circle"></i>
                            <span class="badge badge-primary float-right">3</span>
                            <span> <?= lang('miscellaneous') ?> </span>
                        </a>
                        <ul class="side-nav-second-level" aria-expanded="false">
                            <li>
                                <a data-fancy href="<?= base_url('miscellaneous') ?>"><?= lang('bye_law') ?></a>
                            </li>
                            <li>
                                <a data-fancy href="<?= base_url('miscellaneous/minutes') ?>"><?= lang('minutes') ?></a>
                            </li>
                            <li>
                                <a data-fancy href="<?= base_url('miscellaneous/training') ?>"><?= lang('training') ?></a>
                            </li>
                        </ul>
                    </li>
                    <li class="side-nav-item">
                        <a href="javascript: void(0);" class="side-nav-link">
                            <i class="uil-user"></i>
                            <span class="badge badge-primary float-right">2</span>
                            <span> <?= lang('agent') ?> </span>
                        </a>
                        <ul class="side-nav-second-level" aria-expanded="false">
                            <li>
                                <a data-fancy href="<?= base_url('agents') ?>"><?= lang('agents') ?></a>
                            </li>
                            <li>
                                <a data-fancy href="<?= base_url('agents/transactions') ?>"><?= lang('tranx') ?></a>
                            </li>
                        </ul>
                    </li>
                    <li class="side-nav-item">
                        <a href="javascript: void(0);" class="side-nav-link">
                            <i class=" uil-monitor"></i>
                            <span class="badge badge-primary float-right">2</span>
                            <span> <?= lang('user_trails') ?> </span>
                        </a>
                        <ul class="side-nav-second-level" aria-expanded="false">
                            <li>
                                <a data-fancy href="<?= base_url('userstrails') ?>"><?= lang('user_login') ?></a>
                            </li>
                            <li>
                                <a data-fancy href="<?= base_url('userstrails/activities') ?>"><?= lang('user_activities') ?></a>
                            </li>
                        </ul>
                    </li>

                    <li class="side-nav-title side-nav-item text-primary">Configurations</li>

                    <li class="side-nav-item">
                        <a href="javascript: void(0);" class="side-nav-link">
                            <i class="uil-layer-group"></i>
                            <span class="badge badge-primary float-right">4</span>
                            <span> <?= lang('categories') ?> </span>
                        </a>
                        <ul class="side-nav-second-level" aria-expanded="false">
                            <li>
                                <a data-fancy href="<?= base_url('categories/index') ?>"><?= lang('savings_type') ?></a>
                            </li>
                            <li>
                                <a data-fancy href="<?= base_url('categories/loan_type') ?>"><?= lang('loan_type') ?></a>
                            </li>
                            <ul class="side-nav-second-level" aria-expanded="false">
                                <li class="side-nav-item">
                                    <a href="javascript: void(0);" aria-expanded="false"><?= lang('product') ?>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul class="side-nav-third-level" aria-expanded="false">
                                        <li>
                                            <a data-fancy href="<?= base_url('products/vendors') ?>"><?= lang('vendors') ?></a>
                                        </li>
                                        <li>
                                            <a data-fancy href="<?= base_url('categories/product_type') ?>"><?= lang('product_type') ?></a>
                                        </li>
                                        <li>
                                            <a data-fancy href="<?= base_url('products') ?>"><?= lang('products') ?></a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                            <li>
                                <a data-fancy href="<?= base_url('categories/investment_type') ?>"><?= lang('investment_type') ?></a>
                            </li>
                        </ul>
                    </li>
                    <li class="side-nav-item">
                        <a data-fancy href="<?= base_url('users') ?>" class="side-nav-link">
                            <i class="uil-lock-open-alt"></i>
                            <span> <?= lang('user_role') ?> </span>
                        </a>
                    </li>
                    <li class="side-nav-item">
                        <a data-fancy href="<?= base_url('settings') ?>" class="side-nav-link">
                            <i class="uil-bright"></i>
                            <span> <?= lang('settings') ?> </span>
                        </a>
                    </li>
                </ul>

                <!-- Help Box -->
                <div class="help-box text-white text-center">
                    <img src="<?= $assets ?>images/help-icon.svg" height="90" alt="Helper Icon Image" />
                    <h5 class="mt-3"><?= lang('upload_record') ?></h5>
                    <p class="mb-3"><?= lang('upload_record_note') ?></p>
                    <a data-fancy href="<?= base_url('migration') ?>" class="btn btn-light btn-sm"><?= lang('start_now') ?></a>
                </div>
                <!-- end Help Box -->
                <!-- End Sidebar -->

                <div class="clearfix"></div>

            </div>
            <!-- Sidebar -left -->

        </div>
        <!-- Left Sidebar End -->

        <!-- ============================================================== -->
        <!-- Start Page Content here -->
        <!-- ============================================================== -->

        <div class="content-page">
            <div class="content">
                <!-- Topbar Start -->
                <div class="navbar-custom">
                    <ul class="list-unstyled topbar-right-menu float-right mb-0">
                        <li class="dropdown notification-list d-lg-none">
                            <a class="nav-link dropdown-toggle arrow-none" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                                <i class="dripicons-search noti-icon"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-animated dropdown-lg p-0">
                                <form class="p-3">
                                    <input type="text" class="form-control" placeholder="Search ..." aria-label="Recipient's username">
                                </form>
                            </div>
                        </li>
                        <li class="dropdown notification-list topbar-dropdown">
                            <a class="nav-link dropdown-toggle arrow-none" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                                <img src="<?= $assets ?>images/flags/us.jpg" alt="user-image" class="mr-0 mr-sm-1" height="12">
                                <span class="align-middle d-none d-sm-inline-block">English</span> <i class="mdi mdi-chevron-down d-none d-sm-inline-block align-middle"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-animated topbar-dropdown-menu">

                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item notify-item">
                                    <img src="<?= $assets ?>images/flags/us.jpg" alt="user-image" class="mr-1" height="12"> <span class="align-middle">English</span>
                                </a>
                            </div>
                        </li>

                        <li class="dropdown notification-list">
                            <a class="nav-link dropdown-toggle arrow-none" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                                <i class="dripicons-bell noti-icon"></i>
                                <?php if ($this->exco_notification) { ?>
                                    <span class="noti-icon-badge"></span>
                                <?php } ?>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-animated dropdown-lg">

                                <!-- item-->
                                <div class="dropdown-item noti-title">
                                    <h5 class="m-0">
                                        <span class="float-right">
                                            <a data-fancy href="<?= base_url('settings/clear_notification') ?>" class="text-dark">
                                                <small class="badge badge-danger-lighten">
                                                    <i class="mdi mdi-trash-can"></i> Clear All
                                                </small>
                                            </a>
                                        </span><?= lang('notification') ?>
                                    </h5>
                                </div>


                                <div style="max-height: 230px;" data-simplebar>
                                    <?php foreach ($this->exco_notification as $note) { ?>
                                        <a data-fancy href="<?= $note->url ?>" class="dropdown-item notify-item">
                                            <div class="notify-icon bg-info">
                                                <i class="mdi mdi-account-plus"></i>
                                            </div>

                                            <p class="notify-details"><?= ucwords($note->from) ?>.
                                                <small class="text-primary "><?= $note->description ?></small>
                                                <small class="text-muted"><?= $this->utility->just_date($note->created_on) ?></small>
                                            </p>

                                        </a>
                                    <?php } ?>
                                </div>
                            </div>
                        </li>

                        <li class="dropdown notification-list d-none d-sm-inline-block">
                            <a class="nav-link dropdown-toggle arrow-none" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                                <i class="dripicons-view-apps noti-icon"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-animated dropdown-lg p-0">

                                <div class="p-2">
                                    <div class="row no-gutters">
                                        <?php if ($this->app_settings->monetization == 'subscription') { ?>
                                            <div class="col">
                                                <a class="dropdown-icon-item" data-fancy href="<?= base_url('subscription') ?>">
                                                    <i class="uil uil-money-insert font-20"> </i>
                                                    <span><?= lang('subscription') ?></span>
                                                </a>
                                            </div>
                                            <div class="col">
                                                <a class="dropdown-icon-item" data-fancy href="<?= base_url('subscription/history') ?>">
                                                    <i class="uil uil-book font-20"> </i>
                                                    <span><?= lang('history') ?></span>
                                                </a>
                                            </div>
                                        <?php } ?>

                                        <?php if ($this->app_settings->monetization == 'licence') { ?>
                                            <div class="col">
                                                <a class="dropdown-icon-item" data-fancy href="<?= base_url('licence') ?>">
                                                    <i class="uil uil-money-insert font-20"> </i>
                                                    <span><?= lang('licence') ?></span>
                                                </a>
                                            </div>
                                            <div class="col">
                                                <a class="dropdown-icon-item" data-fancy href="<?= base_url('licence/history') ?>">
                                                    <i class="uil uil-book font-20"> </i>
                                                    <span><?= lang('history') ?></span>
                                                </a>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>

                            </div>
                        </li>

                        <li class="notification-list">
                            <a class="nav-link right-bar-toggle" href="javascript: void(0);">
                                <i class="uil uil-paint-tool noti-icon"></i>
                            </a>
                        </li>

                        <li class="dropdown notification-list">
                            <a class="nav-link dropdown-toggle nav-user arrow-none mr-0 border-0" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                                <span class="account-user-avatar">
                                    <?php if (!$this->coop->logo) { ?>
                                        <img src="<?= $assets ?>images/logo/coopify/default_logo.png" alt="coop-logo" class="rounded-circle">
                                    <?php } ?>

                                    <?php if ($this->coop->logo) { ?>
                                        <img src="<?= $assets ?>images/logo/coop/<?= $this->coop->logo ?>" alt="coop" class="rounded-circle">
                                    <?php } ?>

                                </span>
                                <span>
                                    <span class="account-user-name"><?= strtoupper($this->coop->coop_initial) ?></span>
                                    <span class="account-position"><?= $this->coop->coop_name ?></span>
                                </span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-animated topbar-dropdown-menu profile-dropdown">
                                <!-- item-->
                                <div class=" dropdown-header noti-title">
                                    <h6 class="text-overflow m-0">Welcome !</h6>
                                </div>

                                <?php if ($this->app_settings->monetization == 'subscription') { ?>
                                    <a data-fancy href="<?= base_url('subscription') ?>" class="dropdown-item notify-item">
                                        <i class="mdi mdi-account-circle mr-1"></i>
                                        <span><?= lang('subscription') ?></span>
                                    </a>
                                <?php } ?>

                                <!-- item-->
                                <a data-fancy href="<?= base_url('settings') ?>" class="dropdown-item notify-item">
                                    <i class="mdi mdi-account-edit mr-1"></i>
                                    <span><?= lang('settings') ?></span>
                                </a>

                                <!-- item-->
                                <a data-fancy href="<?= base_url('support') ?>" class="dropdown-item notify-item">
                                    <i class="mdi mdi-lifebuoy mr-1"></i>
                                    <span><?= lang('support') ?></span>
                                </a>

                                <?php if ($this->ion_auth->is_admin()) { ?>
                                    <a data-fancy href="<?= base_url('member/dashboard') ?>" class="dropdown-item notify-item">
                                        <i class=" mdi mdi-account-switch mr-1"></i>
                                        <span><?= lang('personal_account') ?></span>
                                    </a>
                                    <a data-fancy href="<?= base_url('agency/dashboard') ?>" class="dropdown-item notify-item">
                                        <i class=" mdi mdi-account-switch mr-1"></i>
                                        <span><?= lang('agent_account') ?></span>
                                    </a>
                                <?php } ?>

                                <!-- item-->
                                <a data-fancy href="<?= base_url('auth/logout') ?>" class="dropdown-item notify-item">
                                    <i class="mdi mdi-logout mr-1"></i>
                                    <span>Logout</span>
                                </a>

                            </div>
                        </li>

                    </ul>
                    <button class="button-menu-mobile open-left disable-btn">
                        <i class="mdi mdi-menu"></i>
                    </button>
                    <div class="app-search dropdown d-none d-lg-block">
                        <form>
                            <div class="input-group">
                                <input oninput="page_search(this)" type="text" class="form-control dropdown-toggle" placeholder="What would you like to do?" id="top-search">
                                <span class="mdi mdi-magnify search-icon"></span>
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="button">
                                        <div class="spinner-grow spinner-grow-sm" role="status"> ? </div>
                                    </button>
                                </div>
                            </div>

                        </form>
                        <div class="dropdown-menu dropdown-menu-animated dropdown-lg" id="search-dropdown">
                            <!-- item-->
                            <div class="dropdown-header noti-title">
                                <h5 class="text-overflow mb-2">Found <span class="text-danger" id=total-result>0</span> results</h5>
                            </div>
                            <div id="page-search">

                            </div>

                        </div>
                    </div>
                </div>
                <!-- end Topbar -->

                <!-- Start Content-->
                <div class="container-fluid p-0">

                    <?php if ($title == 'Dashboard') { ?>
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box">
                                    <div class="page-title-right">
                                        <form class="form-inline">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <input type="text" class="form-control form-control-light" id="dash-daterange">
                                                    <div class="input-group-append">
                                                        <span data-toggle="tooltip" title="<?= lang('today') ?>" class="input-group-text bg-primary border-primary text-white">
                                                            <i class="mdi mdi-calendar-range font-13"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <a href="" data-toggle="tooltip" title="<?= lang('refresh') ?>" class="btn btn-primary ml-2">
                                                <i class="mdi mdi-autorenew"></i>
                                            </a>
                                            <a href="javascript: void" class="btn btn-primary ml-1">
                                                <i class="mdi mdi-filter-variant"></i>
                                            </a>
                                        </form>
                                    </div>
                                    <h4 class="page-title"><?= $title ?></h4>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <?php if ($title != 'Dashboard') { ?>
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box">
                                    <div class="page-title-right">
                                        <ol class="breadcrumb m-0">
                                            <li class="breadcrumb-item"><a data-fancy href="<?= base_url('dashboard') ?>"><?= lang('dashboard') ?></a></li>
                                            <li class="breadcrumb-item"><a data-fancy href="<?= base_url(strtolower($controller)) ?>"><?= $controller ?></a></li>
                                            <li class="breadcrumb-item active"><?= $title ?></li>
                                        </ol>
                                    </div>
                                    <h4 class="page-title"><?= $title ?></h4>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <!-- end page title -->
                    <?= $view_page ?>


                    <div id="component">

                    </div>
                </div>
                <!-- container -->

            </div>
            <!-- content -->

            <!-- Footer Start -->
            <!--                <footer class="footer">
                                    <div class="container-fluid">
                                        <div class="row">
                                            <div class="col-md-6">
                <?= date('Y') ?> ©  - <?= $this->app_settings->powered_by ?>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="text-md-right footer-links d-none d-md-block">
                                                    <a href="javascript: void(0);">About</a>
                                                    <a href="javascript: void(0);">Support</a>
                                                    <a href="javascript: void(0);">Contact Us</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </footer>-->

            <footer class="footer bg-light border-0 position-fixed p-1" style="z-index:10">
                <div class="container">
                    <div class="text-center">
                        <div class="row font-12">
                            <a href="" class="col text-center ">
                                <span class="text-primary d-block">
                                    <i class="mdi mdi-help-circle-outline text-primary font-18"></i>
                                </span>
                                <span class="text-primary font-12 d-block">Help</span>
                            </a>
                            <a data-fancy href="<?= base_url('settings') ?>" class="col text-center ">
                                <span class="text-primary d-block">
                                    <i class=" mdi mdi-settings-transfer-outline text-danger font-18"></i>
                                </span>
                                <span class="text-danger font-12 d-block">Settings</span>
                            </a>
                            <a data-fancy href="<?= base_url('dashboard') ?>" class="col text-center ">
                                <span class="text-primary d-block">
                                    <i class="mdi mdi-home-outline text-muted font-18"></i>
                                </span>
                                <span class="text-muted font-12 d-block">Home</span>
                            </a>
                            <a href="" class="col text-center ">
                                <span class="text-primary d-block">
                                    <i class="mdi mdi-newspaper text-info font-18"></i>
                                </span>
                                <span class="text-info font-12 d-block">Blog</span>
                            </a>
                            <a data-fancy href="<?= base_url('support') ?>" class="col text-center ">
                                <span class="text-primary d-block">
                                    <i class="mdi mdi-headset text-success font-18"></i>
                                </span>
                                <span class="text-success font-12 d-block"><?= lang('support') ?></span>
                            </a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <!-- END wrapper -->

    <!-- Right Sidebar -->
    <div class="right-bar">

        <div class="rightbar-title">
            <a href="javascript:void(0);" class="right-bar-toggle float-right">
                <i class="dripicons-cross noti-icon"></i>
            </a>
            <h5 class="m-0">Settings</h5>
        </div>

        <div class="rightbar-content h-100" data-simplebar>

            <div class="p-3">
                <div class="alert alert-warning" role="alert">
                    <strong>Customize </strong> the overall color scheme, sidebar menu, etc.
                </div>

                <!-- Settings -->
                <h5 class="mt-3">Color Scheme</h5>
                <hr class="mt-1" />

                <div class="custom-control custom-switch mb-1">
                    <input type="radio" class="custom-control-input" name="color-scheme-mode" value="light" id="light-mode-check" onclick="change_theme('false')" />
                    <label class="custom-control-label" for="light-mode-check">Light Mode</label>
                </div>

                <div class="custom-control custom-switch mb-1">
                    <input type="radio" class="custom-control-input" name="color-scheme-mode" value="dark" id="dark-mode-check" onclick="change_theme('true')" />
                    <label class="custom-control-label" for="dark-mode-check">Dark Mode</label>
                </div>

                <!-- Left Sidebar-->
                <h5 class="mt-4">Left Sidebar</h5>
                <hr class="mt-1" />

                <div class="custom-control custom-switch mb-1">
                    <input type="radio" class="custom-control-input" name="compact" value="fixed" id="fixed-check" checked />
                    <label class="custom-control-label" onclick="change_side_bar('false')" for="fixed-check">Scrollable</label>
                </div>

                <div class="custom-control custom-switch mb-1">
                    <input type="radio" class="custom-control-input" name="compact" value="condensed" id="condensed-check" />
                    <label class="custom-control-label" onclick="change_side_bar('true')" for="condensed-check">Condensed</label>
                </div>

                <a class="btn btn-primary btn-block mt-4" data-fancy href="<?= base_url('settings') ?>"><?= lang('settings') ?></a>

            </div> <!-- end padding-->

        </div>
    </div>
    <!-- Success Alert Modal -->
    <?php if (!empty($message)) { ?>
        <button style="display: none" id="success_triger" type="button" class="btn btn-success" data-toggle="modal" data-target="#success-alert-modal">Success Alert</button>
        <div id="success-alert-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <div class="modal-content modal-filled bg-success">
                    <div class="modal-body p-4">
                        <div class="text-center">
                            <i class="dripicons-checkmark h1"></i>
                            <h4 class="mt-2">Well Done!</h4>
                            <div class="mt-3">
                                <?= $message ?>
                                <div>
                                    <button type="button" class="btn btn-light my-2" data-dismiss="modal"><?= lang('close') ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
    <?php if (!empty($error)) { ?>
        <button style="display: none" id="error_triger" type="button" class="btn btn-danger" data-toggle="modal" data-target="#danger-alert-modal">Danger Alert</button>
        <div id="danger-alert-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <div class="modal-content modal-filled bg-danger">
                    <div class="modal-body p-4">
                        <div class="text-center">
                            <i class="dripicons-wrong h1"></i>
                            <h4 class="mt-2">Oh snap!</h4>
                            <div class="mt-3">
                                <?= $error ?>
                                <div>
                                    <button type="button" class="btn btn-light my-2" data-dismiss="modal"><?= lang('close') ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
    <div id="error_notify" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content modal-filled bg-danger">
                <div class="modal-body p-4">
                    <div class="text-center">
                        <i class="dripicons-wrong h1"></i>
                        <h4 class="mt-2">Oh snap!</h4>
                        <div class="mt-3">
                            <span id="error_text"> </span>
                            <div>
                                <button type="button" class="btn btn-light my-2" data-dismiss="modal"><?= lang('close') ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fancy-loader fade" id="loading" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered ">
            <div class="modal-content modal-filled bg-transparent">
                <div class="modal-body ">
                    <div class="row justify-content-center text-center">
                        <div class="col-6  rounded p-3 ">
                            <div class="spinner-grow bg-transparent" role="status">
                                <img src="<?= $assets ?>images/logo/<?= $this->app_settings->logo ?>" alt="" height="36">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="rightbar-overlay"></div>
    <!-- /Right-bar -->

    <!-- bundle -->
    <script src="<?= $assets ?>js/vendor.min.js"></script>
    <script src="<?= $assets ?>js/app.min.js"></script>

    <!-- third party js -->
    <script src="<?= $assets ?>js/vendor/apexcharts.min.js"></script>
    <script src="<?= $assets ?>js/vendor/jquery-jvectormap-1.2.2.min.js"></script>
    <script src="<?= $assets ?>js/vendor/jquery-jvectormap-world-mill-en.js"></script>
    <script src="<?= $assets ?>js/vendor/dropzone.min.js"></script>
    <!-- init js -->
    <script src="<?= $assets ?>js/ui/component.fileupload.js"></script>
    <script src="<?= $assets ?>js/vendor/summernote-bs4.min.js"></script>
    <!-- Summernote demo -->
    <script src="<?= $assets ?>js/pages/demo.summernote.js"></script>


    <!-- third party js ends -->
    <script src="<?= $assets ?>js/vendor/jquery.dataTables.min.js"></script>
    <script src="<?= $assets ?>js/vendor/dataTables.bootstrap4.js"></script>
    <script src="<?= $assets ?>js/vendor/dataTables.responsive.min.js"></script>
    <script src="<?= $assets ?>js/vendor/responsive.bootstrap4.min.js"></script>
    <script src="<?= $assets ?>js/vendor/dataTables.buttons.min.js"></script>
    <script src="<?= $assets ?>js/vendor/buttons.bootstrap4.min.js"></script>
    <script src="<?= $assets ?>js/vendor/buttons.html5.min.js"></script>
    <script src="<?= $assets ?>js/vendor/buttons.flash.min.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="<?= $assets ?>js/vendor/buttons.print.min.js"></script>
    <script src="<?= $assets ?>js/vendor/dataTables.keyTable.min.js"></script>
    <script src="<?= $assets ?>js/vendor/dataTables.select.min.js"></script>
    <script src="<?= $assets ?>js/pages/demo.datatable-init.js"></script>

    <script src="<?= $assets ?>js/vendor/apexcharts.min.js"></script>
    <script src="<?= $assets ?>js/pages/demo.apex-pie_1.js"></script>
    <!-- demo app -->
    <script src="<?= $assets ?>js/pages/demo.dashboard.js"></script>
    <script src="<?= $assets ?>js/pass.js"> </script>
    <script src="<?= $assets ?>js/custom.js"></script>
    <!-- end demo js-->
    <script>
        $(document).ready(function() {
            $('#success_triger').click();
            $('#error_triger').click();
            $('#migrate_notice_triger').click();
        });
    </script>
    <script>
        var base_url = "<?= base_url(); ?>";
    </script>
</body>

</html>