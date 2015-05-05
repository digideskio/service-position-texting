<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>FWCM Texting [Admin]</title>
    
    <?php require_once($_SERVER['DOCUMENT_ROOT'].'/includes/head.php'); ?>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <div class="login-panel panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Please log In</h3>
                    </div>
                    
                    <div class="panel-body">
                        <form role="form" method="post">
                            <fieldset>
                            	<?php if($login_failed === true){ ?>
                                    <div class="form-group">
                                        <span style="color:#900;">Username / password incorrect</span>
                                    </div>
                            	<?php } ?>
                            	
                                <div class="form-group">
                                    <input class="form-control" placeholder="Username" name="username" type="text" autofocus>
                                </div>
                                
                                <div class="form-group">
                                    <input class="form-control" placeholder="Password" name="password" type="password" value="">
                                </div>
                                
                                <button type="submit" class="btn btn-lg btn-success btn-block">Login</button>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php require_once($_SERVER['DOCUMENT_ROOT'].'/includes/bottom-scripts.php'); ?>
</body>
</html>
