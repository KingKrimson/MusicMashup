<?php

/* * *****************************************************************************
 * Name: sorttracks.php
 * Authour: Andrew Brown
 * Date: 27/02/2013
 * Description:
 * Used in an ajax call. Sorts and returns the relevant itemlist. Depending on the post
 * variable sent, sorts by most favourited, date released, or alphabetical order.
 * 
 * The text output by the file is exactly the same as the original text in the 
 * calling php file, except sorted according to either most favourited, date released,
 * or alphabetical order.
 * **************************************************************************** */
require_once 'databasevars.php';

//find which item type to sort.
$db_server = mysqli_connect($db_hostname, $db_username, $db_password, $db_database);
if (isset($_POST['sortartist'])) {
    sortArtists($db_server, $_POST['sorttype']);
} else if (isset($_POST['sortalbum'])) {
    sortAlbums($db_server, $_POST['sorttype']);
} else if (isset($_POST['sorttrack'])) {
    sortTracks($db_server, $_POST['sorttype']);
} else {
    echo "error!\n";
}
mysqli_close($db_server);

function sortTracks($db_server, $sorttype) {
    //change ORDER BY parameter, depending on sort type.
    if ($sorttype == 'alphabet') {
        $sorttype = 'trackname ASC';
    } else if ($sorttype == 'date') {
        $sorttype = 'albumreleasedate ASC';
    } else {
        $sorttype = 'count DESC';
    }
    if (!$db_server) {
        echo "<p>Could not connect to database:" . mysqli_error($db_server) . "\n";
    } else {
        //new trackquery, with ORDER BY parameter changed to the appropriate sort type.
        $trackquery = 'SELECT albumcoveruri, track.trackid, trackname, albumid, albumname, ' .
                'artistid, artistname, albumreleasedate, COUNT(trackdatetimefavourited) AS count ' .
                'FROM track NATURAL JOIN album NATURAL JOIN artist LEFT JOIN favouritetracks ' .
                'ON track.trackid=favouritetracks.trackid GROUP BY track.trackid ORDER BY ' . $sorttype;
        $result = mysqli_query($db_server, $trackquery);
        if (!$result) {
            echo "<p>Could not access tracks: " . mysqli_error($db_server) . "</p>\n";
        } else { //Output information about all tracks in database.
            $len = mysqli_num_rows($result);
            //field headers + links.
            echo "<table>\n";
            echo "<tr>" .
            "<td>Cover</td>" .
            "<td><a href='#' onclick=sortItems('track','alphabet')>Track</a></td>" .
            "<td>Album</td>" .
            "<td>Artist</td>" .
            "<td><a href='#' onclick=sortItems('track','date')>Release Date</a></td>" .
            "<td><a href='#' onclick=sortItems('track','favourite')>Favourite Count</td>" .
            "</tr>";
            for ($i = 0; $i < $len; ++$i) {
                $trackdetails = mysqli_fetch_assoc($result);
                echo "<tr>\n";
                echo "<td><img src=\"{$trackdetails["albumcoveruri"]}\" height=\"100\" width=\"100\"></img></td>\n";
                echo "<td><a href=tracklist.php?trackid={$trackdetails['trackid']}>{$trackdetails["trackname"]}</a></td>\n";
                echo "<td><a href='albumlist.php?albumid={$trackdetails['albumid']}'>{$trackdetails["albumname"]}</a></td>\n";
                echo "<td><a href='bandlist.php?artistid={$trackdetails['artistid']}'>{$trackdetails["artistname"]}</a></td>\n";
                echo "<td>{$trackdetails["albumreleasedate"]}</td>\n";
                echo "<td>{$trackdetails["count"]}<td>";
                echo "</tr>\n";
            }
            echo "</table>\n";
        }
    }
}

function sortAlbums($db_server, $sorttype) {
    //change ORDER BY parameter depending on sort type.
    if ($sorttype == 'alphabet') {
        $sorttype = 'albumname ASC';
    } else if ($sorttype == 'date') {
        $sorttype = 'albumreleasedate ASC';
    } else {
        $sorttype = 'count DESC';
    }

    if (!$db_server) {
        echo "<p>Could not connect to database:" . mysqli_error($db_server) . "\n";
    } else {
        $albumquery = 'SELECT album.albumid, albumcoveruri, albumname, artistid, artistname, albumreleasedate, ' .
                'COUNT(albumdatetimefavourited) AS count FROM album NATURAL JOIN artist LEFT JOIN favouritealbums ' .
                'ON album.albumid = favouritealbums.albumid GROUP BY albumid ORDER BY ' . $sorttype;
        $result = mysqli_query($db_server, $albumquery);
        if (!$result) {
            echo "<p>Could not access albums: " . mysqli_error($db_server) . "</p>\n";
        } else {
            $len = mysqli_num_rows($result);
            echo "<table>\n";
            //output field headers, links again
            echo "<tr>\n";
            echo "<td>Cover</td>\n";
            echo "<td><a href='#' onclick=sortItems('album','alphabet')>Album</a></td>\n";
            echo "<td>Artist</td>\n";
            echo "<td><a href='#' onclick=sortItems('album','date')>Release Date</a></td>\n";
            echo "<td><a href='#' onclick=sortItems('album','favourite')>Favourite Count</a></td>\n";
            echo "</tr>\n";
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
                echo "<td>{$albumdetails["count"]}</td>";
                echo "</tr>\n";
            }
            echo "</table>\n";
        }
    }
}

function sortArtists($db_server, $sorttype) {
    //change ORDER BY parameter depending on sort type.
    if ($sorttype == 'alphabet') {
        $sorttype = 'artistname ASC';
    } else if ($sorttype == 'date') {
        $sorttype = 'artistyearformed ASC';
    } else {
        $sorttype = 'count DESC';
    }

    if (!$db_server) {
        echo "<p>Could not connect to database:" . mysqli_error($db_server) . "\n";
    } else {
        $artistquery = "SELECT artistid, artistname, artistyearformed, " .
                "COUNT(artistdatetimefavourited) AS count FROM artist " .
                "NATURAL JOIN favouriteartists GROUP BY artistid ORDER BY $sorttype";
        $result = mysqli_query($db_server, $artistquery);
        if (!$result) {
            echo "<p>Could not access bands: " . mysqli_error($db_server) . "</p>\n";
        } else { //Output information about all tracks in database.
            $len = mysqli_num_rows($result);
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
        }
    }
}

?>
