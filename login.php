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

function login() {
    global $db_hostname, $db_username, $db_password, $db_database, $salt1, $salt2;
    if (isset($_POST['login'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $password = $salt1.$password.$salt2;
        if (!($db_server = mysqli_connect($db_hostname, $db_username, $db_password, $db_database))) {
            return -2;
        } else {
            $logonquery = "SELECT userid, username, useravataruri FROM user WHERE "
                    . "username = '$username' AND userpassword = SHA1('$password')";
            $result = mysqli_query($db_server, $logonquery);
            if (!mysqli_num_rows($result)) {
                return -1;
            } else {
                $userdetails = mysqli_fetch_assoc($result);
                setcookie('userid', $userdetails['userid'], time() + (60 * 60 * 24 * 7));
                setcookie('username', $userdetails['username'], time() + (60 * 60 * 24 * 7));
                setcookie('useravataruri', $userdetails['useravataruri'], time() + (60 * 60 * 24 * 7));
                mysqli_close($db_server);
                return 1;
            }
        }
    }
    if (isset($_POST['logout'])) {
        setcookie('username', '', time() - (60 * 60 * 24 * 30));
        setcookie('useravataruri', '', time() - (60 * 60 * 24 * 30));
        return 2;
    }
    return 0;
}

?>
