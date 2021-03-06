<!--Name: header.php
    Author: Andrew Brown
    Date: 03/02/2013
    Description:
    This is the header and navigation bar for the site. These elements have 
    been put into their own file so that the html doesn't have to be copy and
    pasted into the 'main' pages. That's a sure way to introduce little 
    presentation errors into the website. NOTE: This should only ever be 
    'included/required'. It is not intended to be viewed on it's own. -Andrew-->   
<header>
    <img src="./Images/logo.png">
</header>
<div class="mainnav"> <!-- start of Navbar -->
    <ul id="list-nav">
        <li><a href="index.php">Home</a></li>
        <li><a href="bandlist.php">Bands</a></li>
        <li><a href="albumlist.php">Albums</a></li>
        <li><a href="tracklist.php">Tracks</a></li>
        <li><a href="userlist.php">Users</a></li>
        <li><a href="rssfeed.php">RSS</a></li>
        <li><a href="registration.php">Register</a></li>
    </ul>
</div> <!-- End of Navbar --> 
<?php
echo "<div class=login> <!-- start of login -->\n";
echo "<form class='login' action='login.php' method='post'>\n";
echo "<table class='login'>\n";
echo "<tr>\n";
if (isset($_COOKIE["userid"])) { //if user is logged on, display their details.
    echo "<td><img src='{$_COOKIE['useravataruri']}' height='25' width='25'/></td>";
    echo "<td>Hi, {$_COOKIE['username']}!</td>";
    echo "<td><input type='submit' name='logout' value='logout' /></td>";
} else { //Otherwise, show the login bar, and let them try to login.
    echo "<td><input type='text' id='Name' name='username' placeholder='Username' size='16'/></td>\n";
    echo "<td><input type='password' id='Password' name='password' placeholder='Password' size='16'/></td>\n";
    echo "<td><input type='submit' name='login' value='login' /></td>\n";
}
echo "</tr>\n";
echo "</table>\n";
echo "</form>\n";
echo "</div>  <!-- End of login -->\n";
?>