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
        <div id="pagecontent">
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
        </div>
        <div class="clear"></div>
        <?php include_once 'widgetpane.php'; ?>
        <?php include_once 'footer.html'; ?>    
    </body>


</html>