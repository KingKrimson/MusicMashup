<!-- LyricsWidget.php -->

<div id="lyricswidget">
    <img src="Widgets/tracklyricslogo.png"/>
    <div id="lyricsbody">
        <?php
        require_once("databasevars.php"); // Standard variables for access to Database:
        // $db_hostname, $db_username, $db_password, $db_database
        $db_server = mysqli_connect($db_hostname, $db_username, $db_password, $db_database);

        if (isset($_GET['trackid'])) {// If this is a track page
            //--------------------------------------------------------------------------------------------------------------------------------------------//
            // Request a JSON object containing a snipit of lyrics and a link to the full lyrics on "www.lyrics.wikia.com" ie. "LyricWiki" for this track //
            //--------------------------------------------------------------------------------------------------------------------------------------------//
            //Get artistname and trackname from Database
            $query = "SELECT artistname, trackname FROM artist NATURAL JOIN track WHERE trackid = {$_GET['trackid']}";
            $result = mysqli_query($db_server, $query);
            $trackdetails = mysqli_fetch_assoc($result);
	     mysqli_close($db_server);

            //Clean Input Strings
            $trackdetails['artistname'] = str_replace(" ", "_", $trackdetails['artistname']);
            $trackdetails['trackname'] = str_replace(" ", "_", $trackdetails['trackname']);

            //Search LyricWiki using funtion getSong for artist, track, return as JSON object
            $request = "http://lyrics.wikia.com/api.php?func=getSong&artist=" . $trackdetails['artistname'] . "&song=" . $trackdetails['trackname'] . "&fmt=realjson";
            $lyrics = proxy_get_file($request);

            //-----------------------------------------------------------------------------------------------------------------//
            // print the data contained in the JSON object, formatted to the page, or an error message if this is not possible //
            //-----------------------------------------------------------------------------------------------------------------//
            //Decode JSON object and check against null to confim success
            if (($decoded_lyrics = json_decode($lyrics)) != null) {// If the request succeded
                //Print all data with formating
                echo "\t\t<div id=\"lyricsartist\">\n\t\t\t<p>";
                echo $decoded_lyrics->song . " by " . $decoded_lyrics->artist;
                echo "\t\t\t</p>\n\t\t</div>";

                echo "\t\t<div id=\"lyricselement\">\n\t\t\t<p>";
                echo "\"" . $decoded_lyrics->lyrics . "\"";
                echo "\t\t\t</p>\n\t\t</div>";

                echo "\t\t<div id=\"lyricselement\">\n\t\t\t<p>";
                echo "<a href=\"" . $decoded_lyrics->url . "\">Click Here</a>";
                echo " to see the rest of these lyrics on LyricWiki.";
                echo "\t\t\t</p>\n\t\t</div>";
            } 	else{// If the request failed
		echo "\t\t<div id=\"lyricselement\">\n\t\t\t<p>";
		echo "Sorry, we don't seem to be able to find this track's lyrics.";
		echo "\t\t\t</p>\n\t\t</div>";
	}//End if/else(request successful?)
	
}
else{// If this is not a track page
	echo "\t\t<div id=\"lyricselement\">\n\t\t\t<p>";
	echo "Please navigate to a specific track's page to view it's lyrics!";
	echo "\t\t\t</p>\n\t\t</div>";
}// End if/else(is track page?)
//file_get_contents(), accounting for uwe firewall (Will not work if include() from a common lib file)
        function proxy_get_file($uri) {
            $context = stream_context_create(
                    array('http' =>
                        array('proxy' => 'proxysg.uwe.ac.uk:8080',
                            'header' => 'Cache-Control: no-cache'
                        )
                    )
            );
            $contents = file_get_contents($uri, false, $context);
            return $contents;
        }
        ?>
    </div>
</div>
<!-- End LyricsWidget.php -->