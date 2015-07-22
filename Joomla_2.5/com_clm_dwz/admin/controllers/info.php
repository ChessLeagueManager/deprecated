<?php

/**
 * @ CLM DWZ Component
 * @Copyright (C) 2012 Fred Baumgarten. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://sv-hennef.de
 * @author Fred Baumgarten
 * @email dc6iq@gmx.de
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );

class CLM_DWZControllerInfo extends JControllerLegacy
{
  function display()
  {
        require_once(JPATH_COMPONENT.DS.'views'.DS.'info.php');
        CLM_DWZViewInfo::display( );
  }

  function cancel ()
  {
        $this->setRedirect( 'index.php?option=com_clm_dwz' );
  }
}

