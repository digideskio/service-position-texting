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
					<h1 class="page-header">Positions</h1>
				</div>
			</div>
			
			<div class="row">
				<div class="col-xs-12">
					<a href="position_add.php"><i class="fa fa-plus"></i> Add position</a><br><br>
					
					<?php
					$last_name_and_date = '';
					$count_tables = 0;
					$rows = positionsLoadMultiple();
					uasort($rows, 'cmp');
					foreach($rows as $row)
					{
						$name_and_date = date('F j, Y', strtotime($row['date'])).': '.$row['name'];
						
						if($last_name_and_date != $name_and_date)
						{
							if($count_tables)
							{
								echo '</tbody>';
								echo '</table>';
							}
							
							echo '<h3>'.$name_and_date.'</h3>';
							
							echo '<table class="table table-striped table-bordered" style="width:auto;">';
							echo '<thead>';
								echo '<tr>';
									echo '<th>Title</th>';
									echo '<th>Assignee</th>';
									echo '<th>Type</th>';
									echo '<th>Send Reminder</th>';
									echo '<th>Confirmed Text</th>';
									echo '<th>Was There</th>';
									echo '<th>Edit</th>';
									echo '<th>Delete</th>';
								echo '</tr>';
							echo '</thead>';
							
							echo '<tbody>';
							
							$count_tables++;
							$last_name_and_date = $name_and_date;
						}
						
						$row_assignment = assignmentsLoadSingleWithPositionID($row['position_id']);
						$row_leader = leadersLoadSingle($row_assignment['leader_id']);
						
						echo '<tr>';
							echo '<td>'.$row['title'].'</td>';
							
							echo '<td>';
								if($row_leader['first_name'].$row_leader['last_name'] != '')
									echo $row_leader['first_name'].' '.$row_leader['last_name'];
								else
									echo '<em>No assignment...</em>';
							echo '</td>';
							
							echo '<td>';
								if($row['type'] == 0)
									echo 'Leaders only';
								else if($row['type'] == 1)
									echo 'Volunteers only';
								else if($row['type'] == 2)
									echo 'Leaders or volunteers';
							echo '</td>';
							
							echo '<td>';
								if($row['notify_night'])
									echo 'Night before';
								else
									echo 'Morning of';
							echo '</td>';
							
							echo '<td style="text-align:center;">';
								if($row_leader['first_name'].$row_leader['last_name'] != '')
									if($row_assignment['confirmed_text'] == 1)
										echo '<i class="fa fa-check"></i>';
									else if($row_assignment['confirmed_text'] == 2)
										echo '<i class="fa fa-times"></i>';
							echo '</td>';
							
							echo '<td style="text-align:center;">';
								if($row_leader['first_name'].$row_leader['last_name'] != '')
									if($row_assignment['was_there'])
										echo '<a href="assignments_was_there_no.php?id='.$row_assignment['assignment_id'].'"><i class="fa fa-check"></i></a>';
									else
										echo '<a href="assignments_was_there_yes.php?id='.$row_assignment['assignment_id'].'"><i class="fa fa-times"></i></a>';
							echo '</td>';
							
							echo '<td style="text-align:center;"><a href="position_edit.php?id='.$row['position_id'].'"><i class="fa fa-pencil"></i></a></td>';
							
							echo '<td style="text-align:center;"><a href="position_delete.php?id='.$row['position_id'].'" class="confirm_first"><i class="fa fa-trash" style="color:#A00;"></i></a></td>';
						echo '</tr>';
					}
					
					if($count_tables)
					{
						echo '</tbody>';
						echo '</table>';
					}
					
					function cmp($a, $b)
					{
						return strcmp($b['date'], $a['date']);
					}
					?>
				</div>
			</div>
		</div>
	</div>
	
	<?php require_once($_SERVER['DOCUMENT_ROOT'].'/includes/bottom-scripts.php'); ?>
	
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
