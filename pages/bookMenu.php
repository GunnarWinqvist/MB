<?php

///////////////////////////////////////////////////////////////////////////////////////////////////
//
// bookMenu.php
// Menu definition to be used on every book page. 
// 

// Find the first page of the book.
$query = "SELECT idBook, firstPageBook FROM {$tableBook} JOIN {$tablePage} ON page_idBook = idBook WHERE idPage = '{$idPage}';";
$result = $dbAccess->SingleQuery($query);
$row = $result->fetch_object();
$idBook = $row->idBook; //Save for later.
$firstPage = $row->firstPageBook;
$result->close();
if ($firstPage == $idPage) $firstPage = 0; // If we already are on the first page.

// Find the previous page of the book.
$query = "SELECT idPage FROM {$tablePage} WHERE nextPage = '{$idPage}';";
if ($result = $dbAccess->SingleQuery($query)) {
    $row = $result->fetch_object();
    $previousPage = $row->idPage;
    $result->close();
} else {
    // We are on the first page so set previousPage to 0.
    $previousPage = 0;
}

// Find the next page of the book.
$query = "SELECT nextPage FROM {$tablePage} WHERE idPage = '{$idPage}';";
$result = $dbAccess->SingleQuery($query);
$row = $result->fetch_object();
$nextPage = $row->nextPage; // We are on the last page if nextPage=0.
$result->close();

// Find the last page of the book.
$query = "SELECT idPage FROM {$tablePage} WHERE page_idBook = {$idBook} AND nextPage = '0';";
$result = $dbAccess->SingleQuery($query);
$row = $result->fetch_object();
$lastPage = $row->idPage;
$result->close();
if ($lastPage == $idPage) $lastPage = 0; // If we already are on the last page.

$htmlBookMenu      = "";
if ($firstPage) 
    $htmlBookMenu .= "<a class='nav' href='?p=show_page&amp;idPage={$firstPage}' title='Första sidan'>Första sidan</a> | \n";
if ($previousPage) 
    $htmlBookMenu .= "<a class='nav' href='?p=show_page&amp;idPage={$previousPage}' title='Förra sidan'>Förra sidan</a> | \n";
$htmlBookMenu     .= "<a class='nav' href='?p=edit_page&amp;idPage={$idPage}' title='Redigera den här sidan'>Redigera den här sidan</a> | \n";
$htmlBookMenu     .= "<a class='nav' href='?p=main' title='Skapa en sida efter den här'>Skapa en sida efter den här</a> | \n";
$htmlBookMenu     .= "<a class='nav' href='?p=my_page' title='Gå till huvudmenyn'>Gå till huvudmenyn</a> | \n";
if ($nextPage) 
    $htmlBookMenu .= "<a class='nav' href='?p=show_page&amp;idPage={$nextPage}' title='Nästa sidan'>Nästa sidan</a> | \n";
if ($lastPage) 
    $htmlBookMenu .= "<a class='nav' href='?p=show_page&amp;idPage={$lastPage}' title='Sista sidan'>Sista sidan</a> | \n";

$htmlBookMenu     .= "<br /> \n";


?>