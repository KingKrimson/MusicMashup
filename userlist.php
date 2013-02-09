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
require_once 'login.php' ?>
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
            echo '<div id="pagecontent">';
            echo '<h1>Users</h1>';
            echo '<p>Here, you can find a list of all of the users we have in our' .
            'database, along with their age.</p>';
            $db_server = mysqli_connect($db_hostname, $db_username, $db_password, $db_database);
            if (!$db_server) {
                echo '<p>Could not connect to database:' . mysqli_error($db_server) . '</p>';
            } else {
                $userquery = 'SELECT userid, useravataruri, username, userage FROM user ORDER BY username ASC';
                $result = mysqli_query($db_server, $userquery);
                if (!$result) {
                    echo '<p>Could not access users: ' . mysqli_error($db_server) . '</p>';
                } else {
                    $len = mysqli_num_rows($result);
                    echo '<table>';
                    for ($i = 0; $i < $len; ++$i) {
                        $row = mysqli_fetch_assoc($result);
                        echo "<tr>";
                        echo "   <td><img src=\"{$row["useravataruri"]}\" height=\"100\" width=\"100\"></img></td>";
                        echo "   <td><a href=userlist.php?userid={$row["userid"]}>{$row["username"]}</a></td>";
                        echo "   <td>{$row["userage"]}</td>";
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

function show_user($userid) {
    global $db_hostname, $db_username, $db_password, $db_database;
    echo '<div id="pagecontent">';
    if (!($db_server = mysqli_connect($db_hostname, $db_username, $db_password, $db_database))) {
        echo '<p>Could not connect to database:' . mysqli_error($db_server) . '</p>';
    } else {
        $userquery = "SELECT username, userrealname, useravataruri, userage, " .
                "userdescription FROM user WHERE userid=$userid";
        if (!($result = mysqli_query($db_server, $userquery))) {
            echo '<p>Whoops! Couldn\'t find user!</p>';
        } else {
            $userdetails = mysqli_fetch_assoc($result);
            echo "<h1>{$userdetails['username']}</h1>";
            echo "<img src={$userdetails['useravataruri']}>";
            echo "<p>Real name - {$userdetails['userrealname']}</p>";
            echo "<p>Age - {$userdetails['userage']}</p>";
            echo "<p>Description - </p> <p>{$userdetails['userdescription']}</p>";
        }
    }
    echo '</div>';
}
?>