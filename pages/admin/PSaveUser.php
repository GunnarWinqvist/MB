<?php

///////////////////////////////////////////////////////////////////////////////////////////////////
//
// PSaveUser.php
// Called by 'save_user' from index.php.
// The page saves user information for idUser.
// Input: 'firstName', 'familyName', 'eMail1', 'eMail2', 'id', 'redirect' as POST.
// Output:  
// 


///////////////////////////////////////////////////////////////////////////////////////////////////
// Check that the page is reached from the front controller and authority etc.

$intFilter = new CAccessControl();
$intFilter->FrontControllerIsVisitedOrDie();
$intFilter->UserIsSignedInOrRedirectToSignIn();
$intFilter->UserIsAuthorisedOrDie('adm');


///////////////////////////////////////////////////////////////////////////////////////////////////
// Prepare the database and clean input and query the database.
//

$dbAccess           = new CdbAccess();
$tableUser          = DB_PREFIX . 'User';

$idUser             = isset($_POST['id'])          ? $_POST['id']          : NULL;
$firstNameUser      = isset($_POST['firstName'])   ? $_POST['firstName']   : NULL;
$familyNameUser    = isset($_POST['familyName']) ? $_POST['familyName'] : NULL;
$eMail1User         = isset($_POST['eMail1'])      ? $_POST['eMail1']      : NULL;
$eMail2User         = isset($_POST['eMail2'])      ? $_POST['eMail2']      : NULL;
$redirect           = isset($_POST['redirect'])    ? $_POST['redirect']    : NULL;

$idUser 		    = $dbAccess->WashParameter($idUser);
$firstNameUser 	    = $dbAccess->WashParameter(strip_tags($firstNameUser));
$familyNameUser    = $dbAccess->WashParameter(strip_tags($familyNameUser));
$eMail1User 	    = $dbAccess->WashParameter(strip_tags($eMail1User));
$eMail2User         = $dbAccess->WashParameter(strip_tags($eMail2User));

$query = <<<QUERY
UPDATE {$tableUser} SET 
    firstNameUser   = '{$firstNameUser}',
    familyNameUser = '{$familyNameUser}',
    eMail1User     = '{$eMail1User}',
    eMail2User     = '{$eMail2User}'
    WHERE idUser = '{$idUser}';
QUERY;

$dbAccess->SingleQuery($query);


///////////////////////////////////////////////////////////////////////////////////////////////////
// Redirect to another page
//

// If in debug mode show info and exit.
if ($debugEnable) {
    echo $debug;
    exit();
}

header("Location: " . WS_SITELINK . "?p={$redirect}");
exit;


?>

