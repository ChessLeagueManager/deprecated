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

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class CLMViewTurPlayerEdit extends JView {

	function display() {

		
		// Das Modell wird instanziert und steht als Objekt in der Variable $model zur Verfügung
		$model =   &$this->getModel();
		
		// Die Toolbar erstellen, die über der Seite angezeigt wird
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_clm'.DS.'images'.DS.'admin_menue_images.php');
		JToolBarHelper::title( $model->turnierData->name.", ".JText::_('PLAYER').": ".$model->playerData->name, 'clm_turnier.png'  );
	
		if (CLM_usertype == 'admin' OR CLM_usertype == 'tl') {
			JToolBarHelper::save( 'save' );
			JToolBarHelper::apply( 'apply' );
		}
		JToolBarHelper::spacer();
		JToolBarHelper::cancel('cancel', JText::_('CLOSE'));

		// das MainMenu abschalten
		JRequest::setVar( 'hidemainmenu', 1 );
		

		// Das Modell wird instanziert und steht als Objekt in der Variable $model zur Verfügung
		$model =   &$this->getModel();

		// Document/Seite
		$document =& JFactory::getDocument();

		// JS-Array jtext -> Fehlertexte
		$document->addScriptDeclaration("var jtext = new Array();");
		$document->addScriptDeclaration("jtext['enter_name'] = '".JText::_('PLEASE_ENTER')." ".JText::_('PLAYER_NAME')."';");
		$document->addScriptDeclaration("jtext['enter_twz'] = '".JText::_('PLEASE_ENTER')." ".JText::_('TWZ')."';");
		$document->addScriptDeclaration("jtext['number_twz'] = '".JText::_('PLEASE_NUMBER')." ".JText::_('TWZ')."';");

		// Script
		$document->addScript(CLM_PATH_JAVASCRIPT.'turplayeredit.js');


		// Daten an Template übergeben
		$this->assignRef('user', $model->user);
		
		$this->assignRef('player', $model->playerData);
		$this->assignRef('turnier', $model->turnierData);

		$this->assignRef('param', $model->param);


		parent::display();

	}

}
?>