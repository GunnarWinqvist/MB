<?php

///////////////////////////////////////////////////////////////////////////////////////////////////
//
// PLogout.php
// Called by 'logout' from index.php.
// This page performes a logout and send you to redirect.
// Input: 'redirect'
// 


///////////////////////////////////////////////////////////////////////////////////////////////////
// Check that the page is reached from the front controller.
//
$intFilter = new CAccessControl();
$intFilter->FrontControllerIsVisitedOrDie();


///////////////////////////////////////////////////////////////////////////////////////////////////
// Kill the session.
//
$hitCounter = $_SESSION["hitCounter"]; // Save hitCounter before we kill the session.
require_once(TP_SOURCEPATH . 'FDestroySession.php');

// Start a new session and reinitiate hitCounter so that a visitor isn't counted doubble.
session_start();
session_regenerate_id();
$_SESSION["hitCounter"] = $hitCounter;


///////////////////////////////////////////////////////////////////////////////////////////////////
// Redirect to another page if set.
//

// If in debug mode show debug and then exit before redirect.
if ($debugEnable) {
    echo "Logout genomförd.";
    exit();
}

$redirect = isset($_POST['redirect']) ? $_POST['redirect'] : 'main';
header('Location: ' . WS_SITELINK . "?p={$redirect}");
exit;

?>

