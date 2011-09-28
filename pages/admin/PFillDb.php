<?php

///////////////////////////////////////////////////////////////////////////////////////////////////
//
// FillDb.php
// Called by 'fill_db' from index.php.
// Fills the database with information from the file DB_dump.txt.
// 
// Input: 
// Output: 
//


///////////////////////////////////////////////////////////////////////////////////////////////////
// Check that the page is reached from the front controller and authority etc.

$intFilter = new CAccessControl();
$intFilter->FrontControllerIsVisitedOrDie();
$intFilter->UserIsSignedInOrRedirectToSignIn();
$intFilter->UserIsAuthorisedOrDie('adm');         


///////////////////////////////////////////////////////////////////////////////////////////////////
// Prepare the database

$dbAccess       = new CdbAccess();
$tableUser      = DB_PREFIX . 'User';
$tableChild     = DB_PREFIX . 'Child';
$tableBook      = DB_PREFIX . 'Book';
$tablePage      = DB_PREFIX . 'Page';
$tableField     = DB_PREFIX . 'Field';
$tableRelation  = DB_PREFIX . 'Relation';
$delimiter      = "¤";

// Open the file. 
$dumpFileName = TP_DOCUMENTSPATH . "DB_dump.txt";
$fh = fopen($dumpFileName, "rt");
if ($debugEnable) $debug .= "dumpFileName = ".$dumpFileName." fh=".$fh."<br /> \n";
$mainTextHTML = "<p>Databasen har från filen ".$dumpFileName." fyllts med följande information:<p><br /> \n";

do {
    // Find a header. Starts with '-*-'.
    $header = "";
    do {
        $row = fgets($fh);
        if ($debugEnable) $debug .= "row = ".$row."<br /> \n";
        if (preg_match("/-*-/", $row)) $header = trim(trim($row, "-*"));
    } while( !feof($fh) && !$header );
    if ($debugEnable) $debug .= "header = ".$header."<br /> \n";

    //Write row by row to the DB untill an empty row or an eof.
    $i = 0;
    while (!feof($fh) && $row = trim(fgets($fh))) { 
        $row = explode($delimiter, $row);
        for($i=0; $i<count($row);$i++) $row[$i] = $dbAccess->WashParameter($row[$i]);
        if ($debugEnable) $debug .= "row = ".print_r($row, TRUE)."<br /> \n";
    
        // Different querys depending on the header.
        switch ($header) { 
            case 'tableUser':
                $query = <<<Query
INSERT INTO {$tableUser} (idUser, accountUser, passwordUser, authorityUser, firstNameUser, 
    familyNameUser, email1User, email2User)
VALUES ('{$row[0]}', '{$row[1]}', '{$row[2]}', '{$row[3]}', '{$row[4]}', '{$row[5]}', '{$row[6]}', '{$row[7]}');
Query;
            break;
            case 'tableChild':
                $query = <<<Query
INSERT INTO {$tableChild} (idChild, firstNameChild, familyNameChild, birthDateChild, child_idUser)
VALUES ('{$row[0]}', '{$row[1]}', '{$row[2]}', '{$row[3]}', '{$row[4]}');
Query;
            break;
            case 'tableBook':
                $query = <<<Query
INSERT INTO {$tableBook} (idBook, nameBook, firstPageBook, book_idChild)
VALUES ('{$row[0]}', '{$row[1]}', '{$row[2]}', '{$row[3]}');
Query;
            break;
            case 'tablePage':
                $query = <<<Query
INSERT INTO {$tablePage} (idPage, headerPage, stylePage, nextPage, page_idBook)
VALUES ('{$row[0]}', '{$row[1]}', '{$row[2]}', '{$row[3]}', '{$row[4]}');
Query;
            break;
            case 'tableField':
                $query = <<<Query
INSERT INTO {$tableField} (idField, typeField, parameter1Field, parameter2Field, parameter3Field, 
        parameter4Field, parameter5Field, field_idPage)
VALUES ('{$row[0]}', '{$row[1]}', '{$row[2]}', '{$row[3]}', '{$row[4]}', '{$row[5]}', '{$row[6]}', '{$row[7]}');
Query;
            break;
            case 'tableRelation':
                $query = <<<Query
INSERT INTO {$tableRelation} (relation_idGuest, relation_idUser)
VALUES ('{$row[0]}', '{$row[1]}');
Query;
            break;
        }
        $dbAccess->SingleQuery($query);
        $i++;
    }
    $mainTextHTML .= "Tabell ".$header.": ".$i." rader<br /> \n";

} while (!feof($fh));

//Stäng filen
fclose($fh);


///////////////////////////////////////////////////////////////////////////////////////////////////
//
// Bygg upp sidan
//
$page = new CHTMLPage(); 
$pageTitle = "Fyll databasen";

require(TP_PAGESPATH.'rightColumn.php'); 
$page->printPage($pageTitle, $mainTextHTML, "", $rightColumnHTML);

?>
