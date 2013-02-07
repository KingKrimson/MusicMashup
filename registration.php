<!-- Name: registration.php
     Author: Andrew Brown
     Date: 03/02/2013
     Description: 
     The registration page. Here, new users can register, so that they can start
     favouriting artists and tracks, and add some simple information about
     themselves to the database.                                             -->
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" type="text/css" href="./CSS/alternews.css">
        <title>Alternews - Home</title>
    </head>
    <body>
        <?php
        include_once 'header.html';
        echo '<div class="clear">';
        echo '<div id="pagecontent">';
        if (!$_COOKIE && !$_POST) {
            firstLoad();
        } else if ($_POST["num"] == 1) {
            extraDetails();
        } else if ($_POST["num"] == 2) {
            registrationComplete();
        } else {
            echo '<h1>You\'re already registered</h1>';
            echo '<p>Hey there! Looks like you\'re already registered! Get out there and favourite some bands or tracks!</p>';
        }
        echo '</div>';
        echo '<div class="clear"/>';
        include_once 'widgetpane.php';
        include_once 'footer.html';
        ?>
    </body>
</html>

<?php

function firstLoad() {
    ?>
    <h1>Register</h1>
    <p>Start your journey here! Sign up, and dive into the world of alternate rock.</p>
    <div class=register> <!-- start of login -->
        <form class="register" action="registration.php" method="post">
            <table class="register">
                <tr><td><input type="text" id="Name" name="username"  placeholder="Desired username" size="16"/></td></tr>
                <tr><td><input type="text" id="Password" name="password" placeholder="Desired password" size="16"/></td></tr>
                <tr><td><input type="hidden" name="num" value="1"/></td></tr>
                <tr><td><input type="submit" value="Register" /></td></tr>
            </table>
        </form>
    </div>  <!-- End of login --> 
    <?php
}

function extraDetails() {
    require_once 'databasevars.php';
    $db_server = mysql_connect($db_hostname, $db_username, $db_password);
    if (!$db_server) {
        echo '<p>Could not connect to database:' . mysql_error() . '</p>';
    } else {
        $database = mysql_select_db($db_database);
        $insertuserquery = "INSERT INTO user (username, userpassword) VALUES ('$_POST[username]','$_POST[password]')";
        if (!mysql_query($insertuserquery)) {
            echo "<p>Couldn't insert you into database: " . mysql_error() . "</p>";
        } else {
            ?>
            <h1>Tell us about yourself</h1>
            <p>Congratulations <?php echo "{$_POST['username']}"?>! You have successfully registered!
                Why not tell us a bit about yourself?</p>
            <div class=register> <!-- start of login -->
                <form class="register" action="registration.php" method="post">
                    <table class="register">
                        <tr><td><input type="text" id="Realname" name="realname" placeholder="Real name" size="16"/></td></tr>
                        <tr><td><input type="number" id="Age" name="age" placeholder="Age" size="16"/></td></tr>
                        <tr><td><input type="text" id="Description" name="description" placeholder="description"></tr></td>
                        <tr><td><input type="hidden" id="Username" name="username" value="<?php echo "{$_POST['username']}" ?>"></tr></td>
                        <tr><td><input type="hidden" name="num" value="2"/></td></tr>
                        <tr><td><input type="submit" value="Register" /></td></tr>
                    </table>
                </form>
            </div>  <!-- End of login --> 
            <?php
            mysql_close();
        }
    }
}

function registrationComplete() {
    require_once 'databasevars.php';
    $db_server = mysql_connect($db_hostname, $db_username, $db_password);
    if (!$db_server) {
        echo '<p>Could not connect to database:' . mysql_error() . '</p>';
    } else {
        $database = mysql_select_db($db_database);
        $insertuserquery = "UPDATE user SET userrealname='$_POST[realname]', userage='$_POST[age]', userdescription='$_POST[description]' WHERE username='$_POST[username]'";
        if (!mysql_query($insertuserquery)) {
            echo "Couldn't insert your data into database: " . mysql_error();
        } else {
            echo "<h1>Congratulations</h1>";
            echo "<p>Looks like you're all set, {$_POST['username']}! Go favourite some tracks!</p>";
        }
        mysql_close();
    }
}
?>