<!-- Name: userlist.php
     Author: Andrew Brown
     Date: 03/02/2013
     Description: 
     On first viewing, this lists all of the users in the database, along 
     with some basic information about them, such as their avatar and age.
     If you click on a user, you're taking to their specific page, which
     shows you the user's description, and a list of all the artists and
     tracks they favourited.                                                 -->
<!DOCTYPE html>
<?php require_once 'databasevars.php';
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" type="text/css" href="./CSS/alternews.css">
        <title>Alternews - Home</title>
    </head>
    <body>
        <?php include_once 'header.php'; ?>
        <div class="clear"></div>
        <?php
        if (isset($_GET['userid'])) {
            show_user($_GET['userid']);
        } else {
            echo "<div id='pagecontent'>\n";
            echo "<h1>Users</h1>\n";
            echo "<p>Here, you can find a list of all of the users we have in our " .
            "database, along with their age.</p>\n";
            $db_server = mysqli_connect($db_hostname, $db_username, $db_password, $db_database);
            if (!$db_server) {
                echo "<p>Could not connect to database:" . mysqli_error($db_server) . "</p>\n";
            } else {
                $userquery = 'SELECT userid, useravataruri, username, userage FROM user ORDER BY username ASC';
                $result = mysqli_query($db_server, $userquery);
                if (!$result) {
                    echo "<p>Could not access users: " . mysqli_error($db_server) . "</p>\n";
                } else {
                    $len = mysqli_num_rows($result);
                    echo "<table>\n";
                    for ($i = 0; $i < $len; ++$i) {
                        $row = mysqli_fetch_assoc($result);
                        echo "<tr>\n";
                        echo "<td><img src=\"{$row["useravataruri"]}\" height=\"100\" width=\"100\"></img></td>\n";
                        echo "<td><a href=userlist.php?userid={$row["userid"]}>{$row["username"]}</a></td>\n";
                        echo "<td>{$row["userage"]}</td>\n";
                        echo "</tr>\n";
                    }
                    echo "</table>\n";
                }
		  mysqli_close($db_server);
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
 * The get variable was set, so show the user the user that the userid refers
 * too. From here, the user can see detailed information about the selected user,
 * such as name, age, description, and a list of all the items that the user has
 * favourited.
 ******************************************************************************/
function show_user($userid) {
    global $db_hostname,
            $db_username, 
            $db_password, 
            $db_database;

    echo "<div id='pagecontent'>\n";

    if (!($db_server = mysqli_connect($db_hostname, $db_username, $db_password, $db_database))) {
        echo "<p>Could not connect to database: " . mysqli_error($db_server) . "</p>\n";
    } else { //get user info.
        $userquery = "SELECT userid, username, userrealname, useravataruri, userage, " .
                "userdescription FROM user WHERE userid=$userid";
        if (!($result = mysqli_query($db_server, $userquery))) {
            echo "<p>Whoops! Couldn\'t find user!</p>\n";
        } else { //display user details.
            $userdetails = mysqli_fetch_assoc($result);
            echo "<h1>{$userdetails['username']}</h1>\n";
            echo "<img src={$userdetails['useravataruri']}>\n";
            echo "<h2>Real name</h2><p>{$userdetails['userrealname']}</p>\n";
            echo "<h2>Age</h2> <p>{$userdetails['userage']}</p>\n";
            
            //from this line onwards, get and display info about the user's artist,
            //album and track faovurites.
            $favouriteartistquery = "SELECT artistid, artistname, artistdatetimefavourited " .
                    "FROM user NATURAL JOIN favouriteartists NATURAL JOIN artist " .
                    "WHERE favouriteartists.userid = {$userdetails['userid']} " .
                    "ORDER BY artistname ASC";
            echo "<h2>{$userdetails['username']}'s favourite artists</h2>\n";
            $result = mysqli_query($db_server, $favouriteartistquery);
            if (!mysqli_num_rows($result)) {
                echo "<p>{$userdetails['username']} doesn't have any favourite artists yet.</p>\n";
            } else {
                $len = mysqli_num_rows($result);
                for ($i = 0; $i < $len; ++$i) {
                    $favouritedetails = mysqli_fetch_assoc($result);
                    echo "<p><a href='bandlist.php?artistid={$favouritedetails['artistid']}'>" . 
                         "{$favouritedetails['artistname']}</a> - {$favouritedetails['artistdatetimefavourited']}</p>\n";
                }
            }
            $favouritealbumquery = "SELECT albumid, albumname, albumdatetimefavourited " .
                    "FROM user NATURAL JOIN favouritealbums NATURAL JOIN album " .
                    "WHERE favouritealbums.userid = {$userdetails['userid']} " .
                    "ORDER BY albumname ASC";
            echo "<h2>{$userdetails['username']}'s favourite albums</h2>\n";
            $result = mysqli_query($db_server, $favouritealbumquery);
            if (!mysqli_num_rows($result)) {
                echo "<p>{$userdetails['username']} doesn't have any favourite albums yet.</p>\n";
            } else {
                $len = mysqli_num_rows($result);
                for ($i = 0; $i < $len; ++$i) {
                    $favouritealbumdetails = mysqli_fetch_assoc($result);
                    echo "<p><a href='albumlist.php?albumid={$favouritealbumdetails['albumid']}'>" . 
                         "{$favouritealbumdetails['albumname']}</a> - {$favouritealbumdetails['albumdatetimefavourited']}</p>\n";
                }
            }
            $favouritetrackquery = "SELECT trackid, trackname, trackdatetimefavourited " .
                    "FROM user NATURAL JOIN favouritetracks NATURAL JOIN track " .
                    "WHERE favouritetracks.userid = {$userdetails['userid']} " .
                    "ORDER BY trackname ASC";
            echo "<h2>{$userdetails['username']}'s favourite tracks</h2>\n";
            $result = mysqli_query($db_server, $favouritetrackquery);
            if (!mysqli_num_rows($result)) {
                echo "<p>{$userdetails['username']} doesn't have any favourite tracks yet.</p>\n";
            } else {
                $len = mysqli_num_rows($result);
                for ($i = 0; $i < $len; ++$i) {
                    $favouritetrackdetails = mysqli_fetch_assoc($result);
                    echo "<p><a href='tracklist.php?trackid={$favouritetrackdetails['trackid']}'>" .
                         "{$favouritetrackdetails['trackname']}</a> - {$favouritetrackdetails['trackdatetimefavourited']}</p>\n";
                }
            }

            echo "<h2>Description</h2> <p>{$userdetails['userdescription']}</p>\n";
            mysqli_close($db_server);
        }
    }
    echo "</div>\n";
}
?>