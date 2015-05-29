<?php
/**
 * A simple PHP class to gather visitor information, and store it in a database using MYSQLi
 *
 * visitorTracking
 *
 * LICENSE: THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE
 * FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF
 * CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @category   HTML,PHP5,Databases,Geography
 * @author     Tyler Heshka <tyler@heshka.com>
 * @see        http://keybase.io/theshka
 * @license    http://opensource.org/licenses/MIT
 * @version    1.20.00
 * @link       http://tyrexi.us/visitorTracking
*/

/**
 * The visitorTracking class
 *
 * This PHP class gathers detailed visitor information,
 * and stores the visit in a database using MYSQLi.
 */
class visitorTracking
{
	/**
	 * Stores "thisVisit" array
	 */
	var $thisVisit = null;

	/**
	 * MYSQLi connection
	 */
	private $link = null;

   /**
    * The constructor method
    *
    * This method calls the db_connect method, which constructs
    * and initializes the conection to the database. Once established,
    * the track method is called. This method gathers the data to insert.
    *
    * @access public
    */
	public function __construct()
	{

		//Call the db_connect method
		$this->db_connect();

		//Call the track method
		$this->track();

	}

   /**
    * The destructor method
    *
    * This method tests for a connection to the database,
    * if the connection is active, this method will close
    * the connection to the MYSQLi database.
    *
    * @access private
    */
	private function __destruct()
	{

		//Test for database connection
		if( $this->link )
		{
			//Disconnect the database
			$this->link->close();
		}

	}

