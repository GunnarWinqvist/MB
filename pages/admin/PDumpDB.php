<?php

///////////////////////////////////////////////////////////////////////////////////////////////////
//
// PDumpDB.php
// Called by 'dump_db' from index.php.
// This page dumps the database on a file. 
// Input: 
// Output: 
// 


///////////////////////////////////////////////////////////////////////////////////////////////////
// Check authority etc.

$intFilter = new CAccessControl();
$intFilter->FrontControllerIsVisitedOrDie();
$intFilter->UserIsSignedInOrRedirectToSignIn();
$intFilter->UserIsAuthorisedOrDie('adm');         


///////////////////////////////////////////////////////////////////////////////////////////////////
// Prepare the database
//
$dbAccess       = new CdbAccess();
$tableUser      = DB_PREFIX . 'User';
$tableChild     = DB_PREFIX . 'Child';
$tableBook      = DB_PREFIX . 'Book';
$tablePage      = DB_PREFIX . 'Page';
$tableField     = DB_PREFIX . 'Field';
$tableRelation  = DB_PREFIX . 'Relation';
$delimiter      = "¤";
$substitution   = "*";

// Open the file.
$dumpFileName = TP_DOCUMENTSPATH . "DB_dump.txt";
$fh = fopen($dumpFileName, "w");

$querys = array( 
    array( "\r\n-*-tableUser\r\n",     "SELECT * FROM {$tableUser};"),
    array( "\r\n-*-tableChild\r\n",     "SELECT * FROM {$tableChild};"),
    array( "\r\n-*-tableBook\r\n", "SELECT * FROM {$tableBook};"),
    array( "\r\n-*-tablePage\r\n",    "SELECT * FROM {$tablePage};"),
    array( "\r\n-*-tableField\r\n",       "SELECT * FROM {$tableField};"),
    array( "\r\n-*-tableRelation\r\n",   "SELECT * FROM {$tableRelation};")
    );
    
foreach ($querys as $set) {
    list($header, $query) = $set;
    fwrite($fh, $header); // Write the header.
    if ($result = $dbAccess->SingleQuery($query)) {
        while($row = $result->fetch_row()) {
            if ($debugEnable) $debug .= "Query result: ".print_r($row, TRUE)."<br /> \n";
            for($i=0; $i<count($row);$i++) {
                // If $delimiter exists in the text change it with $substitution.
                $row[$i] = str_replace($delimiter, $substitution, $row[$i]); 
            }
            fwrite($fh, implode($delimiter, $row)."\r\n");
        }
        $result->close();
    }
}

fclose($fh);

$documents = WS_SITELINK . "documents/DB_dump.txt";
$mainTextHTML = <<<HTMLCode
<p>Gjorde en lyckad dump av databasen till filen: {$dumpFileName}.
<p>Vill du ladda ner filen?</p>
<a title='Hämta dumpfil' href='{$documents}' ><img src='../images/b_enter.gif' alt='Hämta dumpfil' /></a>

HTMLCode;

///////////////////////////////////////////////////////////////////////////////////////////////////
//
// Bygg upp sidan
//
$page = new CHTMLPage(); 
$pageTitle = "Dumpa databasen";

require(TP_PAGESPATH.'rightColumn.php'); 
$page->printPage($pageTitle, $mainTextHTML, "", $rightColumnHTML);

?>

