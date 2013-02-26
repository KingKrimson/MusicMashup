<?php
require_once 'databasevars.php';
if(isset($_POST['gettitles'])) {
    $db_server = mysqli_connect($db_hostname, $db_username, $db_password, $db_database);
    $trackquery = "SELECT trackname FROM track";
    $result = mysqli_query($db_server, $trackquery);
    $len = mysqli_num_rows($result);
    for ($i = 0; $i < $len; ++$i) {
        $trackdetails = mysqli_fetch_assoc($result);
        echo "<p>{$trackdetails['trackname']}</p>";
    }
}
?>
