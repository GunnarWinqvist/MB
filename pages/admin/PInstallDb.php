<?php

///////////////////////////////////////////////////////////////////////////////////////////////////
//
// InstallDb.php
// Anropas med 'install_db' från index.php.
// Initierar databasen, skapar alla tabeller och fyller den med nödvändig startinformation.
// Endast användare som hör till grupp adm har tillgång till sidan.
//
// Första gången man initerar databasen och således inte kan vara inloggad sätter man 
// kommentarsstreck (//) framför inloggningskraven nedan. Adressera sedan sidan direkt med 
// svenskaskolankualalumpur.com/?p=install_db.
// Vid första initieringen av databasen måste även en uranvändare initieras. Det görs genom att ta 
// bort kommentarsstrecken vid rad 136 och 137 nedan. 
// Efter det kan man logga in som Admin (password admin). Ändra lösenordet omedelbart efter inloggning 
// och glöm inte att spara filen igen när du har återställt kommentarmarkeringarna enligt ovan.
// 
// Om du gör ändringar i databasstrukturen så glöm inte att motsvarande ändringar också måste göras 
// i PFillDb.php.


///////////////////////////////////////////////////////////////////////////////////////////////////
// Kolla behörighet med mera.
//
$intFilter = new CAccessControl();
$intFilter->FrontControllerIsVisitedOrDie();
$intFilter->UserIsSignedInOrRedirectToSignIn(); // Kommentera bort med // första databasinitieringen.
$intFilter->UserIsAuthorisedOrDie('adm');       // Kommentera bort med // första databasinitieringen.


///////////////////////////////////////////////////////////////////////////////////////////////////
// Förbered och genomför en SQL query för att skapa tabeller etc i databasen 'forum'.
//
$dbAccess       = new CdbAccess();
$tableUser      = DB_PREFIX . 'User';
$tableChild     = DB_PREFIX . 'Child';
$tableBook      = DB_PREFIX . 'Book';
$tablePage      = DB_PREFIX . 'Page';
$tableField     = DB_PREFIX . 'Field';
$tableRelation  = DB_PREFIX . 'Relation';

$totalStatements = 13; //Måste uppdateras manuellt om antalet statements ändras.
$query = <<<QUERY

-- Tag bort tabellerna om de redan finns.
DROP TABLE IF EXISTS {$tableRelation};
DROP TABLE IF EXISTS {$tableField};
DROP TABLE IF EXISTS {$tablePage};
DROP TABLE IF EXISTS {$tableBook};
DROP TABLE IF EXISTS {$tableChild};
DROP TABLE IF EXISTS {$tableUser};



-- Table for User.
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
CREATE TABLE {$tableChild} (
  idChild INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
  firstNameChild CHAR(50),
  familyNameChild CHAR(50),
  birthDateChild CHAR(8),
  child_idUser INT,
  FOREIGN KEY (child_idUser) REFERENCES {$tableUser}(idUser)
);

-- Table for Book.
CREATE TABLE {$tableBook} (
  idBook INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
  nameBook CHAR(50),
  firstPageBook INT,
  book_idChild INT,
  FOREIGN KEY (book_idChild) REFERENCES {$tableChild}(idChild)
);

-- Table for page.
CREATE TABLE {$tablePage} (
  idPage INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
  headerPage CHAR(50),
  stylePage INT,
  nextPage INT,
  page_idBook INT,
  FOREIGN KEY (page_idBook) REFERENCES {$tableBook}(idBook)
);

-- Table for field.
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

-- Table for relation guest and user.
CREATE TABLE {$tableRelation} (
  relation_idGuest INT NOT NULL,
  relation_idUser INT NOT NULL,
  FOREIGN KEY (relation_idGuest) REFERENCES {$tableUser}(idUser),
  FOREIGN KEY (relation_idUser) REFERENCES {$tableUser}(idUser),
  PRIMARY KEY (relation_idGuest, relation_idUser)
);



-- Lägg till administratör för att kunna administrera databasen första gången den installeras.
-- Första gången måste kommentarsstrecken på de två raderna som börjar med INSERT och VALUES nedan tas bort.
-- Password måste ändras direkt för att ingen ska kunna kapa databasen.
//INSERT INTO {$tableUser} (accountUser, passwordUser, authorityUser)
//VALUES ('admin', md5('admin'), 'adm');


QUERY;

// In med alltihop i databasen med en multiquery.
$statements = $dbAccess->MultiQueryNoResultSet($query);
if ($debugEnable) $debug .= "{$statements} statements av {$totalStatements} kördes.<br /> \n"; 


///////////////////////////////////////////////////////////////////////////////////////////////////
//
// Bygg upp sidan
//
$page = new CHTMLPage(); 
$pageTitle = "Installera databas";

$mainTextHTML = <<<HTMLCode
<p>Databasen har initierats med följande query:</p>
<code>{$query}</code>
<p>{$statements} statements av {$totalStatements} kördes.</p>
HTMLCode;

require(TP_PAGESPATH.'rightColumn.php'); // Genererar en högerkolumn i $rightColumnHTML
$page->printPage($pageTitle, $mainTextHTML, "", $rightColumnHTML);

?>

