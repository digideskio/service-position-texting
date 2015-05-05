<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/config/main.php');

if($_GET['name'] != '')
{
	eventsEdit($_GET['id'], $_GET['name'], $_GET['date']);
	header('Location: events.php');
	exit;
}
else
{
	$row = eventsLoadSingle($_GET['id']);
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
                    <h1 class="page-header">Edit Event</h1>
                </div>
            </div>
            
            <div class="row">
                <div class="col-xs-12">
                	<form method="get">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" name="name" id="name" value="<?php echo ($_GET['name'] == '') ? $row['name'] : $_GET['name']; ?>" placeholder="Enter event name">
                        </div>
                        
                        <div class="form-group">
                            <label for="date">Date</label>
                            <input type="date" class="form-control" name="date" id="date" value="<?php echo ($_GET['date'] == '') ? $row['date'] : $_GET['date']; ?>">
                        </div>
                        
                        <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
                        
                        <button type="submit" class="btn btn-default">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <?php require_once($_SERVER['DOCUMENT_ROOT'].'/includes/bottom-scripts.php'); ?>
</body>
</html>
