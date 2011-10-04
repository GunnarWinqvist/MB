<?php

///////////////////////////////////////////////////////////////////////////////////////////////////
//
// menu.php
// Menu definition to be used on every page. This code is called from CHTMLPage.php
// 


// First buttons for everyone.
$htmlMenu = <<<HTMLCode
<a class='nav' href='?p=main' title='Framsidan'>Framsidan</a> | 
<a class='nav' href='?p=main' title='Rubrik 2'>Rubrik 2</a> | 
HTMLCode;

// Buttons if you are logged in.
if (isset($_SESSION['idUser'])) {
    $htmlMenu .= <<<HTMLCode
<a class='nav' href='?p=my_page' title='Min sida'>Min sida</a> | 
<a class='nav' href='?p=show_user&amp;id={$_SESSION['idUser']}' title='Mitt konto'>Mitt konto</a> | 
HTMLCode;

// Buttons for admin.
    if ($_SESSION['authorityUser'] == 'adm') {
        $htmlMenu .= <<<HTMLCode
<a class='nav' href='?p=admin' title='Admin'>Admin</a> | 
HTMLCode;
    }
    
// Add welcome text.
    $htmlMenu .= <<<HTMLCode
Välkommen {$_SESSION['firstNameUser']}
<a class='nav' href='?p=logout' title='Logga ut'>Logga ut</a>
HTMLCode;


// If not logged in add buttons and login form.
} else {
    $htmlMenu .= <<<HTMLCode
<a class='nav' href='?p=create' title='Skapa konto'>Skapa konto</a>

<form name='loginForm' action='?p=login_ex' method='post'>
Användarnamn
<input type='text' name='account' size='20' maxlength='20' value='' />
Lösenord<input type='password' name='password' size='20' maxlength='32' value='' />
<input type='submit' title='Logga in' value='Logga in' />
<a class='nav' href='?p=new_passw1' title='Glömt?'>Glömt?</a>
<input type='hidden' name='redirect' value='my_page' />
</form>
HTMLCode;
}


?>