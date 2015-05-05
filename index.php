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
                    <h1 class="page-header">Dashboard</h1>
                </div>
            </div>
            
            <div class="row">
                <div class="col-xs-12">
                	<?php
					$sayings = array('Hello, have a great day!', 'Cheerio! Hope the day is going swell!', 'What\'s up? Have an awesome day!', 'Howdy partner, ride like the wind today!', 'Greetings! May the odds be ever in your favor.', 'Today is going to be rockin\'', 'Good afternoon, your day is going to be stellar!', 'Hi, have a nice day!');
					echo $sayings[rand(0, count($sayings) - 1)];
					?>
                </div>
            </div>
        </div>
    </div>
    
    <?php require_once($_SERVER['DOCUMENT_ROOT'].'/includes/bottom-scripts.php'); ?>
</body>
</html>
