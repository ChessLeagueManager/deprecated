<?php
/**
*	name					dwzliste.php
*	description			Komponenten-Grunddatei
*
*	start					29.11.2010
*	last edit			01.12.2010
*	done					autoload
*
*	complete				no
*	todo					-
*	wanted				-
*	notes					-
*
*	author				Helge Frowein
*	(c)					2010
*/
// no direct access
defined('_JEXEC') or die('Restricted access');


// controller vorladen
require_once (JPATH_COMPONENT.DS.'controller.php');

// Autoload für das dynamische Laden benötigter Klassen
require_once (JPATH_COMPONENT.DS.'autoload.inc.php');

// Controller-Instanz aufrufen
$controller = new DWZListeController(); // Instanziert




// Perform the Request task
$controller->execute( JRequest::getVar('task', null, 'default', 'cmd') );


// Redirect if set by the controller
$controller->redirect();

?>