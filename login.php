<?php
/* Name: login.php
 * Authour: Andrew Brown
 * Date: 10/02/2013
 * Description:
 * This file implements login/off functionality in the website. When a user tries
 * to login using the form in the header, certain variables are sent here, namely
 * the username, and the user password. The database is queried to see if the
 * user exists, and if the right password was given. The password is interesting,
 * as it must be hashed and salted before comparing it against the encrypted value
 * in the database; storing plain text passwords in the database is a security risk.
 */

//require once is important here, as logon.php may be included in other files that access the database. 
require_once 'databasevars.php';

$loginstatus = login();
if($loginstatus == -2 || $loginstatus == -1) { //login has failed, alert user.
    echo "<script>alert(\"Whoops! Try again!\")</script>";
}

//header("Location: http://isa.cems.uwe.ac.uk/~ad3-brown/DSA/MusicMashup/index.php");
header("Location: http://localhost/MusicMashup/index.php"); //redirect to the index/
/*
 * handles login and logout for the site. Has a variety of return values:
 * -2: Couldn't access database.
 * -1: User not found, or password incorrect.
 * 1: login successful.
 * 2: logout successful. 
 */
function login() {
    global $db_hostname, $db_username, $db_password, $db_database, $salt1, $salt2;
    if (isset($_POST['login'])) { //login is set, so grab the relevant variables from post.
        $username = $_POST['username'];
        $password = $_POST['password'];
        $password = $salt1.$password.$salt2; //salt the password, so we can check 
                                             //it against the one stored in the database.
        if (!($db_server = mysqli_connect($db_hostname, $db_username, $db_password, $db_database))) {
            return -2;
        } else {
            //try to find a user with the details the current user entered.
            $logonquery = "SELECT userid, username, useravataruri FROM user WHERE "
                    . "username = '$username' AND userpassword = SHA1('$password')";
            $result = mysqli_query($db_server, $logonquery);
            if (!mysqli_num_rows($result)) { //not found.
                return -1;
            } else { //found. Log the user in using cookies.
                $userdetails = mysqli_fetch_assoc($result);
                //set cookies, and their time to live.
                setcookie('userid', $userdetails['userid'], time() + (60 * 60 * 24 * 7));
                setcookie('username', $userdetails['username'], time() + (60 * 60 * 24 * 7));
                setcookie('useravataruri', $userdetails['useravataruri'], time() + (60 * 60 * 24 * 7));
                mysqli_close($db_server);
                return 1; //success!
            }
        }
    }
    if (isset($_POST['logout'])) { // 'logout' button has been clicked.
        //by setting the cookie deletion date to a time in the past, we force 
        //deletion of the cookie.
        setcookie('userid', $userdetails['userid'], time() - (60 * 60 * 24 * 365));
        setcookie('username', '', time() - (60 * 60 * 24 * 365));
        setcookie('useravataruri', '', time() - (60 * 60 * 24 * 365));
        return 2; //logout successful.
    }
    return 0;
}

?>
