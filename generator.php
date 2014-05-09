<?php
require('vpr_app.php');
//array of folders to search for posterous exports 
$search = array();
//create app instance
$app = new vpr_app();
//scrape data from archive export file
$app->load_from_file("filename","Category to assign to stories");
//scrape data from posterous export directories defined in $search
$app->load_from_directory($seach);
//generate index
$app->create_index();
//generate individual pages
//only needed if loading from posterous files, archive files link back to the archive story
$app->create_pages();
?>