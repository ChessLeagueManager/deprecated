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

class CLMViewCatForm extends JView {

	function display() {

		// Die Toolbar erstellen, die über der Seite angezeigt wird
		if (JRequest::getVar( 'task') == 'edit') { 
			$text = JText::_( 'CATEGORY_EDIT' );
		} else { 
			$text = JText::_( 'CATEGORY_CREATE' );
		}
		
		JToolBarHelper::title( $text );
		
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
		$document->addScriptDeclaration("var jserror = new Array();");
		$document->addScriptDeclaration("jserror['enter_name'] = '".JText::_('PLEASE_ENTER')." ".JText::_('CATEGORY_NAME')."';");
		$document->addScriptDeclaration("jserror['select_director'] = '".JText::_('PLEASE_SELECT')." ".JText::_('TOURNAMENT_DIRECTOR')."';");

		// Script
		$document->addScript(CLM_PATH_JAVASCRIPT.'catform.js');

		// Daten an Template übergeben
		$this->assignRef('user', $model->user);
		
		$this->assignRef('category', $model->category);

		$this->assignRef('form', $model->form);

		
		parent::display();

	}

}
?>