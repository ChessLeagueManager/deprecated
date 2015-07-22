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

class CLMViewTurPlayerForm extends JView {

	function display() {

		
		// Das Modell wird instanziert und steht als Objekt in der Variable $model zur Verfügung
		$model =   &$this->getModel();
		
		// Die Toolbar erstellen, die über der Seite angezeigt wird
		$adminLink = new AdminLink();
		$adminLink->view = "turform";
		$adminLink->more = array('task' => 'edit', 'id' => $model->param['id']);
		$adminLink->makeURL();
		
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_clm'.DS.'images'.DS.'admin_menue_images.php');
		JToolBarHelper::title( '<a href="'.$adminLink->url.'">'.$model->turnier->name."</a>: ".JText::_('PLAYERS_ADD'), 'clm_turnier.png'  );
	
		JToolBarHelper::save();
		JToolBarHelper::apply();
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();

		// das MainMenu abschalten
		JRequest::setVar( 'hidemainmenu', 1 );
		

		// Daten an Template übergeben
		$this->assignRef('user', $model->user);
		
		$this->assignRef('playerlist', $model->PlayersList);

		$this->assignRef('form', $model->form);
		$this->assignRef('param', $model->param);

		$this->assignRef('pagination', $model->pagination);
		
		// zusätzliche Funktionalitäten
		JHTML::_('behavior.tooltip');


		parent::display();

	}

}
?>