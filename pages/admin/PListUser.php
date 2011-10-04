<?php

///////////////////////////////////////////////////////////////////////////////////////////////////
//
// PListUser.php (list_user)
// 
// The page lists alla users that correspond with the search criteria and adds buttons for
// register actions.
//
// Input: 'firstName', 'familyName', 'account'.
// Output:  'id'  
// 


///////////////////////////////////////////////////////////////////////////////////////////////////
// Check that the page is opened via index.php and that the user has the right authority.

$intFilter = new CAccessControl();
$intFilter->FrontControllerIsVisitedOrDie();
$intFilter->UserIsSignedInOrRedirectToSignIn(); 
$intFilter->UserIsAuthorisedOrDie('adm');


///////////////////////////////////////////////////////////////////////////////////////////////////
// Take care of input to the page.

$accountUser    = isset($_POST['account'])    ? $_POST['account']    : NULL;
$firstNameUser  = isset($_POST['firstName'])  ? $_POST['firstName']  : NULL;
$familyNameUser = isset($_POST['familyName']) ? $_POST['familyName'] : NULL;

if ($debugEnable) $debug .= $accountUser . $firstNameUser . $familyNameUser . "<br /> \n";


///////////////////////////////////////////////////////////////////////////////////////////////////
// Prepare the database and clean input.

$dbAccess           = new CdbAccess();
$tableUser          = DB_PREFIX . 'User';

$accountUser 		= $dbAccess->WashParameter($accountUser);
$firstNameUser 		= $dbAccess->WashParameter($firstNameUser);
$familyNameUser     = $dbAccess->WashParameter($familyNameUser);


///////////////////////////////////////////////////////////////////////////////////////////////////
// Query the database.

$query = "SELECT * FROM {$tableUser} ";
if      ($accountUser)      $query .= "WHERE accountUser     LIKE '%{$accountUser}%'";
elseif  ($familyNameUser)   $query .= "WHERE familyNameUser  LIKE '%{$familyNameUser}%'";
elseif  ($firstNameUser)    $query .= "WHERE firstNameUser   LIKE '%{$firstNameUser}%'";
$query .= " ORDER BY familyNameUser;";

$result=$dbAccess->SingleQuery($query);

///////////////////////////////////////////////////////////////////////////////////////////////////
// Prepare a table

// Headers
$mainTextHTML = <<<HTMLCode
<table>
<tr>
    <th>Id</th>
    <th>Användarnamn</th>
    <th>Behörighet</th>
    <th>Förnamn</th>
    <th>Efternamn</th>
</tr>
HTMLCode;

// Table
if ($result) {
    while($row = $result->fetch_row()) {
        if ($debugEnable) $debug .= "Query result: ".print_r($row, TRUE)."<br /> \n";
        $mainTextHTML .= <<<HTMLCode
<tr>
    <td>{$row[0]}</td>
    <td>{$row[1]}</td>
    <td>{$row[3]}</td>
    <td>{$row[4]}</td>
    <td>{$row[5]}</td>
    <td><a title='Visa' href='?p=show_user&amp;id={$row[0]}' ><img src='../images/page.png' alt='Visa' /></a></td>
    <td><a title='Editera' href='?p=edit_user&amp;id={$row[0]}'><img src='../images/page_edit.png' alt='Ändra' /></a></td>
    <td><a title='Konto' href='?p=edit_account&amp;id={$row[0]}'><img src='../images/page_key.png' alt='Konto' /></a></td>
    <td><a title='Radera' href='?p=del_user&amp;id={$row[0]}' onclick="return confirm('Vill du radera användaren ur databasen?');">
            <img src='../images/page_delete.png' alt='Radera' /></a></td>
</tr>

HTMLCode;
    }
    $result->close();

} else {
    $mainTextHTML .= <<<HTMLCode
<tr>
    <td></td><td>Inga sökresultat</td>
</tr>
HTMLCode;
}

// End the table
$mainTextHTML .= <<<HTMLCode
</table>

HTMLCode;



///////////////////////////////////////////////////////////////////////////////////////////////////
// Generate the page.

$page = new CHTMLPage(); 
$pageTitle = "Lista användare";

$page->printPage($pageTitle, $mainTextHTML);

?>

