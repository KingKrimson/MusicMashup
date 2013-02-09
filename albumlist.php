<!-- Name: albumlist.php
     Author: Andrew Brown
     Date: 03/02/2013
     Description: 
     On first viewing, this lists all of the albums in the database, along 
     with some basic information about them, such as their artist, cover, and
     release date. If you click on an album, you'll get more information about 
     it, such as tracks on the database, and a description of the album.     -->
<!DOCTYPE html>
<?php 
require_once 'databasevars.php';
require_once 'login.php' 
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
        echo '<div class="clear"></div>';
        if (isset($_GET['albumid'])) {
            show_album($_GET['albumid']);
        } else {
            echo '<div id="pagecontent">';
            echo '<h1>Albums</h1>';
            echo '<p>Here, you can find a list of all of the albums we have in our' .
            'database, along  with their artists and release dates.</p>';
            $db_server = mysqli_connect($db_hostname, $db_username, $db_password, $db_database);
            if (!$db_server) {
                echo '<p>Could not connect to database:' . mysqli_error($db_server) . '</p>';
            } else {
                $albumquery = 'SELECT albumid, albumcoveruri, albumname, artistid, artistname, albumreleasedate ' .
                        'FROM album NATURAL JOIN artist ORDER BY albumname ASC';
                $result = mysqli_query($db_server, $albumquery);
                if (!$result) {
                    echo '<p>Could not access albums: ' . mysqli_error($db_server) . '</p>';
                } else {
                    $len = mysqli_num_rows($result);
                    echo '<table>';
                    for ($i = 0; $i < $len; ++$i) {
                        $albumdetails = mysqli_fetch_assoc($result);
                        echo "<tr>";
                        echo "<td><img src=\"{$albumdetails["albumcoveruri"]}\" height=\"100\" width=\"100\"></img></td>";
                        echo "<td><a href=\"albumlist.php?albumid={$albumdetails["albumid"]}\">{$albumdetails["albumname"]}</a></td>";
                        echo "<td><a href=\"bandlist.php?artistid={$albumdetails["artistid"]}\">{$albumdetails["artistname"]}</a></td>";
                        echo "<td>{$albumdetails["albumreleasedate"]}</td>";
                        echo "</tr>";
                    }
                    echo '</table>';
                }
            }
            echo '</div>';
        }

        echo '<div class="clear"></div>';
        include_once 'widgetpane.php';
        include_once 'footer.html';
        ?>    
    </body>
</html>

<?php

function show_album($albumid) {
    global $db_hostname;
    global $db_username;
    global $db_password;
    global $db_database;

    echo '<div id=\'pagecontent\'>';
    if (!($db_server = mysqli_connect($db_hostname, $db_username, $db_password, $db_database))) {
        echo '<p>Could not connect to database:' . mysqli_error() . '</p>';
    } else {
        $albumquery = "SELECT albumid, albumcoveruri, albumname, artistid, artistname, albumreleasedate, albumdescription " .
                "FROM album NATURAL JOIN artist WHERE albumid=$albumid";
        if (!($result = mysqli_query($db_server, $albumquery))) {
            echo'<p>Whoops! couldn\'t find album!</p>';
        } else {
            $albumdetails = mysqli_fetch_assoc($result);
            echo "<h1>{$albumdetails['albumname']}</h1>";
            echo"<img src=\"{$albumdetails['albumcoveruri']}\"/>";
            echo "<p>Artist - <a href=bandlist.php?artistid={$albumdetails['artistid']}>{$albumdetails['artistname']}</a></p>";
            echo "<p>Date - {$albumdetails['albumreleasedate']}</p>";
            echo "<h2>Tracks</h2>";
            $trackquery = "SELECT trackid, trackname FROM track WHERE albumid={$albumdetails['albumid']} ORDER BY trackname ASC";
            if(!($result = mysqli_query($db_server, $trackquery))) {
                echo 'Couldn\'t find any tracks.';
            } else {
                $len = mysqli_num_rows($result);
                for($i = 0; $i < $len; ++$i) {
                    $trackdetails = mysqli_fetch_assoc($result);
                    echo "<p><a href=tracklist.php?trackid={$trackdetails['trackid']}>{$trackdetails['trackname']}</a></p>";
                }
            }
            echo "<p>Description - </p> <p>{$albumdetails['albumdescription']}</p>";
        }
    }
    echo '</div>';
}
?>