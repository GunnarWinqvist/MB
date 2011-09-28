<?php

///////////////////////////////////////////////////////////////////////////////////////////////////
//
// FillDb.php
// Called by 'fill_db' from index.php.
// Fills the database with information from the file $fileName.
$fileName   = "DB_dump.txt";
// The values must be separated with $delimiter. 
$delimiter      = "¤";
// Each header starts with $headerSignature
$headerSignature = "-*-";
// and the table must end with an empty line.


///////////////////////////////////////////////////////////////////////////////////////////////////
// Check that the page is opened via index.php and that the user has the right authority.

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


///////////////////////////////////////////////////////////////////////////////////////////////////
// Open the file.

$filePath = TP_DOCUMENTSPATH . $fileName;
$fh = fopen($filePath, "rt");
if ($debugEnable) $debug .= "filePath = ".$filePath." fh=".$fh."<br /> \n";


///////////////////////////////////////////////////////////////////////////////////////////////////
// Search the file for information and add it to the database and to the shown page.

// Init mainTextHTML. Information will be added while the database is filled.
$mainTextHTML = "<p>Databasen har från filen ".$filePath." fyllts med följande information:<p><br /> \n";

do {
    // Find a header. Starts with $headerSignature.
    $header = "";
    do {
        $row = fgets($fh);
        if ($debugEnable) $debug .= "row = ".$row."<br /> \n";
        if (preg_match("/".$headerSignature."/", $row)) $header = trim(trim($row, $headerSignature));
    } while( !feof($fh) && !$header ); // Continue untill a header is found or end of file.
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
        // Make the query.
        $dbAccess->SingleQuery($query);
        $i++;
    }
    $mainTextHTML .= "Tabell ".$header.": ".$i." rader<br /> \n";

} while (!feof($fh));

// Close the file.
fclose($fh);


///////////////////////////////////////////////////////////////////////////////////////////////////
// Build the page.

$pageTitle = "Fyll databasen";

$page = new CHTMLPage(); 
require(TP_PAGESPATH.'rightColumn.php'); 
$page->printPage($pageTitle, $mainTextHTML, "", $rightColumnHTML);

?>
