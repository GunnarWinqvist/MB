<?php

///////////////////////////////////////////////////////////////////////////////////////////////////
//
// PChildren.php
// Called by 'children' from index.php.
// The page lists all children to an user. 
// Input:  idUser from SESSION
// Output: 'idChild'.
// 


///////////////////////////////////////////////////////////////////////////////////////////////////
// Check that the page is reached from the front controller and authority etc.

$intFilter = new CAccessControl();
$intFilter->FrontControllerIsVisitedOrDie();
$intFilter->UserIsSignedInOrRedirectToSignIn();


///////////////////////////////////////////////////////////////////////////////////////////////////
// Prepare the database and input.

$dbAccess         = new CdbAccess();
$tableChild       = DB_PREFIX . 'Child';
$tableBook        = DB_PREFIX . 'Book';

$idUser = $_SESSION['idUser'];
if ($debugEnable) $debug .= "Input: id=" . $idUser . "<br /> \n";


///////////////////////////////////////////////////////////////////////////////////////////////////
// Fetch all children related to the user and list them.

$query = <<<QUERY
SELECT * FROM 
({$tableChild} LEFT OUTER JOIN {$tableBook} ON book_idChild = idChild)
WHERE child_idUser = {$idUser};
QUERY;

$result = $dbAccess->SingleQuery($query); 

// Headers
$mainTextHTML = <<<HTMLCode
<h2>Dina barn</h2>
<table>
HTMLCode;

// Table
if ($result) {
    while($row = $result->fetch_row()) {
        if ($debugEnable) $debug .= "Query result: ".print_r($row, TRUE)."<br /> \n";
        $mainTextHTML .= <<<HTMLCode
<tr>
    <td>{$row[1]}</td>
    <td>{$row[2]}</td>
    <td>{$row[3]}</td>
    <td><a title='Visa bok' href='?p=show_page&amp;idPage={$row[7]}'>{$row[6]}</a></td>
    <td><a title='Editera barn' href='?p=edit_child&amp;id={$row[0]}'><img src='../images/page_edit.png' alt='Ändra' /></a></td>
    <td><a title='Radera barn' href='?p=main' onclick="return confirm('Vill du radera barnet ur databasen?');">
            <img src='../images/page_delete.png' alt='Radera' /></a></td>
    <td><a title='Lägg till bok' href='?p=edit_book&amp;idChild={$row[0]}'><img src='../images/book_add.png' alt='Lägg till bok' /></a></td>
    <td><a title='Editera bok' href='?p=edit_book&amp;idBook={$row[5]}'><img src='../images/book_edit.png' alt='Editerra bok' /></a></td>
</tr>

HTMLCode;
    }
} else {
    $mainTextHTML .= <<<HTMLCode
<tr>
    <td></td><td>Det finns inga barn inlagda än.</td>
</tr>
HTMLCode;
}

$mainTextHTML .= <<<HTMLCode
</table>
<a title='Lägg till barn' href='?p=edit_child'>Lägg till barn</a>
HTMLCode;


///////////////////////////////////////////////////////////////////////////////////////////////////
// Generate the page

$page = new CHTMLPage(); 
$pageTitle = "Visa barn";

require(TP_PAGESPATH.'rightColumn.php'); // Genererar en högerkolumn i $rightColumnHTML
$page->printPage($pageTitle, $mainTextHTML, "", $rightColumnHTML);

?>

