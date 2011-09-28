<?php

///////////////////////////////////////////////////////////////////////////////////////////////////
//
// index.php
// This is the front controller for 'Min bok'. All pages is entered via this code.
// The in parameter ?p= followed by an index gives what page to show. E g www.template.se/?p=main .
//


///////////////////////////////////////////////////////////////////////////////////////////////////
// Files and parameters that are common for all pages on the site.

session_start();
require_once('config.php');


///////////////////////////////////////////////////////////////////////////////////////////////////
// If hit counter is chosen in config then update the visitors counter if it is a new guest.

if (WS_HITCOUNTER) {
    $hitCounter = implode("",file("counter.txt")); //Get the counter value from the fila counter.txt.
    if(!isset($_SESSION["hitCounter"])) { //If it is the first page in a new session.
        $hitCounter++; 
        $fh = fopen('counter.txt', 'w');
        fwrite($fh, $hitCounter); //Write the new value in counter.txt.
        fclose($fh);
    }
    $_SESSION["hitCounter"] = str_pad($hitCounter, 5, "0", STR_PAD_LEFT);
}


///////////////////////////////////////////////////////////////////////////////////////////////////
// Debug handling on or off. Set in config.php

$debug = "";
$debugEnable = WS_DEBUG;
if ($debugEnable) error_reporting(E_ALL | E_STRICT);


///////////////////////////////////////////////////////////////////////////////////////////////////
// Start a timer to show how long it takes to open the page. Set in config.php

if(WS_TIMER) {
	$gTimerStart = microtime(TRUE);
}
 
///////////////////////////////////////////////////////////////////////////////////////////////////
// Enable autoload for all class files.

function __autoload($class_name) {
    require_once(TP_SOURCEPATH . $class_name . '.php');
}


///////////////////////////////////////////////////////////////////////////////////////////////////
// Input to the page is 'p'.

$nextPage = isset($_GET['p']) ? $_GET['p'] : 'main';
if (WS_WORK) $nextPage = 'work'; //Rerout to work sign when the site is closed. Set in config.php.
if ($debugEnable) $debug .= "nextPage = " . $nextPage . "<br /> \n";


// Show the requested page.
switch($nextPage) {	

    // Common pages
    case 'main':        require_once(TP_PAGESPATH . 'PMain.php');               break;
    case 'my_page':     require_once(TP_PAGESPATH . 'PMyPage.php');             break;
    case 'edit_child':  require_once(TP_PAGESPATH . 'PEditChild.php');          break;
    case 'save_child':  require_once(TP_PAGESPATH . 'PSaveChild.php');          break;
    case 'edit_book':   require_once(TP_PAGESPATH . 'PEditBook.php');           break;
    case 'save_book':   require_once(TP_PAGESPATH . 'PSaveBook.php');           break;
    case 'show_page':   require_once(TP_PAGESPATH . 'PShowPage.php');           break;

    // Administrator pages
    case 'admin':       require_once(TP_PAGESPATH . 'admin/PAdmin.php');        break;
    case 'list_user':   require_once(TP_PAGESPATH . 'admin/PListUser.php');     break;
    case 'search_user': require_once(TP_PAGESPATH . 'admin/PSearchUser.php');   break;
    case 'show_user':   require_once(TP_PAGESPATH . 'admin/PShowUser.php');     break;
    case 'edit_user':   require_once(TP_PAGESPATH . 'admin/PEditUser.php');     break;
    case 'save_user':   require_once(TP_PAGESPATH . 'admin/PSaveUser.php');     break;
    case 'edit_account':require_once(TP_PAGESPATH . 'admin/PEditAccount.php');  break;
//    case 'edit_passw':  require_once(TP_PAGESPATH . 'admin/PEditPassword.php'); break;
//    case 'new_passw1':  require_once(TP_PAGESPATH . 'admin/PNewPassw1.php');    break;
//    case 'new_passw2':  require_once(TP_PAGESPATH . 'admin/PNewPassw2.php');    break;
    case 'save_account':require_once(TP_PAGESPATH . 'admin/PSaveAccount.php');  break;
    case 'del_account': require_once(TP_PAGESPATH . 'admin/PDelAccount.php');   break;

    // Install the databasen
    case 'dump_db':     require_once(TP_PAGESPATH . 'admin/PDumpDB.php');       break;
    case 'install_db':  require_once(TP_PAGESPATH . 'admin/PInstallDb.php');    break;
    case 'fill_db':     require_once(TP_PAGESPATH . 'admin/PFillDb.php');       break;
   
    // Login handling
    case 'login_ex':    require_once(TP_PAGESPATH . 'login/PLoginEx.php');      break;
    case 'logout':      require_once(TP_PAGESPATH . 'login/PLogout.php');       break;

    // Work in progres
    case 'work':        require_once(TP_PAGESPATH . 'PWork.php');               break;


    default:            require_once(TP_PAGESPATH . 'PMain.php');               break;}

?>