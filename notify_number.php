<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/config/main.php');

if($_GET['number'] != '')
{
	notifyNumberEdit($_GET['number']);
	header('Location: /');
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
					<h1 class="page-header">Notify Number</h1>
				</div>
			</div>
			
			<div class="row">
				<div class="col-xs-12">
					<form method="get">
						<div class="form-group">
							<label for="number">Notify Number <small style="font-weight:normal; font-style:italic;">Used when a leader says they can't fill a position</small></label>
							<input type="text" class="form-control" name="number" id="number" placeholder="1234567890" value="<?php echo preg_replace('/[^0-9]/', '', file_get_contents($BASE_PATH.'/config/number.txt')); ?>">
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