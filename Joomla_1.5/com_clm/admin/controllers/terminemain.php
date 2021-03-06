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

class CLMControllerTermineMain extends JController {
	

	// Konstruktor
	function __construct( $config = array() ) {
		
		parent::__construct( $config );
		
		$this->_db		= & JFactory::getDBO();
		
		// Register Extra tasks
		$this->registerTask( 'apply','save' );
		$this->registerTask( 'unpublish','publish' );
	
		$this->adminLink = new AdminLink();
		$this->adminLink->view = "terminemain";
	
	}

	// Weiterleitung!
	function add() {
		
		$this->adminLink->view = "termineform";
		$this->adminLink->makeURL();
		
		$this->setRedirect( $this->adminLink->url );
	
	}

	
	/**
	* Container für kopieren
	*
	*/
	function copy() {

		$this->_copyDo();

		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );

	}

	/**
	* eigentliche copy-Funktion
	*
	*/
	function _copyDo() {
		
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );
		
		
		// zu bearbeitende IDs auslesen
		$cid		= JRequest::getVar( 'cid', null, 'post', 'array' );
		JArrayHelper::toInteger($cid);
		// vorerst nur eine ID bearbeiten!
		$termineid = $cid[0];
		
		
		// Daten holen
		$row =& JTable::getInstance( 'termine', 'TableCLM' );

		if ( !$row->load($termineid) ) {
			JError::raiseWarning( 500, CLMText::errorText('TERMINE_TASK', 'NOTEXISTING') );
			return FALSE;
		}
		
		// alten Namen zwischenspeichern für Message und Log
		$nameOld = $row->name;
		
		// Daten für Kopie anpassen
		$row->id				= 0; // neue id wird von DB vergeben
		$row->name			= JText::_('COPY_OF').' '.$row->name;
		$row->published	= 0;


		if (!$row->store()) {	
			JError::raiseWarning( $row->getError() );
			return FALSE; 
		}

		// Ende
		return TRUE;
	
	}


	// Weiterleitung!
	function edit() {
		
		$cid	= JRequest::getVar('cid', array(), '', 'array');
		JArrayHelper::toInteger($cid);
		
		$this->adminLink->view = "termineform";
		$this->adminLink->more = array('task' => 'edit', 'id' => $cid[0]);
		$this->adminLink->makeURL();
		
		$this->setRedirect( $this->adminLink->url );
	
	}


	function publish() {
		
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );
	
		// termine? evtl global inconstruct anlegen
		$user 		=& JFactory::getUser();
		
		$cid		= JRequest::getVar('cid', array(), '', 'array');
		JArrayHelper::toInteger($cid);
		
		$task		= JRequest::getCmd( 'task' );
		$publish	= ($task == 'publish'); // zu vergebender Wert 0/1
		
		// Inhalte übergeben?
		if (empty( $cid )) { 
			
			JError::raiseWarning( 500, 'NO_ITEM_SELECTED' );
		
		} else { // ja, Inhalte vorhanden
			
			// erst jetzt alle Einträge durchgehen
			foreach ($cid as $key => $value) {
		
				// load the row from the db table
				$row =& JTable::getInstance( 'termine', 'TableCLM' );
				$row->load( $value ); // Daten zu dieser ID laden
		
				// Änderung nötig?
				if ($row->published != $publish) {
					// Log
					$clmLog = new CLMLog();
					$clmLog->aktion = JText::_('TERMINE')." ".$row->name.": ".$task;
					$clmLog->params = array(); 
					$clmLog->write();
					// Log geschrieben - Änderungen später
				} else {
					unset($cid[$key]);
				}
		
			} 
			// alle Einträge geprüft
		
			// immer noch Einträge vorhanden?
			if ( !empty($cid) ) { 
		
				$row =& JTable::getInstance( 'termine', 'TableCLM' );
				$row->publish( $cid, $publish );
			
				// Meldung erstellen
				$app =& JFactory::getApplication();
				if ($publish) {
					$app->enqueueMessage( CLMText::sgpl(count($cid), JText::_('TERMINE_TASK'), JText::_('TERMINE_TASKS'))." ".JText::_('CLM_PUBLISHED') );
				} else {
					$app->enqueueMessage( CLMText::sgpl(count($cid), JText::_('TERMINE_TASK'), JText::_('TERMINE_TASKS'))." ".JText::_('CLM_UNPUBLISHED') );
				}
			
			} else {
			
				$app =& JFactory::getApplication();
				$app->enqueueMessage(JText::_('NO_CHANGES'));
			
			}
	
		}
	
		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );
	
	}

	
	
	/**
	* Container für Löschung
	*
	*/
	function delete() {

		$this->_deleteDo();

		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );

	}
	
	
	/**
	* eigentliche Lösch-Funktion
	*
	*/
	function _deleteDo() {
		
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );
	
		$cid = JRequest::getVar('cid', array(), '', 'array');
		JArrayHelper::toInteger($cid);
		// vorerst nur ein markiertes Turnier übernehmen // später über foreach mehrere?
		$termineid = $cid[0];
		
		
		// access? nur admin darf löschen
		if (CLM_usertype != 'admin') {
			JError::raiseWarning( 500, JText::_('NO_ACCESS') );
			return FALSE;
		}
		
		
		// turnierdaten laden
		$row =& JTable::getInstance( 'termine', 'TableCLM' );
		$row->load( $termineid );
		
		// falls Turnier existent?
		if ( !$row->load( $termineid ) ) {
			JError::raiseWarning( 500, CLMText::errorText('TERMINE_TASK', 'NOTEXISTING') );
			return FALSE;
		
		}
		
	
		// Termin löschen
		$query = " DELETE FROM #__clm_termine "
			." WHERE id = ".$termineid
			;
		$this->_db->setQuery( $query );
		if (!$this->_db->query()) {
			JError::raiseError(500, $this->_db->getErrorMsg() ); 
		}
	
		// Log schreiben
		$clmLog = new CLMLog();
		$clmLog->aktion = JText::_('TERMINE_TASK')." ".JText::_('DELETED');
		$clmLog->params = array(); 
		$clmLog->write();
		
		
		// Message
		$app =& JFactory::getApplication();
		$app->enqueueMessage( $row->name.": ".JText::_('TERMINE_TASK')." ".JText::_('DELETED') );
		
		return TRUE;
		
	}
	
	
	// Moves the record up one position
	function orderdown() {
		
		$this->_order(1);
		
		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );
	
	}

	// Moves the record down one position
	function orderup() {
		
		$this->_order(-1);
		
		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );
	
	}

	// Moves the order of a record
	// @param integer The direction to reorder, +1 down, -1 up
	function _order($inc) {
	
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );
	
		$cid = JRequest::getVar('cid', array(), '', 'array');
		JArrayHelper::toInteger($cid);
		$termineid = $cid[0];
	
	
		$row =& JTable::getInstance( 'termine', 'TableCLM' );
		if ( !$row->load( $termineid ) ) {
			JError::raiseWarning( 500, CLMText::errorText('TERMINE_TASK', 'NOTEXISTING') );
			return FALSE;
		}
		$row->move( $inc, '' );
	
		$app =& JFactory::getApplication();
		$app->enqueueMessage( $row->name.": ".JText::_('ORDERING_CHANGED') );
		
		return TRUE;
		
	}

	// Saves user reordering entry
	function saveOrder() {
	
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );
	
		/*
		if (CLM_usertype != 'admin' AND CLM_usertype != 'tl') {
			JError::raiseWarning( 500, JText::_('SECTION_NO_ACCESS') );
			return FALSE;
		}
		*/
	
		$cid		= JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);
	
		$total		= count( $cid );
		$order		= JRequest::getVar( 'order', array(0), 'post', 'array' );
		JArrayHelper::toInteger($order, array(0));
	
		$row =& JTable::getInstance( 'termine', 'TableCLM' );
		$groupings = array();
	
		// update ordering values
		for( $i=0; $i < $total; $i++ ) {
			$row->load( (int) $cid[$i] );
			// track categories
			$groupings[] = $row->category;
	
			if ($row->ordering != $order[$i]) {
				$row->ordering = $order[$i];
				if (!$row->store()) {
					JError::raiseError(500, $db->getErrorMsg() );
				}
			}
		}
		// execute updateOrder for each parent group
		$groupings = array_unique( $groupings );
		foreach ($groupings as $group){
			$row->reorder('saison = '.(int) $group);
		}
		
		$app =& JFactory::getApplication();
		$app->enqueueMessage( JText::_('NEW_ORDERING_SAVED') );
	
		$this->adminLink->makeURL();
		$this->setRedirect( $this->adminLink->url );
	
	}

}