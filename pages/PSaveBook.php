<?php

///////////////////////////////////////////////////////////////////////////////////////////////////
//
// PSaveBook.php
// Called by 'save_book' from index.php.
// The page saves a new or edited book title. If it's a new book a first page is also added. 
// You are redirected to 'redirect'.
// Output: 'nameBook', 'idBook', 'idChild', 'redirect' as POST's.
// Output: 'id'
// 


///////////////////////////////////////////////////////////////////////////////////////////////////
// Check that the page is reached from the front controller and authority etc.

$intFilter = new CAccessControl();
$intFilter->FrontControllerIsVisitedOrDie();
$intFilter->UserIsSignedInOrRedirectToSignIn();


///////////////////////////////////////////////////////////////////////////////////////////////////
// Prepare the database.

$dbAccess             = new CdbAccess();
$tableBook            = DB_PREFIX . 'Book';
$tableChild            = DB_PREFIX . 'Child';
$tablePage            = DB_PREFIX . 'Page';


///////////////////////////////////////////////////////////////////////////////////////////////////
// Get input for the book and clean it.

$nameBook   = isset($_POST['nameBook']) ? $_POST['nameBook']  : NULL;
$idBook     = isset($_POST['idBook'])   ? $_POST['idBook']    : NULL;
$idChild    = isset($_POST['idChild'])  ? $_POST['idChild']   : NULL;
$redirect   = isset($_POST['redirect']) ? $_POST['redirect']  : NULL;


// Clean the input parameters.
$idBook 	= $dbAccess->WashParameter($idBook);
$idChild 	= $dbAccess->WashParameter($idChild);
$nameBook 	= $dbAccess->WashParameter(strip_tags($nameBook));



if ($idBook) { // Edit an existing book

// Check if the session id is owner of the book.
    $query = <<<QUERY
SELECT child_idUser FROM
({$tableBook} JOIN {$tableChild} ON book_idChild = idChild)
WHERE idBook = {$idBook};
QUERY;

} else { // Add a new book.
    $query = <<<QUERY
INSERT INTO {$tableBook} (nameBook, book_idChild)
    VALUES ('{$nameBook}', '{$idChild}');
QUERY;
    $dbAccess->SingleQuery($query);
    $idBook = $dbAccess->LastId(); // Check the id of the new book.
    if ($debugEnable) $debug .= "idBook: " . $idBook . "<br /> \n";
    
    // Add a first page of the new book.
    $query = <<<QUERY
INSERT INTO {$tablePage} (stylePage, page_idBook)
    VALUES ('1', '{$idBook}');
QUERY;
    $dbAccess->SingleQuery($query);

}
    
    
    
 
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

