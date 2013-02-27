<?php
/* * *****************************************************************************
 * Filename: andrewstwitterwidget.html
 * Author: Andrew Brown
 * Date: 17/02/2013
 * Description: 
 * This is my individual widget for the website. The service that it pulls data
 * from is Twitter. If you are on a track, album, or band page, then the widget
 * displays the latest tweets about the corresponding band (i.e "TheSmiths"). 
 * Otherwise, it just displays the latest tweets about alternative rock in
 * general ("AlternativeRock").
 * 
 * To avoid hitting Twitter's 15 minute rate limit for the API, the json strings 
 * returned by twitter are cached in the database. If it's been more than 3 
 * minutes since the last request for the current band, then Twitter is queried, 
 * and the json string is placed in the database. If not, then the json string 
 * is taken from the database and used instead. Since we only have 10 
 * bands (and the generic alternews search for when no band is selected), 
 * then the maximum number of requests that can be sent to Twitter every 
 * 15 minutes is 11*5, or 55. This is well under the limit of 180 requests.
 * For a larger application, the stream api might be more applicable, but
 * for a application the size of Alternews, the search api is more than
 * adequate.   
 *   
 * This widget uses Twitter's Search API.
 *
 * NOTE: As with several other files, this is only meant to be included in other
 * pages. It is not meant to be viewed on it's own.
 * 
 * -Andrew
 * **************************************************************************** */

$twitterquery = "http://search.twitter.com/search.json?"; //base twitter search api uri.
require_once("C:/xampp/htdocs/MusicMashup/databasevars.php");

$db_server = mysqli_connect($db_hostname, $db_username, $db_password, $db_database);

//if there's a 'get' variable, get the artist name and artist id from the 
//MySQL database. Query varies depending on type of 'get' variable (artistid, albumid, etc.) 
if (isset($_GET['artistid']) || isset($_GET['albumid']) || isset($_GET['trackid'])) {
    if (isset($_GET['artistid'])) {
        $query = "SELECT artistname, artistid FROM artist WHERE artistid = {$_GET['artistid']}";
    } else if (isset($_GET['albumid'])) {
        $query = "SELECT artistname, artistid FROM album NATURAL JOIN artist WHERE albumid = {$_GET['albumid']}";
    } else if (isset($_GET['trackid'])) {
        $query = "SELECT artistname, artistid FROM track NATURAL JOIN artist WHERE trackid = {$_GET['trackid']}";
    }

    $result = mysqli_query($db_server, $query);
    $artistdetails = mysqli_fetch_assoc($result);
    $search = str_replace(" ", "", $artistdetails['artistname']);
    $artistid = $artistdetails['artistid'];
} else {
    //No get variable, so just search for AlternativeRock.
    $search = "AlternativeRock";
    $artistid = 0;
}

//check to see if it's been 3 minutes since the last twitter request for appropriate artist.
$cachequery = "SELECT twitterjsonstring FROM twittercache WHERE artistid=$artistid" .
        " AND NOW() < ADDDATE(twitterdatetimecached, INTERVAL 3 MINUTE)";

if (!$results = mysqli_query($db_server, $cachequery)) {
    echo "SQL query error: " . $mysqli_error($db_server);
} else {
    if (mysqli_num_rows($results) != 0) { //it hasn't, so grab cached json string.
        $twitterdetails = mysqli_fetch_assoc($results);
        $jsonstring = $twitterdetails['twitterjsonstring'];
    } else { //It has been, so make new Twitter query.
        //Search for artist. 100 results, english language, recent and popular tweets, and include entities (Hashtags, etc).
        $twitterquery .= "q=" . $search . "&rpp=100&lang=en&resulttype=mixed&include_entities=true";

        //Get search results from twitter.
        $jsonstring = file_get_contents($twitterquery);

        //caches the jsonstring and the current time for the selected artist.
        //the json string is slashed so that any quotes in it don't confuse the database. 
        $cachequery = "UPDATE twittercache SET twitterjsonstring = '" . addslashes($jsonstring) .
                "', twitterdatetimecached = NOW( ) WHERE artistid = $artistid";
        if (!mysqli_query($db_server, $cachequery)) {
            echo "Error caching tweets!";
            echo mysqli_error($db_server);
        }
    }
}

mysqli_close($db_server);

//decode the json string and place it in an object that we can access.
?>


<div id="twitterwidget">
    <img src="Widgets/Twitterlogo.jpg" />
    <div id="twitterbody">
        <?php
//loop through each tweet, outputting the user's picture and name, the time 
//the tweet was made, and the tweet text.
        if (($twitterjson = json_decode($jsonstring)) == null) {
            echo "error decoding string!";
        } else {
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
        }
        ?>
    </div>
    <!-- This code was taken from Twitter; they provide code for the 'tweet'
         button. All I did here was use the content of the variable 'search'
         to customise what the users 'tweet' at. For instance, if they're on
         The Cure's page, then the hashtag will be '#TheCure'.               
         code source: https://dev.twitter.com/docs/tweet-button              -->

    <a href="https://twitter.com/intent/tweet?button_hashtag=<?php echo $search; ?>
       &text=I%20found%20this%20band%20on%20Alternews!" class="twitter-hashtag-button">Tweet #<?php echo $search; ?></a>

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
/* * *****************************************************************************
 * The add_entities function takes two parameters, the text of a tweet, and the
 * 'entities' object related to that text. The function then finds the entities
 * in the text, and adds the appropriate link. For instance, the mention
 * '@TheCure' in the tweet text would normally just appear as plain text.
 * After running the tweet text (and it's entities) through add_entities, the 
 * mention would then appear as a hyperlink to the Alternews twitter page. 
 * **************************************************************************** */

function add_entities($text, $entities) {

    foreach ($entities->urls as $link) {
        $text = str_replace("$link->url", "<a href='{$link->url}'>$link->display_url</a>", $text);
    }

    foreach ($entities->user_mentions as $mention) {
        $text = str_replace("@{$mention->screen_name}", "<a href='https://twitter.com/{$mention->screen_name}'>@{$mention->screen_name}</a>", $text);
    }

    foreach ($entities->hashtags as $hashtag) {
        $text = str_replace("#{$hashtag->text}", "<a href='https://twitter.com/search?q=%23{$hashtag->text}&src=hash'>#{$hashtag->text}</a>", $text);
    }

    return $text;
}

//get a twitter json string via UWE proxy and stop caching.
//Based on the uwe_get_file function provided in the DSA workshops.
function uwe_get_twitter_json($uri) {

    //create oauth request string.

    $context = stream_context_create(
            array('http' =>
                array('proxy' => 'proxysg.uwe.ac.uk:8080',
                    'header' => 'Cache-Control: no-cache'
                )
            ));
    $contents = file_get_contents($uri, false, $context);
    return $contents;
}
?>