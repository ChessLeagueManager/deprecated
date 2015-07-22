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

class CLMViewSonderranglistenForm extends JView {

	function display($tpl = null) { 
	
		//Daten vom Model
		$sonderrangliste	= & $this->get('Sonderrangliste');
		$ordering			= &	$this->get('Ordering');
		$turniere			= &	$this->get('Turniere');
		$saisons			= &	$this->get('Saisons');
		
		if (JRequest::getVar( 'task') == 'add') {
			$isNew = true;
		} else { 
			$isNew = false;
		}
		// Die Toolbar erstellen, die über der Seite angezeigt wird
		if (!$isNew) { 
			$text = JText::_( 'SPECIALRANKING_EDIT' );
		} else { 
			$text = JText::_( 'SPECIALRANKING_CREATE' );
		}
		
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_clm'.DS.'images'.DS.'admin_menue_images.php');
		JToolBarHelper::title( $text, 'clm_headmenu_sonderranglisten.png' );
		
		if (CLM_usertype == 'admin' OR CLM_usertype == 'tl') {
			JToolBarHelper::save( 'save' );
			JToolBarHelper::apply( 'apply' );
		}
		JToolBarHelper::spacer();
		JToolBarHelper::cancel('cancel', JText::_('CLOSE'));

		
		$config	= &JComponentHelper::getParams( 'com_clm' );
		
		//Listen
		$lists['published']			= JHTML::_('select.booleanlist', 'published', 'class="inputbox"', $sonderrangliste->published );
		
		$lists['use_rating_filter']	= JHTML::_('select.booleanlist', 'use_rating_filter', 'class="inputbox"', $sonderrangliste->use_rating_filter );
		$options_rat[]				= JHTML::_('select.option', 0, JText::_( 'SPECIALRANKING_OPTION_RATING_TYPE_0' ));
		$options_rat[]				= JHTML::_('select.option', 1, JText::_( 'SPECIALRANKING_OPTION_RATING_TYPE_1' ));
		$options_rat[]				= JHTML::_('select.option', 2, JText::_( 'SPECIALRANKING_OPTION_RATING_TYPE_2' ));
		$lists['rating_type']		= JHTML::_('select.genericlist', $options_rat, 'rating_type', 'class="inputbox"', 'value', 'text', $sonderrangliste->rating_type );
		
		$lists['use_birthYear_filter']	= JHTML::_('select.booleanlist', 'use_birthYear_filter', 'class="inputbox"', $sonderrangliste->use_birthYear_filter );
		
		$lists['use_sex_filter']	= JHTML::_('select.booleanlist', 'use_sex_filter', 'class="inputbox"', $sonderrangliste->use_sex_filter );
		$options_sex[]				= JHTML::_('select.option', '', JText::_( 'SPECIALRANKING_OPTION_SEX_0' ));
		$options_sex[]				= JHTML::_('select.option', 'M', JText::_( 'SPECIALRANKING_OPTION_SEX_1' ));
		$options_sex[]				= JHTML::_('select.option', 'W', JText::_( 'SPECIALRANKING_OPTION_SEX_2' ));
		$lists['sex']				= JHTML::_('select.genericlist', $options_sex, 'sex', 'class="inputbox"', 'value', 'text', $sonderrangliste->sex );
		
		$lists['use_zps_filter']	= JHTML::_('select.booleanlist', 'use_zps_filter', 'class="inputbox"', $sonderrangliste->use_zps_filter );

		//Reihenfolge
		if (!$isNew) { 
			$options_o[] = JHTML::_('select.option',0,'0 '.JText::_('ORDERING_FIRST'));
			$orderingMax = 1;
			
			foreach($ordering as $rank){
				$options_o[] = JHTML::_('select.option',$rank->ordering,$rank->ordering.' ('.$rank->name.')');
				$orderingMax++;
			}
			$options_o[] = JHTML::_('select.option',$orderingMax, $orderingMax.' '.JText::_('ORDERING_LAST'));
			
			$lists['ordering']	= JHTML::_('select.genericlist',$options_o, 'ordering', 'class="inputbox"','value','text', $sonderrangliste->ordering);
		}
		else {
			$lists['ordering']	= JText::_('SPECIALRANKING_ORDERING_NEW'); // Neue Sonderranglisten werden standardmäßig an den Anfang gesetzt. Die Sortierung kann nach dem Speichern dieser Sonderrangliste geändert werden. 
		}
		
		
		//Listen für Turniere (Joomla 1.5 bietet keine wirklich befriedigende Lösung)
		$turnier_str = "<select id='turnier' class='inputbox' name='turnier'>";
		
		if($sonderrangliste->turnier == 0) {
			$selected = "selected='selected' ";
		} else {
			$selected = '';
		}
			
		$turnier_str .= "<option sid='0' value='0' ".$selected.">".JText::_('CHOOSE_TOURNAMENT')."</option>";
		$sid = null;
		foreach($turniere as $turnier){
			if($turnier->id == $sonderrangliste->turnier) {
				$selected = "selected='selected' ";
			} else {
				$selected = '';
			}
			$turnier_str .= "<option sid='".$turnier->sid."' value='".$turnier->id."' ".$selected.">".$turnier->name."</option>";
		}
		$saison_str .= "</select>";
		$lists['turnier'] = $turnier_str;
		
		
		//Saisons
		$options_s[] = JHTML::_('select.option',0,JText::_('CHOOSE_SAISON'));
		
		foreach($saisons as $saison){
			$options_s[] = JHTML::_('select.option',$saison->id,$saison->name);
		}
		$lists['saison']	= JHTML::_('select.genericlist',$options_s, 'saison', 'class="inputbox" onchange="showTournaments()"','value','text', $sonderrangliste->sid);

				
		// Daten an Template übergeben
		$this->assignRef('params', $params);
		$this->assignRef('sonderrangliste', $sonderrangliste);
		$this->assignRef('isNew', $isNew );
		$this->assignRef('lists' , $lists);

		
		parent::display($tpl); 

	}

}
?>