<?php // no direct access
defined('_JEXEC') or die('Restricted access'); 

// aktuell angezeigtes/ausgewähltes Turnier
$turnierid = JRequest::getInt('turnier', 0);

// Anzeige/Menu beginnen
echo '<div class="module">';

if ($params->get('menulist', 0)) {
	echo '<ul>';
}

$rankingscoreorderby = $params->get('rankingscoreorderby', 1);
if ($rankingscoreorderby == 1) {
	$str_rsorderby = "orderby=snr";
} else {
	$str_rsorderby = "orderby=pos";
}

// alle Turniere mit Turniernamen durchgehen
foreach ($turData as $value) {

	if ($params->get('menulist', 0)) {
		echo '<li>';
	}
	// Name	
	// Link je nach ParameterEinstellung setzen
	switch($params->get('namelinksto', 0)) {
		case 3: // Paarungstafel
			echo modCLM_TurMultipleHelper::makeLink('turnier_rangliste', array("turnier=".$value->id, $str_rsorderby), $value->name);
			break;
		case 2: // alle Partien
			echo modCLM_TurMultipleHelper::makeLink('turnier_paarungsliste', array("turnier=".$value->id), $value->name);
			break;
		case 1: // Runde 1
			// TODO: Rundenname
			echo modCLM_TurMultipleHelper::makeLink('turnier_runde', array("runde=1", "turnier=".$value->id), $value->name);
			break;
		case 0: // TlnLIste
			echo modCLM_TurMultipleHelper::makeLink('turnier_teilnehmer', array("turnier=".$value->id), $value->name);
			break;
		case 5: // Ausschreibung
			echo modCLM_TurMultipleHelper::makeLink('turnier_invitation', array("turnier=".$value->id), $value->name);
			break;
		case 6: // Tabelle
			echo modCLM_TurMultipleHelper::makeLink('turnier_tabelle', array("turnier=".$value->id), $value->name);
			break;
		case 4: // Turnier-Info
		default:
			echo modCLM_TurMultipleHelper::makeLink('turnier_info', array("turnier=".$value->id), $value->name);
			break;
	}
	if (!$params->get('menulist', 0)) {
		echo '<br />';
	}
	
	
	// aktiviertes Turnier aufklappen!
	if ($value->id == $turnierid) {
	
		// Turnier veröffentlicht?
		if ($value->published == 1) {
		
			$listArray = array();
		
			// Teilnehmerliste
			if ($this->params['linkplayerslist'] == 1) {
				$listArray[] = modCLM_TurMultipleHelper::makeLink('turnier_teilnehmer', array("turnier=".$value->id), JText::_('PLAYERSLIST'));
			}
		
			// Runden
			if ($this->params['linkroundseach'] == 1) {
				// alle ausgelesenen Runden durchgehen
				foreach ($turRounds[$value->id] as $round) {
					$listArray[] = modCLM_TurMultipleHelper::makeLink('turnier_runde', array("runde=".$round->nr, "turnier=".$value->id), $round->name);
				}
			}
			
			// alle Matches
			if ($this->params['linkmatchescomplete'] == 1) {
				$listArray[] = modCLM_TurMultipleHelper::makeLink('turnier_paarungsliste', array("turnier=".$value->id), JText::_('MATCHESCOMPLETE'));
			}
			
			// Tabelle
			if ($this->params['linktable'] == 1 AND $value->typ != 3) {
				$listArray[] = modCLM_TurMultipleHelper::makeLink('turnier_tabelle', array("turnier=".$value->id), JText::_('TABLE'));
			}
			
			// Fortschritt/Paarungstafel
			if ($this->params['linkrankingscore'] == 1 AND $value->typ != 3) {
				if (strlen($value->stringRankingScore) > 0) {
					$text = $value->stringRankingScore;
				} elseif ($value->typ == 1) {
					$text = JText::_('RANKING');
				} elseif ($value->typ == 2) {
					$text = JText::_('SCOREBOARD');
				}
				$listArray[] = modCLM_TurMultipleHelper::makeLink('turnier_rangliste', array("turnier=".$value->id, $str_rsorderby), $text);
			}
	
			// Ausschreibung
			if ($this->params['linkinvitation'] == 1 AND $value->invitationLength > 0) {
				$listArray[] = modCLM_TurMultipleHelper::makeLink('turnier_invitation', array("turnier=".$value->id), JText::_('INVITATION'));
			}
			
			// Liste ausgeben
			if ($params->get('menulist', 0)) {
				echo '<ul>';
				foreach ($listArray as $link) {
					echo '<li>'.$link;
				}
				echo '</ul>';
			} else {
				foreach ($listArray as $link) {
					echo modCLM_TurMultipleHelper::getIndent();
					echo $link;
					echo '<br />';
				}
			}
			
			
			// Bemerkungen
			if ($params->get('shownotes', 0) == 1) {
				echo nl2br(JFilterOutput::cleantext($value->bemerkungen)).'<br />';
			}
	
	
		} else { // nicht veröffentlicht
			echo modCLM_TurMultipleHelper::getIndent();
			echo JText::_('UNPUBLISHED');
			echo '<br />';
		}
	
	}
	echo '<br />';

}


if ($params->get('menulist', 0)) {
	echo '</ul>';
}

// ContentID gesetzt, Artikel gefunden
if ($this->params['contentid'] > 0 AND isset($contentTitle)) {
	$contenttext = $params->get('contenttext', '');
	if ($contenttext != '') {
		$textToUse = $contenttext;
	} else {
		$textToUse = $contentTitle;
	}
	echo '<a href="index.php?option=com_content&view=article&id='.$this->params['contentid'].'">'.$textToUse.'</a>';
	echo '<br />';
	
}

// Text unterhalb
if ($this->params['textbottom'] != '') {
	echo nl2br(JFilterOutput::cleantext($this->params['textbottom']));
}


// Menu Ende
echo '</div>';

?>




