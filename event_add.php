<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/config/main.php');

if($_GET['name'] != '')
{
	eventsAdd($_GET['name'], $_GET['date'], $_GET['copy_event_id']);
	header('Location: events.php');
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
                    <h1 class="page-header">Add Event</h1>
                </div>
            </div>
            
            <div class="row">
                <div class="col-xs-12">
                	<form method="get">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" name="name" id="name" value="Sunday morning duties" placeholder="Enter event name">
                        </div>
                        
                        <div class="form-group">
                            <label for="date">Date</label>
                            <input type="date" class="form-control" name="date" id="date" value="<?php echo date('Y-m-d', strtotime('next sunday, 11:59am')); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="date">Copy positions from event:</label>
                            <select class="form-control" name="copy_event_id" id="copy_event_id">
                            	<option value="0">None</option>
                            	<?php
                                $rows = eventsLoadMultiple();
                                uasort($rows, 'cmp');
                                foreach($rows as $row)
                                {
                                    echo '<tr>';
                                        echo '<option value="'.$row['event_id'].'">'.$row['name'].' - '.date('F j, Y', strtotime($row['date'])).'</option>';
                                    echo '</tr>';
                                }
                                
                                function cmp($a, $b)
                                {
                                    return strtotime($b['date']) - strtotime($a['date']);
                                }
								?>
                            </select>
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
