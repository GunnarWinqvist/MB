<?php

///////////////////////////////////////////////////////////////////////////////////////////////////
//
// PCreatePage.php (crate_page)
// 
// On this page you can design a new page in your book. The page is then saved in the DB on save_page. 
//
// Input: 'idPage' 
// Output: 'header', 'style', 'framework', 'idPage', 'redirect' as post.
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
// Generate a form for editing the page.

$mainTextHTML = <<<HTMLCode
<form name='newPage' action='?p=save_page' method='post'>
<table>
<tr><td></td><td>Rubrik</td></tr>
<tr><td></td><td><input type='text' name='header' size='40' maxlength='50' value='' /></td></tr>
<tr><td></td><td>Utseende</td></tr>
<tr><td><input type='radio' name='style' value='1' /></td><td><img src='' alt='Utseende 1' /></td></tr>
<tr><td><input type='radio' name='style' value='2' /></td><td><img src='' alt='Utseende 2' /></td></tr>
<tr><td><input type='radio' name='style' value='3' /></td><td><img src='' alt='Utseende 3' /></td></tr>
<tr><td></td><td>Kolumner</td></tr>
<tr><td><input type='radio' name='framework' value='1' /></td><td>1 kolumn</td></tr>
<tr><td><input type='radio' name='framework' value='2' /></td><td>2 kolumner</td></tr>
<tr><td><input type='radio' name='framework' value='3' /></td><td>3 kolumner</td></tr>
</table>

HTMLCode;


// Add buttons and redirect.
$redirect = "my_page";
$mainTextHTML .= <<<HTMLCode
<input type='image' title='Spara' src='../images/b_enter.gif' alt='Spara' />
<a title='Cancel' href='?p={$redirect}' ><img src='../images/b_cancel.gif' alt='Cancel' /></a>
<input type='hidden' name='idPage' value='{$idPage}' />
<input type='hidden' name='redirect' value='{$redirect}' />

</form>
HTMLCode;


///////////////////////////////////////////////////////////////////////////////////////////////////
// Generate the page.

$header = "Skapa en ny sida";
$page = new CHTMLPage(); 
$page->printPage($header, $mainTextHTML);


?>

