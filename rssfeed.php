<?php
/*******************************************************************************
* Name: rssfeed.php
* Author: Andrew Brown
* Date: 03/02/2013
* Description: 
* The RSS feed for the website. It display the 20 most recently favourited items
*(album, track, artist) in RSS 2.0 format. After processing, the file is simply
* XML; there's no html here.  
********************************************************************************/

include 'databasevars.php';

if (!$db_server = mysqli_connect($db_hostname, $db_username, $db_password, $db_database)) {
    echo "couldn't connect to database: " . mysqli_error($db_server);
} else { //get the most recently favourited items (artist, album, track). Sort 
         //by most recent, so we know that they're already in some kind of order. 
         //Limit each item to 20, as we know we won't need more than that.
         //
    $artistquery = "SELECT artistdatetimefavourited, username, userid, artistname, artistid FROM favouriteartists " .
            "NATURAL JOIN user NATURAL JOIN artist ORDER BY artistdatetimefavourited DESC LIMIT 20";
    $albumquery = "SELECT albumdatetimefavourited, username, userid, albumname, albumid FROM favouritealbums " .
            "NATURAL JOIN user NATURAL JOIN album ORDER BY albumdatetimefavourited DESC LIMIT 20";
    $trackquery = "SELECT trackdatetimefavourited, username, userid, trackname, trackid FROM favouritetracks " .
            "NATURAL JOIN user NATURAL JOIN track ORDER BY trackdatetimefavourited DESC LIMIT 20";

    $artistresults = mysqli_query($db_server, $artistquery);
    $albumresults = mysqli_query($db_server, $albumquery);
    $trackresults = mysqli_query($db_server, $trackquery);
    
    //fetch first lot of item result, sorted by most recent
    $artistdetails = mysqli_fetch_assoc($artistresults);
    $albumdetails = mysqli_fetch_assoc($albumresults);
    $trackdetails = mysqli_fetch_assoc($trackresults);

    echo "<?xml version=\"1.0\"?>\n";
    echo "<rss version=\"2.0\">\n";
    echo "    <channel>\n";
    echo "        <title>Alternews</title>\n";
    echo "        <link>http://www.cems.uwe.ac.uk/~ad3-brown/DSA/MusicMashup</link>\n";
    echo "        <description>Recently favourited items.</description>\n";
    echo "        <category>Music</category>\n";
    echo "        <language>en</language>\n";
    echo "        <managingEditor>andrew@brownfamilymail.co.uk</managingEditor>\n";
    echo "        <webMaster>andrew@brownfamilymail.co.uk</webMaster>\n";


    for ($i = 0; $i < 20; ++$i) {
        $item = newestItem($artistdetails, $albumdetails, $trackdetails);
        if(isset($item['artistid'])) {  //we have to check which item was returned.
            $type = "band";
            $itemname = $item['artistname'];
            $itemid = $item['artistid'];
            $time = $item['artistdatetimefavourited'];
            //fetch the next artist favourite, as this one is being written to
            //the feed; we don't want to use it again. 
            $artistdetails = mysqli_fetch_assoc($artistresults);
        } else if(isset($item['albumid'])) {
            $type = "album";
            $itemname = $item['albumname'];
            $itemid = $item['albumid'];
            $time = $item['albumdatetimefavourited'];
            $albumdetails = mysqli_fetch_assoc($albumresults);
        } else { //must be track that's been returned.
            $type = "track";
            $itemname = $item['trackname'];
            $itemid = $item['trackid'];
            $time = $item['trackdatetimefavourited'];
            $trackdetails = mysqli_fetch_assoc($trackresults);
        }
        $username = $item['username'];
        $userid = $item['userid'];
        
        //output the 'item'.
        echo "        <item>\n";
        echo "            <title>" . $username . " favourited " . $itemname . "</title>\n";
        //output the information about the item, using hyperlinks to the item and user.
        echo "            <link>http://www.cems.uwe.ac.uk/~ad3-brown/DSA/MusicMashup/{$type}list.php?{$typeid}={$itemid}</link>\n";
        echo "            <description>&lt;a href=\"http://www.cems.uwe.ac.uk/~ad3-brown/DSA/MusicMashup/userlist.php?userid={$userid}\"&gt;" . 
                          "$username&lt;/a&gt; favourited the $type " .
                          "&lt;a href=\"http://www.cems.uwe.ac.uk/~ad3-brown/DSA/MusicMashup/{$type}list.php?{$typeid}={$itemid}\"&gt;" . 
                          "$itemname&lt;/a&gt; at $time</description>\n";
        echo "            <pubDate>" . $time . "</pubDate>\n";
        echo "        </item>\n";
    }
    echo "    </channel>\n";
    echo "</rss>\n";
}

/*
 * this function takes $artist, $album, and $track as parameters, and returns
 * the 'newest' one. We can then write that item to the feed.
 */
function newestItem($artist, $album, $track) {
    if ($artist['artistdatetimefavourited'] > $album['albumdatetimefavourited']) {
        if ($artist['artistdatetimefavourited'] > $track['trackdatetimefavourited']) {
            return $artist;
        } else {
            return $track;
        }
    } else if($album['albumdatetimefavourited'] > $track['trackdatetimefavourited']) {
        return $album;
    } else {
        return $track;
    }
}
?>