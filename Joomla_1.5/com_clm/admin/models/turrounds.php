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

class CLMModelTurRounds extends JModel {

	var $_pagination = null;
	var $_total = null;


	// benötigt für Pagination
	function __construct() {
		
		parent::__construct();


		// user
		$this->user =& JFactory::getUser();
		
		// get parameters
		$this->_getParameters();

		// get turnier
		$this->_getTurnier();

		// get players
		$this->_getTurRounds();


		// auf mögliche Auslsoung checken
		if ($this->turnier->typ != 2) {
			$this->_checkRoundToDraw();
		}


		$this->_getPagination();

	}


	// alle vorhandenen Parameter auslesen
	function _getParameters() {
	
		global $mainframe, $option;
		//Joomla 1.6 compatibility
		if (empty($mainframe)) {
			$mainframe = &JFactory::getApplication();
			$option = $mainframe->scope;
		}
	
		// turnierid
		$this->param['id'] = JRequest::getInt('id');
	
	
		// Order
		$this->param['order'] = $mainframe->getUserStateFromRequest( "$option.filter_order", 'filter_order', 'id', 'cmd' );
		$this->param['order_Dir'] = $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",'filter_order_Dir', '', 'word' );
	
		// limit
		$this->limit		= $mainframe->getUserStateFromRequest( $option.'limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$this->limitstart = $mainframe->getUserStateFromRequest( $option.'limitstart', 'limitstart', 0, 'int' );

		$this->setState('limit', $this->limit);
		$this->setState('limitstart', $this->limitstart);
	
	}


	function _getTurnier() {
	
		$query = 'SELECT name, typ, runden'
			. ' FROM #__clm_turniere'
			. ' WHERE id = '.$this->param['id']
			;
		$this->_db->setQuery($query);
		$this->turnier = $this->_db->loadObject();
	
		// INIT der maximal erledigten Runde
		if ($this->turnier->typ != 3) {
			$this->turnier->roundMaxTLOK = 0;
		} else {
			$this->turnier->roundMaxTLOK = ($this->turnier->runden+1);
		}
		// INIT
		$this->turnier->roundToDraw = 0;
	
	}

	function _getTurRounds() {
		
		$query = 'SELECT *'
				. ' FROM #__clm_turniere_rnd_termine'
				.$this->_sqlWhere();
		$this->roundsTotal = $this->_getListCount($query);
		
		$query .= $this->_sqlOrder().' LIMIT '.$this->limitstart.', '.$this->limit;
		
		$this->_db->setQuery($query);
		$this->turRounds = $this->_db->loadObjectList('nr');
		
		
		// matches ermitteln
		foreach ($this->turRounds as $key => $value) {
			$query = 'SELECT COUNT(*), COUNT(ergebnis IS NOT NULL)'
					. ' FROM #__clm_turniere_rnd_spl'
					. ' WHERE turnier = '.$this->param['id'].' AND runde = '.$value->nr;
			$this->_db->setQuery($query);
			$this->turRounds[$key]->countMatches = ($this->_db->loadResult()/2);
			// gespielte matches ermitteln
			$query = 'SELECT COUNT(*), COUNT(ergebnis IS NOT NULL)'
					. ' FROM #__clm_turniere_rnd_spl'
					. ' WHERE turnier = '.$this->param['id'].' AND runde = '.$value->nr.' AND ergebnis IS NOT NULL';
			$this->_db->setQuery($query);
			$this->turRounds[$key]->countResults = ($this->_db->loadResult()/2);
			// angesetzte Matches ermitteln
			$query = 'SELECT COUNT(*)'
					. ' FROM #__clm_turniere_rnd_spl'
					. ' WHERE turnier = '.$this->param['id'].' AND runde = '.$value->nr.' AND ((spieler >= 1 AND gegner >= 1) OR ergebnis = 8)';
			$this->_db->setQuery($query);
			$this->turRounds[$key]->countAssigned = ($this->_db->loadResult()/2);
			
			// tl_ok zwischenspeichern - ist maximale Runde, die erledigt ist
			if ($this->turnier->typ == 1 AND $value->tl_ok == 1 AND $value->nr > $this->turnier->roundMaxTLOK) {
				$this->turnier->roundMaxTLOK = $value->nr;
			} elseif ($this->turnier->typ == 3 AND $value->tl_ok == 1 AND $value->nr < $this->turnier->roundMaxTLOK) {
				$this->turnier->roundMaxTLOK = $value->nr;
			}
		
		}
		
	}
	
	
	function _sqlWhere() {
	
		// init
		$where = array();
		
		$where[] = 'turnier = '.$this->param['id'];
		
	
		$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
		
		return $where;
		
	}
	
	function _sqlOrder() {
		
		// array erlaubter order-Felder:
		$arrayOrderAllowed = array('nr', 'tl_ok', 'published', 'id');
		if (!in_array($this->param['order'], $arrayOrderAllowed)) {
			$this->param['order'] = 'id';
		}
		$orderby = ' ORDER BY '. $this->param['order'] .' '. $this->param['order_Dir'] .', id';
	
		return $orderby;
	
	}
	
	function _checkRoundToDraw() {
	
		// Ch-System und nicht letzte Runde erledigt
		if ($this->turnier->typ == 1 AND $this->turnier->roundMaxTLOK != $this->turnier->runden) { // CH
			// evtl zu losende Runde:
			$temp = $this->turnier->roundMaxTLOK+1;
			// Paarungen der Runde, die auf maximal bestätigte Runde folgt, schon vorhanden?
			if ($this->turRounds[$temp]->countAssigned == 0) { // letzte Runde erledigt?
				$this->turnier->roundToDraw = $temp;
				// Notice absetzen
				JError::raiseNotice(500, JText::_('NOTICE_NEXTROUNDTODRAW').": ".$this->turRounds[$temp]->name);
			}
			
		} elseif ($this->turnier->typ == 3 AND $this->turnier->roundMaxTLOK != 1) { // KO
			// evtl zu losende Runde:
			$temp = $this->turnier->roundMaxTLOK-1;
			// Paarungen der Runde, die auf maximal bestätigte Runde folgt, schon vorhanden?
			if ($this->turRounds[$temp]->countAssigned == 0) { // letzte Runde erledigt?
				$this->turnier->roundToDraw = $temp;
				// Notice absetzen
				JError::raiseNotice(500, JText::_('NOTICE_NEXTROUNDTODRAW').": ".$this->turRounds[$temp]->name);
				
			}
			
			
			
		}
		
	
	}
	
	
	function _getPagination() {
		// Load the content if it doesn't already exist
		if (empty($this->pagination)) {
			jimport('joomla.html.pagination');
			$this->pagination = new JPagination($this->roundsTotal, $this->limitstart, $this->limit );
		}
	}


}

?>