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
        <?php include_once 'header.html'; ?>
        <div class="clear"></div>
        <?php
        include_once 'databasevars.php';
        echo '<div id="pagecontent">';
        if (!$_POST) {
            echo <<< _END
            <h1>Register</h1>
            <p>Start your journey here! Sign up, and dive into the world of alternate rock.</p>
            <div class=register> <!-- start of login -->
                <form class="register" action="registration.php" method="post">
                    <table class="register">
                        <tr><td><input type="text" id="Name" name="username" style="color:#9E9E9E; font-style:italic" value="Desired Username" size="16"/></td></tr>
                        <tr><td><input type="text" id="Password" name="password" style="color:#9E9E9E; font-style:italic" value="Desired Password" size="16"/></td></tr>
                        <tr><td><input type="submit" value="Register" /></td></tr>
                    </table>
                </form>
            </div>  <!-- End of login --> 
_END;
        } else if (!$_POST['username'] && !$_POST['password']) {
            $db_server = mysql_connect($db_hostname, $db_username, $db_password);
            if (!$db_server) {
                echo '<p>Could not connect to database:' . mysql_error() . '</p>';
            } else {
                $database = mysql_select_db($db_database);
                $insertuserquery = "INSERT INTO user (username, userpassword) VALUES ('$_POST[username]','$_POST[password]')";
                if (!mysql_query($insertuserquery)) {
                    echo "Couldn't insert you into database: " . mysql_error();
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
                                       <tr><td><input type="submit" value="Register" /></td></tr>
                                   </table>
                               </form>
                           </div>  <!-- End of login --> 
_END;
                }
            }
            mysql_close($db_server);
        } else {
            $db_server = mysql_connect($db_hostname, $db_username, $db_password);
            if (!$db_server) {
                echo '<p>Could not connect to database:' . mysql_error() . '</p>';
            } else {
                $database = mysql_select_db($db_database);
                $insertuserquery = "UPDATE user SET realname='$_POST[realname]', age='$_POST[age]', description='$_POST[description]' WHERE username='$_POST[username]";
                if (!mysql_query($insertuserquery)) {
                    echo "Couldn't insert your data into database: " . mysql_error();
                } else {
                    echo '<p>looks like you\'re all set! Go favourite some tracks!</p>';
                }
            }
        }
        echo '</div>';
        ?>
        <div class="clear"></div>
        <?php include_once 'widgetpane.php'; ?>
        <?php include_once 'footer.html'; ?>
    </body>
</html>