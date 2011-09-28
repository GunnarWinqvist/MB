<?php

///////////////////////////////////////////////////////////////////////////////////////////////////
//
// InstallDb.php
// Called by 'install_db' from index.php.
// Initiates the database for 'Min bok', creates all tables and fills them with information needed 
// to start the web application.
// See comments in the code for description of the different tables.
//
// Only users with the admin authority can open the page.
//
// The first time you initiates the database and hence can't be logged in you have to put comment 
// marks '//' before the authority checks below. Then address the page direct with 
// www.minbok.se/?p=install_db .
// The first time the database is initiated a first user must also be added. Do that by removing the
// comment signs on two rows at 'Add administrator' below.
// After this you can log in as Admin (password admin). Change the password emediately after login
// and reset the comment marks in this file and save it on the server.
// 
// If you do changes in the database structure don't forget to make the same changes in the file
// PFillDb.php.
//


///////////////////////////////////////////////////////////////////////////////////////////////////
// Check that the page is opened via index.php and that the user has the right authority.

$intFilter = new CAccessControl();
$intFilter->FrontControllerIsVisitedOrDie();
$intFilter->UserIsSignedInOrRedirectToSignIn(); // Comment with '//' at the first database init.
$intFilter->UserIsAuthorisedOrDie('adm');       // Comment with '//' at the first database init.


///////////////////////////////////////////////////////////////////////////////////////////////////
// Prepare the database

$dbAccess       = new CdbAccess();
$tableUser      = DB_PREFIX . 'User';
$tableChild     = DB_PREFIX . 'Child';
$tableBook      = DB_PREFIX . 'Book';
$tablePage      = DB_PREFIX . 'Page';
$tableField     = DB_PREFIX . 'Field';
$tableRelation  = DB_PREFIX . 'Relation';

// $totalStatements must be manually changed if the number of statements are changed. Used for debugging.
$totalStatements = 12;


///////////////////////////////////////////////////////////////////////////////////////////////////
// Following is the multi query that initiates the database for 'Min bok'.

$query = <<<QUERY

-- First remove the tables if they already exists.
-- The order of those statements can't be changed due to foreign key relations.

DROP TABLE IF EXISTS {$tableRelation};
DROP TABLE IF EXISTS {$tableField};
DROP TABLE IF EXISTS {$tablePage};
DROP TABLE IF EXISTS {$tableBook};
DROP TABLE IF EXISTS {$tableChild};
DROP TABLE IF EXISTS {$tableUser};


-- Table for User.
-- authorityUser defines what the user can do on the web site.
--               usr = normal user
--               gst = guest to a normal user
--               adm = administrator

CREATE TABLE {$tableUser} (
  idUser INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
  accountUser CHAR(20) NOT NULL UNIQUE,
  passwordUser CHAR(32) NOT NULL,
  authorityUser CHAR(3) NOT NULL,
  firstNameUser CHAR(50),
  familyNameUser CHAR(50),
  eMail1User CHAR(50),
  eMail2User CHAR(50)
);


-- Table for Child.
-- The foreign key child_idUser connects the child with a user.

CREATE TABLE {$tableChild} (
  idChild INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
  firstNameChild CHAR(50),
  familyNameChild CHAR(50),
  birthDateChild CHAR(8),
  child_idUser INT,
  FOREIGN KEY (child_idUser) REFERENCES {$tableUser}(idUser)
);


-- Table for Book.
-- firstPageBook is a pointer to the first page of the book.
-- The foreign key book_idChild connects the book with a child.

CREATE TABLE {$tableBook} (
  idBook INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
  nameBook CHAR(50),
  firstPageBook INT,
  book_idChild INT,
  FOREIGN KEY (book_idChild) REFERENCES {$tableChild}(idChild)
);


-- Table for page.
-- stylePage defines which layout style to be used on the page.
-- nextPage is a pointer to the following page. In this way a sequence of pages forms a book.
--          If next page is zero it is the last page of the book.
-- The foreign key page_idBook connects the page to a book.

CREATE TABLE {$tablePage} (
  idPage INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
  headerPage CHAR(50),
  stylePage INT,
  nextPage INT,
  page_idBook INT,
  FOREIGN KEY (page_idBook) REFERENCES {$tableBook}(idBook)
);


-- Table for field.
-- typeField defines what kind of field. E g text, picture, ...
-- parameter1Field etc are parameters used for setting up the field.
-- The foreign key field_idPage connects the field to a page.

CREATE TABLE {$tableField} (
  idField INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
  typeField INT,
  parameter1Field INT,
  parameter2Field INT,
  parameter3Field INT,
  parameter4Field INT,
  parameter5Field INT,
  field_idPage INT,
  FOREIGN KEY (field_idPage) REFERENCES {$tablePage}(idPage)
);


-- Table for relating a guest and user.
-- Both foreign keys relation_idGuest and relation_idUser referres to id of a user.
--      if a user is guest it will show in the authorityUser.

CREATE TABLE {$tableRelation} (
  relation_idGuest INT NOT NULL,
  relation_idUser INT NOT NULL,
  FOREIGN KEY (relation_idGuest) REFERENCES {$tableUser}(idUser),
  FOREIGN KEY (relation_idUser) REFERENCES {$tableUser}(idUser),
  PRIMARY KEY (relation_idGuest, relation_idUser)
);


-- Add administrator
-- Add an user named Admin (password admin) to be able to administer the database after it is
-- initialised the first time.
-- The first time you run install_db you have to remove the comment dashes on the two rows that
-- begins with INSERT and VALUES respectively below.
-- When you run this page with the comments below removed, $totalStatements will be one more.

-- INSERT INTO {$tableUser} (accountUser, passwordUser, authorityUser)
-- VALUES ('admin', md5('admin'), 'adm');

QUERY;


///////////////////////////////////////////////////////////////////////////////////////////////////
// Run the query as a multi query.

$statements = $dbAccess->MultiQueryNoResultSet($query);
if ($debugEnable) $debug .= "{$statements} statements of {$totalStatements} were executed.<br /> \n"; 


///////////////////////////////////////////////////////////////////////////////////////////////////
// Build the page.

$pageTitle = "Installera databas";
$mainTextHTML = <<<HTMLCode
<p>Databasen har initierats med följande query:</p>
<code>{$query}</code>
<p>{$statements} statements av {$totalStatements} kördes.</p>
HTMLCode;

$page = new CHTMLPage(); 
require(TP_PAGESPATH.'rightColumn.php'); // Add the right column in $rightColumnHTML
$page->printPage($pageTitle, $mainTextHTML, "", $rightColumnHTML);

?>

