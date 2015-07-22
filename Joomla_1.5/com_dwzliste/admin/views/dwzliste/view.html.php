<?php
/**
*	name					view.html.php
*	description			view für dwzliste
*
*	start					30.11.2010
*	last edit			30.11.2010
*	done					start
*
*	complete				no
*	todo					-
*	wanted				-
*	notes					-
*
*	author				Helge Frowein
*	(c)					2010
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class DWZListeViewDWZListe extends JView {

	function display() {

		// Die Toolbar erstellen, die über der Seite angezeigt wird
		JToolBarHelper::title( 'DWZ-Liste' );

		JToolBarHelper::save( 'save', JText::_('CONFIG_SAVE') );
		
		// Tabs
		jimport('joomla.html.pane');
		
		
		// aktuelle Parameter-Einstellungen
		$paramsdata = &JComponentHelper::getParams( 'com_dwzliste' );
		// XML-Vorgaben zu den Parametern
		$paramsdefs = JPATH_COMPONENT.DS.'config.xml';
		// beides zusammen übergeben
		$params = new JParameter( $paramsdata->_raw, $paramsdefs );
		$this->assignRef('params', $params);
		
		JHTML::_('behavior.tooltip');



		parent::display();

	}

}
?>