<?php

///////////////////////////////////////////////////////////////////////////////////////////////////
//
// PDumpDB.php
// Called by 'dump_db' from index.php.
// This page dumps the database on a file named $fileName for back up purposes.
$fileName   = "DB_dump.txt";
// The values are separated with $delimiter. 
$delimiter      = "¤";
// If $delimiter exists in the text it is substituted with $substitution.
$substitution   = "*";
// Each header starts with $headerSignature.
$headerSignature = "-*-";


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
$fh = fopen($filePath, "w");


///////////////////////////////////////////////////////////////////////////////////////////////////
// Prepare an array of headers and querys.

$querys = array( 
    array( "\r\n".$headerSignature."tableUser\r\n",      "SELECT * FROM {$tableUser};"),
    array( "\r\n".$headerSignature."tableChild\r\n",     "SELECT * FROM {$tableChild};"),
    array( "\r\n".$headerSignature."tableBook\r\n",      "SELECT * FROM {$tableBook};"),
    array( "\r\n".$headerSignature."tablePage\r\n",      "SELECT * FROM {$tablePage};"),
    array( "\r\n".$headerSignature."tableField\r\n",     "SELECT * FROM {$tableField};"),
    array( "\r\n".$headerSignature."tableRelation\r\n",  "SELECT * FROM {$tableRelation};")
    );


///////////////////////////////////////////////////////////////////////////////////////////////////
// Query everything from each table and write it to the file with the right header.

foreach ($querys as $set) {

    // Do this once for every table.
    list($header, $query) = $set;
    
    // Write the header.
    fwrite($fh, $header);
    
    // Query and if it gives a result write every row to the file.
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

// Close the file.
fclose($fh);


///////////////////////////////////////////////////////////////////////////////////////////////////
// Build the page.

$pageTitle = "Dumpa databasen";
$documents = WS_SITELINK . "documents/" . $fileName;
$mainTextHTML = <<<HTMLCode
<p>Gjorde en lyckad dump av databasen till filen: {$fileName}.
<p>Vill du ladda ner filen?</p>
<a title='Hämta dumpfil' href='{$documents}' ><img src='../images/b_enter.gif' alt='Hämta dumpfil' /></a>
HTMLCode;

$page = new CHTMLPage(); 
$page->printPage($pageTitle, $mainTextHTML);

?>

