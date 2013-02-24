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
require_once("C:/xampp/htdocs/MusicMashup/databasevars.php");

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
    $search = str_replace(" ", "", $artistdetails['artistname']);
    $artistid = $artistdetails['artistid'];
} else {
    $search = "alternativerock";
    $artistid = 0;
}

$query = "SELECT twitterjson FROM tweetcache WHERE artistid = $artistid " . 
        "AND DATEDIFF(MINUTE, NOW(), twitterdatetime) > 15";

$twitterquery .= "q=".$search."&rpp=100&lang=en&resulttype=mixed&include_entities=true";

$jsonstring = file_get_contents($twitterquery);
$twitterjson = json_decode($jsonstring);
?>


<div id="twitterwidget">
    <img src="./Twitterlogo.jpg" />
    <div id="twitterbody">
        <?php 
        foreach ($twitterjson->results as $tweet) {
            //add entities to tweet text (see function comment).
            $text = add_entities($tweet->text, $tweet->entities);
            
            echo "<div class=\"tweet\">\n";
            echo "<table>\n";
            echo "<tr><td><img src={$tweet->profile_image_url}></td>"
                 . "<td><a href=\"https://twitter.com/{$tweet->from_user}\">$tweet->from_user</a></td></tr>\n";
            echo "</table>\n";
            echo "{$tweet->created_at}\n";
            echo "<p>$text</p>\n";
            echo "</div>\n";
        }
        ?>
    </div>
    <!-- This code was taken from Twitter; they provide code for the 'tweet'
         button. All I did here was use the content of the variable 'search'
         to customise what the users 'tweet' at. For instance, if they're on
         The Cure's page, then the hashtag will be '#TheCure'.               
         code source: https://dev.twitter.com/docs/tweet-button              -->
    
    <a href="https://twitter.com/intent/tweet?button_hashtag=<?php echo $search;?>&text=I%20found%20this%20band%20on%20Alternews!"
       class="twitter-hashtag-button">Tweet #<?php echo $search;?></a>
    
    <script>
            !function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];
            if(!d.getElementById(id)){js=d.createElement(s);
            js.id=id;js.src="//platform.twitter.com/widgets.js";
            fjs.parentNode.insertBefore(js,fjs);}}
            (document,"script","twitter-wjs");
    </script>
    <!-- End of twitter's code. -->
</div>

<?php
/*******************************************************************************
 * The add_entities function takes two parameters, the text of a tweet, and the
 * 'entities' object related to that text. The function then finds the entities
 * in the text, and adds the appropriate link. For instance, the mention
 * '@TheCure' in the tweet text would normally just appear as plain text.
 * After running the tweet text (and it's entities) through add_entities, the 
 * mention would then appear as a hyperlink to the Alternews twitter page. 
 ******************************************************************************/
function add_entities($text, $entities) {
    
    foreach ($entities->urls as $link) {
        $text = str_replace("$link->url", "<a href='{$link->url}'>$link->display_url</a>", $text);
    }
    
    foreach($entities->user_mentions as $mention) {
        $text = str_replace("@{$mention->screen_name}", "<a href='https://twitter.com/{$mention->screen_name}'>@{$mention->screen_name}</a>", $text);
    }
    
    foreach($entities->hashtags as $hashtag) {
        $text = str_replace("#{$hashtag->text}", "<a href='https://twitter.com/search?q=%23{$hashtag->text}&src=hash'>#{$hashtag->text}</a>", $text);
    }
    
    return $text;
}
?>