<?php

///////////////////////////////////////////////////////////////////////////////////////////////////
//
// PShowPage.php (show_page)
// 
// This is the generik page in 'Min bok' that builds from the database. 
// Input: 'idPage' 
//  
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
$idUser             = $_SESSION['idUser'];

///////////////////////////////////////////////////////////////////////////////////////////////////
// Check if session user is NOT owner of page and NOT adm. If so give message and exit.

$query = <<<Query
SELECT * FROM ((
    {$tablePage} JOIN {$tableBook}  ON page_idBook  = idBook)
                 JOIN {$tableChild} ON book_idChild = idChild)
    WHERE idPage = {$idPage} AND child_idUser = {$idUser};
Query;

if (!$dbAccess->SingleQuery($query) AND ($_SESSION['authorityUser'] != 'adm')) {
    $_SESSION['errorMessage']      = "Du har inte behörighet att titta på den här sidan!";
    header('Location: ' . WS_SITELINK . "?p=main");
    exit;
}


///////////////////////////////////////////////////////////////////////////////////////////////////
// Get info about this page.

$query      = "SELECT * FROM {$tablePage} WHERE idPage = {$idPage};";
$result     = $dbAccess->SingleQuery($query);
$row        = $result->fetch_object();
if ($debugEnable) $debug .= "idPage=".$idPage." header=".$row->headerPage." style=".$row->stylePage
    ." framework=".$row->frameworkPage." nextPage=".$row->nextPage."<br /> \n";
$style      = $row->stylePage;
$framework  = $row->frameworkPage;
$header     = $row->headerPage;
$result->close();


///////////////////////////////////////////////////////////////////////////////////////////////////
// Get data about all fields of the page.

$query      = "SELECT * FROM {$tableField} WHERE field_idPage = {$idPage};";
$column = array("","","","");

if ($result = $dbAccess->SingleQuery($query)) {
    while ($row = $result->fetch_object()) {
        if ($debugEnable) $debug .= "idField=".$row->idField." type=".$row->typeField."<br /> \n";
        switch($row->typeField) {
        
            case 1: // Text field
                $column[$row->parameter4Field] .= <<<HTMLCode
<div class='textField'>
    {$row->textField}
</div>
HTMLCode;
                break;
            
            case 2: // Image field
                $column[$row->parameter4Field] .= <<<HTMLCode
<div class='imageField'>
    <img src='{$row->parameter1Field}' alt='{$row->parameter2Field}' />
</div>
HTMLCode;
            
                break;
            default:
            ;
        }

    }
    $result->close();
} else {
    $column[1] .= <<<HTMLCode
<p>Det finns inget inlagt på sidan än.</p>
HTMLCode;
}

if ($debugEnable) $debug .= print_r($column, TRUE)."<br /> \n";

///////////////////////////////////////////////////////////////////////////////////////////////////
// Add the page menu.

require(TP_PAGESPATH.'bookMenu.php');
$column[1] .= $htmlBookMenu;

///////////////////////////////////////////////////////////////////////////////////////////////////
// Generate the page.

$page = new CHTMLPage($style); 
$page->printPage($header, $column[1], $column[2], $column[3]);


?>

