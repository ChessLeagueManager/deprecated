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

class CLMControllerTurRoundForm extends JController {
	

	// Konstruktor
	function __construct( $config = array() ) {
		
		parent::__construct( $config );
		
		$this->_db		= & JFactory::getDBO();
		
		// Register Extra tasks
		$this->registerTask( 'apply', 'save' );
	
		// turnierid
		$this->param['turnierid'] = JRequest::getInt('turnierid');
		
		// roundid
		$this->param['roundid'] = JRequest::getInt('roundid');
	
		// task
		$this->task = JRequest::getCmd('task');
	
		// Weiterleitung
		$this->adminLink = new AdminLink();
		$this->adminLink->view = "turroundform";
		$this->adminLink->more = array('turnierid' => $this->param['turnierid'], 'roundid' => $this->param['roundid']);
		// Default-Ziel zB bei Eingabefehler
	
	}


	function save() {
	
		$this->_saveDo();
	
		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );
	
	}


	function _saveDo() {
	
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );
	
		if (CLM_usertype != 'admin' AND CLM_usertype != 'tl') {
			JError::raiseWarning(500, JText::_('TOURNAMENT_NO_ACCESS') );
			return FALSE;
		}
	
		// Task
		$task = JRequest::getVar('task');
		
		// Instanz der Tabelle
		$row = & JTable::getInstance( 'turnier_runden', 'TableCLM' );
		$row->load($this->param['roundid']);
	
		// bind
		if (!$row->bind(JRequest::get('post'))) {
			JError::raiseError(500, $row->getError() );
			return FALSE;
		}
		// check
		if (!$row->checkData()) {
			JError::raiseWarning(500, $row->getError() );
			return FALSE;
		}
			// save the changes
		if (!$row->store()) {
			JError::raiseError(500, $row->getError() );
			return FALSE;
		}
		$row->checkin();
	
		if ($this->task == 'apply') {
			$this->adminLink->view = "turroundform";
			$this->adminLink->more = array('turnierid' => $this->param['turnierid'], 'roundid' => $this->param['roundid']);
		} else {
			$this->adminLink->view = "turrounds";
			$this->adminLink->more = array('id' => $this->param['turnierid']);
		}
		
		$stringAktion = JText::_('ROUND_EDITED');
		
		$app =& JFactory::getApplication();
		$app->enqueueMessage($stringAktion);
	
		// Log schreiben
		$clmLog = new CLMLog();
		$clmLog->aktion = $stringAktion;
		$clmLog->params = array('tid' => $this->param['turnierid']); // TurnierID wird als LigaID gespeichert
		$clmLog->write();
			
		return TRUE;
	
	}


	function cancel() {
		
		$this->adminLink->view = "turrounds";
		$this->adminLink->more = array('id' => $this->param['turnierid']);
		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );
		
	}

}