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


/**
* CLMText
* Klassenbibliothek für text-bezogene Funktionalitäten
*/
class CLMText {

	
	
	/**
	* function composeHeadTitle
	* erstellt den Titel der Seite -> Browserzeile
	*
	*/
	function composeHeadTitle($text, $showSitename = TRUE, $showCLM = TRUE ) {
	
		// INIT
		$string = '';
		
		// Seitennamen anzeigen
		if ($showSitename) {
			$mf = JFactory::getApplication();
			$string .= $mf->getCfg('sitename').' - ';
		}
		
		// 'CLM' anzeigen
		if ($showCLM) {
			$string .= 'CLM - ';
		}
	
		// Text als Array geliefert
		if (is_array($text)) {
			$string .= implode(' / ', $text);
		} elseif ($text != '') { // oder einzelner String
			$string .= $text;
		} else {
			$string .= '...';
		}
	
		return $string;
	
	}
	
	
	
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
	* getResultString()
	* erzeugt Ergebnis-String
	*/
	function getResultString($erg, $length = 1) {
	
		$strShort = array("0", "1", "&frac12;", "0", "-", "+", "-", "---", "*");
		$strLong = array("0:1", "1:0", "&frac12;:&frac12;", "0:0", "-/+", "+/-", "-/-", "---", "*");
		
		switch ($length) {
			case 0:
				$string = $strShort[$erg];
				break;
			case 1:
			default:
				$string = $strLong[$erg];
		}
		
		return $string;
	
	}
	
	
	/**
	* getPosString()
	* erzeugt Platzierungs-String
	* $pos - übergibt Platz
	* $afterPoint - ob Platzzahl mit Ordnungspunkt ergänzt werden soll
	* $stringNoPos - überigibt String, falls keine Position vorliegt
	*/
	function getPosString($pos, $afterPoint = 1, $stringNoPos = "") {
	
		if ($pos > 0) {
			$string = $pos;
			switch ($afterPoint) {
				case 1:
					$string .= '.';
					break;
				case 2:
					$string .= '.&nbsp;'.JText::_('TOURNAMENT_POSITION');
					break;
				
			}
		} else {
			$string = $stringNoPos;
		}
		return $string;
	
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
			case 5: // Brettpunkte
				$format = "%01.1f";
				break;
			case 6: // Berliner Wertung
				$format = "%01.1f";
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
	* formatRating()
	* formatiert Wertungszahl
	*/
	function formatRating($rating) {
	
		if ($rating > 0) {
			return $rating;
		} else {
			return '-';
		}
	
	}
	
	
	
	/**
	* formatNote()
	* formatiert öffentliche Notiz
	*/
	function formatNote($text) {
	
		$string = nl2br(JFilterOutput::cleantext($text));
		
		return $string;
	
	}
	
	
	
	function addCatToName($addCatToName, $name, $catidAlltime, $catidEdition) {
	
		// init
		$catStrings = array();
		// get Tree
		list($this->parentArray, $this->parentKeys, $this->parentChilds) = CLMCategoryTree::getTree();
		if ($catidAlltime > 0) {
			$catStrings[] = $this->parentArray[$catidAlltime];
		}
		if ($catidEdition > 0) {
			$catStrings[] = $this->parentArray[$catidEdition];
		}
		// set
		$catName = implode(', ', $catStrings);
		// edit name
		if ($addCatToName == 1) {
			$string = $catName." - ".$name;
		} else {
			$string = $name." - ".$catName;
		}
	
		return $string;
	
	}
	
	
	
	
	function createCLMLink($string, $view, $params = array()) {
	
		$html = '<a href="index.php?option=com_clm&amp;view='.$view;
		
		// Params?
		if (count($params) > 0) {
			foreach ($params as $key => $value) {
				$html .= '&amp;'.$key.'='.$value;
			}
		}
		$html .= '">';
		$html .= $string;
		$html .= '</a>';
	
		return $html;
	
	}
	
	
	
	

}
?>