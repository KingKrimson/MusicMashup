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
        echo '<div class="clear"></div>';
        if (isset($_GET['trackid'])) {
            show_track($_GET['trackid']);
        } else {
            echo '<div id="pagecontent">';
            echo '<h1>Tracks</h1>';
            echo '<p>Here, you can find a list of all of the tracks on our database,' .
            'along with their Artists, containing album, and release dates.</p>';

            $db_server = mysqli_connect($db_hostname, $db_username, $db_password, $db_database);
            if (!$db_server) {
                echo '<p>Could not connect to database:' . mysqli_error($db_server);
            } else {
                $albumquery = 'SELECT albumcoveruri, trackid, trackname, albumid, albumname, artistid, artistname, albumreleasedate ' .
                        'FROM track NATURAL JOIN album NATURAL JOIN artist ORDER BY trackname ASC';
                $result = mysqli_query($db_server, $albumquery);
                if (!$result) {
                    echo '<p>Could not access artists: ' . mysqli_error($db_server) . '</p>';
                } else {
                    $len = mysqli_num_rows($result);
                    echo '<table>';
                    for ($i = 0; $i < $len; ++$i) {
                        $trackdetails = mysqli_fetch_assoc($result);
                        echo "<tr>";
                        echo "<td><img src=\"{$trackdetails["albumcoveruri"]}\" height=\"100\" width=\"100\"></img></td>";
                        echo "<td><a href=tracklist.php?trackid={$trackdetails['trackid']}>{$trackdetails["trackname"]}</a></td>";
                        echo "<td><a href='albumlist.php?albumid={$trackdetails['albumid']}'>{$trackdetails["albumname"]}</a></td>";
                        echo "<td><a href='bandlist.php?artistid={$trackdetails['artistid']}'>{$trackdetails["artistname"]}</a></td>";
                        echo "<td>{$trackdetails["albumreleasedate"]}</td>";
                        echo "</tr>";
                    }
                    echo '</table>';
                }
            }
        }

        echo '</div>';
        echo '<div class="clear"></div>';
        include_once 'widgetpane.php';
        include_once 'footer.html';
        ?>    
    </body>
</html>

<?php

function show_track($trackid) {
    require_once('favourite.php');
    global $db_hostname, $db_username, $db_password, $db_database;
    echo '<div id="pagecontent">';
    if (!($db_server = mysqli_connect($db_hostname, $db_username, $db_password, $db_database))) {
        echo'<p>Couldn\'t connect to the database:' . mysqli_error($db_server) . '</p>';
    } else {
        $trackquery = "SELECT artistid, artistname, albumid, albumcoveruri, albumname, albumreleasedate, trackid, trackname, trackdescription " .
                "FROM artist NATURAL JOIN album NATURAL JOIN track WHERE trackid={$trackid}";
        if (!($result = mysqli_query($db_server, $trackquery))) {
            echo '<p>Whoops! Couldn\'t find track! ' . mysqli_error($db_server) . '</p>';
        } else {
            $trackdetails = mysqli_fetch_assoc($result);
            echo "<h1>{$trackdetails['trackname']}</h1>";
            echo "<img src=\"{$trackdetails['albumcoveruri']}\" />";
            favouriteButton($db_server, $trackdetails['trackid'], 'track');
            echo "<h2>Band</h2> <p><a href='bandlist.php?artistid={$trackdetails['artistid']}'>{$trackdetails['artistname']}</a><p>";
            echo "<h2>Album</h2> <p><a href='albumlist.php?albumid={$trackdetails['albumid']}'>{$trackdetails['albumname']}</a></p>";
            echo "<h2>Date</h2> <p>{$trackdetails['albumreleasedate']}</p>";
            echo "<h2>Favourites</h2>";
            $favouritetrackquery = "SELECT userid, username, trackdatetimefavourited " .
                    "FROM user NATURAL JOIN favouritetracks NATURAL JOIN track " .
                    "WHERE trackid={$trackdetails['trackid']} " .
                    "ORDER BY username ASC";
            $result = mysqli_query($db_server, $favouritetrackquery);
            if (!mysqli_num_rows($result)) {
                echo '<p>Nobody has favourited this track yet.</p>';
            } else {
                $len = mysqli_num_rows($result);
                for ($i = 0; $i < $len; ++$i) {
                    $favouritetrackdetails = mysqli_fetch_assoc($result);
                    echo "<p><a href='tracklist.php?userid={$favouritetrackdetails['userid']}'>{$favouritetrackdetails['username']}</a> - {$favouritetrackdetails['trackdatetimefavourited']}</p>";
                }
            }
            echo "<h2>Description</h2><p>{$trackdetails['trackdescription']}</p>";
        }
    }
    echo '</div>';
}
?>