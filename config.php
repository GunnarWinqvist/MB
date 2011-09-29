<?php

///////////////////////////////////////////////////////////////////////////////////////////////////
//
// config.php
// Configurations file with parameters for the website.
// Is called every time index is opened.
//



///////////////////////////////////////////////////////////////////////////////////////////////////
// Database parameters.

define('DB_HOSTNAME', 'localhost');
define('DB_USERNAME', 'Gunnar');
define('DB_PASSWORD', 'passord');
define('DB_DATABASE', 'min_bok');

define('DB_PREFIX', 	'min_bok_'); //Prefix f�r att kunna anv�nda flera databasar p� en webplats.


///////////////////////////////////////////////////////////////////////////////////////////////////
// Website parameters.

define('WS_SITELINK',       'http://localhost/');
define('WS_TITLE', 			'Min bok');
define('WS_STYLESHEET', 	'style/stylesheetPlain.css');
define('WS_FAVICON', 	    'images/favicon.ico');
define('WS_FOOTER', 		"Dispangulär har gjort den här web-platsen.");
define('WS_CHARSET', 	    'windows-1252');
define('WS_LANGUAGE',       'se');

define('WS_DEBUG',          TRUE);
define('WS_VALIDATORS',     FALSE);
define('WS_TIMER', 		    FALSE);
define('WS_WORK', 		    FALSE);
define('WS_HITCOUNTER',     FALSE);

define('TP_ROOTPATH', 	    dirname(__FILE__) . '/');
define('TP_SOURCEPATH', 	dirname(__FILE__) . '/src/');
define('TP_PAGESPATH', 	    dirname(__FILE__) . '/pages/');
define('TP_IMAGESPATH',     dirname(__FILE__) . '/images/');
define('TP_DOCUMENTSPATH',  dirname(__FILE__) . '/documents/');


?>