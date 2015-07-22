<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// angemeldet
require_once (dirname(__FILE__).DS.'helper.php');

$link = modCLM_ArchivHelper::getLink($params);
$count = modCLM_ArchivHelper::getCount($params);
$saison = modCLM_ArchivHelper::getSaison($params);

require(JModuleHelper::getLayoutPath('mod_clm_archiv'));


