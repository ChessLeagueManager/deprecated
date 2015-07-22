<?php

/**
 * @ DWZ Component
 * @Copyright (C) 2012 Fred Baumgarten. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://sv-hennef.de
 * @author Fred Baumgarten
 * @email dc6iq@gmx.de
*/

// kein direkter Zugriff
defined('_JEXEC') or die('Restricted access');

if (!defined("DS")) {
	define('DS', DIRECTORY_SEPARATOR);
}

// laden des Joomla! Basis Controllers
require_once (JPATH_COMPONENT.DS.'controller.php');

$controller 	= JRequest::getVar( 'controller');

// laden von weiteren Controllern
if($controller = JRequest::getVar('controller')) {
	$path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
	if (file_exists($path)) {
		require_once $path;
	} else {
		$controller = '';
	}
}
// Erzeugen eines Objekts der Klasse controller
$classname	= 'CLM_DWZController'.ucfirst($controller);
$controller = new $classname( );

// den request task ausleben
$controller->execute(JRequest::getCmd('task'));

// Redirect aus dem controller
$controller->redirect();

?>
