<?php

///////////////////////////////////////////////////////////////////////////////////////////////////
//
// PLoginEx.php (login_ex)
// 
// This page performes a login and is called from the form in the menu bar.
// Input: 'account', 'password', 'redirect.
// 


///////////////////////////////////////////////////////////////////////////////////////////////////
// Check that the page is reached from the front controller.

$intFilter = new CAccessControl();
$intFilter->FrontControllerIsVisitedOrDie();


///////////////////////////////////////////////////////////////////////////////////////////////////
// Kill old sessions

// Save hitCounter before we kill the session to avoid double count of the visit.
if (WS_HITCOUNTER) $hitCounter = $_SESSION["hitCounter"]; 
require_once(TP_SOURCEPATH . 'FDestroySession.php');


///////////////////////////////////////////////////////////////////////////////////////////////////
// Take care of input to the page.

$accountUser =  isset($_POST['account'])  ? $_POST['account']  : NULL;
$passwordUser = isset($_POST['password']) ? $_POST['password'] : NULL;
$redirect =     isset($_POST['redirect']) ? $_POST['redirect'] : 'main';

if ($debugEnable) $debug.="Input: account={$accountUser} password={$passwordUser} redirect={$redirect}<br /> \n";

///////////////////////////////////////////////////////////////////////////////////////////////////
// Prepare the database.

$dbAccess           = new CdbAccess();
$tableUser          = DB_PREFIX . 'User';


///////////////////////////////////////////////////////////////////////////////////////////////////
// Clean input.
$accountUser 	= $dbAccess->WashParameter($accountUser);
$passwordUser   = $dbAccess->WashParameter($passwordUser);


///////////////////////////////////////////////////////////////////////////////////////////////////
// Check if an account with this password exists in the database and if so start a new session with 
// userId, userPassword and authority.

$query = <<<Query
SELECT * FROM {$tableUser}
WHERE
	accountUser   = '{$accountUser}' AND
	passwordUser 	= md5('{$passwordUser}')
;
Query;

// Restart the session after closing above.
session_start(); 
session_regenerate_id();

// Set hitCounter again so a login doesn't count as a new visitor.
if (WS_HITCOUNTER) $_SESSION["hitCounter"] = $hitCounter; 

if ($result=$dbAccess->SingleQuery($query)) {
    $row = $result->fetch_object();
    if ($debugEnable) $debug .= print_r($row, TRUE);
    $_SESSION['idUser']            = $row->idUser;
    $_SESSION['accountUser']       = $row->accountUser;  
    $_SESSION['firstNameUser']     = $row->firstNameUser;
    $_SESSION['familyNameUser']   = $row->familyNameUser;
    $_SESSION['authorityUser']     = $row->authorityUser;
    $result->close();
} else {
    $_SESSION['errorMessage']      = "Inloggningen misslyckades";
    $redirect                      = 'main';
}


///////////////////////////////////////////////////////////////////////////////////////////////////
// Redirect to another page

// If in debug mode show debug and then exit before redirect.
if ($debugEnable) {
    echo $debug;
    exit();
}

header('Location: ' . WS_SITELINK . "?p={$redirect}");
exit;

?>

