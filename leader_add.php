<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/config/main.php');

if($_GET['first_name'] != '')
{
	leadersAdd($_GET['first_name'], $_GET['last_name'], $_GET['cell_phone'], $_GET['email'], 1);
	header('Location: leaders.php');
	exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>FWCM Texting [Admin]</title>
    
    <?php require_once($_SERVER['DOCUMENT_ROOT'].'/includes/head.php'); ?>
    
    <style>
    .form-control{
		max-width:250px;
	}
    </style>
</head>

<body>
    <div id="wrapper">
		<?php require_once($_SERVER['DOCUMENT_ROOT'].'/includes/navigation.php'); ?>
		
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Add Leader</h1>
                </div>
            </div>
            
            <div class="row">
                <div class="col-xs-12">
                	<form method="get">
                        <div class="form-group">
                            <label for="first_name">First name</label>
                            <input type="text" class="form-control" name="first_name" id="first_name" placeholder="John">
                        </div>
                        
                        <div class="form-group">
                            <label for="last_name">Last name</label>
                            <input type="text" class="form-control" name="last_name" id="last_name" placeholder="Doe">
                        </div>
                        
                        <div class="form-group">
                            <label for="cell_phone">Cell phone</label>
                            <input type="tel" class="form-control" name="cell_phone" id="cell_phone" placeholder="123-456-7890">
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" name="email" id="email" placeholder="example@example.com">
                        </div>
                        
                        <button type="submit" class="btn btn-default">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <?php require_once($_SERVER['DOCUMENT_ROOT'].'/includes/bottom-scripts.php'); ?>
</body>
</html>
