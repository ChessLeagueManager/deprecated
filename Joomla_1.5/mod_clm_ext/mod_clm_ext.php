<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

// angemeldet
require_once (dirname(__FILE__).DS.'helper.php');

$link	= modCLM_EXTHelper::getLink($params);
$count	= modCLM_EXTHelper::getCount($params);

require(JModuleHelper::getLayoutPath('mod_clm_ext'));


