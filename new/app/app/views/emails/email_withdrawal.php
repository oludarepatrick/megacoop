<!Doctype html>
<html>
    <head>
        <title></title>
        <meta name="viewport" content="width=device-width"/>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    </head>

    <body style="color:#57585A; font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; line-height: 1.6em; background-color: #f6f6f6; margin: 0;">
        <div style="text-align: center;padding: 10px;">
            <div style="display: inline-block; border-radius: 3px; width: 400px; height:100%;  background: white">
                <div style=" padding: 10% 0% 10% 0%;">
                    <img style="width:12%;" src="<?= base_url("assets/images/logo/coop/{$this->coop->logo}") ?>">
                    <h3 style="color:#0364B8; margin: 1% 0% 0% 0%;"><?= $this->coop_short_name ?></h3>
                    <p style="padding: 0% 10% 2% 10%; font-size: 14px;">Smartly automate all your cooperative operations!</p>
                    <a data-fancy href="<?= base_url() ?>" style="border-radius: 3px; border: solid thin #0364B8; color: #0364B8; font-weight: bold; text-decoration: none; padding: 2% 4% 2% 4%;" >My Account </a>
                </div>
                <div style=" padding: 8% 0% 8% 0%; background: #0364B8;">
                    <p style="color:white ; padding: 0% 10% 0% 10%; font-size: 14px;">Amount Withdrawn</p>
                    <h1 style="color:white; margin: 1% 0% 0% 0%; font-size: 40px;"><?= number_format($amount,2)?></h1>
                </div>
                
                <div style="padding: 3%"> 
                    <div style="text-align: left; ">
                        <p style="font-size:14px">Hello <?= ucwords($name) ?> <br> MemberID: <?= $member_id ?></p>
                        <p style="margin: 0% 0% 4% 0%;">This is a withrawal notification on your cooperative account</p> 
                    </div>
                    <div style=" margin: 1% 0% 0% 0%; padding:3% 0% 3% 0%; border-left: solid 5px #0364B8; text-align: left; padding: 1% 2% 1% 4%; background:white;"> 
                        <strong style="width:46%; display: inline-block;">Savings Type:</strong> <span style=" width: 46%; display: inline-block;"><?= $savings_type_name ?></span>
                    </div>
                    
                    <div style="margin: 1% 0% 0% 0%; padding:3% 0% 3% 0%; border-left: solid 5px #0364B8; text-align: left; padding: 1% 2% 1% 4%; background:white;"> 
                        <strong style="width:46%; display: inline-block;">Date:</strong> <span style=" width: 46%; display: inline-block;"> <?= $date ?></span>
                    </div>
                    <div style="margin: 1% 0% 0% 0%; padding:3% 0% 3% 0%; border-left: solid 5px #0364B8; text-align: left; padding: 1% 2% 1% 4%; background:white;"> 
                        <strong style="width:46%; display: inline-block;">Status:</strong> <span style=" width: 46%; display: inline-block;"> <?= $status ?></span>
                    </div>
                </div>
                <div style=" padding: 8% 0% 8% 0%; background: #0364B8;">
                    <p style="color:white ; padding: 0% 10% 0% 10%; font-size: 14px;">Savings Balance</p>
                    <h1 style="color:white; margin: 1% 0% 0% 0%; font-size: 40px;"><?= number_format($balance, 2) ?></h1>
                </div>
                <div style="padding:5% 0% 5% 0%; background: #e3e3f3; padding: 4% 8% 4% 8%"> 
                    <p>Copyright <?= date('Y') ?>  <?= $this->app_settings->app_name ?></p>
                    <p>Powered by <a style=" text-decoration: none; font-weight: bolder; color: black"><?= $this->app_settings->powered_by ?></a></p>
                </div>
            </div>
        </div>
    </body>

</html>