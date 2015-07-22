<?php
/**
*	name					DWZText.class.php
*	description			Klassenbibliothek Text
*
*	start					01.12.2010
*	last edit			05.12.2010
*	done					neu: printCreatedBy
*
*	complete				no
*	todo					-
*	wanted				-
*	notes					-
*
*	author				Helge Frowein
*	(c)					2010 VTT Champions
*/

// Klassenbibliothek DWZText für verschiedene textbezogene Funktionalitäten
class DWZText {

	
	
	function formatName ($name, $param = 0) {
	
		$stringName = '';
		
		// TODO: check auf 'Jun.', 'Sen.'
		if ($param == 0) {
			$stringName .= utf8_encode($name);
		} elseif ($param == 1) {
			$stringName .= utf8_encode(str_replace(",", ", ", $name));
		} else {
			$name_parts = explode(",", $name);
			// evtl Dr.-Titel, etc.
			if (isset($name_parts[2])) {
				$stringName .= utf8_encode($name_parts[2])."&nbsp;";
			}
			$stringName .= utf8_encode($name_parts[1])."&nbsp;".utf8_encode($name_parts[0]);
		}
	
		return $stringName;
	
	}
	
	
	function formatLastEvaluation ($lastevaluation) {
	
		if (is_numeric($lastevaluation)) { // Zahl
			$string = substr($lastevaluation, 4, 2).'/'.substr($lastevaluation, 0, 4);
		} else {
			$string = '-';
		}
	
		return $string;
	
	}
	
	
	function printCreatedBy () {
	
		echo '<center>erstellt mit: <a href="http://sourceforge.net/projects/dwzliste/" target="_blank">Joomla-Komponente \'DWZ-Liste\'</a></center>';
	
	}
	

}
?>