<?php
/**
*	name					autoload.inc.php
*	description			autoload
*
*	start					01.12.2010
*	last edit			01.12.2010
*	done					start
*
*	complete				no
*	todo					-
*	wanted				-
*	notes					-
*
*	author				Helge Frowein
*	(c)					2010
*/
// Klassen in der Komponente
function dwzliste_classes_autoload ($classname) {
  
  $filename = JPATH_COMPONENT.DS.'classes'.DS.$classname.'.class.php';
  
  // sonst sucht Joomla View-Klassen, etc.
  if (is_file($filename)) {
		require_once ($filename);
	}
}

spl_autoload_register('__autoload');
spl_autoload_register('dwzliste_classes_autoload');

?>