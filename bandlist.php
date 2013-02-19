<!-- Name: bandlist.php
     Author: Andrew Brown
     Date: 03/02/2013
     Description: 
     On first viewing, this lists all of the bands in the database. 
     If you click on an band, you'll get more information about 
     it, such as the albums and tracks they've released, and a description 
     of the band. From a specific artist page, users can 'favourite' them;
     these favourites are viewable on the corresponding user and artist pages.-->
<!DOCTYPE html>
<?php
require_once 'databasevars.php';
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" type="text/css" href="./CSS/alternews.css">
        <title>Alternews - Home</title>
    </head>
    <body>
        <?php
        include_once 'header.php';
        if (isset($_GET['artistid'])) {
            show_band($_GET['artistid']);
        } else {
            echo '<div class="clear"></div>';
            echo '<div id="pagecontent">';
            echo '<h1>Bands</h1>';
            if (!($db_server = mysqli_connect($db_hostname, $db_username, $db_password, $db_database))) {
                echo '<p>Could not connect to database:' . mysqli_error($db_server) . '</p>';
            } else {
                $artistquery = 'SELECT artistid, artistname FROM artist ORDER BY artistname ASC';
                if (!($result = mysqli_query($db_server, $artistquery))) {
                    echo '<p>Could not access bands:' . mysqli_error($db_server) . '</p>';
                } else {
                    $len = mysqli_num_rows($result);
                    for ($i = 0; $i < $len; ++$i) {
                        $artistdetails = mysqli_fetch_assoc($result);
                        echo "<p><a href='bandlist.php?artistid={$artistdetails['artistid']}'>{$artistdetails['artistname']}</a></p>";
                    }
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

function show_band($artistid) {
    require_once ('favourite.php');

    global $db_hostname, $db_username, $db_password, $db_database;
    echo '<div id="pagecontent">';
    if (!($db_server = mysqli_connect($db_hostname, $db_username, $db_password, $db_database))) {
        echo '<p>Couldn\t connect to database: ' . mysqli_error($db_server) . '</p>';
    } else {
        $artistquery = "SELECT artistid, artistname, artistdescription FROM artist " .
                "WHERE artistid=$artistid";
        if (!($result = mysqli_query($db_server, $artistquery))) {
            echo '<p>Couldn\'t find band!</p>';
        } else {
            $artistdetails = mysqli_fetch_assoc($result);
            echo "<h1>{$artistdetails['artistname']}</h1>";
            //pics go here?
            favouriteButton($db_server, $artistdetails['artistid'], 'artist');
            echo "<h2>Albums</h2>";
            $albumquery = "SELECT albumid, albumname FROM album WHERE " .
                    "artistid = {$artistdetails['artistid']} ORDER BY albumname ASC";
            if (!($result = mysqli_query($db_server, $albumquery))) {
                echo '<p>No albums found</p>';
            } else {
                $len = mysqli_num_rows($result);
                for ($i = 0; $i < $len; ++$i) {
                    $albumdetails = mysqli_fetch_assoc($result);
                    echo "<p><a href=albumlist.php?albumid={$albumdetails['albumid']}>{$albumdetails['albumname']}</a></p>";
                }
            }
            echo "<h2>Tracks</h2>";
            $trackquery = "SELECT trackid, trackname FROM track WHERE " .
                    "artistid={$artistdetails['artistid']} ORDER BY trackname ASC";
            if (!($result = mysqli_query($db_server, $trackquery))) {
                echo '<p>No albums found</p>';
            } else {
                $len = mysqli_num_rows($result);
                for ($i = 0; $i < $len; ++$i) {
                    $trackdetails = mysqli_fetch_assoc($result);
                    echo "<p><a href=tracklist.php?trackid={$trackdetails['trackid']}>{$trackdetails['trackname']}</a></p>";
                }
            }
            echo "<h2>Favourites</h2>";
            $favouriteartistquery = "SELECT userid, username, artistdatetimefavourited " .
                    "FROM user NATURAL JOIN favouriteartists NATURAL JOIN artist " .
                    "WHERE artistid={$artistdetails['artistid']} " .
                    "ORDER BY username ASC";
            $result = mysqli_query($db_server, $favouriteartistquery);
            if (!mysqli_num_rows($result)) {
                echo '<p>Nobody has favourited this band yet.</p>';
            } else {
                $len = mysqli_num_rows($result);
                for ($i = 0; $i < $len; ++$i) {
                    $favouriteartistdetails = mysqli_fetch_assoc($result);
                    echo "<p><a href='userlist.php?userid={$favouriteartistdetails['userid']}'>{$favouriteartistdetails['username']}</a> - {$favouriteartistdetails['artistdatetimefavourited']}</p>";
                }
            }
            echo "<h2>Description</h2>";
            echo "<p>{$artistdetails['artistdescription']}</p>";
        }
    }
    echo '</div>';
}
?>