<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

class modCLM_TurMultipleHelper {
	
	
	function makeLink($goto, $param = array(), $name, $currentid = 0) {
	
		$url = 'index.php?option=com_clm';
		$url .= '&amp;view='.$goto;
		if (count($param) > 0) {
			$url .= '&amp;'.implode("&amp;", $param);
		}
		//$url .= '&amp;Itemid=1';
		$itemid = JRequest::getVar('Itemid');
		$url .= '&amp;Itemid='.$itemid;	
		
		if ($currentid == 1) {
			$class = ' class="active_link"';
		} else {
			$class = '';
		}
		
		$tag = '<span><a href="'.JRoute::_($url).'"'.$class.'>'.$name.'</a></span>';
		
		return $tag;
	
	}

	function getIndent($menuindent) {
	
		$output = "";
		if ($menuindent > 0) {
			for ($m=1; $m<=$menuindent; $m++) {
				$output .= "&nbsp;";
			}
		}
		$output .= "&bull;&nbsp;";
		return $output;
	
	}

	function getTree() {
	
		// DB
		$_db				= & JFactory::getDBO();
	
		// alle Cats holen
		$query = "SELECT id, name, parentid FROM #__clm_categories";
		$_db->setQuery($query);
		$parentList = $_db->loadObjectList('id');
	
		// Array speichert alle Kategorien in der Tiefe ihrer Verschachtelung
		$parentArray = array();
	
		// Array speichert für alle Kategorien die spezielle einzelne parentID ab
		$parentID = array();
		
		// Array speichert für alle Kategorien die Keys aller vorhandenen Parents ab
		$parentKeys = array();
		
		// Array speichert für alle Kategorien die Childs ab
		$parentChilds = array();
		
		// aufheben für Bearbeitung in parentChilds
		$saved_parentList = $parentList;
		
		// erste Ebene der Parents
		$parentsExisting = array(); // enthält alle IDs von Parents, die bereits ermittelt wurden
		foreach ($parentList as $key => $value) {
			if (!$value->parentid OR $value->parentid == 0) {
				$parentArray[$key] = $value->name; // Name an ID binden
				$parentsExisting[] = $value->id; // ID als existierender Parent eintragen
				// Eintrag kann nun aus Liste gelöscht werden!
				unset($parentList[$key]);
				
			}
		}
	
		$continueLoop = 1; // Flag, ob Schleife weiterlaufen soll
	
		// noch Einträge vorhanden?
		WHILE (count($parentList) > 0 AND $continueLoop == 1) { 
			
			$continueLoop = 0; // abschalten - erst wieder anschalten, wenn Eintrag gefunden
			
			
			// weitere Ebenen
			foreach ($parentList as $key => $value) {
				
				// checken, ob ParentID in Array der bereits ermittelten Parents vorhanden
				if (in_array($value->parentid, $parentsExisting)) {
					
					$parentArray[$key] = $parentArray[$value->parentid].' > '.$value->name;
					
					// Parent
					$parentID[$key] = $value->parentid;
					
					// Key
					$parentKeys[$key] = array($value->parentid);
					// hatte Parent schon keys?
					if (isset($parentKeys[$value->parentid])) {
						$parentKeys[$key] = array_merge($parentKeys[$key], $parentKeys[$value->parentid]);
					}
					$parentsExisting[] = $value->id;
					
					// Eintrag kann nun aus Liste gelöscht werden!
					unset($parentList[$key]);
					
					$continueLoop = 1; // Flag, ob Schleife weiterlaufen soll
					
				}
			}
		
		}
	
	
		// alle Childs
		foreach ($saved_parentList as $key => $value) {
			// nur welche, die auch Kind sind, können Kindschaft den Parents anhängen
			if ($value->parentid > 0) {
				// allen Parents dieses Childs diesen Eintrag anhängen
				foreach ($parentKeys[$key] AS $pvalue) {
					$parentChilds[$pvalue][] = $key;
				}
			}
		}
	
		return array($parentArray, $parentKeys, $parentChilds);
	
	}


}
 