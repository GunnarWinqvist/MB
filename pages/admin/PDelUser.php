<?php

///////////////////////////////////////////////////////////////////////////////////////////////////
//
// PDelUser.php (del_user)
// 
// This page deletes a user from all tables.
//
// Input: 'idUser'
// Output:
// 


///////////////////////////////////////////////////////////////////////////////////////////////////
// Check that the page is opened via index.php and that the user has the right authority.

$intFilter = new CAccessControl();
$intFilter->FrontControllerIsVisitedOrDie();
$intFilter->UserIsSignedInOrRedirectToSignIn();
$intFilter->UserIsAuthorisedOrDie('adm');


///////////////////////////////////////////////////////////////////////////////////////////////////
// Prepare the database and clean input.

$dbAccess           = new CdbAccess();
$tableUser          = DB_PREFIX . 'User';
$tableChild         = DB_PREFIX . 'Child';
$tableBook          = DB_PREFIX . 'Book';
$tablePage          = DB_PREFIX . 'Page';
$tableField         = DB_PREFIX . 'Field';
$tableRelation      = DB_PREFIX . 'Relation';

$idUser      = isset($_GET['id']) ? $_GET['id'] : NULL ;
$idUser      = $dbAccess->WashParameter($idUser);

if ($debugEnable) {
    $debug .= "Input: idUser=" . $idUser ."<br /> \n";
}

///////////////////////////////////////////////////////////////////////////////////////////////////
// Remove the user from all tables. 

$totalStatements = 6; 
$query = <<<QUERY
-- Delete all fields owned by idUser.
DELETE FROM {$tableField} USING {$tableChild} JOIN {$tableBook} JOIN {$tablePage} JOIN  {$tableField}
    WHERE {$tableField}.field_idPage = {$tablePage}.idPage 
    AND {$tablePage}.page_idBook = {$tableBook}.idBook 
    AND {$tableBook}.book_idChild = {$tableChild}.idChild 
    AND {$tableChild}.child_idUser = '{$idUser}';

-- Delete all pages owned by idUser.
DELETE FROM {$tablePage} USING {$tableChild} JOIN {$tableBook} JOIN {$tablePage}
    WHERE {$tablePage}.page_idBook = {$tableBook}.idBook 
    AND {$tableBook}.book_idChild = {$tableChild}.idChild 
    AND {$tableChild}.child_idUser = '{$idUser}';

-- Delete all books owned by idUser.
DELETE FROM {$tableBook} USING {$tableChild} JOIN {$tableBook}
    WHERE {$tableBook}.book_idChild = {$tableChild}.idChild 
    AND {$tableChild}.child_idUser = '{$idUser}';

-- Delete all children owned by idUser.
DELETE FROM {$tableChild} WHERE child_idUser = '{$idUser}';

-- Delete all guests related to idUser.
DELETE FROM {$tableRelation} WHERE relation_idUser = '{$idUser}';

-- Finally delete the user.
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

