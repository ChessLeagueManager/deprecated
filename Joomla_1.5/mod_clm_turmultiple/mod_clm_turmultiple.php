<?php
// no direct access
defined('_JEXEC') or die('Restricted access');


// Include the syndicate functions only once
require_once (dirname(__FILE__).DS.'helper.php');


// INIT
$db  =& JFactory::getDBO();

// Joomla
$this->view = JRequest::getVar('view');
$this->itemid = JRequest::getVar('Itemid');

// Modul-Parameter
$this->params['turnierid'] = $params->get('turnierid', '');
$this->params['menulist'] = $params->get('menulist', 0);
$this->params['menuindent'] = $params->get('menuindent', 3);
$this->params['linkplayerslist'] = $params->get('linkplayerslist', 1);
$this->params['linkroundseach'] = $params->get('linkroundseach', 0);
$this->params['showunpublishedrounds'] = $params->get('showunpublishedrounds', 0);
$this->params['linkmatchescomplete'] = $params->get('linkmatchescomplete', 0);
$this->params['linktable'] = $params->get('linktable', 0);
$this->params['linkrankingscore'] = $params->get('linkrankingscore', 0);
$this->params['textrankingscore'] = $params->get('textrankingscore', '');
$this->params['linkinvitation'] = $params->get('linkinvitation', 0);
$this->params['contentid'] = $params->get('contentid', '');
$this->params['textbottom'] = $params->get('textbottom', '');


// URL-Parameter
$this->runde = JRequest::getVar('runde');

// String mit TurnierIDs gegeben?
if ($this->params['turnierid'] != '') {
	
	// aufspalten
	$arrayTurnierID = explode(',', $this->params['turnierid']);
	$arrayTextRankingScore = explode(',', $this->params['textrankingscore']);
	
	// INIT
	$counter = 0;
	$turData = array();
	$turRounds = array();
	
	// alle Turniere durchgehen
	foreach ($arrayTurnierID as $key => $value) {
	
	
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
				if ($this->params['linkroundseach'] == 1) {
					// published/unpublished
					if ($this->params['showunpublishedrounds'] == 0) {
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
	if ($this->params['contentid'] > 0) {
		$query = "SELECT title"
				. " FROM #__content"
				. " WHERE id = ".$this->params['contentid']
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