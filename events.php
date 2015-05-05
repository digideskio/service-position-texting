<?php require_once($_SERVER['DOCUMENT_ROOT'].'/config/main.php'); ?>

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
    <div id="wrapper">
		<?php require_once($_SERVER['DOCUMENT_ROOT'].'/includes/navigation.php'); ?>
		
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Events</h1>
                </div>
            </div>
            
            <div class="row">
                <div class="col-xs-12">
                	<?php if($_SESSION['message'] != ''){ ?>
                        <div class="alert alert-success alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <?php echo $_SESSION['message']; $_SESSION['message'] = ''; ?>
                        </div>
                	<?php } ?>
                	
                	<a href="event_add.php"><i class="fa fa-plus"></i> Add event</a><br><br>
                    
                    <table class="table table-striped table-bordered" style="width:auto;">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Date</th>
                                <th>Fill Positions</th>
                                <th>Send Texts</th>
                                <th>Edit</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                        
                        <tbody>
							<?php
							$rows = eventsLoadMultiple();
							uasort($rows, 'cmp');
							foreach($rows as $row)
							{
								echo '<tr>';
									echo '<td>'.$row['name'].'</td>';
									echo '<td>'.date('F j, Y', strtotime($row['date'])).'</td>';
									echo '<td style="text-align:center;"><a href="event_fill_positions.php?id='.$row['event_id'].'"><i class="fa fa-magic"></i></a></td>';
									echo '<td style="text-align:center;"><a href="event_send_texts.php?id='.$row['event_id'].'" class="explain_first"><i class="fa fa-paper-plane"></i></a></td>';
									echo '<td style="text-align:center;"><a href="event_edit.php?id='.$row['event_id'].'"><i class="fa fa-pencil"></i></a></td>';
									echo '<td style="text-align:center;"><a href="event_delete.php?id='.$row['event_id'].'" class="confirm_first"><i class="fa fa-trash" style="color:#A00;"></i></a></td>';
								echo '</tr>';
							}
							
							function cmp($a, $b)
							{
								return strtotime($b['date']) - strtotime($a['date']);
							}
                        	?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <?php require_once($_SERVER['DOCUMENT_ROOT'].'/includes/bottom-scripts.php'); ?>
    
    <script>
    $(document).ready(function(e) {
		$(".explain_first").on("click", function(e) {
			e.preventDefault();
			var link = this;
			
			var answer = confirm("This will immediately send a confirmation text to all leaders who have NOT received a text yet for this event.");
			if(answer)
				window.location = link.href;
        });
    });
    </script>
    
    <script>
    $(document).ready(function(e) {
		$(".confirm_first").on("click", function(e) {
			e.preventDefault();
			var link = this;
			
			var answer = confirm("Do you really want to delete this?");
			if(answer)
				window.location = link.href;
        });
    });
    </script>
</body>
</html>
