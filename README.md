# visitorTracking
A simple PHP class to gather visitor information, and store it in a database using MYSQLi

LICENSE
----------------------------------------------
CC0 - Released to the Public Domain

To the extent possible under law, the author and contributers have waived all copyright and 
related or neighboring rights to this work.

THIS SOFTWARE IS BEING PROVIDED "AS IS", WITHOUT ANY EXPRESS OR IMPLIED WARRANTY.  
IN PARTICULAR, THE AUTHOR DOES NOT MAKE ANY REPRESENTATION OR WARRANTY OF ANY KIND 
CONCERNING THE MERCHANTABILITY OF THIS SOFTWARE OR ITS FITNESS FOR ANY PARTICULAR PURPOSE.


INSTALLATION
----------------------------------------------

1) Upload the files to your server. 

2) Edit the database configuration ( /includes/db.php ) 

3) Create the `visitors` table in your database. (.sql file included )

4) Instance the class in your code.

Demo: http://tyrexi.us/demo/visitorTracking


EXAMPLE INITIALIZATION:
----------------------------------------------

//define database
define( 'DB_HOST', 'localhost' );			// set database host
define( 'DB_USER', 'root' ); 				// set database user
define( 'DB_PASS', 'root' ); 				// set database password
define( 'DB_NAME', 'yourdatabasename' );	// set database name

//include the class
include( 'class.visitorTracking.php' );

//instance the class
$visitors = new visitorTracking();
