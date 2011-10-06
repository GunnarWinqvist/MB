<?php

///////////////////////////////////////////////////////////////////////////////////////////////////
//
// PSaveBook.php (save_book)
// 
// The page saves a new or edited book title. If it's a new book a first page is also added. 
// You are redirected to 'redirect'.
//
// Input: 'nameBook', 'idBook', 'idChild', 'redirect' as POST's.
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

$nameBook   = isset($_POST['nameBook']) ? $_POST['nameBook']  : NULL;
$idBook     = isset($_POST['idBook'])   ? $_POST['idBook']    : NULL;
$idChild    = isset($_POST['idChild'])  ? $_POST['idChild']   : NULL;
$redirect   = isset($_POST['redirect']) ? $_POST['redirect']  : NULL;

$idBook 	= $dbAccess->WashParameter($idBook);
$idChild 	= $dbAccess->WashParameter($idChild);
$nameBook 	= $dbAccess->WashParameter(strip_tags($nameBook));
$idUser     = $_SESSION['idUser'];


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

    
///////////////////////////////////////////////////////////////////////////////////////////////////
// Otherwise add a new book.

} else {

    // First we add a new book.
    $query = <<<QUERY
INSERT INTO {$tableBook} (nameBook, book_idChild)
    VALUES ('{$nameBook}', '{$idChild}');
QUERY;
    $dbAccess->SingleQuery($query);
    
    // Check the id of the new book.
    $idBook = $dbAccess->LastId(); 
    if ($debugEnable) $debug .= "idBook: " . $idBook . "<br /> \n";
 
    // Then we must add a first page of the new book.
    $query = <<<QUERY
INSERT INTO {$tablePage} (headerPage, stylePage, page_idBook)
    VALUES ('{$nameBook}', '1', '{$idBook}');
QUERY;
    $dbAccess->SingleQuery($query);
    
    // Check what id that page got.
    $idFirstPage = $dbAccess->LastId();

    // Add a field to the new page.
    $query = <<<QUERY
INSERT INTO {$tableField} (typeField, parameter1Field, parameter2Field, field_idPage)
    VALUES ('2', 'images/defaultImage.gif', 'Neutral bild', {$idFirstPage});
QUERY;
    $dbAccess->SingleQuery($query);

    // At last we update the book with it's first page.
    $query = <<<QUERY
UPDATE {$tableBook} 
    SET firstPageBook = '{$idFirstPage}'
    WHERE idBook = '{$idBook}';
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

