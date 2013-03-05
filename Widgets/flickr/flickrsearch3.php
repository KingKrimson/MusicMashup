<?php

require_once ("flickrlib.php");
require_once ("databasevars.php");

function get_uri_parameter($name, $default) {
    if (isset($_REQUEST[$name])) {
        return $_REQUEST[$name];
    }
    else
        return $default;
}

;

function flickr_uri($method, $params) {

    $uri = "http://api.flickr.com/services/rest/?method=" .
            $method .
            "&api_key=" . FLICK . "&" .
            join($params, "&");
    $xml = uwe_get_file($uri);
    return new SimpleXMLElement($xml);
}

;

function get_flickr_photos($flickr) {
    $h = "";
    $h .= "<div id=flickrbody>";
        foreach ($flickr->photos->photo as $photo) {
        $pa = $photo->attributes();
        $imguri = "http://farm{$pa->farm}.static.flickr.com/{$pa->server}/{$pa->id}_{$pa->secret}_m.jpg";
        $photouri = "http://www.flickr.com/photos/{$pa->owner}/{$pa->id}";
        $h.= "</br><a href='{$photouri}'> <img src='{$imguri}'/> </a>";
    }
    $h .= "</div>";
    return $h;
}

;

function get_search_criteria($default) {
    if (isset($_GET['artistid']) || isset($_GET['albumid']) || isset($_GET['trackid'])) {
        if (isset($_GET['artistid'])) {
            $query = "SELECT artistname FROM artist WHERE artistid = {$_GET['artistid']}";
        } else if (isset($_GET['albumid'])) {
            $query = "SELECT artistname FROM album NATURAL JOIN artist WHERE albumid = {$_GET['albumid']}";
        } else if (isset($_GET['trackid'])) {
            $query = "SELECT artistname FROM track NATURAL JOIN artist WHERE trackid = {$_GET['trackid']}";
        }
        
        global $db_hostname, $db_username, $db_password, $db_database;
        if (!($db_server = mysqli_connect($db_hostname, $db_username, $db_password, $db_database))) {
            $band = $default;
            mysqli_close($db_server);
        } else {
            $result = mysqli_query($db_server, $query);
            $banddetails = mysqli_fetch_assoc($result);
            $band = $banddetails['artistname'];
            mysqli_close($db_server);
        }
    } else {
        $band = $default;
    }
    return $band;
}

;


$searching = get_search_criteria("Alternative Rock");



$flickrXML = flickr_uri("flickr.photos.search", array("text=" . rawurlencode(str_replace(" ", "%20", $searching)), "sort=relevance"));
if(!$flickrXML) {
    echo "error!";
} else {
echo "<div id='flickrwidget'><img src='Widgets/flickr/flickrlogo.png' />" . get_flickr_photos($flickrXML) . "</div>";
}
?>
