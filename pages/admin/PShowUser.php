<?php

///////////////////////////////////////////////////////////////////////////////////////////////////
//
// PShowUser.php (show_user)
// 
// The page shows everything about a user from the register except the password.
// Input: id
// Output: 
// 


///////////////////////////////////////////////////////////////////////////////////////////////////
// Check that the page is opened via index.php and that the user has the right authority.

$intFilter = new CAccessControl();
$intFilter->FrontControllerIsVisitedOrDie();
$intFilter->UserIsSignedInOrRedirectToSignIn();


///////////////////////////////////////////////////////////////////////////////////////////////////
// Take care of input to the page.

$idUser = isset($_GET['id']) ? $_GET['id'] : NULL;
if ($debugEnable) $debug .= "Input: id=" . $idUser . "<br /> \n";


///////////////////////////////////////////////////////////////////////////////////////////////////
// Check if session user is NOT owner of page and NOT adm. If so give message and exit.

if (($_SESSION['idUser'] != $idUser) AND ($_SESSION['authorityUser'] != 'adm')) {
    $_SESSION['errorMessage']      = "Du har inte behörighet att titta på den här användaren!";
    header('Location: ' . WS_SITELINK . "?p=main");
    exit;
    }


///////////////////////////////////////////////////////////////////////////////////////////////////
// Prepare the database and clean input.

$dbAccess           = new CdbAccess();
$tableUser          = DB_PREFIX . 'User';
$tableChild         = DB_PREFIX . 'Child';
$tableBook          = DB_PREFIX . 'Book';
$tablePage          = DB_PREFIX . 'Page';
$tableRelation      = DB_PREFIX . 'Relation';

$idUser 		    = $dbAccess->WashParameter($idUser);


///////////////////////////////////////////////////////////////////////////////////////////////////
// Query the database for all information regarding idUser.

$totalStatements = 5;
$query = <<<QUERY
-- Basic information
SELECT * FROM {$tableUser} WHERE idUser = {$idUser};

-- Number of children in the system.
SELECT COUNT(*) FROM {$tableChild} WHERE child_idUser = {$idUser};

-- Number of books
SELECT COUNT(*) FROM ({$tableChild} JOIN {$tableBook} ON book_idChild = idChild) WHERE child_idUser = {$idUser};

-- Number of pages
SELECT COUNT(*) FROM (({$tableChild} JOIN {$tableBook} ON book_idChild = idChild) 
    JOIN {$tablePage} ON page_idBook = idBook) WHERE child_idUser = {$idUser};

-- Number of guests
SELECT COUNT(*) FROM {$tableRelation} WHERE relation_idUser = {$idUser};

QUERY;

$statements = $dbAccess->MultiQuery($query, $arrayResult); 
if ($debugEnable) $debug .= "{$statements} statements of {$totalStatements}.<br /> \n"; 


///////////////////////////////////////////////////////////////////////////////////////////////////
// Prepare a table for the user.

$arrayPerson     = $arrayResult[0]->fetch_row(); $arrayResult[0]->close();
if ($debugEnable) $debug .= "User = ".print_r($arrayPerson, TRUE)."<br /> \n";
$mainTextHTML = <<<HTMLCode
{$arrayPerson[4]} {$arrayPerson[5]}
<h3>Användarinformation</h3>
<table>
<tr><td>Användarnamn</td><td>{$arrayPerson[1]}</td></tr>
<tr><td>Behörighetsgrupp</td><td>{$arrayPerson[3]}</td></tr>
<tr><td>Förnamn</td><td>{$arrayPerson[4]}</td></tr>
<tr><td>Efternamn</td><td>{$arrayPerson[5]}</td></tr>
<tr><td>e-postadress 1</td><td>{$arrayPerson[6]}</td></tr>
<tr><td>e-postadress 2</td><td>{$arrayPerson[7]}</td></tr>
</table>
HTMLCode;


///////////////////////////////////////////////////////////////////////////////////////////////////
// List engagement.

$arrayChilds = $arrayResult[1]->fetch_row(); $arrayResult[1]->close();
$arrayBooks  = $arrayResult[2]->fetch_row(); $arrayResult[2]->close();
$arrayPages  = $arrayResult[3]->fetch_row(); $arrayResult[3]->close();
$arrayGuests = $arrayResult[4]->fetch_row(); $arrayResult[4]->close();

$mainTextHTML .= <<<HTMLCode
<h3>Aktiviteter</h3>
<table>
<tr><td>Antal barn  </td><td>{$arrayChilds[0]}</td></tr>
<tr><td>Antal böcker</td><td>{$arrayBooks[0]} </td></tr>
<tr><td>Antal sidor </td><td>{$arrayPages[0]} </td></tr>
<tr><td>Antal gäster</td><td>{$arrayGuests[0]}</td></tr>
</table>
HTMLCode;

///////////////////////////////////////////////////////////////////////////////////////////////////
// Lägg till knappar för editering och ändra lösenord. Olika för admin.
if ($_SESSION['authorityUser'] == "adm") {
    $mainTextHTML .= <<<HTMLCode
<a title='Editera' href='?p=edit_user&amp;id={$idUser}' tabindex='1'><img src='../images/b_edit.gif' alt='Editera' /></a>
<a title='Ändra lösenord' href='?p=edit_account&amp;id={$idUser}'><img src='../images/b_password.gif' alt='Ändra lösenord' /></a>
<a title='Radera' href='?p=del_user&amp;id={$idUser}' onclick="return confirm('Vill du radera användaren ur databasen?');">
            <img src='../images/b_delete.gif' alt='Radera' /></a>
HTMLCode;

} else {
    $mainTextHTML .= <<<HTMLCode
<a title='Editera' href='?p=edit_user&amp;id={$idUser}' tabindex='1'><img src='../images/b_edit.gif' alt='Editera' /></a>
<a title='Ändra lösenord' href='?p=edit_passw&amp;id={$idUser}'><img src='../images/b_password.gif' alt='Ändra lösenord' /></a>
HTMLCode;
}


///////////////////////////////////////////////////////////////////////////////////////////////////
// Generate page.

$page = new CHTMLPage(); 
$pageTitle = "Visa person";

$page->printPage($pageTitle, $mainTextHTML);


?>

