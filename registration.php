<!-- Name: registration.php
     Author: Andrew Brown
     Date: 03/02/2013
     Description: 
     The registration page. Here, new users can register, so that they can start
     favouriting artists and tracks, and add some simple information about
     themselves to the database.                                             -->
<?php
if (!$_COOKIE && !$_POST) {
    firstLoad();
} else if ($_POST["num"] == 1) {
    extraDetails();
} else if ($_POST["num"] == 2) {
    registrationComplete();
} else {
    alreadyRegistered();
}

function firstLoad() {
    echo <<< _END
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
            <link rel="stylesheet" type="text/css" href="./CSS/alternews.css">
            <title>Alternews - Home</title>
    </head>
    <body>
_END;
    include_once 'header.html';
    echo '<div class="clear"></div>';
    include_once 'databasevars.php';
    echo <<< _END
        <div id="pagecontent">
            <h1>Register</h1>
            <p>Start your journey here! Sign up, and dive into the world of alternate rock.</p>
            <div class=register> <!-- start of login -->
                <form class="register" action="registration.php" method="post">
                    <table class="register">
                        <tr><td><input type="text" id="Name" name="username" style="color:#9E9E9E; font-style:italic" value="Desired Username" size="16"/></td></tr>
                        <tr><td><input type="text" id="Password" name="password" style="color:#9E9E9E; font-style:italic" value="Desired Password" size="16"/></td></tr>
                        <tr><td><input type="hidden" name="num" value="1"/></td></tr>
                        <tr><td><input type="submit" value="Register" /></td></tr>
                    </table>
                </form>
            </div>  <!-- End of login --> 
        </div>
        <div class="clear"></div >
    </body>
_END;

    include_once 'widgetpane.php';
    include_once 'footer.html';
    echo '</html>';
}

function extraDetails() {
    echo <<< _END
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
            <link rel="stylesheet" type="text/css" href="./CSS/alternews.css">
            <title>Alternews - Home</title>
    </head>
    <body>
_END;
    include_once 'header.html';
    echo '<div class="clear />';

    echo'<div id="pagecontent">';
    include 'databasevars.php';
    $db_server = mysql_connect($db_hostname, $db_username, $db_password);
    if (!$db_server) {
        echo '<p>Could not connect to database:' . mysql_error() . '</p>';
    } else {
        $database = mysql_select_db($db_database);
        $insertuserquery = "INSERT INTO user (username, userpassword) VALUES ('$_POST[username]','$_POST[password]')";
        if (!mysql_query($insertuserquery)) {
            echo "<p>Couldn't insert you into database: " . mysql_error() . "</p>";
        } else {
            echo <<< _END
        <p>Congratulations {$_POST['username']}! You have successfully registered!
            Why not tell us a bit about yourself?</p>
        <div class=register> <!-- start of login -->
            <form class="register" action="registration.php" method="post">
                <table class="register">
                    <tr><td><input type="text" id="Realname" name="realname" style="color:#9E9E9E; font-style:italic" value="" size="16"/></td></tr>
                    <tr><td><input type="number" id="Age" name="age" style="color:#9E9E9E; font-style:italic" value="" size="16"/></td></tr>
                    <tr><td><input type="text" id="Description" name="description"></tr></td>
                    <tr><td><input type="hidden" id="Username name="username" value="{$_POST['username']}"></tr></td>
                    <tr><td><input type="hidden" name="num" value="2"/></td></tr>
                    <tr><td><input type="submit" value="Register" /></td></tr>
                </table>
            </form>
        </div>  <!-- End of login --> 
_END;
            mysql_close();
            echo "<div class='clear'>";
            include 'widgetpane.php';
            include 'footer.html';
            echo '  </body>';
            echo '</html>';
            _END;
        }
    }
}

function registrationComplete() {
    echo <<< _END
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
            <link rel="stylesheet" type="text/css" href="./CSS/alternews.css">
            <title>Alternews - Home</title>
    </head>
    <body>
_END;
    include_once 'header.html';
    echo '<div class="clear" />';
    echo '<div id="pagecontent">';
    include 'databasevars.php';
    $db_server = mysql_connect($db_hostname, $db_username, $db_password);
    if (!$db_server) {
        echo '<p>Could not connect to database:' . mysql_error() . '</p>';
    } else {
        $database = mysql_select_db($db_database);
        $insertuserquery = "UPDATE user SET realname='$_POST[realname]', age='$_POST[age]', description='$_POST[description]' WHERE username='$_POST[username]'";
        if (!mysql_query($insertuserquery)) {
            echo "Couldn't insert your data into database: " . mysql_error();
        } else {
            echo "<p>looks like you\'re all set, {$_POST['username']}! Go favourite some tracks!</p>";
        }
        mysql_close();
        echo '</div>';
    }
    echo '<div class="clear"/>';
    include 'widgetpane.php';
    include 'footer.html';
    echo '  </body>';
    echo '</html>';
}

function alreadyRegistered() {
    echo <<< _END
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
            <link rel="stylesheet" type="text/css" href="./CSS/alternews.css">
            <title>Alternews - Home</title>
    </head>
    <body>
        <div id="pagecontent">
            <p>Hey there! Looks like you're already registered! Get out there and favourite some bands or tracks!</p>
        </div>
    <body>    
_END;
}
?>