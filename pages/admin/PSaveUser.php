<?php

///////////////////////////////////////////////////////////////////////////////////////////////////
//
// PSaveUser.php (save_user)
// 
// The page saves user information for idUser.
// Input: 'firstName', 'familyName', 'eMail1', 'eMail2', 'id', 'redirect' as POST.
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
$tableUser          = DB_PREFIX . 'User';

$idUser             = isset($_POST['id'])          ? $_POST['id']          : NULL;
$firstNameUser      = isset($_POST['firstName'])   ? $_POST['firstName']   : NULL;
$familyNameUser     = isset($_POST['familyName'])  ? $_POST['familyName']  : NULL;
$eMail1User         = isset($_POST['eMail1'])      ? $_POST['eMail1']      : NULL;
$eMail2User         = isset($_POST['eMail2'])      ? $_POST['eMail2']      : NULL;
$redirect           = isset($_POST['redirect'])    ? $_POST['redirect']    : NULL;

$idUser 		    = $dbAccess->WashParameter($idUser);
$firstNameUser 	    = $dbAccess->WashParameter(strip_tags($firstNameUser));
$familyNameUser     = $dbAccess->WashParameter(strip_tags($familyNameUser));
$eMail1User 	    = $dbAccess->WashParameter(strip_tags($eMail1User));
$eMail2User         = $dbAccess->WashParameter(strip_tags($eMail2User));


///////////////////////////////////////////////////////////////////////////////////////////////////
// Check if session user is NOT owner of page and NOT adm. If so give message and exit.

if (($_SESSION['idUser'] != $idUser) AND ($_SESSION['authorityUser'] != 'adm')) {
    $_SESSION['errorMessage']      = "Du har inte behörighet att titta på den här användaren!";
    header('Location: ' . WS_SITELINK . "?p=main");
    exit;
    }


///////////////////////////////////////////////////////////////////////////////////////////////////
// Query the database.

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

// If in debug mode show info and exit.
if ($debugEnable) {
    echo $debug;
    exit();
}

header("Location: " . WS_SITELINK . "?p={$redirect}");
exit;


?>

