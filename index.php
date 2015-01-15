<?php
	/**
	 * include database settings
	 */
	require_once( 'includes/db.php' );
	
	/**
	 * include the class
	 */
	require_once( 'includes/class.visitorTracking.php' );
	
	/**
	 * instance the class
	 */
	$visitors = new visitorTracking();
?>

<html>
	<head>
		<title>PHP + MySQLi - Visitor Tracking Class</title>
	</head>
	
	<body>
		<h2>This visit has been tracked.</h2>
		<pre><?php echo $visitors->displayThisVisit(); ?></pre>
		
		<br><hr><br>
		
		<h2>All tracked visits:</h2>
		<pre><?php echo $visitors->displayVisitors(); ?></pre>
	</body>
</html>
