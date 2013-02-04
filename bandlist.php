<!-- Name: bandlist.php
     Author: Andrew Brown
     Date: 03/02/2013
     Description: 
     On first viewing, this lists all of the bands in the database. 
     If you click on an band, you'll get more information about 
     it, such as the albums and tracks they've released, and a description 
     of the band. From a specific artist page, users can 'favourite' them;
     these favourites are viewable on the corresponding user and artist pages.-->
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
            <h1>Bands</h1>
            <?php
            include_once 'databasevars.php';
            $db_server = mysql_connect($db_hostname, $db_username, $db_password);
            if (!$db_server) {
                echo '<p>Could not connect to database:' . mysql_error() . '</p>';
            } else {
                $database = mysql_select_db($db_database);
                $bandquery = 'SELECT * FROM artist ORDER BY artistname DESC';
                $result = mysql_query($bandquery);
                if (!$result) {
                    echo '<p>Could not access bands:' . mysql_error() . '</p>';
                } else {
                    $len = mysql_num_rows($result);
                    for ($i = 0; $i < $len; ++$i) {
                        echo '<p>'.mysql_result($result, $i, 'artistname').'</p>';
                    }
                }
            }
            ?>
        </div>
        <div class="clear"></div>
        <?php include_once 'widgetpane.php'; ?>
        <?php include_once 'footer.html'; ?>    
    </body>


</html>