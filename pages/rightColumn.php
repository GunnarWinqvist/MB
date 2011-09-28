<?php

///////////////////////////////////////////////////////////////////////////////////////////////////
//
// rightColumn.php
// I den h�r filen �r all kod samlad som genererar h�gerkolumnen f�r alla sidor.
// 

$rightColumnHTML = "";

// Om anv�ndaren �r inloggad s� h�lsa v�lkommen och l�gg till knappar.
if (isset($_SESSION['idUser'])) {
    $rightColumnHTML .= <<<HTMLCode
<div class='login'>
<h2>V�lkommen</h2>
<h3>{$_SESSION['firstNameUser']}</h3>
<ul>
<li><a href='?p=logout'>Logga ut</a></li>
</ul>
HTMLCode;


    
    // Om anv�ndaren �r administrat�r s� l�gg till knappar.
    if ($_SESSION['authorityUser'] == "adm") {
       $rightColumnHTML .= <<<HTMLCode
<h3>Administrat�r</h3>
<a href='?p=admin'>Admin</a>

HTMLCode;
    }
        
// Annars erbjud att logga in.
} else {
    $redirect = $nextPage;
    $rightColumnHTML .= <<<HTMLCode
<div class='login'>
<form name='loginForm' action='?p=login_ex' method='post'>
<input type='hidden' name='redirect' value='{$redirect}' />
<table>
<tr><td><h3>Inloggning</h3></td></tr>
<tr><td>Anv�ndarnamn</td></tr>
<tr><td><input type='text' name='account' size='20' maxlength='20' value='' /></td></tr>
<tr><td>L�senord</td></tr>
<tr><td><input type='password' name='password' size='20' maxlength='32' value='' /></td></tr>
<tr><td><input type='submit' title='Logga in' value='Logga in' />
<a title='Gl�mt?' href='?p=new_passw1'>Gl�mt?</a>
</td></tr>
</table>
</form>

HTMLCode;
}
//<input class='button' type='submit' value="<span>Logga in</span>" />

$rightColumnHTML .= "</div>";





?>