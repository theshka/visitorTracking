<!-- Example 1, display the current visit on a map. -->

<?php
    /**
     * include database settings
     */
    require_once( '../includes/db.php' );

    /**
     * include the class
     */
    require_once( '../includes/class.visitorTracking.php' );

    /**
     * instance the class
     */
    $visitors = new visitorTracking();
?>

<html>
    <head>
        <title>PHP + MySQLi - Visitor Tracking Class - Examples 1</title>
    </head>

    <body>
        <div style="float:left;"><a href="/demo/visitorTracking/">&larr; Demo</a></div>

        <br>

        <h2>This visit has been tracked.</h2>
        <pre><?php $visitors->displayThisVisit(); ?></pre>

        <iframe width="100%" height="325" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"
        src="https://maps.google.co.uk/maps?f=q&source=s_q&hl=en&geocode=&q=<?php echo urlencode($visitors->getThisVisit()['visitor_city'] . ' ' . $visitors->getThisVisit()['visitor_state'] . ' ' . $visitors->getThisVisit()['visitor_country']);?>&output=embed"></iframe>

        <br><hr><br>
        <h2>Code:</h2>
        <XMP>

            < ?php
                /**
                 * include database settings
                 */
                require_once( '../includes/db.php' );

                /**
                 * include the class
                 */
                require_once( '../includes/class.visitorTracking.php' );

                /**
                 * instance the class
                 */
                $visitors = new visitorTracking();
            ? >

            <html>
                <head>
                    <title>PHP + MySQLi - Visitor Tracking Class - Examples 1</title>
                </head>

                <body>
                    <h2>This visit has been tracked.</h2>
                    <pre>< ?php print_r($visitors->getThisVisit()); ? ></pre>

                    <iframe width="100%" height="325" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"
                    src="https://maps.google.co.uk/maps?f=q&source=s_q&hl=en&geocode=&q=< ? php echo urlencode($visitors->getThisVisit()['visitor_city'] . ' ' . $visitors->getThisVisit()['visitor_state'] . ' ' . $visitors->getThisVisit()['visitor_country']); ? >&output=embed"></iframe>

                </body>
            </html>

        </XMP>
    </body>
</html>
