<?php
/*******************************************************************************
 * Filename: databasevars.php
 * Author: Andrew Brown
 * Date: 03/02/2013
 * Description: 
 * These variables are in their own file so they don't have to be declared in 
 * every file that needs to access the database. If you want to access the 
 * database in a particular file, all you need to do is 'require' this file.
 * 
 * Normally it's a bad idea to just publish your password to the internet like 
 * this, but as this website/database is only accessable through the Uni 
 * network, it's somewhat acceptable in this case.
 ******************************************************************************/
$db_hostname = 'localhost';
$db_database = 'music_mashup_database';
$db_username = 'root';
$db_password = ''; 
?>
