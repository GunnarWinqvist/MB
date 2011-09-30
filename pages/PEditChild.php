<?php

///////////////////////////////////////////////////////////////////////////////////////////////////
//
// PEditChild.php (edit_child)
// 
// The page generates a form for editing details of a child. If no id is provided a new child is 
// generated.
// From this page you are sent to PSaveChild and then to PMyPage.
//
// Input: 'id'
// Output: 'firstName', 'familyNamn', 'birthDate', 'id', 'redirect' as POST.
// 


///////////////////////////////////////////////////////////////////////////////////////////////////
// Check that the page is opened via index.php and that the user has the right authority.

$intFilter = new CAccessControl();
$intFilter->FrontControllerIsVisitedOrDie();
$intFilter->UserIsSignedInOrRedirectToSignIn();


///////////////////////////////////////////////////////////////////////////////////////////////////
// Prepare the database and clean input.

$dbAccess   = new CdbAccess();
$tableChild = DB_PREFIX . 'Child';

$idChild    = isset($_GET['id']) ? $_GET['id'] : NULL;
$idChild    = $dbAccess->WashParameter($idChild);

if ($debugEnable) $debug .= "Input: id=" . $idChild . "<br /> \n";


///////////////////////////////////////////////////////////////////////////////////////////////////
// Fetch the present information regarding the child if the child exists.

$aChild = array("","","","","",""); // Initiate arrayUser if we are going to generate a new account.

if ($idChild) {
    $query = "SELECT * FROM {$tableChild} WHERE idChild = {$idChild};";
    $result = $dbAccess->SingleQuery($query); 
    $aChild = $result->fetch_row();
    if ($debugEnable) $debug .= "Child = ".print_r($aChild, TRUE)."<br /> \n";
    $result->close();
}


///////////////////////////////////////////////////////////////////////////////////////////////////
// Generate a form for editing the user.

$mainTextHTML = <<<HTMLCode
<form name='child' action='?p=save_child' method='post'>
<table>
<tr><td>Förnamn</td>
<td><input type='text' name='firstName' size='40' maxlength='50' value='{$aChild[1]}' /></td></tr>
<tr><td>Efternamn</td>
<td><input type='text' name='familyNamn' size='40' maxlength='50' value='{$aChild[2]}' /></td></tr>
<tr><td>Födelsedatum</td>
<td><input type='text' name='birthDate' size='40' maxlength='50' value='{$aChild[3]}' /></td></tr>
</table>

HTMLCode;


// Add buttons and redirect.
$redirect = "my_page";
$mainTextHTML .= <<<HTMLCode
<input type='image' title='Spara' src='../images/b_enter.gif' alt='Spara' />
<a title='Cancel' href='?p={$redirect}' ><img src='../images/b_cancel.gif' alt='Cancel' /></a>
<input type='hidden' name='id' value='{$idChild}' />
<input type='hidden' name='redirect' value='{$redirect}' />

</form>
HTMLCode;



///////////////////////////////////////////////////////////////////////////////////////////////////
// Generate the page

$page = new CHTMLPage(); 
$pageTitle = "Editera barn";

$page->printPage($pageTitle, $mainTextHTML);

?>

