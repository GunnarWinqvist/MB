<?php

///////////////////////////////////////////////////////////////////////////////////////////////////
//
// index.php
// Detta �r en Frontcontroller. Alla sidbyten sker via denna sida.
// Inparametern ?p= anger vilken sida som ska visas.
// T ex www.template.se/?p=main
// Det enda du har anledning att �ndra p� denna sida �r listan l�ngst ner.
//


///////////////////////////////////////////////////////////////////////////////////////////////////
// Filer och parametrar som �r gemensamma f�r alla sidor p� siten.
//
session_start();
require_once('config.php');


///////////////////////////////////////////////////////////////////////////////////////////////////
// Uppdatera bes�ksr�knaren om det �r en ny g�st.
//
/*
$hitCounter = implode("",file("counter.txt")); //H�mta r�knarv�rdet ur filen counter.txt.
if(!isset($_SESSION["hitCounter"])) { //Om det �r den f�rsta sidan i en ny session.
    $hitCounter++; //�ka med 1.
    $fh = fopen('counter.txt', 'w');
    fwrite($fh, $hitCounter); //Skriv in det nya v�rdet i counter.txt.
    fclose($fh);
}
$_SESSION["hitCounter"] = str_pad($hitCounter, 5, "0", STR_PAD_LEFT);
*/

///////////////////////////////////////////////////////////////////////////////////////////////////
// Felhantering p� eller av. Styrs av config.php
//
$debug = "";
$debugEnable = WS_DEBUG;
if ($debugEnable) error_reporting(E_ALL | E_STRICT);


///////////////////////////////////////////////////////////////////////////////////////////////////
// Startar en timer som kan visa hur l�ng tid det tog att f� upp sidan. Styrs av config.php
//
if(WS_TIMER) {
	$gTimerStart = microtime(TRUE);
}
 
///////////////////////////////////////////////////////////////////////////////////////////////////
// M�jligg�r autoload f�r alla klassfiler.
//
function __autoload($class_name) {
    require_once(TP_SOURCEPATH . $class_name . '.php');
}


///////////////////////////////////////////////////////////////////////////////////////////////////
// Input till sidan �r 'p'.
//
$nextPage = isset($_GET['p']) ? $_GET['p'] : 'main';
if (WS_WORK) $nextPage = 'work';
if ($debugEnable) $debug .= "nextPage = " . $nextPage . "<br /> \n";


// Visa den efterfr�gade sidan.
switch($nextPage) {	

    // Allm�na sidor
    case 'main':        require_once(TP_PAGESPATH . 'PMain.php');               break;
    case 'my_page':     require_once(TP_PAGESPATH . 'PMyPage.php');             break;
    case 'edit_child':  require_once(TP_PAGESPATH . 'PEditChild.php');          break;
    case 'save_child':  require_once(TP_PAGESPATH . 'PSaveChild.php');          break;
    case 'edit_book':   require_once(TP_PAGESPATH . 'PEditBook.php');           break;
    case 'save_book':   require_once(TP_PAGESPATH . 'PSaveBook.php');           break;
    case 'show_page':   require_once(TP_PAGESPATH . 'PShowPage.php');           break;


    
    // Administrat�rsidor
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