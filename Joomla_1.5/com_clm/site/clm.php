<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

// kein direkter Zugriff
defined('_JEXEC') or die('Restricted access');


// lädt alle CLM-Klassen - quasi autoload
$classpath = dirname(__FILE__).DS.'classes';
foreach( JFolder::files($classpath) as $file ) {
	JLoader::register(str_replace('.class.php', '', $file), $classpath.DS.$file);
}


// laden des Joomla! Basis Controllers
require_once (JPATH_COMPONENT.DS.'controller.php');

$controller 		= JRequest::getVar( 'controller');

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
$classname	= 'CLMController'.ucfirst($controller);
$controller = new $classname( );

// den request task ausleben
$controller->execute(JRequest::getCmd('task'));

// Redirect aus dem controller
$controller->redirect();

?>