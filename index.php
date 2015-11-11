<?php
	/**
	 * include database settings
	 */
	require_once( 'src/_installation/db.php' );

	/**
	 * include the class
	 */
	require_once( 'src/class.visitorTracking.php' );

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
		<div style="float:left;"><a href="http://tyrexi.us/visitorTrackingDocumentation/" target="_blank">Documentation</a></div>
		<div style="float:right;"><a href="examples/map-example.php">Map Example</a></div>

		<br>

		<h2>This visit has been tracked.</h2>
		<pre><?php echo $visitors->displayThisVisit(); ?></pre>

		<br><hr><br>

		<h2>All tracked visits:</h2>
		<pre><?php echo $visitors->displayVisitors(); ?></pre>
	</body>
</html>
