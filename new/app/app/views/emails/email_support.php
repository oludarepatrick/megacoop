<!Doctype html>
<html>
    <head>
        <title></title>
        <meta name="viewport" content="width=device-width"/>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    </head>

    <body style="color:#57585A; font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; line-height: 1.6em; background-color: #f6f6f6; margin: 0;">
        <div style="text-align: center;padding: 10px;">
            <div style="display: inline-block; border-radius: 3px; width: 400px; height:100%;  background: white">
                <div style=" padding: 0% 0% 10% 0%;">
                    <img style="width:100%;" src="<?= base_url("assets/images/waves.png") ?>">
                    <?php if($this->coop->logo){ ?>
                    <img style="width:15%;" src="<?= base_url("assets/images/logo/coop/".$this->coop->logo) ?>">
                    <?php } ?>
                    <?php if(!$this->coop->logo){ ?>
                    <img style="width:15%;" src="<?= base_url("assets/images/logo/coopify/default_logo.png") ?>">
                    <?php } ?>
                    <h3 style="color:#0364B8; margin: 1% 0% 0% 0%;"><?=$this->coop->coop_name?></h3>
                </div>
                <div style="text-align: left; padding:3%; background: #fff;  border: #f1f1f1 thin solid;">
                    <h2 style="text-align: center; color:#0364B8; "><?=$subject?></h2>
                    <h4>Name: <?= ucwords($this->user->first_name.' '. $this->user->last_name)?></h4>
                    <h4 >Email: <?= ucwords($this->user->email)?></h4>
                    <h4 >Phone: <?= ucwords($this->user->phone)?></h4>
                    <h4>MemberID: <?= ucwords($this->user->username)?></h4>
                    <h4>Cooperative: <?= ucwords($this->coop->coop_name)?></h4>
                    <p style="padding-top: 10px; text-align: justify"> <?=$message?></p>
                </div>
            </div>
        </div>
    </body>

</html>