<?php
class visitorTracking {

	var $thisVisit = null;
	private $link = null;
	
	/**
	 * CLASS CONSTRUCTOR 
	 */
	function __construct() {
		
		//Initialize the database 
		$this->db_connect();
	
		//Track the visit
		$this->track();
		
	}
	
	/**
	 * CLASS DESTRUCTOR 
	 */
	public function __destruct(){
    		//Disconnect the database
        	if( $this->link){
            		$this->disconnect();
        	}
	}
	
	/**
	 * Connect to the database
	 */
	private function db_connect() {
		
		//Establish MYSQLi link 
		mb_internal_encoding( 'UTF-8' );
		mb_regex_encoding( 'UTF-8' );
		mysqli_report( MYSQLI_REPORT_STRICT );
		try {
			$this->link = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME );
			$this->link->set_charset( "utf8" );
		} catch ( Exception $e ) {
			die( 'Unable to connect to database' );
		}
		
	}
	
	/**
	 * Track visit, insert in database
	 */
	private function track(){
	
		//Prepare variables
		$visitor_ip 		= GetHostByAddr($this->getIP());
		$ip_location 		= $this->geoCheckIP($this->getIP());
		$visitor_city 		= $ip_location['town'];
		$visitor_state 		= $ip_location['state'];
		$visitor_country 	= $ip_location['country'];
		$visitor_browser 	= $this->getBrowserType();
		$visitor_date 		= $this->getDate("Y-m-d h:i:sA");
		$visitor_day 		= $this->getDate("d");
		$visitor_month 		= $this->getDate("m");
		$visitor_year 		= $this->getDate("Y");
		$visitor_hour 		= $this->getDate("h");
		$visitor_minute 	= $this->getDate("i");
		$visitor_seconds 	= $this->getDate("s");
		$visitor_referer 	= $this->getReferer();
		$visitor_page 		= $this->getRequestURI();
		
		//Gather variables in array
		$visitor = array(
			'visitor_ip' => $visitor_ip,
			'visitor_city' => $visitor_city,
			'visitor_state' => $visitor_state,
			'visitor_country' => $visitor_country,
			'visitor_browser' => $visitor_browser,
			'visitor_date' => $visitor_date,
			'visitor_day' => $visitor_day,
			'visitor_month' => $visitor_month,
			'visitor_year' => $visitor_year,
			'visitor_hour' => $visitor_hour,
			'visitor_minute' => $visitor_minute,
			'visitor_seconds' => $visitor_seconds,
			'visitor_referer' => $visitor_referer,
			'visitor_page' => $visitor_page
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
            die ( 'ERROR! Please check your database settings.' );
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
	 */	
	private function getIP() {
		
		if ( ! empty( $_SERVER['REMOTE_ADDR'] ) ){
	 
			$ip = $_SERVER['REMOTE_ADDR'];
			
			return $ip;
	 	}
	 	
	 	return false;
	 	
	}
	
	/**
	 * Geo-locate visitor IP address
	 */
	private function geoCheckIP($ip) {
	
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
		   $patterns["domain"] = '#Domain: (.*?)&nbsp;#i';
		   $patterns["country"] = '#Country: (.*?)&nbsp;#i';
		   $patterns["state"] = '#State/Region: (.*?)<br#i';
		   $patterns["town"] = '#City: (.*?)<br#i';
	
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
	 * Get the visitor browser-type
	 */
	private function getBrowserType () {	
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
		if (ereg('Opera(/| )([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version)) 
		{ 
		   $browser_version = $log_version[2]; 
		   $browser_agent = 'opera'; 
		} 
		else if (ereg('MSIE ([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version)) 
		{ 
		   $browser_version = $log_version[1]; 
		   $browser_agent = 'ie'; 
		} 
		else if (ereg('OmniWeb/([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version)) 
		{ 
		   $browser_version = $log_version[1]; 
		   $browser_agent = 'omniweb'; 
		} 
		else if (ereg('Netscape([0-9]{1})', $HTTP_USER_AGENT, $log_version)) 
		{ 
		   $browser_version = $log_version[1]; 
		   $browser_agent = 'netscape';
		} 
		else if (ereg('Mozilla/([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version)) 
		{ 
		   $browser_version = $log_version[1]; 
		   $browser_agent = 'WebKit'; 
		} 
		else if (ereg('Konqueror/([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version)) 
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
	 * Get the date bits, used for search/filtering
	 */
	private function getDate($i) {

		//get the requested date
		$date = date($i);
		
		//return the date
		return $date;

	}
	
	/**
	 * Get the referring page, if any is sent
	 */
	private function getReferer() {
 
		$ref = false;
	 
		if ( ! empty( $_SERVER['HTTP_REFERER'] ) ){
	 
			$ref = $_SERVER['HTTP_REFERER'];
			
			return $ref;
	 	}
		
		return false;
	 
	}
	
	/**
	 * Get the requested page
	 */
	private function getRequestURI() { 
		
		$uri = false;
		
		if ( ! empty( $_SERVER['REQUEST_URI'] ) ){
	 
			$uri = $_SERVER['REQUEST_URI'];
			
			return $uri;
	 	}
	 	
	 	return false;
		
	}
	
	/**
	 * Display the current visit array
	 */	
	public function displayThisVisit() {
		print_r($this->thisVisit);
	}
	
	/**
	 * Display the visitors table
	 */
	public function displayVisitors() {           
		
		/**
		 * Retrieving a single row of data
		 */
		$query = $this->link->query("SELECT COUNT(*) AS count FROM visitors");
		if( $query->num_rows > 0 )
		{
			list( $numrows ) = $query->fetch_row();
			
			// number of rows to show per page
			$rowsperpage = 10;
			
			// find out total pages
			$totalpages = ceil($numrows / $rowsperpage);
			
			// get the current page or set a default
			if (isset($_GET['paginate']) && is_numeric($_GET['paginate'])) {
			   // cast var as int
			   $paginate = (int) $_GET['paginate'];
			} else {
			   // default page num
			   $paginate = 1;
			} // end if
			
			// if current page is greater than total pages...
			if ($paginate > $totalpages) {
			   // set current page to last page
			   $paginate = $totalpages;
			} // end if
			// if current page is less than first page...
			if ($paginate < 1) {
			   // set current page to first page
			   $paginate = 1;
			} // end if
			
			// the offset of the list, based on current page 
			$offset = ($paginate - 1) * $rowsperpage;
			
		}

		echo '
		<table id="mytable" class="table table-bordred table-striped">
		<thead>				
			<th>IP Address</th>
			<th>Browser</th>
			<th>City</th>
			<th>State</th>
			<th>Country</th>
			<th>Date</th>
			<th>Referer</th>
			<th>Page</th>
		</thead>
		<tbody>
		';
		
		$results = $this->link->query( "SELECT * FROM `visitors` ORDER BY `visitor_date` DESC LIMIT $offset, $rowsperpage" );

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
					<td width="10%">' . $r['visitor_ip'] . '</td>
					<td width="10%">' . $r['visitor_browser'] . '</td>
					<td width="10%">' . $r['visitor_city'] . '</td>
					<td width="10%">' . $r['visitor_state'] . '</td>
					<td width="10%">' . $r['visitor_country'] . '</td>
					<td width="10%">' . $r['visitor_date'] . '</td>
					<td width="10%">' . $r['visitor_referer'] . '</td>
					<td width="10%">' . $r['visitor_page'] . '</td>
				</tr>
				';	
			}  
		}
		
		echo'
		
			</tbody>
		</table>
		<br>
		';
				
		/******  build the pagination links ******/
		// range of num links to show
		$range = 3;
		
		// if not on page 1, don't show back links
		if ($paginate > 1) {
		   // show << link to go back to page 1
		   echo "<a href='{$_SERVER['PHP_SELF']}?paginate=1'> First </a>";
		   // get previous page num
		   $prevpage = $paginate - 1;
		   // show < link to go back to 1 page
		   echo "<a href='{$_SERVER['PHP_SELF']}?paginate=$prevpage'> < </a>";
		} // end if 
		
		// loop to show links to range of pages around current page
		for ($x = ($paginate - $range); $x < (($paginate + $range) + 1); $x++) {
		   // if it's a valid page number...
		   if (($x > 0) && ($x <= $totalpages)) {
			  // if we're on current page...
			  if ($x == $paginate) {
				 // 'highlight' it but don't make a link
				 echo "<a>$x</a>";
			  // if not current page...
			  } else {
				 // make it a link
				 echo "<a href='{$_SERVER['PHP_SELF']}?&paginate=$x'>$x</a>";
			  } // end else
		   } // end if 
		} // end for
						 
		// if not on last page, show forward and last page links        
		if ($paginate != $totalpages) {
		   // get next page
		   $nextpage = $paginate + 1;
			// echo forward link for next page 
		   echo "<a href='{$_SERVER['PHP_SELF']}?paginate=$nextpage'> > </a>";
		   // echo forward link for lastpage
		   echo "<a href='{$_SERVER['PHP_SELF']}?paginate=$totalpages'> Last </a>";
		} // end if
		/****** end build pagination links ******/
		
	}
	
}
