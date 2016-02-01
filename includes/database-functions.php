<?php
// Set timezone
date_default_timezone_set('America/Chicago');


// Function to add an event
function eventsAdd($name, $date, $copy_event_id){
	// Set global
	global $PDO;
	
	
	// Database call
	$query = "INSERT INTO events SET name = :name, date = :date";
	$statement = $PDO->prepare($query);
	$params = array(
		'name' => $name,
		'date' => $date,
	);
	$statement->execute($params);
	$new_event_id = $PDO->lastInsertId();
	
	
	// Copy positions
	$query = "INSERT INTO positions (event_id, title, notify_night) SELECT :new_event_id, title, notify_night FROM positions WHERE event_id = :copy_event_id";
	$statement = $PDO->prepare($query);
	$params = array(
		'new_event_id' => $new_event_id,
		'copy_event_id' => $copy_event_id,
	);
	$statement->execute($params);
	
	
	// Copy assignments
	$query = "SELECT * FROM positions WHERE event_id = :new_event_id";
	$statement = $PDO->prepare($query);
	$params = array(
		'new_event_id' => $new_event_id,
	);
	$statement->execute($params);
	while($row = $statement->fetch())
	{
		$query_update = "INSERT INTO assignments SET position_id = :position_id";
		$statement_update = $PDO->prepare($query_update);
		$params_update = array(
			'position_id' => $row['position_id'],
		);
		$statement_update->execute($params_update);
	}
}


// Function to edit an event
function eventsEdit($event_id, $name, $date){
	// Set global
	global $PDO;
	
	
	// Database call
	$query = "UPDATE events SET name = :name, date = :date WHERE event_id = :event_id";
	$statement = $PDO->prepare($query);
	$params = array(
		'name' => $name,
		'date' => $date,
		'event_id' => $event_id
	);
	$statement->execute($params);
}


// Function to delete an event
function eventsDelete($event_id){
	// Set global
	global $PDO;
	
	
	// Database call
	$query = "DELETE FROM events WHERE event_id = :event_id";
	$statement = $PDO->prepare($query);
	$params = array(
		'event_id' => $event_id
	);
	$statement->execute($params);
}


// Function to load a single event
function eventsLoadSingle($event_id){
	// Set global
	global $PDO;
	
	
	// Database call
	$query = "SELECT * FROM events WHERE event_id = :event_id";
	$statement = $PDO->prepare($query);
	$params = array(
		'event_id' => $event_id
	);
	$statement->execute($params);
	
	
	// Return
	return $statement->fetch();
}


// Function to load multiple events
function eventsLoadMultiple(){
	// Set global
	global $PDO;
	
	
	// Database call
	$query = "SELECT * FROM events";
	$statement = $PDO->query($query);
	
	
	// Return
	return $statement->fetchAll();
}


