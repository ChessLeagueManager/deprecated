<?php
/**
*	name					admin.dwzliste.php
*	description			admin-File
*
*	start					30.11.2010
*	last edit			06.07.2011
*	done					ACL entfernt
*
*	complete				yes
*	todo					-
*	wanted				-
*	notes					-
*
*	author				Helge Frowein
*	(c)					2010-2011
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

// den Basis-Controller einbinden (com_*/controller.php)
require_once (JPATH_COMPONENT.DS.'controller.php');

// Require specific controller if requested (im hidden-field der adminForm!)
if( $controller = JRequest::getWord('controller') ) {
	$path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
	if (file_exists($path)) {
		require_once $path;
	} else {
		$controller = '';
	}
}

$classname  = 'DWZListeController'.$controller;
$controller = new $classname( ); // Instanziert

$controller->execute( JRequest::getVar('task'));

$controller->redirect();
?>