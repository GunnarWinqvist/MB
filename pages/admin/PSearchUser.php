<?php

///////////////////////////////////////////////////////////////////////////////////////////////////
//
// PSearchUser.php
// Called by 'search' from index.php.
// On this page you can search for a user on first name, family name or account.
// Input:  
// Output:  'firstName', 'familyName', 'account'. 
// 


///////////////////////////////////////////////////////////////////////////////////////////////////
// Check that the page is reached from the front controller and authority etc.

$intFilter = new CAccessControl();
$intFilter->FrontControllerIsVisitedOrDie();
$intFilter->UserIsSignedInOrRedirectToSignIn(); 
$intFilter->UserIsAuthorisedOrDie('adm');       


///////////////////////////////////////////////////////////////////////////////////////////////////
// Build the search form.

$mainTextHTML = <<<HTMLCode
<form name='search' action='?p=list_user' method='post'>
<h2>Skriv in det du vill söka på. Del av namn eller hela namnet.</h2>
<p>Fyll inte i något om du vill lista alla i registret.</p>
<table class='formated'>
<tr><td>Användarnamn</td>
<td><input type='text' name='account' size='40' maxlength='20' value='' /></td></tr>
<tr><td>Förnamn</td>
<td><input type='text' name='firstName' size='40' maxlength='50' value='' /></td></tr>
<tr><td>Efternamn</td>
<td><input type='text' name='familyName' size='40' maxlength='50' value='' /></td></tr>
<tr><td></td><td><input type='image' title='Sök' src='../images/b_enter.gif' alt='Sök' /></td></tr>
</table>
</form>
HTMLCode;


///////////////////////////////////////////////////////////////////////////////////////////////////
// Generate the page

$page = new CHTMLPage(); 
$pageTitle = "Sök person";

$page->printPage($pageTitle, $mainTextHTML);

?>

