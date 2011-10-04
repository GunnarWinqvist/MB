<?php

///////////////////////////////////////////////////////////////////////////////////////////////////
//
// PEditUser.php (edit_user)
// 
// The page generates a form for editing details of an user. 
// From this page you are sent to PSaveUser and after that redirected to PShowUser.
//
// Input: 'id'
// Output: 'firstName', 'familyNamn', 'eMail1', 'eMail2', 'id', 'redirect' as POST.
// 


///////////////////////////////////////////////////////////////////////////////////////////////////
// Check that the page is opened via index.php and that the user has the right authority.

$intFilter = new CAccessControl();
$intFilter->FrontControllerIsVisitedOrDie();
$intFilter->UserIsSignedInOrRedirectToSignIn();


///////////////////////////////////////////////////////////////////////////////////////////////////
// Prepare the database and clean input.

$dbAccess           = new CdbAccess();
$tableUser        = DB_PREFIX . 'User';

$idUser = isset($_GET['id']) ? $_GET['id'] : NULL;
$idUser = $dbAccess->WashParameter($idUser);
if ($debugEnable) $debug .= "Input: id=" . $idUser . "<br /> \n";


///////////////////////////////////////////////////////////////////////////////////////////////////
// Check if session user is NOT owner of page and NOT adm. If so give message and exit.

if (($_SESSION['idUser'] != $idUser) AND ($_SESSION['authorityUser'] != 'adm')) {
    $_SESSION['errorMessage']      = "Du har inte behörighet att titta på den här användaren!";
    header('Location: ' . WS_SITELINK . "?p=main");
    exit;
    }


///////////////////////////////////////////////////////////////////////////////////////////////////
// Fetch the present information regarding the user.

$totalStatements = 1;
$query = <<<QUERY
SELECT * FROM {$tableUser} WHERE idUser = {$idUser};
QUERY;

$statements = $dbAccess->MultiQuery($query, $arrayResult); 
if ($debugEnable) $debug .= "{$statements} statements av {$totalStatements} kördes.<br /> \n"; 

// Put in an array for each table and close the result.
$arrayUser     = $arrayResult[0]->fetch_row(); $arrayResult[0]->close();

if ($debugEnable) $debug .= "User = ".print_r($arrayUser, TRUE)."<br /> \n";


///////////////////////////////////////////////////////////////////////////////////////////////////
// Generate a form for editing the user.

$mainTextHTML = <<<HTMLCode
{$arrayUser[4]} {$arrayUser[5]}
<form name='user' action='?p=save_user' method='post'>
<h3>Användarinformation för användaren - {$arrayUser[1]}</h3>
<table>
<tr><td>Förnamn</td>
<td><input type='text' name='firstName' size='40' maxlength='50' value='{$arrayUser[4]}' /></td></tr>
<tr><td>Efternamn</td>
<td><input type='text' name='familyName' size='40' maxlength='50' value='{$arrayUser[5]}' /></td></tr>
<tr><td>e-postadress 1</td>
<td><input type='text' name='eMail1' size='40' maxlength='50' value='{$arrayUser[6]}' /></td></tr>
<tr><td>e-postadress 2</td>
<td><input type='text' name='eMail2' size='40' maxlength='50' value='{$arrayUser[7]}' /></td></tr>
</table>

HTMLCode;


// Add buttons and redirect.
$redirect = "show_user&amp;id=" . $idUser;
$mainTextHTML .= <<<HTMLCode
<input type='image' title='Spara' src='../images/b_enter.gif' alt='Spara' />
<a title='Cancel' href='?p={$redirect}' ><img src='../images/b_cancel.gif' alt='Cancel' /></a>
<input type='hidden' name='id' value='{$idUser}' />
<input type='hidden' name='redirect' value='{$redirect}' />

</form>
HTMLCode;



///////////////////////////////////////////////////////////////////////////////////////////////////
// Generate the page

$page = new CHTMLPage(); 
$pageTitle = "Editera användare";

$page->printPage($pageTitle, $mainTextHTML);

?>