   /**
    * Connect to the databse
    *
    * This method sets the character encoding for the databse,
    * then trys to connect to the databse using MYSQLi. The
    * constants are defined in a seperate file and included at runtime.
    * If the method fails to connect with the database, the script dies,
    * and a unable to connect error is shown to the user.
    *
    * @access private
    */
	private function db_connect()
	{

		//Establish MYSQLi link
		mb_internal_encoding( 'UTF-8' );
		mb_regex_encoding( 'UTF-8' );
		mysqli_report( MYSQLI_REPORT_STRICT );

		try
		{
			$this->link = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME );
			$this->link->set_charset( "utf8" );
		}
		catch ( Exception $e )
		{
			die( 'Unable to connect to database' );
		}

	}

	/**
	 * Tracking Method
	 *
	 * This is the main tracking method. It gathers all of the
	 * other methods in the class in to an array, and then inserts
	 * the array in to the database. If a connection to the database
	 * cannot be established, an error is shown to the user.
	 *
	 * @access protected
	 */
	protected function track()
	{
		//TODO: rewrite geoCheckIP function, consolidate variables with array.

		//Prepare variables
		$visitor_ip 		= $this->getIP(TRUE);
		$ip_location		= $this->geoCheckIP($this->getIP());
		$visitor_city		= $ip_location['town'];
		$visitor_state		= $ip_location['state'];
		$visitor_country	= explode('-', $ip_location['country']);
		$visitor_ccode		= trim($visitor_country[0]);
		$visitor_cname		= trim($visitor_country[1]);
		$visitor_flag		= $this->getFlag($visitor_ccode);
		$visitor_browser	= $this->getBrowserType();
		$visitor_OS			= $this->getOS();
		$visitor_date		= $this->getDate("Y-m-d h:i:sA");
		$visitor_day		= $this->getDate("d");
		$visitor_month		= $this->getDate("m");
		$visitor_year		= $this->getDate("Y");
		$visitor_hour		= $this->getDate("h");
		$visitor_minute		= $this->getDate("i");
		$visitor_seconds	= $this->getDate("s");
		$visitor_referer	= $this->getReferer();
		$visitor_page		= $this->getRequestURI();

		//Gather variables in array
		$visitor = array(
			'visitor_ip' 		=> $visitor_ip,
			'visitor_city' 		=> $visitor_city,
			'visitor_state' 	=> $visitor_state,
			'visitor_country' 	=> $visitor_cname,
			'visitor_flag' 		=> $visitor_flag,
			'visitor_browser' 	=> $visitor_browser,
			'visitor_OS' 		=> $visitor_OS,
			'visitor_date' 		=> $visitor_date,
			'visitor_day' 		=> $visitor_day,
			'visitor_month' 	=> $visitor_month,
			'visitor_year' 		=> $visitor_year,
			'visitor_hour' 		=> $visitor_hour,
			'visitor_minute' 	=> $visitor_minute,
			'visitor_seconds' 	=> $visitor_seconds,
			'visitor_referer' 	=> $visitor_referer,
			'visitor_page' 		=> $visitor_page
		);

		//Make sure the array isn't empty
        	if( empty( $visitor ) )
        	{
            		return false;
        	}

        	//Insert the data
        	$sql = "INSERT INTO `visitors`";
        	$fields = array();
        	$values = array();

        	foreach( $visitor as $field => $value )
        	{
            		$fields[] = $field;
            		$values[] = "'".$value."'";
        	}
        	$fields = ' (' . implode(', ', $fields) . ')';
        	$values = '('. implode(', ', $values) .')';

        	$sql .= $fields .' VALUES '. $values;

        	$query = $this->link->query( $sql );

        	if( $this->link->error )
        	{
       			//return false;
       			die ( 'DB ERROR! Unable to track visit.' );
       			return false;
        	}
        	else
        	{
        		//set thisVisit variable equal to visitor array
        		$this->thisVisit = $visitor;

        		//return true
        		return true;

        	}

	}

	/**
	 * Get visitor IP address
	 *
	 * This method tests rigorously for the current users IP address
	 * It tests the $_SERVER vars to find IP addresses even if they
	 * are being proxied, forwarded, or otherwise obscured.
	 *
	 * @param boolean $getHostByAddr the IP address with hostname
	 * @return string $ip the formatted IP address
	 */
	private function getIP($getHostByAddr=FALSE)
	{

		foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key)
		{
			if (array_key_exists($key, $_SERVER) === true)
			{
				foreach (array_map('trim', explode(',', $_SERVER[$key])) as $ip)
				{
					if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false)
					{
						if ($getHostByAddr === TRUE)
						{
							return getHostByAddr($ip);
						}
						else
						{
							return $ip;
						}
					}
				}
			}
		}

	}

	/**
	 * Geo-locate visitor IP address
	 *
	 * This method accepts an IP address. It then tests the address
	 * for validity, connects to the netip.de geo server, and user
	 * a set of regex patterns to scrape the location results.
	 * The method then returns an array of the geolocation information.
	 *
	 * @param string $ip the IPv4 address to lookup on netip.de
	 * @return array geolocation data: country, state, town
	 */
	private function geoCheckIP($ip)
	{

		//check, if the provided ip is valid
		if(!filter_var($ip, FILTER_VALIDATE_IP) || $ip == 'localhost')
		{
			//throw new InvalidArgumentException("IP is not valid");
			return false;
		}

		//contact ip-server
		$response=@file_get_contents('http://www.netip.de/search?query='.$ip);
		if (empty($response))
		{
			//throw new InvalidArgumentException("Error contacting Geo-IP-Server");
			return false;
		}

		//Array containing all regex-patterns necessary to extract ip-geoinfo from page
		$patterns=array();
		$patterns["domain"] 	= '#Domain: (.*?)&nbsp;#i';
		$patterns["country"] 	= '#Country: (.*?)&nbsp;#i';
		$patterns["state"] 		= '#State/Region: (.*?)<br#i';
		$patterns["town"] 		= '#City: (.*?)<br#i';

 		//Array where results will be stored
		$ipInfo=array();

		//check response from ipserver for above patterns
		foreach ($patterns as $key => $pattern)
		{
			//store the result in array
			$ipInfo[$key] = preg_match($pattern,$response,$value) && !empty($value[1]) ? $value[1] : 'not found';
		}

		   return $ipInfo;

	}

	/**
	 * Get country flag
	 *
	 * This method accepts a 2-charcter, lowercase, country code.
	 * It then matches the code to the corresponding image file
	 * in the includes/famfamfam-countryflags directory. Finally,
	 * it returns a complete HTML IMG tag.
	 *
	 * @param string $countryCode the two character country code from geoCheckIP
	 * @return string $flag the finished img tag containing country flag
	 */
	private function getFlag($countryCode)
	{

		$flag = '<img src="includes/famfamfam-countryflags/' . strtolower($countryCode) . '.gif" height="15px" width="25px"/>';

		return $flag;

	}

	/**
	 * Get the visitor browser-type
	 *
	 * This method tests the $_SERVER vars for an HTTP_USER_AGENT entry.
	 * Through a series of if statements, preg_match, and regex patterns,
	 * a browser-type will be returned. If a browser-type is unable to be
	 * determined 'other' will be used in it's place.
	 *
	 * @param null
	 * @return string|null $browser_agent the formatted browser-type string
	 */
	private function getBrowserType ()
	{

		if (!empty($_SERVER['HTTP_USER_AGENT']))
		{
			$HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT'];
		}
		else if (!empty($HTTP_SERVER_VARS['HTTP_USER_AGENT']))
		{
			$HTTP_USER_AGENT = $HTTP_SERVER_VARS['HTTP_USER_AGENT'];
		}
		else if (!isset($HTTP_USER_AGENT))
		{
			$HTTP_USER_AGENT = '';
		}
		if (preg_match('#Opera(/| )([0-9].[0-9]{1,2})#', $HTTP_USER_AGENT, $log_version))
		{
			$browser_version = $log_version[2];
			$browser_agent = 'Opera';
		}
		else if (preg_match('#MSIE ([0-9].[0-9]{1,2})#', $HTTP_USER_AGENT, $log_version))
		{
			$browser_version = $log_version[1];
			$browser_agent = 'IE';
		}
		else if (preg_match('#OmniWeb/([0-9].[0-9]{1,2})#', $HTTP_USER_AGENT, $log_version))
		{
			$browser_version = $log_version[1];
			$browser_agent = 'OmniWeb';
		}
		else if (preg_match('#Netscape([0-9]{1})#', $HTTP_USER_AGENT, $log_version))
		{
			$browser_version = $log_version[1];
			$browser_agent = 'Netscape';
		}
		else if (preg_match('#Mozilla/([0-9].[0-9]{1,2})#', $HTTP_USER_AGENT, $log_version))
		{
			$browser_version = $log_version[1];
			$browser_agent = 'WebKit';
		}
		else if (preg_match('#Konqueror/([0-9].[0-9]{1,2})#', $HTTP_USER_AGENT, $log_version))
		{
			$browser_version = $log_version[1];
			$browser_agent = 'konqueror';
		}
		else
		{
			$browser_version = 0;
			$browser_agent = 'other';
		}

		return $browser_agent;

	}

	/**
	 * Get the visitor operating system
	 *
	 * This method tests the $_SERVER vars for an HTTP_USER_AGENT entry
	 * Through a series of if statements, preg_match, and regex patterns,
	 * the users OS will be returned. If a OS is unable to be determined
	 * the string 'other' will be used in it's place.
	 *
	 * @param null
	 * @return string $os_platform the formatted os-type string
	 */
	private function getOS()
	{

		$user_agent	=	$_SERVER['HTTP_USER_AGENT'];
		$os_platform	=	"Unknown OS Platform";
		$os_array	=	array(
						'/windows nt 6.3/i'     =>  'Windows 8.1',
						'/windows nt 6.2/i'     =>  'Windows 8',
						'/windows nt 6.1/i'     =>  'Windows 7',
						'/windows nt 6.0/i'     =>  'Windows Vista',
						'/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
						'/windows nt 5.1/i'     =>  'Windows XP',
						'/windows xp/i'         =>  'Windows XP',
						'/windows nt 5.0/i'     =>  'Windows 2000',
						'/windows me/i'         =>  'Windows ME',
						'/win98/i'              =>  'Windows 98',
						'/win95/i'              =>  'Windows 95',
						'/win16/i'              =>  'Windows 3.11',
						'/macintosh|mac os x/i' =>  'Mac OS X',
						'/mac_powerpc/i'        =>  'Mac OS 9',
						'/linux/i'              =>  'Linux',
						'/ubuntu/i'             =>  'Ubuntu',
						'/iphone/i'             =>  'iPhone',
						'/ipod/i'               =>  'iPod',
						'/ipad/i'               =>  'iPad',
						'/android/i'            =>  'Android',
						'/blackberry/i'         =>  'BlackBerry',
						'/webos/i'              =>  'Mobile'
					);

		foreach ($os_array as $regex => $value)
		{
			if (preg_match($regex, $user_agent))
			{
				$os_platform    =   $value;
			}
		}

		return $os_platform;

	}

	/**
	 * Get the date
	 *
	 * This method accepts a PHP gmdate() value. It is Identical to the date()
	 * function except that the time returned is Greenwich Mean Time (GMT).
	 * This is used to prevent timezone errors and inconsistencies.
	 *
	 * @param string $i the requested gmdate() character
	 * @return string $date the formatted gmdate date
	 */
	private function getDate($i)
	{

		//get the requested date
		$date = gmdate($i);

		//return the date
		return $date;

	}

	/**
	 * Get the referring page
	 *
	 * This method tests the $_SERVER vars for an HTTP_REFERER entry.
	 * If a referer is present, it will be returned. Otherwise, null
	 * will the the response.
	 *
	 * @param null
	 * @return string|null $ref the path to the refering page
	 */
	private function getReferer()
	{

		if ( ! empty( $_SERVER['HTTP_REFERER'] ) )
		{
			$ref = $_SERVER['HTTP_REFERER'];

			return $ref;
		}

		return false;

	}

	/**
	 * Get the requested page
	 *
	 * This method tests the $_SERVER vars for an REQUEST_URI entry.
	 * If the requested page is recorded by the server, it will be
	 * retuened. Otherwise, null will be the response.
	 *
	 * @param null
	 * @return string|null $uri the path to the requested page
	 */
	private function getRequestURI() {

		if ( ! empty( $_SERVER['REQUEST_URI'] ) )
		{
			$uri = $_SERVER['REQUEST_URI'];

			return $uri;
	 	}

	 	return false;

	}

	/**
	* Return the current visit array
	*
	* This method simply returns the compiled visitor information
	* in an array. You can use this to capture the current visit data
	* and display it, or use it for another purpose in your application.
	*
	* @param null
	* @return array $this->thisVisit() the compiled visitor information
	*/
	public function getThisVisit()
	{

		return($this->thisVisit);

	}

	/**
	 * Display the current visit array
	 *
	 * This method is identical to the getThisVisit method. The key
	 * difference is that this method is already wrapped in a print_r
	 * statement. This is used in the class examples.
	 *
	 * @param null
	 * @return array $this->thisVisit() the formatted and compiled visitor information
	 */
	public function displayThisVisit()
	{

		print_r($this->thisVisit);

	}

	/**
	 * Display Visitors
	 *
	 * This method queries the database for all entries in the visitors table,
	 * it then paginates the results, and outputs a unstyled HTML table. This
	 * method is used in the class examples.
	 *
	 * @param null
	 * @return array $this->displayVisitors() the html output from the database
	 */
	public function displayVisitors()
	{

		/**
		 * Retrieving a single row of data
		 */
		$query = $this->link->query("SELECT COUNT(*) AS `count` FROM `visitors`");
		if( $query->num_rows > 0 )
		{
			list( $numrows ) = $query->fetch_row();

			// number of rows to show per page
			$rowsperpage = 10;

			// find out total pages
			$totalpages = ceil($numrows / $rowsperpage);

			// get the current page or set a default
			if (isset($_GET['paginate']) && is_numeric($_GET['paginate']))
			{
			   // cast var as int
			   $paginate = (int) $_GET['paginate'];
			}
			else
			{
			   // default page num
			   $paginate = 1;
			}

			// if current page is greater than total pages...
			if ($paginate > $totalpages)
			{
			   // set current page to last page
			   $paginate = $totalpages;
			} // end if
			// if current page is less than first page...
			if ($paginate < 1)
			{
			   // set current page to first page
			   $paginate = 1;
			} // end if

			// the offset of the list, based on current page
			$offset = ($paginate - 1) * $rowsperpage;

		}

		echo '
		<table id="visitor" class="table">
			<thead>
				<th>IP Address</th>
				<th>Browser</th>
				<th>OS</th>
				<th>City</th>
				<th>State</th>
				<th>Country</th>
				<th>Date</th>
				<th>Page</th>
				<th>Referer</th>
			</thead>
			<tbody>
		';

		$results = $this->link->query( "SELECT * FROM `visitors` ORDER BY `id` DESC LIMIT $offset, $rowsperpage" );

		if( $this->link->error )
		{
			return false;
		}
		else
		{
			$row = array();
			while( $r = $results->fetch_assoc() )
			{
				echo
				'
				<tr>
					<td width="20%">' . $r['visitor_ip'] . '</td>
					<td width="10%">' . $r['visitor_browser'] . '</td>
					<td width="10%">' . $r['visitor_OS'] . '</td>
					<td width="10%">' . $r['visitor_city'] . '</td>
					<td width="10%">' . $r['visitor_state'] . '</td>
					<td width="10%">' . $r['visitor_flag'] . ' ' . $r['visitor_country'] . '</td>
					<td width="10%">' . $r['visitor_date'] . '</td>
					<td width="10%">' . $r['visitor_page'] . '</td>
					<td width="10%">' . $r['visitor_referer'] . '</td>
				</tr>
				';
			}
		}

		echo'

			</tbody>
		</table>

		<br>
		';

		echo'
		<div class="pagination" style="display:table;">
		';

			/******  build the pagination links ******/
			// range of num links to show
			$range = 3;

			// if not on page 1, don't show back links
			if ($paginate > 1)
			{
			   // show << link to go back to page 1
			   echo "<a href='{$_SERVER['PHP_SELF']}?paginate=1'> First </a>";
			   // get previous page num
			   $prevpage = $paginate - 1;
			   // show < link to go back to 1 page
			   echo "<a href='{$_SERVER['PHP_SELF']}?paginate=$prevpage'> < </a>";
			} // end if

			// loop to show links to range of pages around current page
			for ($x = ($paginate - $range); $x < (($paginate + $range) + 1); $x++)
			{
				// if it's a valid page number...
				if (($x > 0) && ($x <= $totalpages))
				{
					// if we're on current page...
					if ($x == $paginate) {
						// 'highlight' it but don't make a link
						echo "<a>$x</a>";
					// if not current page...
					}
					else {
					 // make it a link
					 echo "<a href='{$_SERVER['PHP_SELF']}?&paginate=$x'>$x</a>";
					} // end else
				} // end if
			} // end for

			// if not on last page, show forward and last page links
			if ($paginate != $totalpages)
			{
				// get next page
				$nextpage = $paginate + 1;
				// echo forward link for next page
				echo "<a href='{$_SERVER['PHP_SELF']}?paginate=$nextpage'> > </a>";
				// echo forward link for lastpage
				echo "<a href='{$_SERVER['PHP_SELF']}?paginate=$totalpages'> Last </a>";
			} // end if
			/****** end build pagination links ******/

		echo'
		</div>
		';
	}

}
