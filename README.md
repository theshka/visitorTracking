visitorTracking
======

[![GitHub License](https://img.shields.io/badge/license-MIT%20License-blue.svg)](https://github.com/theshka/visitorTracking/blob/master/LICENSE)
[![Software Version](https://img.shields.io/badge/version-1.2.1-red.svg)](https://github.com/theshka/visitorTracking/releases/latest)
[![Github Downloads](https://img.shields.io/github/downloads/theshka/visitorTracking/latest/total.svg)](https://github.com/theshka/visitorTracking/releases)

---

**visitorTracking** is a simple PHP class to gather visitor information, and store it in a database using MYSQLi. It's more modern and complete than others I have seen around on Google/HotScripts. (SQLi, oop(ish?), etc...) No bells or whistles, fully customizable.

## Download
* [Dev-Master](https://github.com/theshka/visitorTracking/archive/master.zip)
* [Version 1.2.1](https://github.com/theshka/visitorTracking/archive/v.1.2.1.zip)

### Old Versions
* [Version 0.0.1](https://github.com/theshka/visitorTracking/archive/v0.1.zip)

## Documentation
[http://tyrexi.us/visitorTrackingDocumentation](http://tyrexi.us/visitorTrackingDocumentation)

## Demo
[http://tyrexi.us/visitorTracking](http://tyrexi.us/visitorTracking)

## Install
1. Upload the files to your server.

1. Edit the database configuration ( `src/_installation/db.php` )

1. Create the `visitors` table in your database. (`src/_installation/visitors.sql` )

1. Instance the class in your code.

## Usage
```php
//define database
define( 'DB_HOST', 'localhost' );           // set database host
define( 'DB_USER', 'root' );                // set database user
define( 'DB_PASS', '' );                    // set database password
define( 'DB_NAME', 'yourdatabasename' );    // set database name

//include the class
include( 'src/class.visitorTracking.php' );

//instance the class
$visitors = new visitorTracking();
```
The constructor method automatically calls the tracking method `$visitors->track();` which inserts the collected data.

You print the array containing the current visit by echoing `$visitors->displayThisVisit();`

You can output a table containing all the paginated data from the database by echoing `$visitors->displayVisitors();`

Alternatively, you can just grab the table data from your database `SELECT * FROM visitors ORDER BY date DESC` and format it according to your specifications.

## License
This project is licensed under the [MIT LICENSE](https://github.com/theshka/visitorTracking/blob/master/LICENSE)

## Contributors

### Contributors on GitHub
* [Contributors](https://github.com/theshka/visitorTracking/graphs/contributors)

## Contributing
If you would like to help make this software better, please follow our guidelines found in [CONTRIBUTING.md](https://github.com/theshka/visitorTracking/blob/master/CONTRIBUTING.md)

## Contact
* Homepage: http://heshka.com
* E-mail: tyler@heshka.com
* KeyBase: https://keybase.io/theshka
