<!Doctype html>
<html>
    <head>
        <title></title>
        <meta name="viewport" content="width=device-width"/>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    </head>

    <body style="color:#57585A; font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; line-height: 1.6em; background-color: #f6f6f6; margin: 0;">
        <div style="text-align: center;padding: 10px;">
            <div style="display: inline-block; border-radius: 3px; width: 400px; height:100%;  background: #0364B8">
                <div style="text-align: left; padding:3%; background: #fff;  border: #f1f1f1 thin solid;">
                    <h2 style="text-align: center; color:#0364B8; ">Password Reset</h2>
                    <!--<h4><?= $this->coop->coop_name ?></h4>-->
                    <p style="padding-top: 10px;">You have initiated a password reset process, kindly proceed by clicking the button below</p>
                    <p>Please ignore if you did not initiate the process</p>
                    <p style=" text-align: center;"><br>
                        <a data-fancy href="<?= base_url($url)?>" style="border-radius: 3px; border: solid 2px #0364B8; color: #0364B8; font-weight: bold; text-decoration: none; padding: 3% 6% 3% 6%;" >Reset Password </a>
                    </p>
                </div>
             
                <div style="padding:5% 0% 5% 0%; background: #e3e3f3; padding: 4% 8% 4% 8%"> 
                    <p>Copyright <?= date('Y') ?>  <?= $this->app_settings->app_name ?></p>
                    <p>Powered by <a style=" text-decoration: none; font-weight: bolder; color: black"><?= $this->app_settings->powered_by ?></a></p>
                </div>
            </div>
        </div>
    </body>

</html>