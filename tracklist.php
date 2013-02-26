<!-- Name: tracklist.php
     Author: Andrew Brown
     Date: 03/02/2013
     Description: 
     On first viewing, this lists all of the tracks in the database, along 
     with some basic information about them, such as their artist, album cover, 
     and album release date. If you click on a track, you'll get more 
     information about it, such as a description and a list of users who
     favourited it.                                                          -->
<?php
require_once 'databasevars.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" type="text/css" href="./CSS/alternews.css">
        <title>Alternews - Home</title>
    </head>
    <body>
        <?php
        include_once 'header.php';
        echo "<div class='clear'></div>\n";
        if (isset($_GET['trackid'])) { //user is trying to view a specific track.
            show_track($_GET['trackid']);
        } else { //show all tracks.
            echo "<div id='pagecontent'>\n";
            echo "<h1>Tracks</h1>\n";
            echo "<p>Here, you can find a list of all of the tracks on our database,' .
            'along with their Artists, containing album, and release dates.</p>\n";

            $db_server = mysqli_connect($db_hostname, $db_username, $db_password, $db_database);
            if (!$db_server) {
                echo "<p>Could not connect to database:" . mysqli_error($db_server) . "\n";
            } else {
                $albumquery = 'SELECT albumcoveruri, trackid, trackname, albumid, albumname,' . 
                        ' artistid, artistname, albumreleasedate ' .
                        'FROM track NATURAL JOIN album NATURAL JOIN artist ORDER BY trackname ASC';
                $result = mysqli_query($db_server, $albumquery);
                if (!$result) {
                    echo "<p>Could not access artists: " . mysqli_error($db_server) . "</p>\n";
                } else { //Output information about all tracks in database.
                    $len = mysqli_num_rows($result);
                    echo "<table>\n";
                    for ($i = 0; $i < $len; ++$i) {
                        $trackdetails = mysqli_fetch_assoc($result);
                        echo "<tr>\n";
                        echo "<td><img src=\"{$trackdetails["albumcoveruri"]}\" height=\"100\" width=\"100\"></img></td>\n";
                        echo "<td><a href=tracklist.php?trackid={$trackdetails['trackid']}>{$trackdetails["trackname"]}</a></td>\n";
                        echo "<td><a href='albumlist.php?albumid={$trackdetails['albumid']}'>{$trackdetails["albumname"]}</a></td>\n";
                        echo "<td><a href='bandlist.php?artistid={$trackdetails['artistid']}'>{$trackdetails["artistname"]}</a></td>\n";
                        echo "<td>{$trackdetails["albumreleasedate"]}</td>\n";
                        echo "</tr>\n";
                    }
                    echo "</table>\n";
                }
            }
        }

        echo "</div>\n";
        include_once 'widgetpane.php';
        echo "<div class='clear'></div>\n";
        include_once 'footer.html';
        ?>    
    </body>
</html>

<?php

/*******************************************************************************
 * The get variable was set, so show the user the track the variable refers too.
 * This function makes a database query using the trackid, and outputs detailed
 * information about the selected track. The user can also favourite the track 
 * by clicking the favourite button on this page.
 ******************************************************************************/
function show_track($trackid) {
    require_once('favourite.php');
    global $db_hostname, 
           $db_username, 
           $db_password, 
           $db_database;
    
    echo "<div id='pagecontent'>\n";
    if (!($db_server = mysqli_connect($db_hostname, $db_username, $db_password, $db_database))) {
        echo"<p>Couldn't connect to the database:" . mysqli_error($db_server) . "</p>\n";
    } else {
        //get detailed information about the track. 
        $trackquery = "SELECT artistid, artistname, albumid, albumcoveruri, albumname,". 
                " albumreleasedate, trackid, trackname, trackdescription " .
                "FROM artist NATURAL JOIN album NATURAL JOIN track WHERE trackid={$trackid}";
        if (!($result = mysqli_query($db_server, $trackquery))) {
            echo "<p>Whoops! Couldn't find track! " . mysqli_error($db_server) . "</p>\n";
        } else { // output the information about the tracks.
            $trackdetails = mysqli_fetch_assoc($result);
            echo "<h1>{$trackdetails['trackname']}</h1>\n";
            echo "<img src=\"{$trackdetails['albumcoveruri']}\" width=\"400\" height=\"400\" />\n";
            
            //if the user is logged in, they can favourite the track using this button.
            favouriteButton($db_server, $trackdetails['trackid'], 'track');
            
            echo "<h2>Band</h2> <p><a href='bandlist.php?artistid={$trackdetails['artistid']}'>{$trackdetails['artistname']}</a><p>\n";
            echo "<h2>Album</h2> <p><a href='albumlist.php?albumid={$trackdetails['albumid']}'>{$trackdetails['albumname']}</a></p>\n";
            echo "<h2>Date</h2> <p>{$trackdetails['albumreleasedate']}</p>\n";
            echo "<h2>Favourites</h2>\n";
            //get information about the track's favourites. 
            $favouritetrackquery = "SELECT userid, username, trackdatetimefavourited " .
                    "FROM user NATURAL JOIN favouritetracks NATURAL JOIN track " .
                    "WHERE trackid={$trackdetails['trackid']} " .
                    "ORDER BY trackdatetimefavourited ASC";
            $result = mysqli_query($db_server, $favouritetrackquery);
            if (!mysqli_num_rows($result)) {
                echo "<p>Nobody has favourited this track yet.</p>\n";
            } else { //output favourite information.
                $len = mysqli_num_rows($result);
                for ($i = 0; $i < $len; ++$i) {
                    $favouritetrackdetails = mysqli_fetch_assoc($result);
                    echo "<p><a href='tracklist.php?userid={$favouritetrackdetails['userid']}'>".
                    "{$favouritetrackdetails['username']}</a> - " . 
                    "{$favouritetrackdetails['trackdatetimefavourited']}</p>\n";
                    }
                }
            }
            echo "<h2>Description</h2><p>{$trackdetails['trackdescription']}</p>\n";
            echo "<p>Description taken from Wikipedia.<p>\n";
        }
    echo "</div>\n";
}
?>