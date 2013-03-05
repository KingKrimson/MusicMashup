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
 * Normally it's a bad idea to just publish your password and salts to the 
 * internet like this, but as this website/database is only accessable through 
 * the Uni network, it's somewhat acceptable in this case.
 ******************************************************************************/
//database variables, for connecting to the database.
$db_hostname = 'localhost';
$db_database = 'Music_Mashup_Database';
$db_username = 'root';
$db_password = '';

//mysql5
//fet11008259
//fet11008259
//spookyghost

//Salts for passwords. 
$salt1 = "sa1ty";
$salt2 = "ch1p5";
?>
