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
                    <h2 style="text-align: center; color:#0364B8; ">Robot Alert</h2>
                    <p style="padding-top: 10px;">Robot Registration Attempt Blocked!</p>
                    <h4 style="color:#0364B8;  margin-top: 20px">Robot Details</h4>
                    <p>IP : <?=$server['REMOTE_ADDR']?></p>
                    <p>Port : <?=$server['REMOTE_PORT']?></p>
                    <p>Time : <?=date('Y-m-d g:i:s')?></p>
                    <h4 style="color:#0364B8;  margin-top: 20px">Post Data</h4>
                    <code>
                        <?= var_dump($post)?>
                    </code>
                </div>
             
                <div style="padding:5% 0% 5% 0%; background: #e3e3f3; padding: 4% 8% 4% 8%"> 
                    <p>Copyright <?= date('Y') ?>  <?= $this->app_settings->app_name ?></p>
                    <p>Powered by <a style=" text-decoration: none; font-weight: bolder; color: black"><?= $this->app_settings->powered_by ?></a></p>
                </div>
            </div>
        </div>
    </body>

</html>