// Magically fill the positions
// Not really magic, we'll base it off the number of times the leader has served this semester
function eventsFillPositions($event_id){
	// Set global
	global $PDO;
	
	
	// Calculate begin/end date
	if(date('Ymd') < date('Y0630'))
	{
		// First semester
		$begin_date = date('Y-01-01');
		$end_date = date('Y-06-30');
	}
	else
	{
		// Second semester
		$begin_date = date('Y-07-01');
		$end_date = date('Y-12-31');
	}
	
	
	// Get people
	$query = "SELECT * FROM leaders WHERE active = 1 ORDER BY RAND()";
	$statement = $PDO->query($query);
	
	while($row = $statement->fetch())
	{
		// Count times served
		$query_count = "SELECT COUNT(*) FROM assignments INNER JOIN positions ON assignments.position_id=positions.position_id INNER JOIN events ON positions.event_id=events.event_id WHERE assignments.leader_id = :leader_id AND assignments.was_there = 1 AND events.date > '".$begin_date."' AND events.date < '".$end_date."'";
		$statement_count = $PDO->prepare($query_count);
		$params_count = array(
			'leader_id' => $row['leader_id']
		);
		$statement_count->execute($params_count);
		
		
		// Make sure they're not already assigned to a position
		$query_check_assigned = "SELECT COUNT(*) FROM assignments INNER JOIN positions ON assignments.position_id=positions.position_id WHERE positions.event_id = :event_id AND assignments.leader_id = :leader_id";
		$statement_check_assigned = $PDO->prepare($query_check_assigned);
		$params_check_assigned = array(
			'event_id' => $event_id,
			'leader_id' => $row['leader_id']
		);
		$statement_check_assigned->execute($params_check_assigned);
		
		if($statement_check_assigned->fetchColumn() == 0)
		{
			// Add to array
			$people[] = array(
				'num_times_served' => $statement_count->fetchColumn(),
				'leader_id' => $row['leader_id'],
				'is_volunteer' => $row['is_volunteer'],
			);
		}
	}
	
	
	// Sort ascending by times served
	usort($people, function($a, $b){
		if($a['num_times_served'] == $b['num_times_served'])
			return 0;
		
		return ($a['num_times_served'] < $b['num_times_served']) ? -1 : 1;
	});
	
	
	// Fill the leaders only roles
	$query = "SELECT * FROM positions INNER JOIN assignments ON positions.position_id=assignments.position_id WHERE positions.event_id = :event_id AND assignments.leader_id = 0 ORDER BY positions.type ASC";
	$statement = $PDO->prepare($query);
	$params = array(
		'event_id' => $event_id
	);
	$statement->execute($params);
	
	while($row = $statement->fetch())
	{
		// Reset
		unset($person);
		
		
		// Choose a person
		foreach($people as $key => $person)
		{
			if($row['type'] == 0 && $person['is_volunteer'] == 0)
			{
				$person['chosen'] = 1;
				unset($people[$key]);
				break;
			}
			else if($row['type'] == 1 && $person['is_volunteer'] == 1)
			{
				$person['chosen'] = 1;
				unset($people[$key]);
				break;
			}
			else if($row['type'] == 2)
			{
				$person['chosen'] = 1;
				unset($people[$key]);
				break;
			}
		}
		
		
		// Check if person has been chosen
		if($person['chosen'] == 1)
		{
			// Assign the person to this position
			$query_update = "UPDATE assignments SET leader_id = :leader_id WHERE assignment_id = :assignment_id";
			$statement_update = $PDO->prepare($query_update);
			$params_update = array(
				'leader_id' => $person['leader_id'],
				'assignment_id' => $row['assignment_id']
			);
			$statement_update->execute($params_update);
		}
	}
}


// Send a confirmation text to all assigned leaders
function eventsSendConfirmationTexts($event_id){
	// Set global
	global $PDO;
	
	
	// Store count
	$count = 0;
	
	
	// Loop through all assigned leaders
	$query = "SELECT * FROM assignments INNER JOIN leaders ON assignments.leader_id=leaders.leader_id INNER JOIN positions ON assignments.position_id=positions.position_id INNER JOIN events ON  positions.event_id=events.event_id WHERE positions.event_id = :event_id";
	$statement = $PDO->prepare($query);
	$params = array(
		'event_id' => $event_id
	);
	$statement->execute($params);
	
	while($row = $statement->fetch())
	{
		if($row['cell_phone'] != 0 && $row['assignment_id'] != $row['last_text_re_assignment_id'])
		{
			$text = 'Hey '.$row['first_name'].', you\'ve been assigned the position "'.$row['title'].'" for '.$row['name'].' on '.date('M j', strtotime($row['date'])).'. Please reply YES to confirm, or NO if unable.';
			send_sms($row['cell_phone'], $text);
			$count++;
			
			
			// Set so we know when they reply to a text, which text they are replying to
			$query_update = "UPDATE leaders SET last_text_re_assignment_id = :assignment_id WHERE leader_id = :leader_id";
			$statement_update = $PDO->prepare($query_update);
			$params_update = array(
				'assignment_id' => $row['assignment_id'],
				'leader_id' => $row['leader_id']
			);
			$statement_update->execute($params_update);
		}
	}
	
	
	// Return
	return $count;
}


// Function to add a leader
function leadersAdd($first_name, $last_name, $cell_phone, $email, $is_volunteer, $active){
	// Set global
	global $PDO;


	// Database call
	$query = "INSERT INTO leaders SET first_name = :first_name, last_name = :last_name, cell_phone = :cell_phone, email = :email, is_volunteer = :is_volunteer, active = :active";
	$statement = $PDO->prepare($query);
	$params = array(
		'first_name' => $first_name,
		'last_name' => $last_name,
		'cell_phone' => preg_replace('/[^0-9]/', '', $cell_phone),
		'email' => $email,
		'is_volunteer' => $is_volunteer,
		'active' => $active,
	);
	$statement->execute($params);
}


