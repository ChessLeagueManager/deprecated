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

// no direct access
defined('_JEXEC') or die('Restricted access');

// Make sure the user is authorized to view this page
$user = & JFactory::getUser();
$jid = $user->get('id');
DEFINE ('CLM_ID', $jid);

// CLM Userstatus auslesen
$query = "SELECT a.usertype, a.user_clm FROM #__clm_user as a"
	." LEFT JOIN #__clm_saison as s ON s.id = a.sid"
	." WHERE a.jid = ".$jid
	." AND a.published = 1 "
	." AND s.published = 1 AND s.archiv = 0 ";
$db	= & JFactory::getDBO();
$db->setQuery($query);
$userdata = $db->loadObjectList();

if ($userdata[0]->usertype != '') {
	DEFINE ('CLM_admin', $userdata[0]->usertype);
	DEFINE ('CLM_usertype', $userdata[0]->usertype);
	DEFINE ('CLM_user', $userdata[0]->user_clm);
} else { 
	DEFINE ('CLM_admin', 'NO');
	DEFINE ('CLM_usertype', 'NO');
	DEFINE ('CLM_user', 'NO');
}
// Pfad zum JS-Verzeichnis
DEFINE ('CLM_PATH_JAVASCRIPT', 'components'.DS.'com_clm'.DS.'javascript'.DS);

// Set the table directory
JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_clm'.DS.'tables');


// init der Berechtigungen:

// diese Seiten sind mit jeglichem Zugang möglich (CLM_admin != "0")
$arrayAccessSimple = array('ergebnisse', 'runden', 'vereine', 'meldelisten', 'ranglisten', 'gruppen', 'mannschaften', 'users', 'check');

// diese Seiten sind verschiedenen Admin-Rängen vorbehalten
$arrayAccessMulti = array();
$arrayAccessMulti['saisons'] = array('admin');
$arrayAccessMulti['swt'] = array('admin');
$arrayAccessMulti['ligen'] = array('admin', 'dwz', 'dv', 'sl');
$arrayAccessMulti['mturniere'] = array('admin', 'dwz', 'dv', 'tl'); //mtmt
$arrayAccessMulti['paarung'] = array('admin', 'dwz', 'dv', 'sl');
$arrayAccessMulti['paarungsliste'] = array('admin', 'dv', 'sl');
$arrayAccessMulti['db'] = array('admin', 'dwz');
$arrayAccessMulti['elobase'] = array('admin', 'dwz');
$arrayAccessMulti['dwz'] = array('admin', 'dwz', 'dv');
$arrayAccessMulti['logfile'] = array('admin', 'dwz', 'dv', 'sl', 'spl');
$arrayAccessMulti['config'] = array('admin');

// Parameter auslesen 
$config = &JComponentHelper::getParams( 'com_clm' );
$val=$config->get('menue',1);

JSubMenuHelper::addEntry(JText::_('INFO'), 'index.php?option=com_clm&section=info', (JRequest::getVar('section')) == 'info'?true:false);

if ($val == 0) {
	JSubMenuHelper::addEntry(JText::_('ERGEBNISSE'),  'index.php?option=com_clm&section=ergebnisse', (JRequest::getVar('section')) == 'ergebnisse'?true:false); 
}

if (in_array(CLM_admin, $arrayAccessMulti['saisons'])) {
// if (CLM_admin === 'admin' ) {
	JSubMenuHelper::addEntry(JText::_('SAISON'), 'index.php?option=com_clm&section=saisons', (JRequest::getVar('section')) == 'saisons'?true:false);
}

if(CLM_admin != "NO") {
	JSubMenuHelper::addEntry(JText::_('TERMINE'), 'index.php?option=com_clm&view=terminemain', (JRequest::getVar('view')) == 'terminemain'?true:false);
}

if(CLM_admin != "NO") {
	JSubMenuHelper::addEntry(JText::_('TURNIERE'), 'index.php?option=com_clm&view=turmain', (JRequest::getVar('view')) == 'turmain'?true:false);
	JSubMenuHelper::addEntry(JText::_('LIGEN'), 'index.php?option=com_clm&section=ligen', (JRequest::getVar('section')) == 'ligen'?true:false);
	JSubMenuHelper::addEntry(JText::_('MTURNIERE'), 'index.php?option=com_clm&section=mturniere', (JRequest::getVar('section')) == 'mturniere'?true:false); //mtmt
}

if ($val == 0) {
	JSubMenuHelper::addEntry(JText::_('SPIELTAGE'), 'index.php?option=com_clm&section=runden', (JRequest::getVar('section')) == 'runden'?true:false);
}

