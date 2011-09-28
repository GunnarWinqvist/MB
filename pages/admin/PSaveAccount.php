<?php

///////////////////////////////////////////////////////////////////////////////////////////////////
//
// PSaveAccount.php
// Called by 'save_account' from index.php.
// The page saves account information and redirects to 'redirect'.
// Input: 'id', 'account', 'password1', 'password2', 'authority', 'send', 'redirect' som POSTs.
// Output: 'id'
// 


///////////////////////////////////////////////////////////////////////////////////////////////////
// Check that the page is reached from the front controller and authority etc.

$intFilter = new CAccessControl();
$intFilter->FrontControllerIsVisitedOrDie();
$intFilter->UserIsSignedInOrRedirectToSignIn();
$intFilter->UserIsAuthorisedOrDie('adm');


///////////////////////////////////////////////////////////////////////////////////////////////////
// Prepare the database.

$dbAccess             = new CdbAccess();
$tableUser            = DB_PREFIX . 'User';


///////////////////////////////////////////////////////////////////////////////////////////////////
// Get account input for the user, clean the parameters, check if the user exists ($idUser is set) 
// and update the database.

$idUser         = isset($_POST['id'])         ? $_POST['id']          : NULL;
$accountUser    = isset($_POST['account'])    ? $_POST['account']     : NULL;
$password1User  = isset($_POST['password1'])  ? $_POST['password1']   : NULL;
$password2User  = isset($_POST['password2'])  ? $_POST['password2']   : NULL;
$authorityUser  = isset($_POST['authority'])  ? $_POST['authority']   : NULL;
$redirect       = isset($_POST['redirect'])   ? $_POST['redirect']    : NULL;
$send           = isset($_POST['send'])       ? $_POST['send']        : NULL;


// Clean the input parameters.
$idUser 		= $dbAccess->WashParameter($idUser);
$accountUser 	= $dbAccess->WashParameter(strip_tags($accountUser));
$authorityUser  = $dbAccess->WashParameter(strip_tags($authorityUser));
$password1User  = $dbAccess->WashParameter(strip_tags($password1User));


// If the password is not entered or they are different then exit and go to edit_account.
if (!$idUser) $redirect = "edit_account";
if (!$password1User || ($password1User != $password2User)) { 
    $_SESSION['errorMessage'] = "Fel på lösenordet!";
    header("Location: " . WS_SITELINK . $redirect);
    exit;
}


if ($idUser) { // If the user exists update the database.
    $query = <<<QUERY
UPDATE {$tableUser} SET 
    accountUser   = '{$accountUser}',
    passwordUser  = md5('{$password1User}'),
    authorityUser = '{$authorityUser}'
    WHERE idUser  = '{$idUser}';
QUERY;
} else { // Else enter a new user.
    $query = <<<QUERY
INSERT INTO {$tableUser} (accountUser, passwordUser, authorityUser)
    VALUES ('{$accountUser}', md5('{$password1User}'), '{$authorityUser}');
QUERY;
}
$dbAccess->SingleQuery($query);

// If $idUser is empty then it's a new user. Get the id.
if (!$idUser)  $idUser = $dbAccess->LastId();
if ($debugEnable) $debug .= "idUser: " . $idUser . "<br /> \n";

// Send the password in a mail if it is requested.
if ($send) {
    // Get the mail address.
    $query = "SELECT eMail1User, eMail2User FROM {$tableUser} WHERE idUser = '{$idUser}';";
    $result = $dbAccess->SingleQuery($query);
    $row = $result->fetch_object();
    $result->close();
    if ($row->eMail1User)     $eMailAdr = $row->eMail1User;
    elseif ($row->eMail2User) $eMailAdr = $row->eMail1User;
    else                      $eMailAdr = "";
    if ($eMailAdr) {
        $subject = "Nytt lösenord";
        $text = <<<Text
Din användarinformation till Svenska skolföreningens hemsida.
Användarnamn: {$accountUser}
Lösenord: {$password1User}

Du kan själv logga in på sidan och ändra ditt lösenord.
Text;
        mail( $eMailAdr, $subject, $text);
    } else {
        $_SESSION['errorMessage'] = "Ingen mejladress att skicka lösenordet till!";
    }
}

// Everything went fine so far. Redirect to show user.
$redirect = "show_user&amp;id=".$idUser;

///////////////////////////////////////////////////////////////////////////////////////////////////
// Redirect 

// If in debug mode exit before redirect.
if ($debugEnable) {
    echo $debug;
    exit();
}

header("Location: " . WS_SITELINK . "?p=" . $redirect);
exit;


?>

