<?php

///////////////////////////////////////////////////////////////////////////////////////////////////
//
// PMain.php
// Called by 'main' from index.php.
// This is the main, welcome page that everyone comes to entering the website..
// 
//


///////////////////////////////////////////////////////////////////////////////////////////////////
// Check that the page is opened via index.php.

$intFilter = new CAccessControl();
$intFilter->FrontControllerIsVisitedOrDie();


///////////////////////////////////////////////////////////////////////////////////////////////////
// Build the page

$page = new CHTMLPage(); 
$pageTitle = "Min bok";

// L�gg in din text f�r huvudkolumnen h�r nedan i HTML-kod.
$mainTextHTML = <<<HTMLCode
<h2>V�lkommen till min bok!</h2>
HTMLCode;


$page->printPage($pageTitle, $mainTextHTML);


?>

