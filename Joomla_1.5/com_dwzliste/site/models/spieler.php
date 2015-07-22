<?php
/**
*	name					liste.php
*	description			Modelt für liste
*
*	start					30.11.2010
*	last edit			16.12.2010
*	done					Bugfix ZPS als String statt als Int
*
*	complete				no
*	todo					-
*	wanted				-
*	notes					-
*
*	author				Helge Frowein
*	(c)					2010 VTT Champions
*/
defined('_JEXEC') or die();

jimport('joomla.application.component.model');


class DWZListeModelSpieler extends JModel {
	
	
	function __construct() {
		
		parent::__construct();

		$this->params = &JComponentHelper::getParams( 'com_dwzliste' );

		$this->mglnr = JRequest::getString('mglnr', '001');
		$this->zps = JRequest::getString('zps', $this->params->get('zps', '22059'));

		$this->_getData();

	}
	
	
	
	function _getData() {
	
		
		$urlCSV = "http://www.schachbund.de/dwz/db/spieler-csv.php?zps=".$this->zps."-".$this->mglnr;
		
		$this->url = "http://www.schachbund.de/dwz/db/spieler.html?zps=".$this->zps."-".$this->mglnr;
		
		if (!$handle = fopen($urlCSV, "r")) { 
		
			JError::raiseNotice(100, JText::_('NO_CONNECTION'));
		
		} else {
			// INIT
			$counter = 0;
			$this->rows = array();
			while ($row = fgetcsv($handle, 500, "|")) {
			
				$counter++;
			
				// Zeile 1: Datum
				if ($counter == 1) {
					$this->date = $row[0];
				
				// Teile 2: Vereinsnummer, Mitgliedsnummer, Status, Name, Geschlecht, Geburtsjahr, FIDE-Titel, Woche der letzten Auswertung, DWZ, DWZ-Index
				} elseif ($counter == 2) {
					$this->playerData = $row;
				
				// Zeile 3: FIDE-Elo, Partien, Titel, ID, Land
				} elseif ($counter == 3) {
					$this->fideData = $row;
				
				// Zeile 4++: Eintragsnummer, Turniercode, Turniername, Punkte, Partien, Erwartungswert, Gegner, Leistung, DWZ, DWZ-Index
				} elseif ($row[0] != "") { // leere Zeilen ausscheiden
					$this->rows[] = $row;
				}


			}
		
		}
		
		
	}
	

}
?>
