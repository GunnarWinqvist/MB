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
<h3>Administrat�r</h3>
<ul>
<li><a href='?p=edit_account'>L�gg till ny anv�ndare</a></li>
<li><a href='?p=search_user'>S�k en person</a></li>
<li><a href='?p=dump_db'>Dumpa databasen p� fil</a></li>
<p>F�ljande aktiviteter f�rst�r databasen och g�r inte att backa!</p>
<li><a href='?p=install_db' 
    onclick="return confirm('Vill du installera om databasen? Alla data blir f�rst�rda och kan inte �terskapas.');">
    Ominstallera databasen</a></li>
<li><a href='?p=fill_db' 
    onclick="return confirm('Vill du fylla databasen fr�n fil? Alla gamla data kommer att skrivas �ver.');">
    Fyll databasen fr�n fil</a></li>
</ul>
HTMLCode;


///////////////////////////////////////////////////////////////////////////////////////////////////
// Generate the page.

$page = new CHTMLPage(); 
$pageTitle = "Titel";

$page->printPage($pageTitle, $mainTextHTML);


?>

