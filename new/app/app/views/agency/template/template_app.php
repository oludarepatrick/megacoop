<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title><?= $title ?> | <?= isset($sub_title) ? $sub_title : ''  ?></title>
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

    <!-- App css -->
    <link data-fancy href="<?= $assets ?>css/icons.min.css" rel="stylesheet" type="text/css" />
    <link data-fancy href="<?= $assets ?>css/app-modern.min.css" rel="stylesheet" type="text/css" id="light-style" />
    <link data-fancy href="<?= $assets ?>css/app-modern-dark.min.css" rel="stylesheet" type="text/css" id="dark-style" />
    <style>
        .bg-gen {
            background-image: url('<?= base_url("assets/images/waves.png") ?>');
            background-repeat: no-repeat;
        }
    </style>
</head>

<body class="loading" data-layout-config='{"leftSideBarTheme":"light","layoutBoxed":false, "leftSidebarCondensed":<?= $this->user->side_bar ?>, "leftSidebarScrollable":false,"darkMode":<?= $this->user->theme ?>, "showRightSidebarOnStart": false}'>

    <!-- <div id="preloader">
        <div id="status">
            <div class="bouncing-loader">
                <div></div>
                <div></div>
                <div></div>
            </div>
        </div>
    </div> -->

    <!-- Begin page -->
    <div class="wrapper">
        <!-- ========== Left Sidebar Start ========== -->
        <div class="left-side-menu">
            <!-- LOGO -->
            <a data-fancy href="<?= base_url('agency/dashboard') ?>" class="logo text-center logo-light">
                <span class="logo-lg">
                    <img src="<?= $assets ?>images/logo/<?= $this->app_settings->logo ?>" alt="" height="36">
                    <!-- <b class="font-weight-bolder font-18"><?= $this->app_settings->app_name ?></b> -->
                </span>
                <span class="logo-sm">
                    <img src="<?= $assets ?>images/logo/<?= $this->app_settings->logo ?>" alt="" height="36">
                </span>
            </a>


            <!-- LOGO -->
            <a data-fancy href="<?= base_url('agency/dashboard') ?>" class="logo text-center logo-dark">
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
                    <li class="side-nav-title side-nav-item text-primary">Navigation</li>

                    <li class="side-nav-item">
                        <a data-fancy href="<?= base_url('agency/dashboard') ?>" class="side-nav-link">
                            <i class="uil-home-alt"></i>
                            <!--<span class="badge badge-success float-right">4</span>-->
                            <span> <?= lang('dashboard') ?> </span>
                        </a>
                    </li>
                </ul>
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

                                <!-- item-->
                                <a data-fancy href="<?= base_url('agency/profile') ?>" class="dropdown-item notify-item bg-light">
                                    <i class="mdi mdi-account-heart mr-1"></i>
                                    <span><?= ucwords($this->user->first_name . ' ' . $this->user->last_name) ?></span>
                                </a>
                                <?php if ($this->ion_auth->is_admin()) { ?>
                                    <a data-fancy href="<?= base_url('dashboard') ?>" class="dropdown-item notify-item">
                                        <i class=" mdi mdi-account-switch mr-1"></i>
                                        <span><?= lang('coop_account') ?></span>
                                    </a>
                                    <a data-fancy href="<?= base_url('member/dashboard') ?>" class="dropdown-item notify-item">
                                        <i class=" mdi mdi-account-switch mr-1"></i>
                                        <span><?= lang('personal_account') ?></span>
                                    </a>
                                <?php } ?>
                                <?php if (!$this->ion_auth->is_admin()) { ?>
                                    <a data-fancy href="<?= base_url('member/dashboard') ?>" class="dropdown-item notify-item">
                                        <i class=" mdi mdi-account-switch mr-1"></i>
                                        <span><?= lang('personal_account') ?></span>
                                    </a>
                                <?php } ?>

                                <!-- item-->
                                <a data-fancy href="<?= base_url('auth/logout') ?>" class="dropdown-item notify-item">
                                    <i class="mdi mdi-logout mr-1"></i>
                                    <span><?= lang('logout') ?></span>
                                </a>

                            </div>
                        </li>

                    </ul>
                    <button class="button-menu-mobile open-left disable-btn">
                        <i class="mdi mdi-menu"></i>
                    </button>
                </div>
                <!-- end Topbar -->

                <!-- Start Content-->
                <div class="container-fluid p-0">
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a data-fancy href="<?= base_url('member/dashboard') ?>"><?= lang('dashboard') ?></a></li>
                                        <!--<li class="breadcrumb-item"><a data-fancy href="<?= base_url('member/' . strtolower($controller)) ?>"><?= $controller ?></a></li>-->
                                        <li class="breadcrumb-item active"><?= $title ?></li>
                                    </ol>
                                </div>
                                <h4 class="page-title"><?= $title ?></h4>
                            </div>
                        </div>
                    </div>
                    <!-- end page title -->

                    <?= $view_page ?>

                </div>
                <!-- container -->

            </div>
            <!-- content -->
            <footer class="footer bg-light border-0 position-fixed p-1" style="z-index:10">
                <div class="container mb-1">
                    <div class="text-center">
                        <div class="row font-12">
                            <a data-fancy href="<?= base_url('member/miscellaneous/minutes') ?>" class="col text-center ">
                                <span class="text-primary d-block">
                                    <i class="mdi mdi-help-circle-outline text-primary font-18"></i>
                                </span>
                                <span class="text-primary font-12 d-block"><?= lang('minutes') ?></span>
                            </a>
                            <a data-fancy href="<?= base_url('member/profile') ?>" class="col text-center ">
                                <span class="text-primary d-block">
                                    <i class=" mdi mdi-account-plus text-danger font-18"></i>
                                </span>
                                <span class="text-danger font-12 d-block">Profile</span>
                            </a>
                            <a data-fancy href="<?= base_url('member/dashboard') ?>" class="col text-center ">
                                <span class="text-primary d-block">
                                    <i class="mdi mdi-home-outline text-muted font-18"></i>
                                </span>
                                <span class="text-muted font-12 d-block">Home</span>
                            </a>
                            <a data-fancy href="<?= base_url('member/miscellaneous/') ?>" class="col text-center ">
                                <span class="text-primary d-block">
                                    <i class="mdi mdi-newspaper text-info font-18"></i>
                                </span>
                                <span class="text-info font-12 d-block text-nowrap"><?= lang('bye_law') ?></span>
                            </a>
                            <a href="" class="col text-center " data-toggle="modal" data-target="#bottom-modal-support">
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

        <!-- ============================================================== -->
        <!-- End Page content -->
        <!-- ============================================================== -->


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
                            <div class="spinner-grow" role="status">
                                <img src="<?= $assets ?>images/logo/<?= $this->app_settings->logo ?>" alt="" height="36">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="bottom-modal-support" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-md modal-bottom">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="bottomModalLabel"><?= lang('support') ?></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <?= form_open('member/dashboard/support', 'class="comment-area-box"') ?>
                    <div class="form-group">
                        <input type="text" name="subject" placeholder="Subject" class="form-control ">
                    </div>
                    <div class="border rounded">
                        <div class="form-group">
                            <textarea rows="4" class="form-control border-0 resize-none" name="message" placeholder="Message" required></textarea>
                        </div>
                        <div class="p-2 bg-light">
                            <div class="float-right">
                                <button type="submit" class="btn btn-sm btn-primary"><i class='uil uil-message mr-1'></i><?= lang('send') ?></button>
                            </div>
                            <div>
                                <a href="#" class="btn btn-sm px-1 btn-light"><i class='uil uil-question-circle'> </i></a>
                            </div>
                        </div>
                    </div>
                    <?= form_close() ?>
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

    <!-- third party:js -->
    <script src="<?= $assets ?>js/vendor/apexcharts.min.js"></script>
    <script src="<?= $assets ?>js/pages/demo.apex-pie.js"></script>

    <!-- third party end -->

    <!-- Chat js -->
    <script src="<?= $assets ?>js/ui/component.chat.js"></script>

    <script src="<?= $assets ?>js/vendor/jquery.rateit.min.js"></script>
    <script src="<?= $assets ?>js/ui/component.rating.js"></script>
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