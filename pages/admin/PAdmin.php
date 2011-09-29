<?php

///////////////////////////////////////////////////////////////////////////////////////////////////
//
// PAdmin.php
// Called by 'admin' from index.php.
// Administration page with tools for the administrator.
// 
// Input: 
// Output: 
//


///////////////////////////////////////////////////////////////////////////////////////////////////
// Check that the page is reached from the front controller and authority etc.

$intFilter = new CAccessControl();
$intFilter->FrontControllerIsVisitedOrDie();
$intFilter->UserIsSignedInOrRedirectToSignIn(); 
$intFilter->UserIsAuthorisedOrDie('adm');       


///////////////////////////////////////////////////////////////////////////////////////////////////
// Build the page.

$mainTextHTML = <<<HTMLCode
<h3>Administratör</h3>
<ul>
<li><a href='?p=edit_account'>Lägg till ny användare</a></li>
<li><a href='?p=search_user'>Sök en person</a></li>
<li><a href='?p=dump_db'>Dumpa databasen på fil</a></li>
<p>Följande aktiviteter förstör databasen och går inte att backa!</p>
<li><a href='?p=install_db' 
    onclick="return confirm('Vill du installera om databasen? Alla data blir förstörda och kan inte återskapas.');">
    Ominstallera databasen</a></li>
<li><a href='?p=fill_db' 
    onclick="return confirm('Vill du fylla databasen från fil? Alla gamla data kommer att skrivas över.');">
    Fyll databasen från fil</a></li>
</ul>
HTMLCode;


///////////////////////////////////////////////////////////////////////////////////////////////////
// Generate the page.

$page = new CHTMLPage(); 
$pageTitle = "Titel";

$page->printPage($pageTitle, $mainTextHTML);


?>

