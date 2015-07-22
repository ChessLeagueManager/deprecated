<?php
/**
*	name					liste.php
*	description			Modelt für liste
*
*	start					30.11.2010
*	last edit			12.12.2010
*	done					Umbau date
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


class DWZListeModelListe extends JModel {
	
	
	function __construct() {
		
		parent::__construct();

		$this->params = &JComponentHelper::getParams( 'com_dwzliste' );

		$this->order['by'] = JRequest::getVar('filter_order', 'dwz');
		$this->order['dir'] = JRequest::getVar('filter_order_Dir', 'desc');

		$this->_getData();

	}
	
	
	
	function _getData() {
	
		$zps = $this->params->get('zps', 22059);
		
		$urlCSV = "http://www.deutscher-schachbund.de/dwz/db/verein-csv.php?zps=".$zps;
		
		$this->url = "http://www.schachbund.de/dwz/db/verein.html?zps=".$zps;
		
		if (!$handle = fopen($urlCSV, "r")) { 
		
			JError::raiseNotice(100, JText::_('NO_CONNECTION'));
		
		} else {
			$counter = 0;
			while ($row = fgetcsv($handle, 500, "|")) {
				
				$counter++;
			
				if ($counter == 1) { // datum
					$this->date = $row[0];
			
				} elseif ($row[0] != "") { // leere Zeilen ausscheiden
					$this->data[$counter] = $row;
					
				}
				// array enthält:
				// in Zeile 0: Datum der Aktualisierung
				// 0 => ZPS
				// 1 => MglNr
				// 2 => Status
				// 3 => Nachname,Vorname
				// 4 => Geschlecht (M/W)
				// 5 => Geburtsjahr
				// 6 => Titel
				// 7 => letzte Auswertung (JJJJWW)
				// 8 => DWZ
				// 9 => AUswertungen
				// 10 => ELO

			}
		
			// Sortierung
			// Obtain a list of columns
			foreach ($this->data as $key => $temp) {
				// dwz
				if (is_numeric($temp[8])) {
					$dwz[$key]  = $temp[8];
				} elseif ($temp[8] == 'Restpartien') {
					$dwz[$key] = 1;
				} else {
					$dwz[$key] = 0;
				}
				//name
				$name[$key] = $temp[3];
				// year
				$year[$key] = $temp[5];
				// evaluation
				$eval[$key] = $temp[7];
				// mascfem
				$mascfem[$key] = $temp[4];
				// status
				$status[$key] = $temp[2];
				//elo
				if (is_numeric($temp[10])) {
					$elo[$key]  = $temp[10];
				} else {
					$elo[$key]  = 0;
				}
			}
			
			switch ($this->order['by']) {
				case 'name':
					if ($this->order['dir'] == 'desc') {
						array_multisort($this->data, SORT_STRING, $name, SORT_DESC);
					} else {
						array_multisort($this->data, SORT_STRING, $name, SORT_ASC);
					}
					break;
				case 'mascfem':
					if ($this->order['dir'] == 'desc') {
						array_multisort($this->data, SORT_STRING, $mascfem, SORT_DESC, $dwz, SORT_DESC);
					} else {
						array_multisort($this->data, SORT_STRING, $mascfem, SORT_ASC, $dwz, SORT_DESC);
					}
					break;
				case 'status':
					if ($this->order['dir'] == 'desc') {
						array_multisort($this->data, SORT_STRING, $status, SORT_DESC, $dwz, SORT_DESC);
					} else {
						array_multisort($this->data, SORT_STRING, $status, SORT_ASC, $dwz, SORT_DESC);
					}
					break;
				case 'year':
					if ($this->order['dir'] == 'desc') {
						array_multisort($this->data, SORT_NUMERIC, $year, SORT_DESC, $dwz, SORT_DESC);
					} else {
						array_multisort($this->data, SORT_NUMERIC, $year, SORT_ASC, $dwz, SORT_DESC);
					}
					break;
				case 'eval':
					if ($this->order['dir'] == 'desc') {
						array_multisort($this->data, SORT_NUMERIC, $eval, SORT_DESC, $dwz, SORT_DESC);
					} else {
						array_multisort($this->data, SORT_NUMERIC, $eval, SORT_ASC, $dwz, SORT_DESC);
					}
					break;
				case 'elo':
					if ($this->order['dir'] == 'desc') {
						array_multisort($this->data, SORT_NUMERIC, $elo, SORT_DESC, $dwz, SORT_DESC);
					} else {
						array_multisort($this->data, SORT_NUMERIC, $elo, SORT_ASC, $dwz, SORT_DESC);
					}
					break;
				case 'dwz':
				default:
					if ($this->order['dir'] == 'desc') {
						array_multisort($this->data, SORT_NUMERIC, $dwz, SORT_DESC);
					} else {
						array_multisort($this->data, SORT_NUMERIC, $dwz, SORT_ASC);
					}
					break;
			
			}
		
		}
		
	}
	

}
?>
