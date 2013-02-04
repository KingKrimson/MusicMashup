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
        <?php include_once 'header.html';?>
        <div class="clear"></div>
        
        <div class="clear"></div>
        <?php include_once 'widgetpane.php'; ?>
        <?php include_once 'footer.html'; ?>    
    </body>


</html>