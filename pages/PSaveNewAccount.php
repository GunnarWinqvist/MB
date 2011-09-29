<?php

///////////////////////////////////////////////////////////////////////////////////////////////////
//
// PSaveAccount.php (save_account)
// 
// The page saves information for a new account and redirects to 'redirect'.
// Input: 'account', 'password1', 'password2', 'firstName', 'familyName', 'eMail' as POST's.
// 
// 


///////////////////////////////////////////////////////////////////////////////////////////////////
// Check that the page is opened via index.php and that the user has the right authority.

$intFilter = new CAccessControl();
$intFilter->FrontControllerIsVisitedOrDie();


///////////////////////////////////////////////////////////////////////////////////////////////////
// Prepare the database.

$dbAccess             = new CdbAccess();
$tableUser            = DB_PREFIX . 'User';


///////////////////////////////////////////////////////////////////////////////////////////////////
// Get account input for the user, clean the parameters, check if the user exists ($idUser is set) 
// and update the database.

$accountUser    = isset($_POST['account'])    ? $_POST['account']     : NULL;
$password1User  = isset($_POST['password1'])  ? $_POST['password1']   : NULL;
$password2User  = isset($_POST['password2'])  ? $_POST['password2']   : NULL;
$firstNameUser  = isset($_POST['firstName'])  ? $_POST['firstName']   : NULL;
$familyNameUser = isset($_POST['familyName']) ? $_POST['familyName']  : NULL;
$eMailUser      = isset($_POST['eMail'])      ? $_POST['eMail']       : NULL;


///////////////////////////////////////////////////////////////////////////////////////////////////
// Clean the input parameters.

$accountUser 	= $dbAccess->WashParameter(strip_tags($accountUser));
$password1User  = $dbAccess->WashParameter(strip_tags($password1User));
$password2User  = $dbAccess->WashParameter(strip_tags($password2User));
$firstNameUser  = $dbAccess->WashParameter(strip_tags($firstNameUser));
$familyNameUser = $dbAccess->WashParameter(strip_tags($familyNameUser));
$eMailUser      = $dbAccess->WashParameter(strip_tags($eMailUser));


///////////////////////////////////////////////////////////////////////////////////////////////////
// If the password is not entered or they are different then exit and go to edit_account.

if (!$password1User || ($password1User != $password2User)) { 
    $_SESSION['errorMessage'] = "Fel p� l�senordet!";
    header("Location: " . WS_SITELINK . "?p=main");
    exit;
}
// Validation of the other parameters can be done with QuickForm in a later release.


///////////////////////////////////////////////////////////////////////////////////////////////////
// Enter a new user in the database.

$query = <<<QUERY
INSERT INTO {$tableUser} (accountUser, passwordUser, authorityUser, firstNameUser, familyNameUser, eMail1User)
    VALUES ('{$accountUser}', md5('{$password1User}'), 'usr', '{$firstNameUser}', '{$familyNameUser}', '{$eMailUser}');
QUERY;

$dbAccess->SingleQuery($query);

/*
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
        $subject = "Nytt l�senord";
        $text = <<<Text
Din anv�ndarinformation till Svenska skolf�reningens hemsida.
Anv�ndarnamn: {$accountUser}
L�senord: {$password1User}

Du kan sj�lv logga in p� sidan och �ndra ditt l�senord.
Text;
        mail( $eMailAdr, $subject, $text);
    } else {
        $_SESSION['errorMessage'] = "Ingen mejladress att skicka l�senordet till!";
    }
}

// Everything went fine so far. Redirect to show user.
$redirect = "show_user&amp;id=".$idUser;
*/


///////////////////////////////////////////////////////////////////////////////////////////////////
// Build the page.

$pageTitle = "Spara ny anv�ndare";
$mainTextHTML = <<<HTMLCode
<h2>V�lkommen {$firstNameUser}!</h2>
<p>Du har just skapat ett nytt anv�ndarkonto p� Min bok med f�ljande information:</p>
<ul>
<li>Anv�ndarnamn: {$accountUser}</li>
<li>Namn: {$firstNameUser} {$familyNameUser}</li>
<li>E-postadress: {$eMailUser}</li>
</ul>
<p>Du kan nu prova att logga in.</p>
HTMLCode;

$page = new CHTMLPage(); 
$page->printPage($pageTitle, $mainTextHTML);

?>

