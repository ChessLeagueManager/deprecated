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

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.controller' );

class CLMControllerTurForm extends JController {
	

	// Konstruktor
	function __construct( $config = array() ) {
		
		parent::__construct( $config );
		
		$this->_db		= & JFactory::getDBO();
		
		// Register Extra tasks
		$this->registerTask( 'apply', 'save' );
	
		$this->adminLink = new AdminLink();
		$this->adminLink->view = "turform";
	
	}


	function save() {
	
		if ($this->_saveDo()) { // erfolgreich?
			
			$app =& JFactory::getApplication();
			
			if ($this->neu) { // neues Turnier?
				$app->enqueueMessage( JText::_('TOURNAMENT_CREATED') );
			} else {
				$app->enqueueMessage( JText::_('TOURNAMENT_EDITED') );
			}
		
		}
		// sonst Fehlermeldung schon geschrieben

		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );
	
	}


	function _saveDo() {
	
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );
	
		if (CLM_usertype != 'admin' AND CLM_usertype != 'tl') {
			JError::raiseWarning(500, JText::_('SECTION_NO_ACCESS') );
			return FALSE;
		}
	
		// Task
		$task = JRequest::getVar('task');
		
		// Instanz der Tabelle
		$row = & JTable::getInstance( 'turniere', 'TableCLM' );
		
		if (!$row->bind(JRequest::get('post'))) {
			JError::raiseError(500, $row->getError() );
			return FALSE;
		}
		
		// Rundenzahl berechnen!
		if ($row->typ == 2) {
			$tempTeil = $row->teil;
			if ($tempTeil%2 != 0) { // gerade machen
				$tempTeil++;
			}
			$row->runden = $tempTeil-1;
		} elseif ($row->typ == 3) {
			$row->runden = ceil(log($row->teil)/log(2));
		}
		
		// Parameter
		$paramsStringArray = array();
		foreach ($row->params as $key => $value) {
			$paramsStringArray[] = $key.'='.intval($value);
		}
		$row->params = implode("\n", $paramsStringArray);
		
		
		if (!$row->checkData()) {
			// pre-save checks
			JError::raiseWarning(500, $row->getError() );
			// Weiterleitung bleibt im Formular !!
			$this->adminLink->more = array('task' => $task, 'id' => $row->id);
			return FALSE;
		
		}
		
		// if new item, order last in appropriate group
		if (!$row->id) {
			$this->neu = TRUE; // Flag für neues Turnier
			$stringAktion = JText::_('TOURNAMENT_CREATED');
			// $where = "sid = " . (int) $row->sid; warum nur in Saison?
			$row->ordering = $row->getNextOrder(); // ( $where );
		} else {
			$this->neu = FALSE;
			$stringAktion = JText::_('TOURNAMENT_EDITED');
		}
		
		// save the changes
		if (!$row->store()) {
			JError::raiseError(500, $row->getError() );
		}
		$row->checkin();

		
		// bei bereits bestehendem Turnier noch calculateRanking
		if (!$this->neu) {
			$tournament = new CLMTournament($row->id, TRUE);
			$tournament->calculateRanking();
			$tournament->setRankingPositions();
		}
		

		// Log schreiben
		$clmLog = new CLMLog();
		$clmLog->aktion = $stringAktion.": ".$row->name;
		$clmLog->params = array('sid' => $row->sid, 'tid' => $row->id); // TurnierID wird als LigaID gespeichert
		$clmLog->write();
		

		// wenn 'apply', weiterleiten in form
		if ($task == 'apply') {
			// Weiterleitung bleibt im Formular
			$this->adminLink->more = array('task' => 'edit', 'id' => $row->id);
		} else {
			// Weiterleitung in Liste
			$this->adminLink->view = "turmain"; // WL in Liste
		}
	
		return TRUE;
	
	}


	function cancel() {
		
		$this->adminLink->view = "turmain";
		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );
		
	}

}