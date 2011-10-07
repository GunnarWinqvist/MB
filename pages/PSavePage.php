<?php

///////////////////////////////////////////////////////////////////////////////////////////////////
//
// PSavePage.php (save_page)
// 
// The page saves a new page from create_page.  
// You are redirected to 'redirect'.
//
// Input: 'header', 'style', 'framework', 'idPage', 'redirect' as post.
// Output: 'id'
// 


///////////////////////////////////////////////////////////////////////////////////////////////////
// Check that the page is opened via index.php and that the user has the right authority.

$intFilter = new CAccessControl();
$intFilter->FrontControllerIsVisitedOrDie();
$intFilter->UserIsSignedInOrRedirectToSignIn();


///////////////////////////////////////////////////////////////////////////////////////////////////
// Prepare the database.

$dbAccess             = new CdbAccess();
$tableBook            = DB_PREFIX . 'Book';
$tableChild           = DB_PREFIX . 'Child';
$tablePage            = DB_PREFIX . 'Page';
$tableField           = DB_PREFIX . 'Field';


///////////////////////////////////////////////////////////////////////////////////////////////////
// Get input for the book and clean it.

$header     = isset($_POST['header'])   ? $_POST['header']   : NULL;
$idPage     = isset($_POST['idPage'])   ? $_POST['idPage']   : NULL;
$style      = isset($_POST['style'])    ? $_POST['style']    : NULL;
$framework  = isset($_POST['framework'])? $_POST['framework']: NULL;
$redirect   = isset($_POST['redirect']) ? $_POST['redirect'] : NULL;

$header 	= $dbAccess->WashParameter(strip_tags($header));
$idPage 	= $dbAccess->WashParameter($idPage);
$style 	    = $dbAccess->WashParameter($style);
$framework 	= $dbAccess->WashParameter($framework);
$idUser     = $_SESSION['idUser'];


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

/*
///////////////////////////////////////////////////////////////////////////////////////////////////
// Edit an existing book if idBook is set.

if ($idBook) { 

// Check if the session id is NOT owner of the book. If so give a message and exit.
    $query = <<<QUERY
SELECT * FROM
({$tableBook} JOIN {$tableChild} ON book_idChild = idChild)
WHERE idBook = {$idBook}
AND child_idUser = {$idUser};
QUERY;
    if (!$dbAccess->SingleQuery($query)) {
        $_SESSION['errorMessage']      = "Det är inte din bok. Du kan inte ändra i den.";
        header('Location: ' . WS_SITELINK . "?p=main");
        exit;
    }
    
    // Query for updating the book.
    $query = <<<QUERY
UPDATE {$tableBook}
    SET nameBook = '{$nameBook}'
    WHERE idBook = '{$idBook}';
QUERY;
    $dbAccess->SingleQuery($query);
*/


///////////////////////////////////////////////////////////////////////////////////////////////////
// Otherwise add a new page.

    // First we need to know which book the page is in.
    $query = "SELECT page_idBook FROM {$tablePage} WHERE idPage = {$idPage}";
    $result = $dbAccess->SingleQuery($query);
    $row = $result->fetch_object();
    $idBook = $row->page_idBook; 
    $result->close();

    // Then we add a new page with the input from create_page to this book.
    $query = <<<QUERY
INSERT INTO {$tablePage} (headerPage, stylePage, frameworkPage, page_idBook)
    VALUES ('{$header}', '{$style}', '{$framework}', '{$idBook}');
QUERY;
    $dbAccess->SingleQuery($query);
    
    // Check the id of the new page.
    $newPageId = $dbAccess->LastId(); 
    if ($debugEnable) $debug.="idBook=".$idBook." newPageId=".$newPageId."<br /> \n";
 
    // And last update the nextPage on the previous page.
    $query = <<<QUERY
UPDATE {$tablePage} 
    SET nextPage = '{$newPageId}'
    WHERE idPage = '{$idPage}';
QUERY;
    $dbAccess->SingleQuery($query);
    


 
///////////////////////////////////////////////////////////////////////////////////////////////////
// Redirect 

// If in debug mode exit before redirect.
if ($debugEnable) {
    echo $debug;
    exit();
}

header("Location: " . WS_SITELINK . "?p=" . $redirect);
exit;


?>

