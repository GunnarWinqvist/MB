<?php

///////////////////////////////////////////////////////////////////////////////////////////////////
//
// PShowUser.php
// Called by 'show_user' from index.php.
// The page shows everything about a user from the register except the password.
// Input: id
// Output: 
// 


///////////////////////////////////////////////////////////////////////////////////////////////////
// Check that the page is reached from the front controller and authority etc.

$intFilter = new CAccessControl();
$intFilter->FrontControllerIsVisitedOrDie();
$intFilter->UserIsSignedInOrRedirectToSignIn();
$intFilter->UserIsAuthorisedOrDie('adm');


///////////////////////////////////////////////////////////////////////////////////////////////////
// Take care of input to the page.

$idUser = isset($_GET['id']) ? $_GET['id'] : NULL;
if ($debugEnable) $debug .= "Input: id=" . $idUser . "<br /> \n";


///////////////////////////////////////////////////////////////////////////////////////////////////
// Prepare the database and clean input.

$dbAccess           = new CdbAccess();
$tableUser          = DB_PREFIX . 'User';
$idUser 		    = $dbAccess->WashParameter($idUser);

///////////////////////////////////////////////////////////////////////////////////////////////////
// Query the database for all information regarding idUser.

$totalStatements = 1;
$query = <<<QUERY
SELECT * FROM {$tableUser} WHERE idUser = {$idUser};
QUERY;

$statements = $dbAccess->MultiQuery($query, $arrayResult); 
if ($debugEnable) $debug .= "{$statements} statements of {$totalStatements}.<br /> \n"; 


///////////////////////////////////////////////////////////////////////////////////////////////////
// Prepare a table for the user.

$arrayPerson     = $arrayResult[0]->fetch_row(); $arrayResult[0]->close();
if ($debugEnable) $debug .= "User = ".print_r($arrayPerson, TRUE)."<br /> \n";
$mainTextHTML = <<<HTMLCode
{$arrayPerson[4]} {$arrayPerson[5]}
<h3>Anv�ndarinformation</h3>
<table>
<tr><td>Anv�ndarnamn</td><td>{$arrayPerson[1]}</td></tr>
<tr><td>Beh�righetsgrupp</td><td>{$arrayPerson[3]}</td></tr>
<tr><td>F�rnamn</td><td>{$arrayPerson[4]}</td></tr>
<tr><td>Efternamn</td><td>{$arrayPerson[5]}</td></tr>
<tr><td>e-postadress 1</td><td>{$arrayPerson[6]}</td></tr>
<tr><td>e-postadress 2</td><td>{$arrayPerson[7]}</td></tr>
</table>
HTMLCode;



///////////////////////////////////////////////////////////////////////////////////////////////////
// L�gg till knappar f�r editering och �ndra l�senord. Olika f�r admin.
if ($_SESSION['authorityUser'] == "adm") {
    $mainTextHTML .= <<<HTMLCode
<a title='Editera' href='?p=edit_user&amp;id={$idUser}' tabindex='1'><img src='../images/b_edit.gif' alt='Editera' /></a>
<a title='�ndra l�senord' href='?p=edit_account&amp;id={$idUser}'><img src='../images/b_password.gif' alt='�ndra l�senord' /></a>
<a title='Radera' href='?p=del_account&amp;id={$idUser}' onclick="return confirm('Vill du radera anv�ndaren ur databasen?');">
            <img src='../images/b_delete.gif' alt='Radera' /></a>
HTMLCode;

} else {
    $mainTextHTML .= <<<HTMLCode
<a title='Editera' href='?p=edit_user&amp;id={$idUser}' tabindex='1'><img src='../images/b_edit.gif' alt='Editera' /></a>
<a title='�ndra l�senord' href='?p=edit_passw&amp;id={$idUser}'><img src='../images/b_password.gif' alt='�ndra l�senord' /></a>
HTMLCode;
}


///////////////////////////////////////////////////////////////////////////////////////////////////
// Generate page.

$page = new CHTMLPage(); 
$pageTitle = "Visa person";

require(TP_PAGESPATH.'rightColumn.php'); // Genererar en h�gerkolumn i $rightColumnHTML
$page->printPage($pageTitle, $mainTextHTML, "", $rightColumnHTML);


?>

