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
* CLMImage
* Klassenbibliothek für bild-bezogene Funktionalitäten
*/
class CLMImage {

	
	/**
	* imageURL()
	* Stellt die URL eines Frontend-Images zusammen
	*/
	function imageURL($image) {
	
		$string = JURI::root().'components'.DS.'com_clm'.DS.'images'.DS.$image;
		
		return $string;
	
	}
	
}
?>