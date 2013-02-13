<?php

/* Name: favourite.php
 * Authour: Andrew Brown
 * Date: 10/02/2013
 * Description:
 * This file implements favouriting in the website; it is to be included in all
 * the pages where favouriting is required. When the user clicks the 'favourite' 
 * button, several parameters are sent to this program, namely userid and [item]id.
 * The program then inserts the userid and itemid into the database, along with 
 * the current time.
 */
require_once('databasevars.php');

if (isset($_POST['Favourite'])) {
    if (isset($_POST['artistid'])) {
        $favouritestatus = favourite($_POST['userid'], $_POST['artistid'], 'artist');
    } else if (isset($_POST['albumid'])) {
        $favouritestatus = favourite($_POST['userid'], $_POST['albumid'], 'album');
    } else if (isset($_POST['trackid'])) {
        $favouritestatus = favourite($_POST['userid'], $_POST['trackid'], 'track');
    }
} else
    $favouritestatus = false;

function favourite($userid, $itemid, $typeofitem) {
    global $db_hostname, $db_username, $db_password, $db_database;

    $db_server = mysqli_connect($db_hostname, $db_username, $db_password, $db_database);
    $favouritequery = "INSERT INTO favourite" . "$typeofitem" . "s"
            . "($typeofitem" . "datetimefavourited, " . "$typeofitem" . "id, userid)"
            . " VALUES (NOW(), $itemid, $userid)";
    if (!($result = mysqli_query($db_server, $favouritequery))) {
        echo 'Error favouriting item:' . mysqli_error($db_server);
        return false;
    } else {
        //Also, add info to RSS feed.
        return true;
    }
}

function favouriteButton($db_server, $itemid, $typeofitem) {
    if (isset($_COOKIE['userid'])) {
        $favouritequery = "SELECT * FROM user NATURAL JOIN favourite" . "{$typeofitem}s "
                . "NATURAL JOIN " . "$typeofitem " .
                "WHERE " . "$typeofitem" . "id=$itemid AND " .
                "userid = {$_COOKIE['userid']}";
        if (!($result = mysqli_query($db_server, $favouritequery))) {
            echo 'ERROR: ' . mysqli_error($db_server);
        } else {
            if (mysqli_num_rows($result) > 0) {
                echo "<p>This " . $typeofitem . " is one of your favourites!</p>";
            } else {
                echo "<form action='' method='post'>
                  <input type='submit' name='Favourite' value='favourite'>
                  <input type='hidden' name='{$typeofitem}id' value=$itemid>
                  <input type='hidden' name='userid' value={$_COOKIE['userid']}>
                  </form>";
            }
        }
    } else {
        echo "<p>Please log in to favourite this " . $typeofitem . "!</p>";
    }
}
?>
