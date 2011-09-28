<?php

///////////////////////////////////////////////////////////////////////////////////////////////////
//
// PShowPage.php
// Called by 'page' from index.php.
// This is the generik page in Min Bok that builds from the database. 
// Input: 'idPage' 
// Output: 
// 


///////////////////////////////////////////////////////////////////////////////////////////////////
// Check that the page is reached from the front controller and authority etc.

$intFilter = new CAccessControl();
$intFilter->FrontControllerIsVisitedOrDie();
$intFilter->UserIsSignedInOrRedirectToSignIn();


///////////////////////////////////////////////////////////////////////////////////////////////////
// Take care of input.
$idPage  = isset($_GET['idPage'])  ? $_GET['idPage']  : NULL;


///////////////////////////////////////////////////////////////////////////////////////////////////
// Prepare database.

$dbAccess           = new CdbAccess();
$tableChild         = DB_PREFIX . 'Child';
$tableBook          = DB_PREFIX . 'Book';
$tablePage          = DB_PREFIX . 'Page';
$tableField         = DB_PREFIX . 'Field';
$idPage 		    = $dbAccess->WashParameter($idPage);


///////////////////////////////////////////////////////////////////////////////////////////////////
// Sheck if session id is approved to see the page and edit the page.





///////////////////////////////////////////////////////////////////////////////////////////////////
// Get info about this page.

$query      = "SELECT * FROM {$tablePage} WHERE idPage = {$idPage};";
$result     = $dbAccess->SingleQuery($query);
$row        = $result->fetch_object();
$style      = $row->stylePage;
$header     = $row->headerPage;
$result->close();


///////////////////////////////////////////////////////////////////////////////////////////////////
// Fetch data about the page.

$query      = "SELECT * FROM {$tableField} WHERE field_idPage = {$idPage};";

if ($result = $dbAccess->SingleQuery($query)) {
    while ($row = $result->fetch_object()) {
        switch($row->typeField) {
        
            case '1': // Text field
                $mainTextHTML = <<<HTMLCode
<div class='textField'>
    {$row->parameter1Field}
</div>
HTMLCode;
                break;
            
            case '2': // Image field
                $mainTextHTML = <<<HTMLCode
<div class='imageField'>
    <img src='{$row->parameter1Field}' alt='{$row->parameter2Field}' />
</div>
HTMLCode;
            
                break;
            default:
            ;
        }

    $result->close();
    }
} else {
    $mainTextHTML = <<<HTMLCode
<p>Det finns inget inlagt på sidan än.</p>
HTMLCode;
}







///////////////////////////////////////////////////////////////////////////////////////////////////
// Generate the page.

$page = new CHTMLPage($style); 

$page->printPage($header, $mainTextHTML, "", "");

?>

