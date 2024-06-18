<?php

require_once '../app/config/Database.php';

$database = new \config\Database();

// Create database
$database->createDatabase();

// Create tables
$database->createUsersTable();

$database->createFollowersTable();

$database->createBooksTable();

$database->createDefaultBooks();

$database->createReviewsTable();

$database->createListsTable();

$database->createBooksInListsTable();

$database->createUserFollowListsTable();

$database->createUserBooksTable();
 
$database->createGenresTable();
 
$database->insertDefaultGenres();

$database->createReviews();

// Create default admin user
$database->createDefaultAdminUser();

// Extra users
$database->createExtraUsers();

// Default lists
$database->insertDefaultLists();

// Extra follows
$database->createDefaultListFollows();

// Books in lists
$database->createBooksInLists();

$database->createUserFollows();

$database->closeConnection();

?>
