<?php

/**
 * @DWZ Component
 * @Copyright (C) 2012 Fred Baumgarten. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://sv-hennef.de
 * @author Fred Baumgarten
 * @email dc6iq@gmx.de
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

if (!defined("DS")) {
	define('DS', DIRECTORY_SEPARATOR);
}

$controllerName = 'info';

require_once( JPATH_COMPONENT.DS.'controllers'.DS.$controllerName.'.php' );
$controllerName = 'CLM_DWZController'.$controllerName;

// Create the controller
$controller = new $controllerName();

// Perform the Request task
$controller->execute( JRequest::getCmd('task') );

// Redirect if set by the controller
$controller->redirect();

?>