// Function to edit a leader
function leadersEdit($leader_id, $first_name, $last_name, $cell_phone, $email, $is_volunteer, $active){
	// Set global
	global $PDO;


	// Database call
	$query = "UPDATE leaders SET first_name = :first_name, last_name = :last_name, cell_phone = :cell_phone, email = :email, is_volunteer = :is_volunteer, active = :active WHERE leader_id = :leader_id";
	$statement = $PDO->prepare($query);
	$params = array(
		'first_name' => $first_name,
		'last_name' => $last_name,
		'cell_phone' => preg_replace('/[^0-9]/', '', $cell_phone),
		'leader_id' => $leader_id,
		'email' => $email,
		'is_volunteer' => $is_volunteer,
		'active' => ($active) ? 1 : 0,
	);
	$statement->execute($params);
}


// Function to delete a leader
function leadersDelete($leader_id){
	// Set global
	global $PDO;


	// Database call
	$query = "DELETE FROM leaders WHERE leader_id = :leader_id";
	$statement = $PDO->prepare($query);
	$params = array(
		'leader_id' => $leader_id
	);
	$statement->execute($params);
	$row = $statement->fetch();

	// Database call
	$query = "DELETE FROM assignments WHERE leader_id = :leader_id";
	$statement = $PDO->prepare($query);
	$params = array(
		'leader_id' => $leader_id
	);
	$statement->execute($params);
}


// Function to load a single leader
function leadersLoadSingle($leader_id){
	// Set global
	global $PDO;


	// Database call
	$query = "SELECT * FROM leaders WHERE leader_id = :leader_id";
	$statement = $PDO->prepare($query);
	$params = array(
		'leader_id' => $leader_id
	);
	$statement->execute($params);


	// Return
	return $statement->fetch();
}


// Function to load multiple leaders
function leadersLoadMultiple(){
	// Set global
	global $PDO;


	// Database call
	$query = "SELECT * FROM leaders";
	$statement = $PDO->query($query);


	// Return
	return $statement->fetchAll();
}


// Function to set is active to yes
function leadersIsActiveYes($leader_id){
	// Set global
	global $PDO;


	// Database call
	$query = "UPDATE leaders SET active = 1 WHERE leader_id = :leader_id";
	$statement = $PDO->prepare($query);
	$params = array(
		'leader_id' => $leader_id
	);
	$statement->execute($params);
}


// Function to set is active to no
function leadersIsActiveNo($leader_id){
	// Set global
	global $PDO;


	// Database call
	$query = "UPDATE leaders SET active = 0 WHERE leader_id = :leader_id";
	$statement = $PDO->prepare($query);
	$params = array(
		'leader_id' => $leader_id
	);
	$statement->execute($params);
}


// Function to count the number of times served
function leadersCountTimesWasThere($leader_id){
	// Set global
	global $PDO;


	// Database call
	$query = "SELECT COUNT(*) FROM assignments WHERE was_there = 1 AND leader_id = :leader_id";
	$statement = $PDO->prepare($query);
	$params = array(
		'leader_id' => $leader_id
	);
	$statement->execute($params);
	
	
	// Return
	return $statement->fetchColumn();
}


// Function to count the number of times served this semester
function leadersCountTimesWasThereThisSemester($leader_id){
	// Set global
	global $PDO;


	// Calculate date
	if(date('Ymd') < date('Y0630'))
	{
		// First semester
		$begin_date = date('Y-01-01');
		$end_date = date('Y-06-30');
	}
	else
	{
		// Second semester
		$begin_date = date('Y-07-01');
		$end_date = date('Y-12-31');
	}
	
	
	// Get times served this semester
	$query_count = "SELECT COUNT(*) FROM assignments INNER JOIN positions ON assignments.position_id=positions.position_id INNER JOIN events ON positions.event_id=events.event_id WHERE assignments.leader_id = :leader_id AND assignments.was_there = 1 AND events.date > '".$begin_date."' AND events.date < '".$end_date."'";
	$statement_count = $PDO->prepare($query_count);
	$params_count = array(
		'leader_id' => $leader_id
	);
	$statement_count->execute($params_count);
	
	return $statement_count->fetchColumn();
}


// Function to add a position
function positionsAdd($event_id, $title, $notify_night, $type, $leader_id = 0){
	// Set global
	global $PDO;


	// Database call
	$query = "INSERT INTO positions SET event_id = :event_id, title = :title, notify_night = :notify_night, type = :type";
	$statement = $PDO->prepare($query);
	$params = array(
		'event_id' => $event_id,
		'title' => $title,
		'notify_night' => $notify_night,
		'type' => $type,
	);
	$statement->execute($params);
	$new_position_id = $PDO->lastInsertId();
	
	
	// Add assignment
	assignmentsAdd($new_position_id, $leader_id);
}


