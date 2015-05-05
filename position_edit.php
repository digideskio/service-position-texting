<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/config/main.php');

if($_GET['title'] != '')
{
	positionsEdit($_GET['id'], $_GET['event_id'], $_GET['title'], $_GET['notify_night'], $_GET['leader_id']);
	header('Location: positions.php');
	exit;
}
else
{
	$row = positionsLoadSingle($_GET['id']);
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
                    <h1 class="page-header">Edit Position</h1>
                </div>
            </div>
            
            <div class="row">
                <div class="col-xs-12">
                	<form method="get">
                        <div class="form-group">
                            <label for="event_id">Event</label>
                            <select class="form-control" name="event_id" id="event_id">
                            	<?php
                                $rows = eventsLoadMultiple();
                                uasort($rows, 'cmp');
                                foreach($rows as $r)
                                {
                                    echo '<tr>';
                                        echo '<option value="'.$r['event_id'].'"';
										if($row['event_id'] == $r['event_id']) echo ' selected';
										echo '>'.date('F j, Y', strtotime($r['date'])).': '.$r['name'].'</option>';
                                    echo '</tr>';
                                }
                                
                                function cmp($a, $b)
                                {
                                    return strtotime($b['date']) - strtotime($a['date']);
                                }
								?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" class="form-control" name="title" id="title" value="<?php echo $row['title']; ?>" placeholder="Greeter">
                        </div>
                        
                        <div class="form-group">
                            <label for="notify_night">Send Reminder When?</label>
                            <select class="form-control" name="notify_night" id="notify_night">
                            	<option value="0" <?php if($row['notify_night'] == 0){ echo 'selected'; } ?>>The morning of</option>
                            	<option value="1" <?php if($row['notify_night'] == 1){ echo 'selected'; } ?>>The night before</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="event_id">Assigned Leader</label>
                            <select class="form-control" name="leader_id" id="leader_id">
                            	<option value="0">None</option>
                            	<?php
								$row_assignment = assignmentsLoadSingleWithPositionID($row['position_id']);
								
                                $rows = leadersLoadMultiple();
                                uasort($rows, 'cmp2');
                                foreach($rows as $r)
                                {
                                    echo '<tr>';
                                        echo '<option value="'.$r['leader_id'].'"';
										if($row_assignment['leader_id'] == $r['leader_id']) echo ' selected';
										echo '>'.$r['first_name'].' '.$r['last_name'].'</option>';
                                    echo '</tr>';
                                }
                                
                                function cmp2($a, $b)
                                {
									return strcmp($a['last_name'], $b['last_name']);
                                }
								?>
                            </select>
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