if (CLM_admin != "NO") {
	JSubMenuHelper::addEntry(JText::_('VEREINE'), 'index.php?option=com_clm&section=vereine', (JRequest::getVar('section')) == 'vereine'?true:false);
	JSubMenuHelper::addEntry(JText::_('MANNSCHAFTEN'), 'index.php?option=com_clm&section=mannschaften', (JRequest::getVar('section')) == 'mannschaften'?true:false);
	JSubMenuHelper::addEntry(JText::_('USER'), 'index.php?option=com_clm&section=users', (JRequest::getVar('section')) == 'users'?true:false);
}

if (in_array(CLM_admin, $arrayAccessMulti['swt'])) {
// if (CLM_admin === 'admin') {
	JSubMenuHelper::addEntry(JText::_('SWT'), 'index.php?option=com_clm&section=swt', (JRequest::getVar('section')) == 'swt'?true:false);
}

if (in_array(CLM_admin, $arrayAccessMulti['elobase'])) {
// if (CLM_admin === 'admin' || CLM_admin === 'dwz') {
	JSubMenuHelper::addEntry(JText::_('ELOBASE'), 'index.php?option=com_clm&section=elobase', (JRequest::getVar('section')) == 'elobase'?true:false);
}

if (in_array(CLM_admin, $arrayAccessMulti['db'])) {
// if (CLM_admin === 'admin' || CLM_admin === 'dwz') {
	JSubMenuHelper::addEntry(JText::_('DATABASE'), 'index.php?option=com_clm&section=db', (JRequest::getVar('section')) == 'db'?true:false);
}

if (in_array(CLM_admin, $arrayAccessMulti['logfile'])) {
// if (CLM_admin === 'admin' || CLM_admin === 'dwz' || CLM_admin === 'dv' || CLM_admin === 'spl') {
	JSubMenuHelper::addEntry(JText::_('LOGFILE'), 'index.php?option=com_clm&view=logmain', (JRequest::getVar('view')) == 'logmain'?true:false);
}

if (in_array(CLM_admin, $arrayAccessMulti['config'])) {
// if (CLM_admin === 'admin') {
	JSubMenuHelper::addEntry(JText::_('CONFIG_TITLE'), 'index.php?option=com_clm&view=config', (JRequest::getVar('view')) == 'config'?true:false);
}


// Berechtigungen via Controllername checken
$controllerName = JRequest::getCmd( 'section');
// Zugangscheck
if (in_array($controllerName, $arrayAccessSimple)) { // jeglicher Zugang
	if (CLM_admin != "NO") {
		$controllerName = $controllerName;
	} else {
		JError::raiseWarning( 500, JText::_( 'NO_PERMISSION' ) );
		$controllerName = 'info';
	}

} elseif (array_key_exists($controllerName, $arrayAccessMulti)) { // verschiedene Zugänge
	// dieser Zugang vorgesehen?
	if (in_array(CLM_admin, $arrayAccessMulti[$controllerName])) {
		$controllerName = $controllerName;
	} else {
		JError::raiseWarning( 500, JText::_( 'NO_PERMISSION' ) );
		$controllerName = 'info';
	}
} else {
	// default
	$controllerName = 'info';
}

