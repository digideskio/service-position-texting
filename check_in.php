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
					<h1 class="page-header">Check In</h1>
				</div>
			</div>
			
			<div class="row">
				<div class="col-xs-12">
					<?php
					$last_name_and_date = '';
					$count_tables = 0;
					$rows = positionsLoadMultipleToday();
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
									echo '<th>Position</th>';
									echo '<th>Was There</th>';
								echo '</tr>';
							echo '</thead>';
							
							echo '<tbody>';
							
							$count_tables++;
							$last_name_and_date = $name_and_date;
						}
						
						
						$row_assignment = assignmentsLoadSingleWithPositionID($row['position_id']);
						$row_leader = leadersLoadSingle($row_assignment['leader_id']);
						
						echo '<tr>';
							echo '<td>';
								echo '<strong>'.$row['title'].'</strong><br>';
							
								if($row_leader['first_name'].$row_leader['last_name'] != '')
									echo $row_leader['first_name'].' '.$row_leader['last_name'];
								else
									echo '<em>No assignment...</em>';
							echo '</td>';
							
							echo '<td style="text-align:center; vertical-align:middle; font-size:30px;">';
								if($row_leader['first_name'].$row_leader['last_name'] != '')
									if($row_assignment['was_there'])
										echo '<a href="assignments_was_there_no.php?id='.$row_assignment['assignment_id'].'&check_in=1"><i class="fa fa-check"></i></a>';
									else
										echo '<a href="assignments_was_there_yes.php?id='.$row_assignment['assignment_id'].'&check_in=1"><i class="fa fa-times"></i></a>';
							echo '</td>';
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
