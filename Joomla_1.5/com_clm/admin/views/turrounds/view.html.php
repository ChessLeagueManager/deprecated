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

class CLMViewTurRounds extends JView {

	function display() {

		
		// Das Modell wird instanziert und steht als Objekt in der Variable $model zur Verfügung
		$model =   &$this->getModel();
		
		// Die Toolbar erstellen, die über der Seite angezeigt wird
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_clm'.DS.'images'.DS.'admin_menue_images.php');
		JToolBarHelper::title( $model->turnier->name.": ".JText::_('ROUNDS'), 'clm_turnier.png'  );
	
		JToolBarHelper::spacer();
		if (CLM_usertype == 'admin' OR CLM_usertype == 'tl') {
			
			// auslosen
			if ($model->turnier->roundToDraw != 0) {
				JToolBarHelper::spacer();
				JToolBarHelper::custom('assignMatches', 'edit.png', 'edit_f2.png', JText::_('MATCHES_ASSIGN'), FALSE);
			
			}
			
			
			JToolBarHelper::spacer();
			JToolBarHelper::publishList();
			JToolBarHelper::unpublishList();
		}
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();

		if (CLM_usertype == 'admin' OR CLM_usertype == 'tl') {
		
			JToolBarHelper::divider();
			JToolBarHelper::spacer();
			JToolBarHelper::custom( 'turform', 'config.png', 'config_f2.png', JText::_('TOURNAMENT'), false);
		
		}

		// Daten an Template übergeben
		$this->assignRef('user', $model->user);
		
		$this->assignRef('turrounds', $model->turRounds);

		$this->assignRef('form', $model->form);
		$this->assignRef('param', $model->param);

		$this->assignRef('pagination', $model->pagination);
		
		// zusätzliche Funktionalitäten
		JHTML::_('behavior.tooltip');


		parent::display();

	}

}
?>