<?php

///////////////////////////////////////////////////////////////////////////////////////////////////
//
// PEditBook.php (edit_book)
// 
// This page generates a form for editing or adding a book title in the database. If there is no 
// input a new book and the first page is generated. From this page you are sent to PSaveBook and 
// then to PMyPage.
//
// Input: 'idBook' for editing and 'idChild' for a new book.
// Output: 'nameBook', 'idBook', 'idChild', 'redirect' as POST's.
// 


///////////////////////////////////////////////////////////////////////////////////////////////////
// Check that the page is opened via index.php and that the user has the right authority.

$intFilter = new CAccessControl();
$intFilter->FrontControllerIsVisitedOrDie();
$intFilter->UserIsSignedInOrRedirectToSignIn();


///////////////////////////////////////////////////////////////////////////////////////////////////
// Take care of input.
$idBook  = isset($_GET['idBook'])  ? $_GET['idBook']  : NULL;
$idChild = isset($_GET['idChild']) ? $_GET['idChild'] : NULL;


///////////////////////////////////////////////////////////////////////////////////////////////////
// If $idBook has a value then idBook shall be edited. Get the old info.

// Initiate aBook array if we are going to generate a new account.
$aBook     = array("","","","","","");

$redirect = "my_page";
if ($idBook) {
    $dbAccess   = new CdbAccess();
    $idBook 	= $dbAccess->WashParameter($idBook);
    $tableBook  = DB_PREFIX . 'Book';
    $tableChild = DB_PREFIX . 'Child';
    $query      = "SELECT * FROM ({$tableBook} JOIN {$tableChild} ON book_idChild = idChild) WHERE idBook = {$idBook};";
    $result     = $dbAccess->SingleQuery($query); 
    $aBook      = $result->fetch_row();
    $result->close();
    
    // Check if the logged in user is NOT owner of the book. If so exit with a message.
    if ($aBook[8] != $_SESSION['idUser']) {
        $_SESSION['errorMessage']      = "Det är inte din bok. Du kan inte ändra i den.";
        header('Location: ' . WS_SITELINK . "?p=main");
        exit;
    }

}



///////////////////////////////////////////////////////////////////////////////////////////////////
// Make a form for editing the book.

$mainTextHTML = <<<HTMLCode
<form action='?p=save_book' method='post'>
<table>
<tr><td>Boktitel</td>
<td><input type='text' name='nameBook' size='20' maxlength='20' value='{$aBook[1]}' /></td></tr>
<tr><td>
<input type='image' title='Spara' src='../images/b_enter.gif' alt='Spara' />
<a title='Cancel' href='?p={$redirect}' ><img src='../images/b_cancel.gif' alt='Cancel' /></a>
</td></tr>
</table>
<input type='hidden' name='idBook'   value='{$idBook}' />
<input type='hidden' name='idChild'  value='{$idChild}' />
<input type='hidden' name='redirect' value='{$redirect}' />
</form>
HTMLCode;


///////////////////////////////////////////////////////////////////////////////////////////////////
// Generate the page.

$page = new CHTMLPage(); 
$pageTitle = "Editera bok";

$page->printPage($pageTitle, $mainTextHTML);

?>

