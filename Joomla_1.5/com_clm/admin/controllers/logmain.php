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

class CLMControllerLogMain extends JController {
	

	// Konstruktor
	function __construct( $config = array() ) {
		
		parent::__construct( $config );
		
		$this->_db		= & JFactory::getDBO();
	
		$this->adminLink = new AdminLink();
		$this->adminLink->view = "logmain";
	
	}


	function delete() {
	
		$this->_deleteDo();

		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );
	
	}


	function _deleteDo() {
	
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );
	
		if ( CLM_usertype !== 'admin') {
			JError::raiseWarning( 500, JText::_( 'LOGFILE_FORBIDDEN' ) );
			return;
		}
	
		$cid		= JRequest::getVar('cid', array(), '', 'array');
		JArrayHelper::toInteger($cid);
		
		// Inhalte übergeben?
		if (empty( $cid )) { 
			
			JError::raiseWarning( 500, 'NO_ITEM_SELECTED' );
		
		} else { // ja, Inhalte vorhanden
	
			$query = 'DELETE FROM #__clm_log WHERE id = '.implode(' OR id = ', $cid);
			$this->_db->setQuery($query);
			$this->_db->query();
		
			$app =& JFactory::getApplication();
			$app->enqueueMessage( count($cid)." ".JText::_( 'LOGFILE_DELETE_SUCCESS') );
	
		}
		
		return TRUE;
	
	}


	function deleteAll() {
	
		$this->_deleteAllDo();

		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );
	
	}
	
	
	function _deleteAllDo() {
	
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );
	
		if ( CLM_usertype !== 'admin') {
			JError::raiseWarning( 500, JText::_( 'LOGFILE_FORBIDDEN' ) );
			return;
		}
	
		$query	= "TRUNCATE `#__clm_log` ";
		$this->_db->setQuery($query);
		$this->_db->query();
	
		$app =& JFactory::getApplication();
		$app->enqueueMessage( JText::_( 'LOGFILE_DELETE_SUCCESS') );

		return TRUE;
	
	}
	

}