<!-- Name: registration.php
     Author: Andrew Brown
     Date: 03/02/2013
     Description: 
     The registration page. Here, new users can register, so that they can start
     favouriting artists and tracks, and add some simple information about
     themselves to the database.     
-->
<?php require_once 'databasevars.php';
 ?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" type="text/css" href="./CSS/alternews.css">
        <title>Alternews - Home</title>
    </head>
    <body>
        <?php
        include_once 'header.php';
        echo "<div class='clear'>\n";
        echo "<div id='pagecontent'>\n";
        if (!isset($_COOKIE['username'])) { //If user isn't logged in
            if (!isset($_POST['num'])) { //If user hasn't begun the registration process
                firstLoad();
            } else if ($_POST['num'] == 1) { //User has finished intial step, can add extra details.
                extraDetails();
            } else if ($_POST['num'] == 2) { //Registration has been completed.
                registrationComplete();
            }
        } else { //user is logged in.
            echo "<h1>You're already registered</h1>\n";
            echo "<p>Hey there {$_COOKIE['username']}! Looks like you're already registered! Get out there and favourite some bands or tracks!</p>";
        }
        echo "</div>\n";
        include_once 'widgetpane.php';
        echo "<div class='clear'/>\n";
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
                <tr><td><input type="password" id="Password" name="password" placeholder="Desired password" size="16"/></td></tr>
                <!-- use hidden field to determine what stage the user is at in the process. -->
                <tr><td><input type="hidden" name="num" value="1"/></td></tr> 
                <tr><td><input type="submit" value="Register" /></td></tr>
            </table>
        </form>
    </div>
    <?php
}

function extraDetails() {
    global $db_hostname, 
           $db_username,
           $db_password,
           $db_database,
           $salt1, 
           $salt2;
    
    //salt the password.
    $password = $salt1 . $_POST['password'] . $salt2;
    if (!($db_server = mysqli_connect($db_hostname, $db_username, $db_password, $db_database))) {
        echo "<p>Could not connect to database:" . mysqli_error($db_server) . "</p>\n";
    } else { //insert user into databse.
        //password is hashed. It was salted earlier.
        $insertuserquery = "INSERT INTO user (username, userpassword) VALUES ('$_POST[username]', SHA1('$password'))";
        if (!mysqli_query($db_server, $insertuserquery)) {
            echo "<p>Couldn't insert you into database: " . mysqli_error($db_server) . "</p>\n";
        } else { //user can now enter more details about themselves.
            ?>
            <h1>Tell us about yourself</h1>
            <p>Congratulations <?php echo "{$_POST['username']}" ?>! You have successfully registered!
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
            mysqli_close($db_server);
        }
    }
}

function registrationComplete() {
    global $db_hostname;
    global $db_username;
    global $db_password;
    global $db_database;


    if (!($db_server = mysqli_connect($db_hostname, $db_username, $db_password, $db_database))) {
        echo "<p>Could not connect to database:" . mysqli_error() . "</p>\n";
    } else { //update user details so that the info they added in extraDetails() is in the db.
        $insertuserquery = "UPDATE user SET userrealname='$_POST[realname]', userage='$_POST[age]'," . 
                           " userdescription='$_POST[description]' WHERE username='$_POST[username]'";
        if (!mysqli_query($db_server, $insertuserquery)) {
            echo "Couldn't insert your data into database: " . mysqli_error();
        } else {
            echo "<h1>Congratulations</h1>";
            echo "<p>Looks like you're all set, {$_POST['username']}! Go favourite some tracks!</p>";
        }
        mysqli_close($db_server);
    }
}
?>