<?php

///////////////////////////////////////////////////////////////////////////////////////////////////
//
// InstallDb.php
// Anropas med 'install_db' fr�n index.php.
// Initierar databasen, skapar alla tabeller och fyller den med n�dv�ndig startinformation.
// Endast anv�ndare som h�r till grupp adm har tillg�ng till sidan.
//
// F�rsta g�ngen man initerar databasen och s�ledes inte kan vara inloggad s�tter man 
// kommentarsstreck (//) framf�r inloggningskraven nedan. Adressera sedan sidan direkt med 
// svenskaskolankualalumpur.com/?p=install_db.
// Vid f�rsta initieringen av databasen m�ste �ven en uranv�ndare initieras. Det g�rs genom att ta 
// bort kommentarsstrecken vid rad 136 och 137 nedan. 
// Efter det kan man logga in som Admin (password admin). �ndra l�senordet omedelbart efter inloggning 
// och gl�m inte att spara filen igen n�r du har �terst�llt kommentarmarkeringarna enligt ovan.
// 
// Om du g�r �ndringar i databasstrukturen s� gl�m inte att motsvarande �ndringar ocks� m�ste g�ras 
// i PFillDb.php.


///////////////////////////////////////////////////////////////////////////////////////////////////
// Kolla beh�righet med mera.
//
$intFilter = new CAccessControl();
$intFilter->FrontControllerIsVisitedOrDie();
$intFilter->UserIsSignedInOrRedirectToSignIn(); // Kommentera bort med // f�rsta databasinitieringen.
$intFilter->UserIsAuthorisedOrDie('adm');       // Kommentera bort med // f�rsta databasinitieringen.


///////////////////////////////////////////////////////////////////////////////////////////////////
// F�rbered och genomf�r en SQL query f�r att skapa tabeller etc i databasen 'forum'.
//
$dbAccess       = new CdbAccess();
$tableUser      = DB_PREFIX . 'User';
$tableChild     = DB_PREFIX . 'Child';
$tableBook      = DB_PREFIX . 'Book';
$tablePage      = DB_PREFIX . 'Page';
$tableField     = DB_PREFIX . 'Field';
$tableRelation  = DB_PREFIX . 'Relation';

$totalStatements = 13; //M�ste uppdateras manuellt om antalet statements �ndras.
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



-- L�gg till administrat�r f�r att kunna administrera databasen f�rsta g�ngen den installeras.
-- F�rsta g�ngen m�ste kommentarsstrecken p� de tv� raderna som b�rjar med INSERT och VALUES nedan tas bort.
-- Password m�ste �ndras direkt f�r att ingen ska kunna kapa databasen.
//INSERT INTO {$tableUser} (accountUser, passwordUser, authorityUser)
//VALUES ('admin', md5('admin'), 'adm');


QUERY;

// In med alltihop i databasen med en multiquery.
$statements = $dbAccess->MultiQueryNoResultSet($query);
if ($debugEnable) $debug .= "{$statements} statements av {$totalStatements} k�rdes.<br /> \n"; 


///////////////////////////////////////////////////////////////////////////////////////////////////
//
// Bygg upp sidan
//
$page = new CHTMLPage(); 
$pageTitle = "Installera databas";

$mainTextHTML = <<<HTMLCode
<p>Databasen har initierats med f�ljande query:</p>
<code>{$query}</code>
<p>{$statements} statements av {$totalStatements} k�rdes.</p>
HTMLCode;

require(TP_PAGESPATH.'rightColumn.php'); // Genererar en h�gerkolumn i $rightColumnHTML
$page->printPage($pageTitle, $mainTextHTML, "", $rightColumnHTML);

?>

