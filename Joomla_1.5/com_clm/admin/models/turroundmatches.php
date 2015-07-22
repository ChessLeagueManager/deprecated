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
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class CLMModelTurRoundMatches extends JModel {

	// benötigt für Pagination
	function __construct() {
		
		parent::__construct();


		// user
		$this->user =& JFactory::getUser();
		
		// get parameters
		$this->_getParameters();

		// get turnier
		$this->_getTurnier();

		// get round
		$this->_getTurRound();

		$this->_getMatches();


	}


	// alle vorhandenen Parameter auslesen
	function _getParameters() {
	
		// turnierid
		$this->param['turnierid'] = JRequest::getInt('turnierid');
	
		// roundid
		$this->param['roundid'] = JRequest::getInt('roundid');
	
	}


	function _getTurnier() {
	
		$query = 'SELECT name, dg, typ, params'
			. ' FROM #__clm_turniere'
			. ' WHERE id = '.$this->param['turnierid']
			;
		$this->_db->setQuery($query);
		$this->turnier = $this->_db->loadObject();
	
	}

	function _getTurRound() {
		
		$query = "SELECT a.nr, a.name, a.gemeldet, a.editor, a.zeit, a.edit_zeit, a.tl_ok, p.name as ename, u.name as gname"
			." FROM `#__clm_turniere_rnd_termine` as a "
			." LEFT JOIN #__clm_user as u ON u.jid = a.gemeldet "
			." LEFT JOIN #__clm_user as p ON p.jid = a.editor "
			." WHERE a.turnier = ".$this->param['turnierid']
			." AND a.id = ".$this->param['roundid']
			;
		$this->_db->setQuery($query);
		$this->round = $this->_db->loadObject();
		
	}
	
	
	function _getMatches() {
	
		$query = 'SELECT a.*, q.teil, q.name as tname'
				. ' FROM #__clm_turniere_rnd_spl as a '
				. ' LEFT JOIN #__clm_turniere AS q ON q.id = a.turnier '
				. ' WHERE a.heim = 1' //  AND q.published = 1
				. ' AND a.turnier ='.$this->param['turnierid']
				// . ' AND a.dg = '.$filter_dg
				. ' AND a.runde = '.$this->round->nr // nicht ID, sondern RundenNr
				. ' AND a.heim = 1'
				. ' ORDER BY a.dg ASC, a.runde ASC, a.brett ASC '
				;
		$this->_db->setQuery($query);
		$this->matches = $this->_db->loadObjectList();
	
		// Ergebnisliste laden
		$query = "SELECT a.eid, a.erg_text "
			." FROM #__clm_ergebnis as a "
			;
		$this->_db->setQuery( $query );
		$this->ergebnisse	= $this->_db->loadObjectList();
	
		// Spielerliste laden
		$query = "SELECT snr, name"
			." FROM `#__clm_turniere_tlnr` " 
			." WHERE turnier = ".$this->param['turnierid']
			." ORDER BY snr ASC "
			;
		$this->_db->setQuery( $query );
		$this->players	= $this->_db->loadAssocList();
	
	
	}
	
	


}

?>