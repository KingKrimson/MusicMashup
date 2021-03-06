<!-- Name: widgetpane.php
     Author: Andrew Brown
     Date: 03/02/2012
     A container for the group's individual widgets. As with 'header.html' and 
     'footer.html', this page is not intended to be viewed on it's own: it's 
     meant to be included in all of the 'main' pages of the website, so every 
     page has the widgets on them. 'widgetpane.html' is extensible, as all 
     you need to do to add a new widget is 'include/require' it within the 
     'div' tag. -Andrew                                                      -->
     
     <div id="widgetpane">
         <h1>Widgets</h1>
         <h2>Twitter Feed</h2>
         <?php require_once("./Widgets/andrewstwitterwidget.php");?>
         <h2>Recent Facebook Activity</h2>
         <?php require_once("./Widgets/facebookwidget.htm");?>
         <h2>Track Lyrics</h2>
         <?php require_once("./Widgets/LyricsWidget.php");?>
         <h2>Flickr Band Pictures</h2>
         <?php require_once("./Widgets/flickr/flickrsearch3.php");?>
     </div>