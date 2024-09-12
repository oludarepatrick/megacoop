<!Doctype html>
<html>
    <head>
        <title></title>
        <meta name="viewport" content="width=device-width"/>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    </head>

    <body style="color:#57585A; font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; line-height: 1.6em; background-color: #f6f6f6; margin: 0;">
        <div style="text-align: center;padding: 10px;">
            <div style="display: inline-block; border-radius: 3px; width: 400px; height:100%;  background: #0364B8">
                <div style=" padding: 0% 0% 10% 0%; background: white;">
                    <img style="width:100%;" src="<?= base_url("assets/images/waves.png") ?>">
                    <?php if($this->coop->logo){ ?>
                    <img style="width:15%;" src="<?= base_url("assets/images/logo/coop/".$this->coop->logo) ?>">
                    <?php } ?>
                    <?php if(!$this->coop->logo){ ?>
                    <img style="width:15%;" src="<?= base_url("assets/images/logo/coopify/default_logo.png") ?>">
                    <?php } ?>
                    <h3 style="color:#0364B8; margin: 1% 0% 0% 0%;"><?= ucwords($this->coop->coop_name)?></h3>
                    <p style="padding: 0% 10% 2% 10%; font-size: 14px;">Smartly automate all your cooperative operations!</p>
                    <a data-fancy href="<?= base_url() ?>" style="border-radius: 3px; border: solid thin #0364B8; color: #0364B8; font-weight: bold; text-decoration: none; padding: 2% 4% 2% 4%;" >My Account </a>
                </div>
                <div style="text-align: left; padding:3%; background: #fff;  border: #f1f1f1 thin solid;">
                    <h2 style="text-align: center; color:#0364B8; ">Member Account Creation!</h2>
                     <p style="font-size:14px">Hello <?= ucwords($name) ?> <br> MemberID: <?= $member_id ?></p>
                    <p style="padding-top: 10px;">I am happy to inform you that your account has been set up. You can easily login to your 
                        dashboard with your <b>Email address / MemberID</b> and <b>Password</b></p>
                     <div style="padding:0% 0% 3% 0%; border-left: solid 5px #0364B8; text-align: left; padding: 0% 2% 0% 4%; background:white;"> 
                        <p>Your member <b>Member ID:   <?=$member_id?> </b></p>
                        <p>Default Password: <?=$password?> </p>
                        <p>Kindly login using your Email or Member ID and password!</p>
                        <p style="color: red"> Once you login, change the default password to your preference. </p>
                    </div>

                    <p>Thank you!<br><?= $this->coop->contact_name ?></p>
                </div>
                
                <div style="padding: 3%"> 
                    <div style="text-align: left; color:white;">
                        <h3 style="margin: 0% 0% 4% 0%; ">Interesting Features</h3>
                        <p style="margin: 0% 0% 6% 0%;">The easy way to transact with your cooperative and the world!!</p> 
                    </div>
                    <div style="padding:3% 0% 3% 0%; text-align: left; padding: 1% 2% 1% 4%; background:white; border-radius: 3px;"> 
                        <p><b style="color:#0364B8;">Savings</b>
                            <br>Fast and easy way to save money in to your cooperative account without leaving the comfort of your home <br>  
                        </p>
                    </div>
                    <div style="padding:3% 0% 3% 0%; text-align: left; background:white; border-radius: 3px; padding: 1% 2% 1% 4%; margin-top: 15px"> 
                        <p><b style="color:#0364B8;">Loan</b>
                            <br>Faster loan request and faster approval and disbursement process by the cooperative executives.            
                        </p>
                    </div>
                    <div style="padding:3% 0% 3% 0%; text-align: left; background:white; border-radius: 3px; padding: 1% 2% 1% 4%; margin-top: 15px"> 
                        <p><b style="color:#0364B8;">Wallet</b>
                            <br>From your wallet, securely send money to bank accounts, settle your monthly savings, repay loan and much more!           
                        </p>
                    </div>
                </div>
                <div style="padding:5% 0% 5% 0%; background: #e3e3f3; padding: 4% 8% 4% 8%"> 
                    <p>Copyright <?= date('Y') ?>  <?= $this->app_settings->app_name ?></p>
                    <p>Powered by <a style=" text-decoration: none; font-weight: bolder; color: black"><?= $this->app_settings->powered_by ?></a></p>
                </div>
            </div>
        </div>
    </body>
</html>