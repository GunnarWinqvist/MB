<?php

///////////////////////////////////////////////////////////////////////////////////////////////////
//
// PEditAccount.php
// Called by 'edit_account' from index.php.
// This page generates a form for entering information to a new account. 
// The form information is passed on to PSaveAccount.php.
// 
// Output: 'account', 'password1', 'password2', 'firstName', 'familyName', 'eMail' as POST's.
// 


///////////////////////////////////////////////////////////////////////////////////////////////////
// Check that the page is opened via index.php.

$intFilter = new CAccessControl();
$intFilter->FrontControllerIsVisitedOrDie();



///////////////////////////////////////////////////////////////////////////////////////////////////
// Make a form for entering the accountinformation.

$mainTextHTML = <<<HTMLCode
<form action='?p=save_new' method='post'>
<h3>Fyll i användarinformation.</h3>
<table>
<tr><td>Användarnamn</td>
<td><input type='text' name='account' size='20' maxlength='20' /></td></tr>
<tr><td>Lösenord</td>
<td><input type='password' name='password1' size='20' maxlength='20' /></td></tr>
<tr><td>Lösenord igen</td>
<td><input type='password' name='password2' size='20' maxlength='20' /></td></tr>
<tr><td>Förnamn</td>
<td><input type='text' name='firstName' size='40' maxlength='50' /></td></tr>
<tr><td>Efternamn</td>
<td><input type='text' name='familyName' size='40' maxlength='50' /></td></tr>
<tr><td>e-postadress 1</td>
<td><input type='text' name='eMail' size='40' maxlength='50' /></td></tr>


<tr><td>
<input type='image' title='Spara' src='../images/b_enter.gif' alt='Spara' />
<a title='Cancel' href='?p=main' ><img src='../images/b_cancel.gif' alt='Cancel' /></a>
</td></tr>
</table>
</form>
HTMLCode;


///////////////////////////////////////////////////////////////////////////////////////////////////
// Generate the page.

$page = new CHTMLPage(); 
$pageTitle = "Skapa användarkonto";
$page->printPage($pageTitle, $mainTextHTML);

?>

