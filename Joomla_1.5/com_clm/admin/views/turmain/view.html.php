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

class CLMViewTurMain extends JView {

	function display()
	{

		// Die Toolbar erstellen, die über der Seite angezeigt wird
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_clm'.DS.'images'.DS.'admin_menue_images.php');
		JToolBarHelper::title( JText::_( 'TOURNAMENTS' ), 'clm_turnier.png'  );
			
		JToolBarHelper::custom('catmain','forward.png','forward_f2.png', JText::_('CATEGORIES'), false);
		JToolBarHelper::custom('showSpecialrankings','specialrankings.png','specialrankings_f2.png', JText::_('SPECIALRANKINGS_BUTTON'), false);
		JToolBarHelper::spacer();
		JToolBarHelper::spacer();


		// nur, wenn Admin
		if (CLM_usertype == 'admin' OR CLM_usertype == 'tl') {
			JToolBarHelper::custom('add','new.png','new_f2.png', JText::_('TOURNAMENT_CREATE'), false);
			JToolBarHelper::customX('copy', 'copy.png', 'copy_f2.png', JText::_('COPY'));
		}
		JToolBarHelper::editListX();
		// nur, wenn Admin
		if (CLM_usertype == 'admin' OR CLM_usertype == 'tl') {
			JToolBarHelper::spacer();
			JToolBarHelper::custom('createRounds', 'back.png', 'edit_f2.png', JText::_('ROUNDS_CREATE'), TRUE);
			JToolBarHelper::custom('deleteRounds', 'cancel.png', 'unarchive_f2.png', JText::_('ROUNDS_DELETE'), TRUE);
			JToolBarHelper::spacer();
			JToolBarHelper::publishList();
			JToolBarHelper::unpublishList();
		}
		
		if (CLM_usertype === 'admin') {
			JToolBarHelper::spacer();
			JToolBarHelper::custom('delete','delete.png','delete_f2.png', JText::_('TOURNAMENT_DELETE'), false);
		}

		// Das Modell wird instanziert und steht als Objekt in der Variable $model zur Verfügung
		$model =   &$this->getModel();

		

		// Daten an Template übergeben
		$this->assignRef('user', $model->user);
		
		$this->assignRef('turniere', $model->turniere);

		$this->assignRef('param', $model->param);

		$this->assignRef('form', $model->form);
		
		$this->assignRef('pagination', $model->pagination);

		// zusätzliche Funktionalitäten
		JHTML::_('behavior.tooltip');

		parent::display();

	}

}
?>