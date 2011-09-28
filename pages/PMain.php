<?php

///////////////////////////////////////////////////////////////////////////////////////////////////
//
// PMain.php
// Called by 'main' from index.php.
// This is the main page that everyone comes to entering the website..
// 
// Input: 
// Output: 
//


///////////////////////////////////////////////////////////////////////////////////////////////////
// Check authority etc.

$intFilter = new CAccessControl();
$intFilter->FrontControllerIsVisitedOrDie();


///////////////////////////////////////////////////////////////////////////////////////////////////
// Build the page

$page = new CHTMLPage(); 
$pageTitle = "Titel";

// Lägg in din text för huvudkolumnen här nedan i HTML-kod.
$mainTextHTML = <<<HTMLCode
This is the main page.
HTMLCode;


require(TP_PAGESPATH.'rightColumn.php'); // Genererar en högerkolumn i $rightColumnHTML
$page->printPage($pageTitle, $mainTextHTML, "", $rightColumnHTML);


?>

