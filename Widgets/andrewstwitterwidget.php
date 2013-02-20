<?php
/*******************************************************************************
 * Filename: andrewstwitterwidget.html
 * Author: Andrew Brown
 * Date: 17/02/2013
 * Description: 
 * This is my individual widget for the website. The service that it pulls data
 * from is Twitter. If you are on a track, album, or band page, then the widget
 * displays the latest tweets about the corresponding band (i.e "#thesmiths"). 
 * Otherwise, it just displays the latest tweets about alternative rock in
 * general ("#alternativerock").
 *   
 * This widget uses Twitter's Search API.
 *
 * NOTE: As with several other files, this is only meant to be included in other
 * pages. It is not meant to be viewed on it's own.
 ******************************************************************************/

$twitterquery = "http://search.twitter.com/search.json?";
require_once("../databasevars.php");

$db_server = mysqli_connect($db_hostname, $db_username, $db_password, $db_database);
if (isset($_GET['artistid']) || isset($_GET['albumid']) || isset($_GET['trackid'])) {
    if (isset($_GET['artistid'])) {
        $query = "SELECT artistname, artistid FROM artist WHERE artistid = {$_GET['artistid']}";
    } else if (isset($_GET['albumid'])) {
        $query ="SELECT artistname, artistid FROM album NATURAL JOIN artist WHERE albumid = {$_GET['albumid']}";
    } else if (isset($_GET['trackid'])) {
        $query ="SELECT artistname, artistid FROM track NATURAL JOIN artist WHERE trackid = {$_GET['trackid']}";
    }
    
    $result = mysqli_query($db_server, $query);
    $artistdetails = mysqli_fetch_assoc($result);
    $search = strtolower(str_replace(" ", "", $artistdetails['artistname']));
    $artistid = $artistdetails['artistid'];
} else {
    $search = "alternativerock";
    $artistid = 0;
}

$query = "SELECT twitterjson FROM tweetcache WHERE artistid = $artistid " . 
        "AND DATEDIFF(MINUTE, NOW(), twitterdatetime) > 15";

$twitterquery .= "q=".$search."&rpp=20&lang=en&resulttype=mixed&include_entities=true";

$jsonstring = file_get_contents($twitterquery);
$twitterjson = json_decode($jsonstring);
?>


<div id="twitterwidget">
    <!--<img src="../Images/SiteImages/twitter.jpg" />-->
    <div id="twitterbody">
        <?php 
        foreach ($twitterjson->results as $tweet) {
            echo "<div class=\"tweet\">\n";
            echo "<table>\n";
            echo "<tr><td><img src={$tweet->profile_image_url}></td>"
                 . "<td><a href=\"https://twitter.com/{$tweet->from_user}\">$tweet->from_user</a></td></tr>\n";
            echo "</table>\n";
            echo "{$tweet->created_at}\n";
            echo "<p>{$tweet->text}</p>";
            echo "</div>\n";
        }
        ?>
    </div>
</div>

