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

class CLMViewTermineForm extends JView {

	function display() {

		
		// Das Modell wird instanziert und steht als Objekt in der Variable $model zur Verfügung
		$model =   &$this->getModel();
		
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_clm'.DS.'images'.DS.'admin_menue_images.php');
		
		if (JRequest::getVar( 'task') == 'edit') { 
			$text = JText::_( 'EDIT' );
		} else {
			$text = JText::_( 'NEW' );
		}
		JToolBarHelper::title( JText::_('TITLE_TERMINE').": ".JText::_('TERMINE_TASK').': <small><small>[ '. $text.' ]</small></small>', 'clm_headmenu_termine.png' );
		JToolBarHelper::save();
		JToolBarHelper::apply();
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();

		JRequest::setVar( 'hidemainmenu', 1 );

		
		// Daten an Template übergeben
		$this->assignRef('user', $model->user);
		
		$this->assignRef('termine', $model->termine);

		$this->assignRef('form', $model->form);


		parent::display();

	}

}
?>