<?php

///////////////////////////////////////////////////////////////////////////////////////////////////
//
// PEditAccount.php
// Called by 'edit_account' from index.php.
// This page generates a form for editing or adding an account. If there is no input a new account
// is generated. From this page you are sent to PShowUser.
// Input: 'id' for editing.
// Output: 'account', 'password1', 'password2', 'authority', 'send', 'redirect' as POST's.
// 


///////////////////////////////////////////////////////////////////////////////////////////////////
// Check that the page is reached from the front controller and authority etc.

$intFilter = new CAccessControl();
$intFilter->FrontControllerIsVisitedOrDie();
$intFilter->UserIsSignedInOrRedirectToSignIn();
$intFilter->UserIsAuthorisedOrDie('adm');


///////////////////////////////////////////////////////////////////////////////////////////////////
// Take care of input.
$idUser = isset($_GET['id']) ? $_GET['id'] : NULL;

// Initiate arrayUser if we are going to generate a new account.
$arrayUser     = array("","","","","","");


///////////////////////////////////////////////////////////////////////////////////////////////////
// If $idUser has a value then idUser shall be edited. Get the old info.

$redirect = "search_user";
if ($idUser) {
    $dbAccess           = new CdbAccess();
    $idUser 		    = $dbAccess->WashParameter($idUser);
    $tableUser        = DB_PREFIX . 'User';
    $query = "SELECT * FROM {$tableUser} WHERE idUser = {$idUser};";
    $result = $dbAccess->SingleQuery($query); 
    $arrayUser = $result->fetch_row();
    $result->close();
    $redirect = "show_user&amp;id=".$idUser;
}

///////////////////////////////////////////////////////////////////////////////////////////////////
// Generate a random password.

$min=5; // minimum length of password
$max=10; // maximum length of password
$pwd=""; // to store generated password

for ( $i=0; $i<rand($min,$max); $i++ ) {
    $num=rand(48,122);
    if(($num > 97 && $num < 122))     $pwd.=chr($num);
    else if(($num > 65 && $num < 90)) $pwd.=chr($num);
    else if(($num >48 && $num < 57))  $pwd.=chr($num);
    else if($num==95)                 $pwd.=chr($num);
    else $i--;
}


///////////////////////////////////////////////////////////////////////////////////////////////////
// Make a form for editing the account.

$mainTextHTML = <<<HTMLCode
<form action='?p=save_account' method='post'>
<h3>Fyll i användarinformation.</h3>
<p>Skapa ett nytt användarkonto eller ändra uppgifter för ett redan existerande användarkonto.</p>
<table>
<tr><td>Användarnamn</td>
<td><input type='text' name='account' size='20' maxlength='20' value='{$arrayUser[1]}' /></td></tr>
<tr><td>Lösenord</td>
<td><input type='password' name='password1' size='20' maxlength='20' value='{$pwd}' /></td></tr>
<tr><td>Lösenord igen</td>
<td><input type='password' name='password2' size='20' maxlength='20' value='{$pwd}' /></td></tr>
<tr><td>Behörighetsgrupp</td>
HTMLCode;

if ($arrayUser[3] == "adm") {
    $mainTextHTML .= <<<HTMLCode
<td><input type='radio' name='authority' value='usr'  /> Vanlig användare 
<input type='radio' name='authority' value='adm' checked='checked' /> Administratör </td></tr>
HTMLCode;
} else {
    $mainTextHTML .= <<<HTMLCode
<td><input type='radio' name='authority' value='usr' checked='checked' /> Vanlig användare 
<input type='radio' name='authority' value='adm' /> Administratör </td></tr>
HTMLCode;
}
$mainTextHTML .= <<<HTMLCode
<tr><td>Skicka lösenordet med mejl till användaren</td>
<td><input type='checkbox' name='send' value='1' /></td></tr>
<tr><td>
<input type='image' title='Spara' src='../images/b_enter.gif' alt='Spara' />
<a title='Cancel' href='?p={$redirect}' ><img src='../images/b_cancel.gif' alt='Cancel' /></a>
</td></tr>
</table>
<input type='hidden' name='id' value='{$idUser}' />
<input type='hidden' name='redirect' value='{$redirect}' />
</form>
HTMLCode;


///////////////////////////////////////////////////////////////////////////////////////////////////
// Generate the page.

$page = new CHTMLPage(); 
$pageTitle = "Editera användarkonto";

require(TP_PAGESPATH.'rightColumn.php'); 
$page->printPage($pageTitle, $mainTextHTML, "", $rightColumnHTML);

?>