// Function to edit a position
function positionsEdit($position_id, $event_id, $title, $notify_night, $type, $leader_id){
	// Set global
	global $PDO;


	// Database call
	$query = "UPDATE positions SET event_id = :event_id, title = :title, notify_night = :notify_night, type = :type WHERE position_id = :position_id";
	$statement = $PDO->prepare($query);
	$params = array(
		'event_id' => $event_id,
		'title' => $title,
		'notify_night' => $notify_night,
		'type' => $type,
		'position_id' => $position_id,
	);
	$statement->execute($params);
	
	
	// Update assignment
	$row_assignment = assignmentsLoadSingleWithPositionID($position_id);
	if($row_assignment['leader_id'] != $leader_id)
		assignmentsEdit(intval($row_assignment['assignment_id']), $position_id, $leader_id);
}


// Function to delete a position
function positionsDelete($position_id){
	// Set global
	global $PDO;


	// Database call
	$query = "DELETE FROM positions WHERE position_id = :position_id";
	$statement = $PDO->prepare($query);
	$params = array(
		'position_id' => $position_id
	);
	$statement->execute($params);
}


// Function to load a single position
function positionsLoadSingle($position_id){
	// Set global
	global $PDO;


	// Database call
	$query = "SELECT * FROM positions WHERE position_id = :position_id";
	$statement = $PDO->prepare($query);
	$params = array(
		'position_id' => $position_id
	);
	$statement->execute($params);


	// Return
	return $statement->fetch();
}


// Function to load multiple positions
function positionsLoadMultiple(){
	// Set global
	global $PDO;


	// Database call
	$query = "SELECT * FROM positions INNER JOIN events ON positions.event_id=events.event_id";
	$statement = $PDO->query($query);


	// Return
	return $statement->fetchAll();
}


// Function to load multiple positions for today
function positionsLoadMultipleToday(){
	// Set global
	global $PDO;


	// Database call
	$query = "SELECT * FROM positions INNER JOIN events ON positions.event_id=events.event_id WHERE events.date = CURDATE()";
	$statement = $PDO->query($query);


	// Return
	return $statement->fetchAll();
}


// Function to add an assignment
function assignmentsAdd($position_id, $leader_id){
	// Set global
	global $PDO;


	// Database call
	$query = "INSERT INTO assignments SET position_id = :position_id, leader_id = :leader_id";
	$statement = $PDO->prepare($query);
	$params = array(
		'position_id' => $position_id,
		'leader_id' => $leader_id
	);
	$statement->execute($params);
}


// Function to edit an assignment
function assignmentsEdit($assignment_id, $position_id, $leader_id){
	// Set global
	global $PDO;


	// Database call
	$query = "UPDATE assignments SET position_id = :position_id, leader_id = :leader_id, was_there = 0, confirmed_text = 0 WHERE assignment_id = :assignment_id";
	$statement = $PDO->prepare($query);
	$params = array(
		'position_id' => $position_id,
		'leader_id' => $leader_id,
		'assignment_id' => $assignment_id
	);
	$statement->execute($params);
}


// Function to delete an assignment
function assignmentsDelete($assignment_id){
	// Set global
	global $PDO;


	// Database call
	$query = "DELETE FROM assignments WHERE assignment_id = :assignment_id";
	$statement = $PDO->prepare($query);
	$params = array(
		'assignment_id' => $assignment_id
	);
	$statement->execute($params);
}


// Function to load a single assignment
function assignmentsLoadSingle($assignment_id){
	// Set global
	global $PDO;


	// Database call
	$query = "SELECT * FROM assignments WHERE assignment_id = :assignment_id";
	$statement = $PDO->prepare($query);
	$params = array(
		'assignment_id' => $assignment_id
	);
	$statement->execute($params);


	// Return
	return $statement->fetch();
}


// Function to load a single assignment
function assignmentsLoadSingleWithPositionID($position_id){
	// Set global
	global $PDO;


	// Database call
	$query = "SELECT * FROM assignments WHERE position_id = :position_id";
	$statement = $PDO->prepare($query);
	$params = array(
		'position_id' => $position_id
	);
	$statement->execute($params);


	// Return
	return $statement->fetch();
}


