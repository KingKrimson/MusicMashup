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
        <script src="./js/sortitems.js"></script>
        <title>Alternews - Bands</title>
    </head>
    <body>
        <?php
        include_once 'header.php';
        if (isset($_GET['artistid'])) { //user wants to see specific artist
            show_band($_GET['artistid']);
        } else { //display all bands in database.
            echo "<div class='clear'></div>\n";
            echo "<div id='pagecontent'>\n";
            echo "<h1>Bands</h1>\n";
            if (!($db_server = mysqli_connect($db_hostname, $db_username, $db_password, $db_database))) {
                echo "<p>Could not connect to database:" . mysqli_error($db_server) . "</p>\n";
            } else {
                //get artist info from database, including favourite info.
                $artistquery = "SELECT artistid, artistname, artistyearformed, " .
                        "COUNT(artistdatetimefavourited) AS count FROM artist " .
                        "NATURAL JOIN favouriteartists GROUP BY artistid ORDER BY artistname ASC";
                if (!($result = mysqli_query($db_server, $artistquery))) {
                    echo "<p>Could not access bands:" . mysqli_error($db_server) . "</p>\n";
                } else { //display artist info.
                    $len = mysqli_num_rows($result);
                    echo "<span id='artists'>\n";
                    echo "<table>";
                    echo "<tr><td><a href='#' onclick=sortItems('artist','alphabet')>Artist</a></td>" 
                    . "<td><a href='#' onclick=sortItems('artist','date')>Year formed</a></td>"
                    . "<td><a href='#' onclick=sortItems('artist','favourite')>Favourite Count</a></td></tr>\n";
                    for ($i = 0; $i < $len; ++$i) {
                        $artistdetails = mysqli_fetch_assoc($result);
                        echo "<tr>\n";
                        echo "<td><a href='bandlist.php?artistid={$artistdetails['artistid']}'>" .
                        "{$artistdetails['artistname']}</a></td>\n";
                        echo "<td>{$artistdetails['artistyearformed']}</td>\n";
                        echo "<td class>{$artistdetails['count']}</td>";
                    }
                    echo "</table>\n";
                    echo "</span>\n";
                }
            }
            mysqli_close($db_server);
        }

        echo "</div>\n";
        include_once 'widgetpane.php';
        echo "<div class='clear'></div>\n";
        include_once 'footer.html';
        ?>    
    </body>
</html>

<?php
/* * *****************************************************************************
 * The get variable was set, so show the user the band the variable refers too.
 * This function makes a database query using the artistid, and outputs detailed
 * information about the selected artist. The user can also favourite the artist 
 * by clicking the favourite button on this page.
 * **************************************************************************** */

function show_band($artistid) {
    require_once ('favourite.php');

    global $db_hostname, $db_username, $db_password, $db_database;
    echo "<div id='pagecontent'>\n";
    if (!($db_server = mysqli_connect($db_hostname, $db_username, $db_password, $db_database))) {
        echo "<p>Couldn\t connect to database: " . mysqli_error($db_server) . "</p>\n";
    } else {
        //get information about the band.
        $artistquery = "SELECT artistid, artistname, artistdescription FROM artist " .
                "WHERE artistid=$artistid";
        if (!($result = mysqli_query($db_server, $artistquery))) {
            echo "<p>Couldn\'t find band!</p>\n";
        } else { //output detailed information about the band.
            $artistdetails = mysqli_fetch_assoc($result);
            echo "<h1>{$artistdetails['artistname']}</h1>\n";

            //user can favourite band if logged in.
            favouriteButton($db_server, $artistdetails['artistid'], 'artist');
            echo "<h2>Albums</h2>\n";
            $albumquery = "SELECT albumid, albumname FROM album WHERE " .
                    "artistid = {$artistdetails['artistid']} ORDER BY albumname ASC";
            if (!($result = mysqli_query($db_server, $albumquery))) {
                echo "<p>No albums found</p>\n";
            } else {
                $len = mysqli_num_rows($result);
                for ($i = 0; $i < $len; ++$i) {
                    $albumdetails = mysqli_fetch_assoc($result);
                    echo "<p><a href=albumlist.php?albumid={$albumdetails['albumid']}>{$albumdetails['albumname']}</a></p>\n";
                }
            }
            echo "<h2>Tracks</h2>\n";
            //get track info
            $trackquery = "SELECT trackid, trackname FROM track WHERE " .
                    "artistid={$artistdetails['artistid']} ORDER BY trackname ASC";
            if (!($result = mysqli_query($db_server, $trackquery))) {
                echo "<p>No bands found</p>\n";
            } else { //output track info
                $len = mysqli_num_rows($result);
                for ($i = 0; $i < $len; ++$i) {
                    $trackdetails = mysqli_fetch_assoc($result);
                    echo "<p><a href=tracklist.php?trackid={$trackdetails['trackid']}>{$trackdetails['trackname']}</a></p>\n";
                }
            }
            echo "<h2>Favourites</h2>\n";
            //get info about favourites
            $favouriteartistquery = "SELECT userid, username, artistdatetimefavourited" .
                    " FROM user NATURAL JOIN favouriteartists NATURAL JOIN artist" .
                    " WHERE artistid={$artistdetails['artistid']}" .
                    " ORDER BY artistdatetimefavourited ASC";
            $result = mysqli_query($db_server, $favouriteartistquery);
            if (!mysqli_num_rows($result)) {
                echo '<p>Nobody has favourited this band yet.</p>';
            } else { //display favourite info.
                $len = mysqli_num_rows($result);
                for ($i = 0; $i < $len; ++$i) {
                    $favouriteartistdetails = mysqli_fetch_assoc($result);
                    echo "<p><a href='userlist.php?userid={$favouriteartistdetails['userid']}'>" .
                    "{$favouriteartistdetails['username']}</a> " .
                    " - {$favouriteartistdetails['artistdatetimefavourited']}</p>\n";
                }
            }
            echo "<h2>Description</h2>\n";
            echo "<p>{$artistdetails['artistdescription']}</p>\n";
            echo "<p>Description taken from Wikipedia.<p>\n";
        }
    }
    echo "</div>\n";
}
?>