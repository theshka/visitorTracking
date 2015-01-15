# visitorTracking
A simple PHP class to gather visitor information, and store it in a database using MYSQLi

INSTALLATION
------------------------------------------

1) Upload the files to your server. 

2) Edit the database configuration ( /includes/db.php ) 

3) Create the `visitors` table in your database. (.sql file included )

4) Instance the class in your code.

Enjoy!


EXAMPLE INITIALIZATION:
------------------------------------------

//define database
define( 'DB_HOST', 'localhost' );			// set database host
define( 'DB_USER', 'root' ); 				// set database user
define( 'DB_PASS', 'root' ); 				// set database password
define( 'DB_NAME', 'yourdatabasename' );	// set database name

//include the class
include( 'class.visitorTracking.php' );

//instance the class
$visitors = new visitorTracking();
