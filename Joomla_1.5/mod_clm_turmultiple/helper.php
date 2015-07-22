<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

class modCLM_TurMultipleHelper {
	
	
	function makeLink($goto, $params = array(), $string) {
	
		$url = 'index.php?option=com_clm';
		$url .= '&amp;view='.$goto;
		if (count($params) > 0) {
			$url .= '&amp;'.implode("&amp;", $params);
		}
		$url .= '&amp;Itemid='.$this->itemid;
		
		if ($this->view == $goto) {
			$class = ' class="active_link"';
		} else {
			$class = '';
		}
		
		$tag = '<span><a href="'.JRoute::_($url).'"'.$class.'>'.$string.'</a></span>';
		
		return $tag;
	
	}


	function getIndent() {
	
		$output = "";
		if ($this->params['menuindent'] > 0) {
			for ($m=1; $m<=$this->params['menuindent']; $m++) {
				$output .= "&nbsp;";
			}
		}
		$output .= "&bull;&nbsp;";
		return $output;
	
	}

}