// Function to set was there to yes
function assignmentsWasThereYes($assignment_id){
	// Set global
	global $PDO;


	// Database call
	$query = "UPDATE assignments SET was_there = 1 WHERE assignment_id = :assignment_id";
	$statement = $PDO->prepare($query);
	$params = array(
		'assignment_id' => $assignment_id
	);
	$statement->execute($params);
}


// Function to set was there to no
function assignmentsWasThereNo($assignment_id){
	// Set global
	global $PDO;


	// Database call
	$query = "UPDATE assignments SET was_there = 0 WHERE assignment_id = :assignment_id";
	$statement = $PDO->prepare($query);
	$params = array(
		'assignment_id' => $assignment_id
	);
	$statement->execute($params);
}


// Function to set was there to yes
function assignmentsConfirmedTextYes($cell_phone){
	// Set global
	global $PDO;
	
	
	// Database call
	$query = "SELECT last_text_re_assignment_id FROM leaders WHERE cell_phone = :cell_phone";
	$statement = $PDO->prepare($query);
	$params = array(
		'cell_phone' => $cell_phone,
	);
	$statement->execute($params);
	$row = $statement->fetch();


	// Database call
	$query = "UPDATE assignments SET confirmed_text = 1 WHERE assignment_id = :assignment_id";
	$statement = $PDO->prepare($query);
	$params = array(
		'assignment_id' => $row['last_text_re_assignment_id'],
	);
	$statement->execute($params);
}


// Function to set was there to no
function assignmentsConfirmedTextNo($cell_phone){
	// Set global
	global $PDO, $BASE_PATH;
	
	
	// Database call
	$query = "SELECT last_text_re_assignment_id, first_name FROM leaders WHERE cell_phone = :cell_phone";
	$statement = $PDO->prepare($query);
	$params = array(
		'cell_phone' => $cell_phone,
	);
	$statement->execute($params);
	$row = $statement->fetch();


	// Database call
	$query = "UPDATE assignments SET confirmed_text = 2 WHERE assignment_id = :assignment_id";
	$statement = $PDO->prepare($query);
	$params = array(
		'assignment_id' => $row['last_text_re_assignment_id'],
	);
	$statement->execute($params);
	
	
	// Get notify number
	$notify_number = preg_replace('/[^0-9]/', '', file_get_contents($BASE_PATH.'/config/number.txt'));
	
	
	// Alert notify number
	if($row['first_name'] != '')
		send_sms($notify_number, $row['first_name'].' said they couldn\'t fill their position. -FWCM');
}


// Change notify number
function notifyNumberEdit($number){
	// Set global
	global $PDO, $BASE_PATH;
	
	
	// Set notify number
	file_put_contents($BASE_PATH.'/config/number.txt', preg_replace('/[^0-9]/', '', $number));
}


