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
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" type="text/css" href="./CSS/alternews.css">
        <title>Alternews - Home</title>
    </head>
    <body>
        <?php include_once 'header.html';?>
        <div class="clear"></div>
        <div id="pagecontent">
            <h1>Users</h1>
            <p>Here, you can find a list of all of the users we have in our
                database, along with their age.</p>
            <?php
            include_once 'databasevars.php';
            $db_server = mysql_connect($db_hostname, $db_username, $db_password);
            if (!$db_server) {
                echo '<p>Could not connect to database:' . mysql_error() . '</p>';
            } else {
                $database = mysql_select_db($db_database);
                $userquery = 'SELECT useravataruri, username, userage FROM user ORDER BY username ASC';
                $result = mysql_query($userquery);
                if (!$result) {
                    echo '<p>Could not access users: ' . mysql_error() . '</p>';
                } else {
                    $len = mysql_num_rows($result);
                    echo '<table>';
                    for ($i = 0; $i < $len; ++$i) {
                        $row = mysql_fetch_array($result, MYSQL_ASSOC);
                        echo "<tr>";
                        echo "   <td><img src=\"{$row["useravataruri"]}\" height=\"100\" width=\"100\"></img></td>";
                        echo "   <td>{$row["username"]}</td>";
                        echo "   <td>{$row["userage"]}</td>";
                        echo "</tr>";
                    }
                    echo '</table>';
                }
            }
            ?>
        </div>
        <div class="clear"></div>
        <?php include_once 'widgetpane.php'; ?>
        <?php include_once 'footer.html'; ?>    
    </body>


</html>