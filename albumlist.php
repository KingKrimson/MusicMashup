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
        echo "<div class='clear'></div>\n";
        
        //check to see if the user is trying to view a specific album.
        if (isset($_GET['albumid'])) { //They are, so show the album.
            show_album($_GET['albumid']);
        } else { //They're not, so list all albums in database.
            echo "<div id='pagecontent'>\n";
            echo "<h1>Albums</h1>\n";
            echo "<p>Here, you can find a list of all of the albums we have in our" .
            "database, along  with their artists and release dates.</p>\n";
            $db_server = mysqli_connect($db_hostname, $db_username, $db_password, $db_database);
            if (!$db_server) {
                echo '<p>Could not connect to database:' . mysqli_error($db_server) . '</p>\n';
            } else {
                
                //get all relevant fields from the database. 
                $albumquery = 'SELECT albumid, albumcoveruri, albumname, artistid, artistname, albumreleasedate ' .
                        'FROM album NATURAL JOIN artist ORDER BY albumname ASC';
                $result = mysqli_query($db_server, $albumquery);
                if (!$result) {
                    echo "<p>Could not access albums: " . mysqli_error($db_server) . "</p>\n";
                } else {
                    $len = mysqli_num_rows($result);
                    echo "<span id=albums>\n";
                    echo "<table>\n";
                    //loop through results, outputting details about each album
                    //users can click on details (tracks, albums, etc), and via 
                    //the use of get variables, be taken to the appropriate
                    //item.
                    for ($i = 0; $i < $len; ++$i) {
                        $albumdetails = mysqli_fetch_assoc($result);
                        echo "<tr>\n";
                        echo "<td><img src=\"{$albumdetails["albumcoveruri"]}\" height=\"100\" width=\"100\"></img></td>\n";
                        echo "<td><a href=\"albumlist.php?albumid={$albumdetails["albumid"]}\">{$albumdetails["albumname"]}</a></td>\n";
                        echo "<td><a href=\"bandlist.php?artistid={$albumdetails["artistid"]}\">{$albumdetails["artistname"]}</a></td>\n";
                        echo "<td>{$albumdetails["albumreleasedate"]}</td>\n";
                        echo "</tr>\n";
                    }
                    echo "</table>\n";
                    echo "</span>\n";
                }
            }
            echo "</div>\n";
        }

        include_once 'widgetpane.php';        
        echo "<div class='clear'></div>\n";
        include_once 'footer.html';
        ?>    
    </body>
</html>

<?php

/*******************************************************************************
 * The get variable was set, so show the user the album the variable refers too.
 * This function makes a database query using the albumid, and outputs detailed
 * information about the selected album. The user can also favourite the album 
 * by clicking the favourite button on this page.
 ******************************************************************************/
function show_album($albumid) {
    require_once('favourite.php'); //allow favouriting functionality.
    global $db_hostname;
    global $db_username;
    global $db_password;
    global $db_database;

    echo "<div id='pagecontent'>\n";
    if (!($db_server = mysqli_connect($db_hostname, $db_username, $db_password, $db_database))) {
        echo "<p>Could not connect to database:' . mysqli_error() . '</p>\n";
    } else {
        //get appropriate details about the album.
        $albumquery = "SELECT albumid, albumcoveruri, albumname, artistid, artistname,".
                " albumreleasedate, albumdescription " .
                "FROM album NATURAL JOIN artist WHERE albumid=$albumid";
        if (!($result = mysqli_query($db_server, $albumquery))) {
            echo'<p>Whoops! couldn\'t find album!</p>\n';
        } else { //display album details, including tracks, favourites, and description.
            $albumdetails = mysqli_fetch_assoc($result);
            echo "<h1>{$albumdetails['albumname']}</h1>\n";
            echo"<img src=\"{$albumdetails['albumcoveruri']}\" height=\"400\" width=\"400\"/>\n";
            
            //If the user is logging in, they can favourite the item. 
            favouriteButton($db_server, $albumdetails['albumid'], 'album');
            echo "<h2>Artist</h2> <p><a href=bandlist.php?artistid={$albumdetails['artistid']}>" . 
                 "{$albumdetails['artistname']}</a></p>\n";
                 
            echo "<h2>Date</h2> <p>{$albumdetails['albumreleasedate']}</p>\n";
            echo "<h2>Tracks</h2>\n";
            $trackquery = "SELECT trackid, trackname FROM track WHERE albumid=" . 
                    "{$albumdetails['albumid']} ORDER BY trackname ASC";
                    
            if (!($result = mysqli_query($db_server, $trackquery))) {
                echo "Couldn\'t find any tracks.\n";
            } else {
                $len = mysqli_num_rows($result);
                for ($i = 0; $i < $len; ++$i) {
                    $trackdetails = mysqli_fetch_assoc($result);
                    echo "<p><a href=tracklist.php?trackid={$trackdetails['trackid']}>" .
                            "{$trackdetails['trackname']}</a></p>\n";
                }
            }
            echo "<h2>Favourites</h2>\n";
            //get details about favourites.
            $favouritealbumquery = "SELECT userid, username, albumdatetimefavourited " .
                    "FROM user NATURAL JOIN favouritealbums NATURAL JOIN album " .
                    "WHERE albumid={$albumdetails['albumid']} " .
                    "ORDER BY albumdatetimefavourited ASC";
            $result = mysqli_query($db_server, $favouritealbumquery);
            if (!mysqli_num_rows($result)) {
                echo "<p>Nobody has favourited this album yet.</p>\n";
            } else {
                $len = mysqli_num_rows($result);
                for ($i = 0; $i < $len; ++$i) {
                    $favouritealbumdetails = mysqli_fetch_assoc($result);
                    echo "<p><a href='userlist.php?userid={$favouritealbumdetails['userid']}'>" . 
                         "{$favouritealbumdetails['username']}</a> - " . 
                         "{$favouritealbumdetails['albumdatetimefavourited']}</p>\n";
                }
            }
            echo "<h2>Description</h2> <p>{$albumdetails['albumdescription']}</p>\n";
            echo "<p>Description taken from Wikipedia.<p>\n";
        }
    }
    echo "</div>\n";
}
?>