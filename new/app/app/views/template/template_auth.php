<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title><?= lang('login') ?> | <?= $app_settings->app_name ?> | <?= lang('login_note') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="<?= lang('login_note') ?>" name="description" />
    <meta content="<?= $app_settings->app_name ?>" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" data-fancy href="<?= $assets ?>images/logo/<?= $app_settings->favicon ?>">

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

<body class="authentication-bg pb-0" data-layout-config='{"darkMode":false}'>
    <?= $view_page ?>
    <?php if (!empty($message)) { ?>
        <button style="display: none" id="success_triger" type="button" class="btn btn-success" data-toggle="modal" data-target="#success-alert-modal">Success Alert</button>
        <div id="success-alert-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <div class="modal-content modal-filled bg-success">
                    <div class="modal-body p-4">
                        <div class="text-center">
                            <i class="mdi mdi-check-box-outline h1"></i>
                            <h4 class="mt-2">Well Done!</h4>
                            <div class="mt-3">
                                <?= $message ?>
                                <div>
                                    <a data-fancy href="<?= base_url('auth/logout') ?>" class="btn btn-outline-light my-2"><?= lang('login_now') ?></a>
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
                    <?php } ?>

                    <!-- bundle -->
                    <script src="<?= $assets ?>js/vendor.min.js"></script>
                    <script src="<?= $assets ?>js/app.min.js"></script>
                    <script src="<?= $assets ?>js/auth.js"></script>
                    <script>
                        var base_url = "<?= base_url(); ?>";
                    </script>
                    <script>
                        const auth_loader = () => {
                            $('#login').hide();
                            $('#auth').show();
                        };

                        const wait_loader = () => {
                            $('#action_btn').hide();
                            $('#wait').show();
                        };
                    </script>

                    <script>
                        $(document).ready(function() {
                            $('#success_triger').click();
                            $('#error_triger').click();
                            //                $('#migrate_notice_triger').click();
                        });
                    </script>

</body>

</html>