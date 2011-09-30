<?php

///////////////////////////////////////////////////////////////////////////////////////////////////
//
// PSaveChild.php (save_child)
// 
// The page saves information from PEditChild for the child id.
//
// Input: 'firstName', 'familyNamn', 'birthDate', 'id', 'redirect' as POST.
// Output:  
// 


///////////////////////////////////////////////////////////////////////////////////////////////////
// Check that the page is opened via index.php and that the user has the right authority.

$intFilter = new CAccessControl();
$intFilter->FrontControllerIsVisitedOrDie();
$intFilter->UserIsSignedInOrRedirectToSignIn();


///////////////////////////////////////////////////////////////////////////////////////////////////
// Prepare the database and clean input.

$dbAccess           = new CdbAccess();
$tableChild         = DB_PREFIX . 'Child';

$idChild             = isset($_POST['id'])          ? $_POST['id']          : NULL;
$firstNameChild      = isset($_POST['firstName'])   ? $_POST['firstName']   : NULL;
$familyNameChild    = isset($_POST['familyNamn']) ? $_POST['familyNamn'] : NULL;
$birthDateChild      = isset($_POST['birthDate'])   ? $_POST['birthDate']   : NULL;
$redirect            = isset($_POST['redirect'])    ? $_POST['redirect']    : NULL;

$idChild 		     = $dbAccess->WashParameter($idChild);
$firstNameChild 	 = $dbAccess->WashParameter(strip_tags($firstNameChild));
$familyNameChild     = $dbAccess->WashParameter(strip_tags($familyNameChild));
$birthDateChild 	 = $dbAccess->WashParameter(strip_tags($birthDateChild));
$idUser              = $_SESSION['idUser'];


///////////////////////////////////////////////////////////////////////////////////////////////////
// Check that it is the user's child and query the database.

if ($idChild) { // If the child exists check that it's the users child and update the database.
    $query = "SELECT child_idUser FROM {$tableChild} WHERE idChild = {$idChild};";
    $result = $dbAccess->SingleQuery($query);
    $row = $result->fetch_object();
    $result->close();
    if ($idUser != $row->child_idUser) {
        $_SESSION['errorMessage']      = "Du har inte behörighet att ändra informationen om det här barnet";
        header('Location: ' . WS_SITELINK . "?p=main");
        exit;
    }
    $query = <<<QUERY
UPDATE {$tableChild} SET 
    firstNameChild   = '{$firstNameChild}',
    familyNameChild  = '{$familyNameChild}',
    birthDateChild     = '{$birthDateChild}'
    WHERE idChild = '{$idChild}';
QUERY;
} else { // Else enter a new child.
    $query = <<<QUERY
INSERT INTO {$tableChild} (firstNameChild, familyNameChild, birthDateChild, child_idUser)
    VALUES ('{$firstNameChild}', '{$familyNameChild}', '{$birthDateChild}', '{$idUser}');
QUERY;
}

$dbAccess->SingleQuery($query);

// If $idChild is empty then it's a new child. Get the id.
if (!$idChild)  $idChild = $dbAccess->LastId();
if ($debugEnable) $debug .= "idChild: " . $idChild . "<br /> \n";


///////////////////////////////////////////////////////////////////////////////////////////////////
// Redirect to another page

// If in debug mode show info and exit.
if ($debugEnable) {
    echo $debug;
    exit();
}

header("Location: " . WS_SITELINK . "?p={$redirect}");
exit;


?>

