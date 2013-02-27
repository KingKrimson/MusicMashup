<?php

/* * *****************************************************************************
 * Name: sorttracks.php
 * Authour: Andrew Brown
 * Date: 27/02/2013
 * Description:
 * Used in an ajax call. Sorts and returns the tracklist. Depending on the post
 * variable sent, sorts by most favourited, most recently favourited, or 
 * alphabetical order.
 * **************************************************************************** */
require_once 'databasevars.php';

$db_server = mysqli_connect($db_hostname, $db_username, $db_password, $db_database);
if (isset($_POST['sorttitles'])) {
    
}

function sortTracks($db_server, $sorttype) {
    $trackquery = "SELECT albumcoveruri, trackid, trackname, albumid, albumname," .
            " artistid, artistname, albumreleasedate " .
            "FROM track NATURAL JOIN album NATURAL JOIN artist ORDER BY $sorttype ASC";
    $result = mysqli_query($db_server, $trackquery);
    if (!$result) {
        echo "<p>Could not access artists: " . mysqli_error($db_server) . "</p>\n";
    } else {
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

function sortAlbums($db_server, $sorttype) {
    $albumquery = "SELECT albumid, albumcoveruri, albumname, artistid, artistname, albumreleasedate " .
            "FROM album NATURAL JOIN artist ORDER BY $sorttype ASC";
    $result = mysqli_query($db_server, $albumquery);
    if (!$result) {
        echo "<p>Could not access albums: " . mysqli_error($db_server) . "</p>\n";
    } else {
        $len = mysqli_num_rows($result);
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
    }
}

function sortArtists($db_server, $sorttype) {
    $artistquery = "SELECT artistid, artistname FROM artist ORDER BY $sorttype ASC";
    if (!($result = mysqli_query($db_server, $artistquery))) {
        echo "<p>Could not access bands:" . mysqli_error($db_server) . "</p>\n";
    } else { //display artist info.
        $len = mysqli_num_rows($result);
        for ($i = 0; $i < $len; ++$i) {
            $artistdetails = mysqli_fetch_assoc($result);
            echo "<p><a href='bandlist.php?artistid={$artistdetails['artistid']}'>{$artistdetails['artistname']}</a></p>\n";
        }
    }
}

?>
