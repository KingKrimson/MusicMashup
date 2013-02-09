<?php

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
            $logonquery = "SELECT username, useravataruri FROM user WHERE "
                    . "username = '$username' AND userpassword = SHA1('$password')";
            if (!($result = mysqli_query($db_server, $logonquery))) {
                echo mysqli_error($db_server);
                return -1;
            } else {
                $userdetails = mysqli_fetch_assoc($result);
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
