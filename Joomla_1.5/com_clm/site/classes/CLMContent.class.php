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
* CLMContent 
* Klassenbibliothek für content-bezogene, modulare, Funktionalitäten
* keine Printausgabe, immer nur String-Rückgabe
*/
class CLMContent {

	
	/**
	* componentheading()
	* erstellt cpmponentheading
	*/
	function componentheading($text) {
	
		$string = '<div class="componentheading">';
		$string .= $text;
		$string .= '</div>';
	
		return $string;
	
	}
	
	
	function clmWarning($text) {
		
		$string = '<div id="wrong">';
		$string .= $text;
		$string .= '</div>';
	
		return $string;
	
	}
	
	
	/**
	* clmFooter()
	* erstellt clmFooter mit Versionsnummer und Link
	*/
	/*
	function clmFooter() {
	
		$Dir = JPATH_ADMINISTRATOR .DS. 'components'.DS.'com_clm';
		$data = JApplicationHelper::parseXMLInstallFile($Dir.DS.'clm.xml');
		
		$string = '<br /><br /><br /><hr>';
		$string .= '<div style="float:left; text-align:left; padding-left:1%">CLM '.$data['version'].'</div>';
		$string .= '<div style=" text-align:right; padding-right:1%">';
		$string .= '<label for="name" class="hasTip" title="'.JText::_('Das Chess League Manager (CLM) Projekt ist freie, kostenlose Software unter der GNU / GPL. Besuchen Sie unsere Projektseite www.fishpoke.de für die neueste Version, Dokumentationen und Fragen. Wenn Sie an der Entwicklung des CLM teilnehmen wollen melden Sie sich bei uns per E-mail. Wir sind für jede Hilfe dankbar !').'">Sie wollen am Projekt teilnehmen oder haben Verbesserungsvorschläge? - <a href="http://www.fishpoke.de">CLM Projektseite</a></label></div>';
	
		return $string;
	
	}
	*/
	
	

}
?>