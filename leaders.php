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
                    <h1 class="page-header">Leaders</h1>
                </div>
            </div>
            
            <div class="row">
                <div class="col-xs-12">
                	<a href="leader_add.php"><i class="fa fa-plus"></i> Add leader</a><br><br>
                    
                    <table class="table table-striped table-bordered" style="width:auto;">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Cell</th>
                                <th>Email</th>
                                <th># Times Served</th>
                                <th># Times Served This Sem.</th>
                                <th>Active</th>
                                <th>Edit</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                        
                        <tbody>
							<?php
							$rows = leadersLoadMultiple();
							uasort($rows, 'cmp');
							foreach($rows as $row)
							{
								echo '<tr>';
									echo '<td>'.$row['last_name'].', '.$row['first_name'].'</td>';
									echo '<td>'.$row['cell_phone'].'</td>';
									echo '<td>'.$row['email'].'</td>';
									echo '<td style="text-align:center;">'.leadersCountTimesWasThere($row['leader_id']).'</td>';
									echo '<td style="text-align:center;">'.leadersCountTimesWasThereThisSemester($row['leader_id']).'</td>';
									echo '<td style="text-align:center;">';
										if($row['active'])
											echo '<a href="leaders_is_active_no.php?id='.$row['leader_id'].'"><i class="fa fa-check"></i></a>';
										else
											echo '<a href="leaders_is_active_yes.php?id='.$row['leader_id'].'"><i class="fa fa-times"></i></a>';
									echo '</td>';
									echo '<td style="text-align:center;"><a href="leader_edit.php?id='.$row['leader_id'].'"><i class="fa fa-pencil"></i></a></td>';
									echo '<td style="text-align:center;"><a href="leader_delete.php?id='.$row['leader_id'].'" class="confirm_first"><i class="fa fa-trash" style="color:#A00;"></i></a></td>';
								echo '</tr>';
							}
							
							function cmp($a, $b)
							{
								return strcmp($a['last_name'], $b['last_name']);
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