// Function to send reminders
function send_reminders(){
	// Set global
	global $PDO;
	
	
	// ***** Send night before *****
	if(intval(date('G')) > 20)
	{
		// Debug
		echo 'After 8pm'.PHP_EOL;
		
		
		// Database call
		$query = "SELECT * FROM events WHERE date = :date";
		$statement = $PDO->prepare($query);
		$params = array(
			'date' => date('Y-m-d', strtotime('tomorrow')),
		);
		$statement->execute($params);
		
		
		// Loop through them
		while($row_event = $statement->fetch())
		{
			// Debug
			echo 'Found event: '.$row_event['name'].PHP_EOL;
			
			
			// Database call
			$query_position = "SELECT * FROM positions WHERE notify_night = 1 AND event_id = :event_id";
			$statement_position = $PDO->prepare($query_position);
			$params_position = array(
				'event_id' => $row_event['event_id'],
			);
			$statement_position->execute($params_position);
			
			
			// Loop through them
			while($row_position = $statement_position->fetch())
			{
				// Debug
				echo 'Found position: '.$row_event['title'].PHP_EOL;
				
				
				// Database call
				$query_assignment = "SELECT * FROM assignments WHERE position_id = :position_id";
				$statement_assignment = $PDO->prepare($query_assignment);
				$params_assignment = array(
					'position_id' => $row_position['position_id'],
				);
				$statement_assignment->execute($params_assignment);
				$row_assignment = $statement_assignment->fetch();
				
				
				// Make sure there's an assignment
				if($statement_assignment->rowCount() == 0)
				{
					// Debug
					echo 'No assignment'.PHP_EOL;
					continue;
				}
				
				
				// Database call
				$query_leader = "SELECT * FROM leaders WHERE leader_id = :leader_id";
				$statement_leader = $PDO->prepare($query_leader);
				$params_leader = array(
					'leader_id' => $row_assignment['leader_id'],
				);
				$statement_leader->execute($params_leader);
				$row_leader = $statement_leader->fetch();
				
				
				// Debug
				echo 'Found leader: '.$row_leader['first_name'].PHP_EOL;
				
				
				// Send text if needed
				if(!$row_assignment['sent_reminder'])
				{
					// Debug
					echo 'Hasn\'t sent reminder'.PHP_EOL;
					
					
					// Make sure there's a cellphone
					if($row_leader['cell_phone'] == 0)
					{
						// Debug
						echo 'No cell phone'.PHP_EOL;
						continue;
					}
					
					
					// Send text
					$text = 'Hey '.$row_leader['first_name'].', this is just a reminder that you are assigned to "'.$row_position['title'].'" for '.$row_event['name'].' tomorrow.';
					send_sms($row_leader['cell_phone'], $text);
					
					
					// Database call
					$query_assignment = "UPDATE assignments SET sent_reminder = 1 WHERE assignment_id = :assignment_id";
					$statement_assignment = $PDO->prepare($query_assignment);
					$params_assignment = array(
						'assignment_id' => $row_assignment['assignment_id'],
					);
					$statement_assignment->execute($params_assignment);
				}
			}
		}
	}
	
	
	// ***** Send morning of *****
	if(intval(date('G')) > 7)
	{
		// Debug
		echo 'After 7am'.PHP_EOL;
		
		
		// Database call
		$query = "SELECT * FROM events WHERE date = :date";
		$statement = $PDO->prepare($query);
		$params = array(
			'date' => date('Y-m-d'),
		);
		$statement->execute($params);
		
		
		// Loop through them
		while($row_event = $statement->fetch())
		{
			// Debug
			echo 'Found event: '.$row_event['name'].PHP_EOL;
			
			
			// Database call
			$query_position = "SELECT * FROM positions WHERE notify_night = 0 AND event_id = :event_id";
			$statement_position = $PDO->prepare($query_position);
			$params_position = array(
				'event_id' => $row_event['event_id'],
			);
			$statement_position->execute($params_position);
			
			
			// Loop through them
			while($row_position = $statement_position->fetch())
			{
				// Debug
				echo 'Found position: '.$row_event['title'].PHP_EOL;
				
				
				// Database call
				$query_assignment = "SELECT * FROM assignments WHERE position_id = :position_id";
				$statement_assignment = $PDO->prepare($query_assignment);
				$params_assignment = array(
					'position_id' => $row_position['position_id'],
				);
				$statement_assignment->execute($params_assignment);
				$row_assignment = $statement_assignment->fetch();
				
				
				// Make sure there's an assignment
				if($statement_assignment->rowCount() == 0)
				{
					// Debug
					echo 'No assignment'.PHP_EOL;
					continue;
				}
				
				
				// Database call
				$query_leader = "SELECT * FROM leaders WHERE leader_id = :leader_id";
				$statement_leader = $PDO->prepare($query_leader);
				$params_leader = array(
					'leader_id' => $row_assignment['leader_id'],
				);
				$statement_leader->execute($params_leader);
				$row_leader = $statement_leader->fetch();
				
				
				// Debug
				echo 'Found leader: '.$row_leader['first_name'].PHP_EOL;
				
				
				// Send text if needed
				if(!$row_assignment['sent_reminder'])
				{
					// Debug
					echo 'Hasn\'t sent reminder'.PHP_EOL;
					
					
					// Make sure there's a cellphone
					if($row_leader['cell_phone'] == 0)
					{
						// Debug
						echo 'No cell phone'.PHP_EOL;
						continue;
					}
					
					
					// Send text
					$text = 'Hey '.$row_leader['first_name'].', this is just a reminder that you are assigned to "'.$row_position['title'].'" for '.$row_event['name'].' today.';
					send_sms($row_leader['cell_phone'], $text);
					
					
					// Debug
					echo 'send_sms('.$row_leader['cell_phone'].', '.$text.');'.PHP_EOL;
					
					
					// Database call
					$query_assignment = "UPDATE assignments SET sent_reminder = 1 WHERE assignment_id = :assignment_id";
					$statement_assignment = $PDO->prepare($query_assignment);
					$params_assignment = array(
						'assignment_id' => $row_assignment['assignment_id'],
					);
					$statement_assignment->execute($params_assignment);
				}
			}
		}
	}
}
?>
