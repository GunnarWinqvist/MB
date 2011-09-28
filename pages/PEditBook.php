<?php

///////////////////////////////////////////////////////////////////////////////////////////////////
//
// PEditBook.php
// Called by 'edit_book' from index.php.
// This page generates a form for editing or adding a book title in the database. If there is no 
// input a new book and the first page is generated. From this page you are sent to PSaveBook and 
// then to PMyPage.
// Input: 'idBook' for editing and 'idChild' for a new book.
// Output: 'nameBook', 'idBook', 'idChild', 'redirect' as POST's.
// 


///////////////////////////////////////////////////////////////////////////////////////////////////
// Check that the page is reached from the front controller and authority etc.

$intFilter = new CAccessControl();
$intFilter->FrontControllerIsVisitedOrDie();
$intFilter->UserIsSignedInOrRedirectToSignIn();


///////////////////////////////////////////////////////////////////////////////////////////////////
// Take care of input.
$idBook  = isset($_GET['idBook'])  ? $_GET['idBook']  : NULL;
$idChild = isset($_GET['idChild']) ? $_GET['idChild'] : NULL;

// Initiate aBook if we are going to generate a new account.
$aBook     = array("","","","","","");


///////////////////////////////////////////////////////////////////////////////////////////////////
// If $idBook has a value then idBook shall be edited. Get the old info.

$redirect = "my_page";
if ($idBook) {
    $dbAccess   = new CdbAccess();
    $idBook 	= $dbAccess->WashParameter($idBook);
    $tableBook  = DB_PREFIX . 'Book';
    $query      = "SELECT * FROM {$tableBook} WHERE idBook = {$idBook};";
    $result     = $dbAccess->SingleQuery($query); 
    $aBook      = $result->fetch_row();
    $result->close();
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

require(TP_PAGESPATH.'rightColumn.php'); 
$page->printPage($pageTitle, $mainTextHTML, "", $rightColumnHTML);

?>

