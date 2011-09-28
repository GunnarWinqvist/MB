<?php

///////////////////////////////////////////////////////////////////////////////////////////////////
//
// index.php
// Detta är en Frontcontroller. Alla sidbyten sker via denna sida.
// Inparametern ?p= anger vilken sida som ska visas.
// T ex www.template.se/?p=main
// Det enda du har anledning att ändra på denna sida är listan längst ner.
//


///////////////////////////////////////////////////////////////////////////////////////////////////
// Filer och parametrar som är gemensamma för alla sidor på siten.
//
session_start();
require_once('config.php');


///////////////////////////////////////////////////////////////////////////////////////////////////
// Uppdatera besöksräknaren om det är en ny gäst.
//
/*
$hitCounter = implode("",file("counter.txt")); //Hämta räknarvärdet ur filen counter.txt.
if(!isset($_SESSION["hitCounter"])) { //Om det är den första sidan i en ny session.
    $hitCounter++; //Öka med 1.
    $fh = fopen('counter.txt', 'w');
    fwrite($fh, $hitCounter); //Skriv in det nya värdet i counter.txt.
    fclose($fh);
}
$_SESSION["hitCounter"] = str_pad($hitCounter, 5, "0", STR_PAD_LEFT);
*/

///////////////////////////////////////////////////////////////////////////////////////////////////
// Felhantering på eller av. Styrs av config.php
//
$debug = "";
$debugEnable = WS_DEBUG;
if ($debugEnable) error_reporting(E_ALL | E_STRICT);


///////////////////////////////////////////////////////////////////////////////////////////////////
// Startar en timer som kan visa hur lång tid det tog att få upp sidan. Styrs av config.php
//
if(WS_TIMER) {
	$gTimerStart = microtime(TRUE);
}
 
///////////////////////////////////////////////////////////////////////////////////////////////////
// Möjliggör autoload för alla klassfiler.
//
function __autoload($class_name) {
    require_once(TP_SOURCEPATH . $class_name . '.php');
}


///////////////////////////////////////////////////////////////////////////////////////////////////
// Input till sidan är 'p'.
//
$nextPage = isset($_GET['p']) ? $_GET['p'] : 'main';
if (WS_WORK) $nextPage = 'work';
if ($debugEnable) $debug .= "nextPage = " . $nextPage . "<br /> \n";


// Visa den efterfrågade sidan.
switch($nextPage) {	

    // Allmäna sidor
    case 'main':        require_once(TP_PAGESPATH . 'PMain.php');               break;
    case 'my_page':     require_once(TP_PAGESPATH . 'PMyPage.php');             break;
    case 'edit_child':  require_once(TP_PAGESPATH . 'PEditChild.php');          break;
    case 'save_child':  require_once(TP_PAGESPATH . 'PSaveChild.php');          break;
    case 'edit_book':   require_once(TP_PAGESPATH . 'PEditBook.php');           break;
    case 'save_book':   require_once(TP_PAGESPATH . 'PSaveBook.php');           break;
    case 'show_page':   require_once(TP_PAGESPATH . 'PShowPage.php');           break;


    
    // Administratörsidor
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

    // Installera databasen
    case 'dump_db':     require_once(TP_PAGESPATH . 'admin/PDumpDB.php');       break;
    case 'install_db':  require_once(TP_PAGESPATH . 'admin/PInstallDb.php');    break;
    case 'fill_db':     require_once(TP_PAGESPATH . 'admin/PFillDb.php');       break;
   
    // Loginhantering
    case 'login_ex':    require_once(TP_PAGESPATH . 'login/PLoginEx.php');      break;
    case 'logout':      require_once(TP_PAGESPATH . 'login/PLogout.php');       break;

    // Work in progres
    case 'work':        require_once(TP_PAGESPATH . 'PWork.php');               break;


    default:            require_once(TP_PAGESPATH . 'PMain.php');               break;}

?>