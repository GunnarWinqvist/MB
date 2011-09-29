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

// Lägg in din text för huvudkolumnen här nedan i HTML-kod.
$mainTextHTML = <<<HTMLCode
<h2>Välkommen till min bok!</h2>
HTMLCode;


$page->printPage($pageTitle, $mainTextHTML);


?>

