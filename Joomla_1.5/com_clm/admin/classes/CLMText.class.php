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


// Klassenbibliothek CLMText für verschiedene textbezogene Funktionalitäten
class CLMText {

	
	
	/**
	* function sgpl
	* weist einer vorgegebenen Zahl einen Singular- oder Pluraltext zu
	*
	*/
	function sgpl ($count, $text_sg, $text_pl, $complete_string = TRUE) {
	
		if ($count == 1) {
			$text_return = $text_sg;
		} else {
			$text_return = $text_pl;
		}
	
		if ($complete_string == TRUE) { // kompletter String!
			return $count."&nbsp;".$text_return;
		} else {
			return $text_return;
		}
			
	}
	
	/**
	* function tiebrFormat
	* erstellt formatierten String einer Feinwertung
	*
	*/
	function tiebrFormat ($tiebrID, $value) {
	
		switch ($tiebrID) {
			case 1: // buchholz
				$format = "%01.1f";
				break;
			case 2: // buchholz-Summe
				$format = "%01.1f";
				break;
			case 3: // sonneborn-berger
				$format = "%01.2f";
				break;
			case 4: // Anzahl Siege
				$format = "%01.0f";
				break;
			case 11: // Buchholz 1 Streichresultat
				$format = "%01.1f";
				break;
			case 12: // Buchholz-Summe 1 Streichresultat
				$format = "%01.1f";
				break;
			case 13: // sonneborn-berger 1 Streichresultat
				$format = "%01.2f";
				break;
		
		}
		
		return sprintf($format, $value);
	}	


	/**
	* function selectOpener
	* formatiert einen standartisierten String für Verwendung als Erklärung in einem Dropdown
	*
	*/
	function selectOpener($text) {
	
		return " - ".$text." - ";
	
	}

	
	function selectEntity($entity) {
	
		return JText::_('SELECT').' '.$entity;
	
	}
	
	
	/**
	* function stringOnchange
	* gibt für ein formularfeld eine JS-onchnage-Anweisung aus, wenn $filter == TRUE
	*
	*/
	function stringOnchange ($filter) {
	
		if ($filter) {
			return 'onchange="document.adminForm.submit();"';
		} else {
			return '';
		}
	
	}
	
	
	/**
	* function errorText
	* setzt einen Fehlertext zusammen
	*
	*/
	function errorText ($subject, $error) {
	
		return JText::_($subject).": ".JText::_('ERROR_'.$error);
	
	}
	
	

}
?>