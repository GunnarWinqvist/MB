<?php

///////////////////////////////////////////////////////////////////////////////////////////////////
//
// config.php
// Konfigurationsfil med parametrar som g�ller f�r hela denna website.
// Anropas varje g�ng man passerar index.php.
//


///////////////////////////////////////////////////////////////////////////////////////////////////
// Databasparametrar
// F�s fr�n webplatsleverant�ren.
//

define('DB_HOSTNAME', 'localhost');
define('DB_USERNAME', 'Gunnar');
define('DB_PASSWORD', 'passord');
define('DB_DATABASE', 'min_bok');

define('DB_PREFIX', 	'min_bok_'); //Prefix f�r att kunna anv�nda flera databasar p� en webplats.


///////////////////////////////////////////////////////////////////////////////////////////////////
// Website-gemensamma parametrar.
//
define('WS_SITELINK',       'http://localhost/');         // Adressen till webplatsens huvudsida.
define('WS_TITLE', 			'Template');            // Namn p� webplatsen.
define('WS_STYLESHEET', 	'style/stylesheetPlain.css'); // Vilket stylesheet vill du anv�nda.	
define('WS_FAVICON', 	    'images/favicon.ico');        // Pekar p� flikiconen.
define('WS_FOOTER', 		"Dispangul�r har gjort den h�r web-platsen.");
define('WS_CHARSET', 	    'windows-1252');              // Ange charset. windows-1252=svenska tecken
define('WS_LANGUAGE',       'se');                         // Defaultspr�k svenska.

define('WS_DEBUG',          TRUE);                      // Visa debug-information    
define('WS_VALIDATORS',     FALSE);	                    // Visa l�nkar till w3c validators tools.
define('WS_TIMER', 		    FALSE);                      // Visa timer f�r sidgenerering.
define('WS_WORK', 		    FALSE);                      // Arbete med siten p�g�r.

define('TP_ROOTPATH', 	    dirname(__FILE__) . '/');        // Klasser, funktioner, kod
define('TP_SOURCEPATH', 	dirname(__FILE__) . '/src/');    // Klasser, funktioner, kod
define('TP_PAGESPATH', 	    dirname(__FILE__) . '/pages/');  // Pagecontrollers och moduler
define('TP_IMAGESPATH',     dirname(__FILE__) . '/images/'); // Bilder och grafik.
define('TP_DOCUMENTSPATH',  dirname(__FILE__) . '/documents/'); // Dokument.
//define('TP_PEARPATH',       dirname(__FILE__) . '/src/pear/PEAR/'); // Om PEAR-biblioteket �r lokalt installerat.
define('TP_PEARPATH',       FALSE); // Om PEAR-biblioteket �r centralt installerat.


///////////////////////////////////////////////////////////////////////////////////////////////////
// Meny-inneh�ll i array.
// �ndringar m�ste g�ras i index.php samtidigt.
//
$menuElements = Array (
    'Framsidan'     => 'main',
    'Min sida'       => 'my_page',
    'Rubrik 2'        => 'main',
    'Rubrik 3'         => 'main',
    'Rubrik 4'       => 'main',
);
define('WS_MENU', serialize($menuElements)); // G�r om menyelementen till en global konstant.


?>