/*
switch ($controllerName) {
	
	// wenn nichts passt dann nimm dies
	default:
		$controllerName = 'info';
		break;
	// die folgenden Bereiche kennen wir
	case 'ergebnisse':
		if (CLM_admin != "0") {
			$controllerName = 'ergebnisse';
		} else {
			JError::raiseWarning( 500, JText::_( 'NO_PERMISSION' ) );
			$controllerName = 'info';
		}
		break;
	case 'saisons':
		if (CLM_admin === 'admin') {
			$controllerName = 'saisons';
		} else {
			JError::raiseWarning( 500, JText::_( 'NO_PERMISSION' ) );
			$controllerName = 'info';
		}
		break;
	case 'ligen';
		if (CLM_admin === 'admin' || CLM_admin === 'dwz' || CLM_admin === 'dv' || CLM_admin === 'sl') {
			$controllerName = 'ligen';
		} else {
			JError::raiseWarning( 500, JText::_( 'NO_PERMISSION' ) );
			$controllerName = 'info';
		}
		break;
  case 'paarung';
	if (CLM_admin === 'admin' || CLM_admin === 'dv' || CLM_admin === 'dwz' || CLM_admin === 'sl') {
	$controllerName = 'paarung';
	} else {
		JError::raiseWarning( 500, JText::_( 'NO_PERMISSION' ) );
		$controllerName = 'info';
	}
		break;
  case 'paarungsliste';
	if (CLM_admin === 'admin' || CLM_admin === 'dv' || CLM_admin === 'sl') {
	$controllerName = 'paarungsliste';
	} else {
		JError::raiseWarning( 500, JText::_( 'NO_PERMISSION' ) );
		$controllerName = 'info';
	}
		break;
  case 'db';
	if (CLM_admin === 'admin' || CLM_admin === 'dwz' ) {
	$controllerName = 'db';
	} else {
		JError::raiseWarning( 500, JText::_( 'NO_PERMISSION' ) );
		$controllerName = 'info';
	}
		break;
  case 'elobase';
	if (CLM_admin === 'admin' OR CLM_admin === 'dwz') {
	$controllerName = 'elobase';
	} else {
		JError::raiseWarning( 500, JText::_( 'NO_PERMISSION' ) );
		$controllerName = 'info';
	}
		break;
  case 'runden';
	if (CLM_admin != "0") {
	$controllerName = 'runden';
	} else {
		JError::raiseWarning( 500, JText::_( 'NO_PERMISSION' ) );
		$controllerName = 'info';
	}
		break;
  case 'vereine';
	if (CLM_admin != "0") {
	$controllerName = 'vereine';
	} else {
		JError::raiseWarning( 500, JText::_( 'NO_PERMISSION' ) );
		$controllerName = 'info';
	}
		break;
  case 'meldelisten';
	if (CLM_admin != "0") {
	$controllerName = 'meldelisten';
	} else {
		JError::raiseWarning( 500, JText::_( 'NO_PERMISSION' ) );
		$controllerName = 'info';
	}
		break;
  case 'ranglisten';
	if (CLM_admin != "0") {
	$controllerName = 'ranglisten';
	} else {
		JError::raiseWarning( 500, JText::_( 'NO_PERMISSION' ) );
		$controllerName = 'info';
	}
		break;
  case 'gruppen';
	if (CLM_admin != "0") {
	$controllerName = 'gruppen';
	} else {
		JError::raiseWarning( 500, JText::_( 'NO_PERMISSION' ) );
		$controllerName = 'info';
	}
		break;

  case 'mannschaften':
	if (CLM_admin != "0") {
	$controllerName = 'mannschaften';
	} else {
		JError::raiseWarning( 500, JText::_( 'NO_PERMISSION' ) );
		$controllerName = 'info';
	}
		break;
  case 'users';
	if (CLM_admin != "0") {
	$controllerName = 'users';
	} else {
		JError::raiseWarning( 500, JText::_( 'NO_PERMISSION' ) );
		$controllerName = 'info';
	}
		break;
  case 'dwz';
	if (CLM_admin === 'admin' || CLM_admin === 'dwz' || CLM_admin === 'dv') {
	$controllerName = 'dwz';
	} else {
		JError::raiseWarning( 500, JText::_( 'NO_PERMISSION' ) );
		$controllerName = 'info';
	}
		break;
  case 'swt';
	if (CLM_admin === 'admin') {
	$controllerName = 'swt';
	} else {
		JError::raiseWarning( 500, JText::_( 'NO_PERMISSION' ) );
		$controllerName = 'info';
	}
		break;
  case 'konfiguration';
	$controllerName = 'info';
		break;
  case 'check';
	if (CLM_admin != "0") {
	$controllerName = 'check';
	} else {
		JError::raiseWarning( 500, JText::_( 'NO_PERMISSION' ) );
		$controllerName = 'info';
	}
		break;

    // die richtige Datei einbinden
	case 'info':
		// Temporary interceptor
		$task = JRequest::getCmd('task');
		if ($task == 'info') {	$controllerName = 'info';}
		break;
}
*/

// lädt alle CLM-Klassen - quasi autoload
$classpath = dirname(__FILE__).DS.'classes';
foreach( JFolder::files($classpath) as $file ) {
	JLoader::register(str_replace('.class.php', '', $file), $classpath.DS.$file);
}

// alternative CLM-Struktur für Turniere & Termine
if ($viewName = JRequest::getCmd('view')) {
	
	
	$language =& JFactory::getLanguage();
	$language->load('com_clm');
	if ( in_array($viewName, array('catform', 'catmain', 'logmain', 'turform', 'turinvite', 'turmain', 'turplayeredit', 'turplayerform', 'turplayers', 'turroundform', 'turroundmatches','turrounds'))) {
		$language->load('com_clm.turnier');
	} elseif ($viewName == 'config') {
		$language->load('com_clm.config');
	}
	
	// den Basis-Controller einbinden (com_*/controller.php)
	require_once (JPATH_COMPONENT.DS.'controller.php');
	
	// Require specific controller if requested (im hidden-field der adminForm!)
	if( $controller = JRequest::getWord('controller') ) {
	
		$path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
		if (file_exists($path)) {
			require_once $path;
		} else {
			$controller = '';
		}
	
	}
	
	$classname  = 'CLMController'.$controller;
	$controller = new $classname( ); // Instanziert
	// alles was im Basis-Controller zur Verfügung steht, steht jetzt den entsprechenden Scripten zur Verfügung!
	
	
} else {
	
	// bisherige CLM-Architektur
	require_once( JPATH_COMPONENT.DS.'controllers'.DS.$controllerName.'.php' );
	$controllerName = 'CLMController'.$controllerName;

	// Create the controller
	$controller = new $controllerName();

}

// Perform the Request task
$controller->execute( JRequest::getCmd('task') );

// Redirect if set by the controller
$controller->redirect();
	

?>
