<!-- Name: tracklist.php
     Author: Andrew Brown
     Date: 03/02/2013
     Description: 
     On first viewing, this lists all of the tracks in the database, along 
     with some basic information about them, such as their artist, album cover, 
     and album release date. If you click on a trcaks, you'll get more 
     information about it, such as a description and a list of users who
     favourited it.                                                          -->
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
            <h1>Albums</h1>
            <p>Here, you can find a list of all of the tracks on our database,
               along with their Artists, containing album, and release dates.</p>
            <?php
            include_once 'databasevars.php';
            $db_server = mysql_connect($db_hostname, $db_username, $db_password);
            if (!$db_server) {
                echo '<p>Could not connect to database:' . mysql_error() . '</p>';
            } else {
                $database = mysql_select_db($db_database);
                $albumquery = 'SELECT albumcoveruri, trackname, albumname, artistname, year ' .
                        'FROM track NATURAL JOIN album NATURAL JOIN artist ORDER BY trackname DESC';
                $result = mysql_query($albumquery);
                if (!$result) {
                    echo '<p>Could not access artists: ' . mysql_error() . '</p>';
                } else {
                    $len = mysql_num_rows($result);
                    echo '<table>';
                    for ($i = 0; $i < $len; ++$i) {
                        $row = mysql_fetch_array($result, MYSQL_ASSOC);
                        echo "<tr>";
                        echo "   <td><img src=\"{$row["albumcoveruri"]}\" height=\"100\" width=\"100\"></img></td>";
                        echo "   <td>{$row["trackname"]}</td>";
                        echo "   <td>{$row["albumname"]}</td>";
                        echo "   <td>{$row["artistname"]}</td>";
                        echo "   <td>{$row["year"]}</td>";
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