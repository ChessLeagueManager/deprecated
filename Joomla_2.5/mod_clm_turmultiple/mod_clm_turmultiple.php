<?php
// no direct access
defined('_JEXEC') or die('Restricted access');


// Include the syndicate functions only once
require_once (dirname(__FILE__).DS.'helper.php');


// INIT
$db  =& JFactory::getDBO();

// Joomla
$view = JRequest::getVar('view');
$itemid = JRequest::getVar('Itemid');

// Modul-Parameter
$param['categoryid'] = $params->get('categoryid', '');
$param['turnierid'] = $params->get('turnierid', '');
$param['menulist'] = $params->get('menulist', 0);
$param['menuindent'] = $params->get('menuindent', 3);
$param['linkplayerslist'] = $params->get('linkplayerslist', 1);
$param['linkroundseach'] = $params->get('linkroundseach', 0);
$param['showunpublishedrounds'] = $params->get('showunpublishedrounds', 0);
$param['linkmatchescomplete'] = $params->get('linkmatchescomplete', 0);
$param['linktable'] = $params->get('linktable', 0);
$param['linkrankingscore'] = $params->get('linkrankingscore', 0);
$param['textrankingscore'] = $params->get('textrankingscore', '');
$param['linkinvitation'] = $params->get('linkinvitation', 0);
$param['contentid'] = $params->get('contentid', '');
$param['textbottom'] = $params->get('textbottom', '');


// URL-Parameter
$runde = JRequest::getVar('runde');

$arrayTurniere = array();

// CategoryID vorgegeben?
if ($param['categoryid'] != '' AND $param['categoryid'] > 0) {
	
	list($parentArray, $parentKeys, $parentChilds) = modCLM_TurMultipleHelper::getTree();
	
	// für jede Kategorie Unterkategorien ermitteln
	$arrayAllCatid = array();
	if (isset($parentChilds[$param['categoryid']])) {
		$arrayAllCatid = $parentChilds[$param['categoryid']];
		$arrayAllCatid[] = $param['categoryid'];
	} else {
		$arrayAllCatid[] = $param['categoryid'];
	}
	$addWhere = '( ( catidAlltime = '.implode( ' OR catidAlltime = ', $arrayAllCatid ).' )
						OR 
						( catidEdition = '.implode( ' OR catidEdition = ', $arrayAllCatid ).' ) )'; 
	
	// zugewiesene Turniere
	$query = 'SELECT id'
				. ' FROM #__clm_turniere'
				. ' WHERE '.$addWhere
				. ' ORDER BY ordering'
				;
	$db->setQuery($query);
	$arrayTurniere = $db->loadResultArray();
	
}


// String mit TurnierIDs gegeben?
if ($param['turnierid'] != '') {
	
	// aufspalten
	$arrayTurnierID = explode(',', $param['turnierid']);
	
	$arrayTurniere = array_merge($arrayTurniere, $arrayTurnierID);
	
}
	
	
// sollen Turniere ausgegeben werden?
if (count($arrayTurniere) > 0) {
	
	// doppelte ausscheiden
	$arrayTurniere = array_unique($arrayTurniere);	
	
	$arrayTextRankingScore = explode(',', $param['textrankingscore']);
	
	// INIT
	$counter = 0;
	$turData = array();
	$turRounds = array();
	
	// alle Turniere durchgehen
	foreach ($arrayTurniere as $key => $value) {
	
		// in Int umwandeln
		$value = intval($value);
	
		if ($value > 0) { // gültige ID?
		
			// Turnierdaten holen
			$query = "SELECT *, CHAR_LENGTH(invitationText) AS invitationLength"
					. " FROM #__clm_turniere"
					. " WHERE id = ".$value
						;
			$db->setQuery($query);
			if ($temp = $db->loadObject()) {
				$turData[$value] = $temp;
			
				$counter++; // Turnier existent!
			
				// Link Scoreboard
				if (isset($arrayTextRankingScore[$key])) { // text gegeben?
					$turData[$value]->stringRankingScore = $arrayTextRankingScore[$key];
				} else {
					$turData[$value]->stringRankingScore = "";
				}
			
				// Runden
				if ($param['linkroundseach'] == 1) {
					// published/unpublished
					if ($param['showunpublishedrounds'] == 0) {
						$sqlPublished = " AND published = '1'";
					} else {
						$sqlPublished = "";
					}
					// Abfrage
					$query = "SELECT id, name, dg, nr, abgeschlossen, tl_ok, published"
							. " FROM #__clm_turniere_rnd_termine"
							. " WHERE turnier = ".$value.$sqlPublished
							. " ORDER BY ordering ASC, nr ASC"
							;
					$db->setQuery($query);
					$turRounds[$value] = $db->loadObjectList();
				}
				
			}
		
		}
	
	}
	
	// content
	if ($param['contentid'] > 0) {
		$query = "SELECT title"
				. " FROM #__content"
				. " WHERE id = ".$param['contentid']
				;
		$db->setQuery($query);
		$contentTitle = $db->loadResult();
	}
	
	if ($counter > 0) {
		// nur dann Anzeige
		require(JModuleHelper::getLayoutPath('mod_clm_turmultiple'));
	}


}
// ohne turnierID gar keine Anzeige des Moduls


?>