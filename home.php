<!-- Name: homepage.php
     Author: Andrew Brown
     Date: 03/02/2013
     Description: 
     The homepage for the website. This is really a jumping off point for the 
     rest of the site; there's not much else here otherwise.                 -->

<!DOCTYPE html>
<?php require_once 'login.php'; ?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" type="text/css" href="./CSS/alternews.css">
        <title>Alternews - Home</title>
    </head>
    <body>
        <?php include_once 'header.php';?>
        <div class="clear"></div>
        <div id="pagecontent">
            <h1>Welcome to Alternews!</h1>
            <p>Alternews is your one stop shop for all of your alternate rock
               needs. You can check out the best alt-rock bands, their albums,
               and their hit tracks. Be sure to let us know what your favourite
               bands and tracks are! Alternatively, you can get to know the 
               other users of the site, and meet like minded music fans!</p>
            <p>To get started, click on one of the links above. When you
               want to register, just click on the register page and fill
               in your details!</p>
        </div>    
        <div class="clear"></div>
        <?php include_once 'widgetpane.php'; ?>
        <?php include_once 'footer.html'; ?>    
    </body>


</html>