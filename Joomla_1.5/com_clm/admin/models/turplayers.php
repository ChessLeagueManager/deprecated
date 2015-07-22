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

class CLMModelTurPlayers extends JModel {

	var $_pagination = null;
	var $_total = null;


	// benötigt für Pagination
	function __construct() {
		
		parent::__construct();


		// user
		$this->user =& JFactory::getUser();
		
		// get parameters
		$this->_getParameters();

		// get all data
		$this->_getData();

		// Pagination
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
	
		// search
		$this->param['search'] = $mainframe->getUserStateFromRequest( "$option.search", 'search', '', 'string' );
		$this->param['search'] = JString::strtolower( $this->param['search'] );
	
		// club
		$this->param['vid'] = $mainframe->getUserStateFromRequest( "$option.filter_vid", 'filter_vid', '0', 'string' );
		
	
		// Order
		$this->param['order'] = $mainframe->getUserStateFromRequest( "$option.filter_order", 'filter_order', 'snr', 'cmd' );;
		$this->param['order_Dir'] = $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",'filter_order_Dir', '', 'word' );
	
		// limit
		$this->limit		= $mainframe->getUserStateFromRequest( $option.'limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$this->limitstart = $mainframe->getUserStateFromRequest( $option.'limitstart', 'limitstart', 0, 'int' );

		$this->setState('limit', $this->limit);
		$this->setState('limitstart', $this->limitstart);
	
	}


	function _getData() {
	
		// turnier
		$query = 'SELECT name, teil, typ, tiebr1, tiebr2, tiebr3'
			. ' FROM #__clm_turniere'
			. ' WHERE id = '.$this->param['id']
			;
		$this->_db->setQuery($query);
		$this->turnier = $this->_db->loadObject();
	
		// players
		$query = 'SELECT *'
				. ' FROM #__clm_turniere_tlnr'
				.$this->_sqlWhere();
		$this->playersTotal = $this->_getListCount($query);
		
		$query .= $this->_sqlOrder().' LIMIT '.$this->limitstart.', '.$this->limit;
		
		$this->_db->setQuery($query);
		$this->turPlayers = $this->_db->loadObjectList();
		
		// Flag, ob gestartet
		$tournament = new CLMTournament($this->param['id'], TRUE);
		$tournament->checkTournamentStarted();
		$this->turnier->started = $tournament->started;
		
		// wenn nicht gestartet, check, ob Startnummern okay
		if (!$tournament->started AND !$tournament->checkCorrectSnr()) {
			
			JError::raiseWarning(500, JText::_('PLEASE_CORRECT_SNR') );
		
		}
		
		
	}
	
	
	
	function _sqlWhere() {
	
		// init
		$where = array();
		
		$where[] = 'turnier = '.$this->param['id'];
		
		// Saison - nur Filter, wenn eingestellt
		if ($this->param['vid'] != '0') {
			$where[] = 'zps = '.$this->_db->Quote($this->param['vid']); 
		}
		if ($this->param['search'] != '') {
			$where[] = 'LOWER(name) LIKE '.$this->_db->Quote( '%'.$this->_db->getEscaped( $this->param['search'], true ).'%', false );
		}
	
		$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
		
		return $where;
		
	}
	
	function _sqlOrder() {
		
		// array erlaubter order-Felder:
		$arrayOrderAllowed = array('name', 'rankingPos', 'titel', 'snr', 'NATrating', 'FIDEelo', 'twz', 'verein', 'ordering', 'sum_punkte');
		if (!in_array($this->param['order'], $arrayOrderAllowed)) {
			$this->param['order'] = 'id';
		}
		
		// normale Sortierung
		if ($this->param['order'] != 'sum_punkte') {
			$orderby = ' ORDER BY '. $this->param['order'] .' '. $this->param['order_Dir'] .', id';
		
		// Sortierung nach Punkten
		} else {
			$orderby = ' ORDER BY sum_punkte '. $this->param['order_Dir'];
			$fwFieldNames = array(1 => 'sum_bhlz', 'sum_busum', 'sum_sobe', 'sum_wins');
			// alle durchgehen
			for ($f=1; $f<=3; $f++) {
				$fieldName = 'tiebr'.$f; // Feldname in #_turniere
				if ($this->turnier->$fieldName > 0) {
					$orderby .= ', '.$fwFieldNames[$this->turnier->$fieldName].' '.$this->param['order_Dir'];
				}
			}
		}
	
		return $orderby;
	
	}
	
	function _getPagination() {
		// Load the content if it doesn't already exist
		if (empty($this->pagination)) {
			jimport('joomla.html.pagination');
			$this->pagination = new JPagination($this->playersTotal, $this->limitstart, $this->limit );
		}
	}


}

?>