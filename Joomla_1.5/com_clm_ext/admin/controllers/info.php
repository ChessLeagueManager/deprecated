<?php

/**
  * @ CLM Extern Component
 * @Copyright (C) 2008 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );

class CLM_EXTControllerInfo extends JController
{
  function display()
  {
        require_once(JPATH_COMPONENT.DS.'views'.DS.'info.php');
        CLM_EXTViewInfo::display( );
  }

	function cancel ()
  {
        $this->setRedirect( 'index.php?option=com_clm_ext' );
  }

}

