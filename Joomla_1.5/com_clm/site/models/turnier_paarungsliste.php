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

defined('_JEXEC') or die();

jimport('joomla.application.component.model');


class CLMModelTurnier_Paarungsliste extends JModel {
	
	
	function __construct() {
		
		parent::__construct();

		$this->turnierid = JRequest::getInt('turnier', 0);

		$this->_getTurnierData();

		$this->_getTurnierPlayers();

		$this->_getTurnierRounds();
		
		$this->_getTurnierMatches();

	}
	
	
	
	function _getTurnierData() {
	
		$query = "SELECT *"
			." FROM #__clm_turniere"
			." WHERE id = ".$this->turnierid
			;
		$this->_db->setQuery( $query );
		$this->turnier = $this->_db->loadObject();

		// TO-DO: auslagern
		// zudem PGN-Parameter auswerten
		$turParams = new JParameter($this->turnier->params);
		$pgnInput = $turParams->get('pgnInput', 1);
		$pgnPublic = $turParams->get('pgnPublic', 1);
		
		// User ermitteln
		$user =& JFactory::getUser();
		
		// Flag für View und Template setzen: pgnShow
		// FALSE - PGN nicht verlinken/anzeigen
		// TRUE - PGN-Links setzen und anzeigen 
		// 'pgnInput möglich' UND ('pgn öffentlich' ORDER 'User eingeloggt')
		if ($pgnInput == 1 AND ($pgnPublic == 1 OR $user->id > 0) ) {
			$this->pgnShow = TRUE;
		} else {
			$this->pgnShow = FALSE;
		}

		$this->displayTlOK = $turParams->get('displayTlOK', 0);

		// turniernamen anpassen?
		$addCatToName = $turParams->get('addCatToName', 0);
		if ($addCatToName != 0 AND ($this->turnier->catidAlltime > 0 OR $this->turnier->catidEdition > 0)) {
			$this->turnier->name = CLMText::addCatToName($addCatToName, $this->turnier->name, $this->turnier->catidAlltime, $this->turnier->catidEdition);
		}


	}
	
	
	function _getTurnierPlayers() {
	
		$query = "SELECT snr, name, twz"
			." FROM `#__clm_turniere_tlnr`"
			." WHERE turnier = ".$this->turnierid
			;
		$this->_db->setQuery( $query );
		$this->players = $this->_db->loadObjectList('snr');
	
		// Default für Leereinträge
		$this->players[0]->name = "";
		$this->players[0]->twz = "";
	
	}
	
	function _getTurnierRounds() {
	
		if ($this->turnier->typ != 3) {
			$sortDir = 'ASC';
		} else {
			$sortDir = 'DESC'; // KO-System mit Finale untern anzeigen
		}
	
		$query = "SELECT *"
			." FROM #__clm_turniere_rnd_termine"
			." WHERE turnier = ".$this->turnierid
			." ORDER BY nr $sortDir"
			;
		$this->_db->setQuery( $query );
		$this->rounds = $this->_db->loadObjectList();
	
	
	}
	
	function _getTurnierMatches() {
	
		// alle ermittelten Runden duirchgehen
		foreach ($this->rounds as $value) {
			$query = "SELECT *"
				." FROM #__clm_turniere_rnd_spl"
				." WHERE turnier = ".$this->turnierid." AND runde = ".$value->nr." AND heim = '1'"
				." ORDER BY runde ASC, brett ASC"
				;
			$this->_db->setQuery( $query );
			$this->matches[$value->nr] = $this->_db->loadObjectList();
		
		}

	}
	

}
?>
