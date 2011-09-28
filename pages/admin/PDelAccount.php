<?php

///////////////////////////////////////////////////////////////////////////////////////////////////
//
// PDelAccount.php
// Called by 'del_account' from index.php.
// This page deletes a user from all tables.
// Input: 'idUser'
// Output:
// 


///////////////////////////////////////////////////////////////////////////////////////////////////
// Check that the page is reached from the front controller and authority etc.

$intFilter = new CAccessControl();
$intFilter->FrontControllerIsVisitedOrDie();
$intFilter->UserIsSignedInOrRedirectToSignIn();
$intFilter->UserIsAuthorisedOrDie('adm');


///////////////////////////////////////////////////////////////////////////////////////////////////
// Prepare the database.

$dbAccess               = new CdbAccess();
$tableUser            = DB_PREFIX . 'User';


///////////////////////////////////////////////////////////////////////////////////////////////////
// Take care of input to the page.

$idUser      = isset($_GET['id']) ? $_GET['id'] : NULL ;
$idUser      = $dbAccess->WashParameter($idUser);

if ($debugEnable) {
    $debug .= "Input: idUser=" . $idUser ."<br /> \n";
}

///////////////////////////////////////////////////////////////////////////////////////////////////
// Remove the user from all tables. 

$totalStatements = 1; 
$query = <<<QUERY
DELETE FROM {$tableUser} WHERE idUser = '{$idUser}';
QUERY;

// Uppdate with code for removing everything related to the user.

$statements = $dbAccess->MultiQueryNoResultSet($query);
if ($debugEnable) $debug .= "{$statements} statements av {$totalStatements} kördes.<br /> \n"; 


///////////////////////////////////////////////////////////////////////////////////////////////////
// Redirect to another page

// If in debug mode exit before redirect.
if ($debugEnable) {
    echo $debug;
    exit();
}

$redirect = "list_user";
header("Location: " . WS_SITELINK . "?p=" . $redirect);
exit;


?>

