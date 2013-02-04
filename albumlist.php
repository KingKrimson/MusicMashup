<!-- Name: albumlist.php
     Author: Andrew Brown
     Date: 03/02/2013
     Description: 
     On first viewing, this lists all of the albums in the database, along 
     with some basic information about them, such as their artist, cover, and
     release date. If you click on an album, you'll get more information about 
     it, such as tracks on the database, and a description of the album.     -->
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" type="text/css" href="./CSS/alternews.css">
        <title>Alternews - Home</title>
    </head>
    <body>
        <?php include_once 'header.html'; ?>
        <div class="pagecontent"></div>
        <?php include_once 'widgetpane.php'; ?>
        <?php include_once 'footer.html'; ?>    
    </body>

</html>