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

defined('_JEXEC') or die('Restricted access');

$sid			= JRequest::getInt('saison','1');
$zps			= JRequest::getVar('zps');
$itemid			= JRequest::getInt('Itemid','1');
$verein			= $this->verein;
$vereinstats 	= $this->vereinstats;
$mannschaft		= $this->mannschaft;
$vereinsliste 	= $this->vereinsliste;
$saisons	 	= $this->saisons;
$turniere	 	= $this->turniere;

// Login Status prüfen
$clmuser= $this->clmuser;
$user	= & JFactory::getUser();

// Konfigurationsparameter auslesen
$config 			= &JComponentHelper::getParams( 'com_clm' );
$conf_vereinsdaten	=$config->get('conf_vereinsdaten',1);
$googlemaps_ver   	= $config->get('googlemaps_ver',1);
$googlemaps   		= $config->get('googlemaps',0);
$googlemaps_rtype   		= $config->get('googlemaps_rtype',0);

// Browsertitelzeile setzen
$doc =& JFactory::getDocument();
$daten['title'] = $verein[0]->name;
$doc->setHeadData($daten);
	// Aufbereitung Googledaten 1. Spiellokal
	$spiellokal1G = explode(",", $verein[0]->lokal); 
    if ($googlemaps_rtype == 1) {
        $google_address = $spiellokal1G[0].','.$spiellokal1G[1].','.$spiellokal1G[2]; }
    elseif ($googlemaps_rtype == 2) {
        $google_address = $spiellokal1G[1].','.$spiellokal1G[2]; }
    elseif ($googlemaps_rtype == 3) {
        $google_address = $spiellokal1G[0].','.$spiellokal1G[1]; }
	else $google_address = $verein[0]->lokal;
 
// Stylesheet laden
require_once(JPATH_COMPONENT.DS.'includes'.DS.'css_path.php');
require_once(JPATH_COMPONENT.DS.'includes'.DS.'image_path.php');
require_once(JPATH_COMPONENT.DS.'includes'.DS.'googlemaps.php');


echo '<div id="clm"><div id="verein">';

// Überprüfen ob diese Mannschaft bereits angelegt ist
if (!isset($verein[0]->name)){

echo '<div class="componentheading">'. JText::_('CLUB_NO_DATA') .'</div>';

}
	
?>
<Script language="JavaScript">
<!-- Vereinsliste
function goto(form) { var index=form.select.selectedIndex
if (form.select.options[index].value != "0") {
location=form.select.options[index].value;}}
//-->
</SCRIPT>

<div class="clmbox">
<?php if (isset($verein[0]->name)){ ?><a href="index.php?option=com_clm&view=dwz&saison=<?php echo $sid; ?>&zps=<?php echo $zps; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>"><?php echo JText::_('CLUB_MEMBER_LIST') ?></a> | 
<?php } ?><a href="index.php?option=com_clm&view=vereinsliste&saison=<?php echo $sid; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>"><?php echo JText::_('CLUBS_LIST') ?></a>
<span class="right">
    <form name="form1">
        <select name="select" onchange="goto(this.form)" class="selectteam">
        <option value=""><?php echo JText::_('CLUB_SELECTTEAM') ?></option>
        <?php  $cnt = 0;   foreach ($vereinsliste as $vereinsliste) { $cnt++;?>
         <option value="<?php echo JURI::base(); ?>index.php?option=com_clm&view=verein&saison=<?php echo $sid; ?>&zps=<?php echo $vereinsliste->zps; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>"
        <?php if ($vereinsliste->zps == $zps) { echo 'selected="selected"'; } ?>><?php echo $vereinsliste->name; ?></option>
        <?php } ?>
        </select>
    </form>
</span>
<div class="clr"></div>
</div>
<br />

<?php
// Vereinsdaten ändern
if ($conf_vereinsdaten == 1) {
	if ($user->get('id') > 0 AND  $clmuser[0]->published > 0 AND $clmuser[0]->zps == $zps OR $clmuser[0]->user_clm == 100) {
	 echo '<span class="edit"><a href="' . JURI::base() .'index.php?option=com_clm&view=verein&saison='. $sid .'&zps='. $zps .'&layout=vereinsdaten'; if ($itemid <>'') { echo "&Itemid=".$itemid; } echo '">'. JText::_('CLUB_DATA_EDIT') .'</a></span>'; 
	} 
} 
 ?>
<div class="componentheading"><?php echo $verein[0]->name; ?></div>

<?php if (isset($verein[0]->name)){ ?>
	<table cellpadding="0" cellspacing="0" width="100%">
    	<tr>
        <td width="40%" valign="top">
            <div class="column2">

            <div class="column">
            
                <table class="vereinstats">
                <tr>
                    <td><?php echo JText::_('CLUBS_LIST_MEMBER') ?>:</td>
                    <td><?php echo $vereinstats[0]->Mgl; ?> (<?php echo $vereinstats[0]->Mgl_m; ?> <?php echo JText::_('CLUBS_LIST_MEMBERM') ?> | <?php echo $vereinstats[0]->Mgl_w; ?> <?php echo JText::_('CLUBS_LIST_MEMBERW') ?>)</td>
                </tr>
                <tr>
                    <td><?php echo JText::_('CLUBS_LIST_DWZAV') ?>:</td>
                    <td><?php echo substr ($vereinstats[0]->DWZ, 0, -5 ); ?> ( <?php echo $vereinstats[0]->DWZ_SUM; ?> )</td>
                </tr>
                <tr>
                    <td><?php echo JText::_('CLUBS_LIST_ELOAV') ?>:</td>
                    <td><?php echo substr ($vereinstats[0]->FIDE_Elo, 0, -5); ?> ( <?php echo $vereinstats[0]->ELO_SUM; ?> )</td>
                </tr>
                </table>
            </div>
            
			</div> 
            <br />
			<div class="column2">
            <div class="column">
                <h4><?php echo JText::_('CLUB_CHIEF') ?></h4>
                <?php echo $verein[0]->vs; ?><br>
                <?php if ($verein[0]->vs_mail <>'') { echo JHTML::_( 'email.cloak', $verein[0]->vs_mail ) . "<br>"; } ?>
                <?php echo $verein[0]->vs_tel; ?>            </div>
            
            <?php if ( ($verein[0]->tl ==! false) or ($verein[0]->tl_mail ==! false) or ($verein[0]->tl_tel ==! false) ) { ?>
            <div class="column">
                <h4><?php echo JText::_('CLUB_TOURNEMENTS') ?></h4>
                <?php echo $verein[0]->tl; ?><br>
                <?php if ($verein[0]->tl_mail <>'') { echo JHTML::_( 'email.cloak', $verein[0]->tl_mail ) . "<br>"; } ?>
                <?php echo $verein[0]->tl_tel; ?>            </div>
            <?php } ?>
            
            <?php if ( ($verein[0]->jw ==! false) or ($verein[0]->jw_mail ==! false) or ($verein[0]->jw_tel ==! false) ) { ?>
            <div class="column">
                <h4><?php echo JText::_('CLUB_YOUTH') ?></h4>
                <?php echo $verein[0]->jw; ?><br>
                <?php if ($verein[0]->jw_mail <>'') { echo JHTML::_( 'email.cloak', $verein[0]->jw_mail ) . "<br>"; } ?>
                <?php echo $verein[0]->jw_tel; ?>            </div>
            <?php } ?>
            
            <?php if ( ($verein[0]->pw ==! false) or ($verein[0]->pw_mail ==! false) or ($verein[0]->pw_tel ==! false) ) { ?>
            <div class="column">
                <h4><?php echo JText::_('CLUB_PRESS') ?></h4>
                <?php echo $verein[0]->pw; ?><br>
                <?php if ($verein[0]->pw_mail <>'') { echo JHTML::_( 'email.cloak', $verein[0]->pw_mail ) . "<br>"; } ?>
                <?php echo $verein[0]->pw_tel; ?>            </div>
            <?php } ?>
            
            <?php if ( ($verein[0]->kw ==! false) or ($verein[0]->kw_mail ==! false) or ($verein[0]->kw_tel ==! false) ) { ?>
            <div class="column">
                <h4><?php echo JText::_('CLUB_MONEY') ?></h4>
                <?php echo $verein[0]->kw; ?><br>
                <?php if ($verein[0]->kw_mail <>'') { echo JHTML::_( 'email.cloak', $verein[0]->kw_mail ) . "<br>"; } ?>
                <?php echo $verein[0]->kw_tel; ?>            </div>
            <?php } ?>
            
            <?php if ( ($verein[0]->sw ==! false) or ($verein[0]->sw_mail ==! false) or ($verein[0]->sw_tel ==! false) ) { ?>
            <div class="column">
                <h4><?php echo JText::_('CLUB_SENIOR') ?></h4>
                <?php echo $verein[0]->sw; ?><br>
                <?php if ($verein[0]->sw_mail <>'') { echo JHTML::_( 'email.cloak', $verein[0]->sw_mail ) . "<br>"; } ?>
                <?php echo $verein[0]->sw_tel; ?>            </div>
            <?php } ?>
            
			<?php if ( $verein[0]->bemerkungen <> '') { ?>
            <div class="column">
            <br /><h4><?php echo JText::_('TEAM_NOTICE') ?></h4>
            <?php echo  str_replace ( "," , "<br />", $verein[0]->bemerkungen); ?>
            </div>
            <?php	} ?>
          </div>
        </td>
        <td width="60%" valign="top">
       	  <div class="column2">
            <div class="column">
              <?php $lokal = explode(",", $verein[0]->adresse); ?>
              <h4><?php echo JText::_('CLUB_LOCATION') ?></h4>
                
                <!-- Google Maps-->
                <?php if ( ($verein[0]->lokal ==! false) and ($googlemaps_ver == "1") and ($googlemaps == "1") ) { ?>
              <center><div style="border:1px solid #CCC;"><div id="map" style="width: 100%; height: 300px"><script>load();</script>map</div></div></center><br>
                <?php } ?>
                
                <div style="float:left; width: 50%;">
				<?php if($verein[0]->lokal ==! false ) { 
					$spiellokal1 = explode(",", $verein[0]->lokal);
					if ($googlemaps_rtype == 1) {
						echo str_replace ( "," , "<br />", $verein[0]->lokal) . 
						'<br><a href="http://maps.google.com/maps?hl=de&saddr=&daddr='. $spiellokal1[0].','. $spiellokal1[1].','.$spiellokal1[2] .'" target="_blank">'. JText::_('CLM_ROUTE') .'</a>'; 
					} elseif ($googlemaps_rtype == 2) {
						echo str_replace ( "," , "<br />", $verein[0]->lokal) . 
						'<br><a href="http://maps.google.com/maps?hl=de&saddr=&daddr='. $spiellokal1[1].','.$spiellokal1[2] .'" target="_blank">'. JText::_('CLM_ROUTE') .'</a>'; 
					} elseif ($googlemaps_rtype == 3) {
						echo str_replace ( "," , "<br />", $verein[0]->lokal) . 
						'<br><a href="http://maps.google.com/maps?hl=de&saddr=&daddr='. $spiellokal1[0].','.$spiellokal1[1] .'" target="_blank">'. JText::_('CLM_ROUTE') .'</a>'; 
					} else {
						echo str_replace ( "," , "<br />", $verein[0]->lokal) . 
						'<br><a href="http://maps.google.com/maps?hl=de&saddr=&daddr='. $verein[0]->lokal .'" target="_blank">'. JText::_('CLM_ROUTE') .'</a>'; 
					}
				} ?>
                </div>
                <div style="float:right; width: 50%;">
				<?php if($verein[0]->adresse ==! false ) { 
					$spiellokal2 = explode(",", $verein[0]->adresse);
					if ($googlemaps_rtype == 1) {
						echo  str_replace ( "," , "<br />", $verein[0]->adresse ) . 
						'<br><a href="http://maps.google.com/maps?hl=de&saddr=&daddr='. $spiellokal2[0].','. $spiellokal2[1].','.$spiellokal2[2] .'" target="_blank">'. JText::_('CLM_ROUTE') .'</a>'; 
					} elseif ($googlemaps_rtype == 2) {
						echo  str_replace ( "," , "<br />", $verein[0]->adresse ) . 
						'<br><a href="http://maps.google.com/maps?hl=de&saddr=&daddr='. $spiellokal2[1].','.$spiellokal2[2] .'" target="_blank">'. JText::_('CLM_ROUTE') .'</a>'; 
					} elseif ($googlemaps_rtype == 3) {
						echo  str_replace ( "," , "<br />", $verein[0]->adresse ) . 
						'<br><a href="http://maps.google.com/maps?hl=de&saddr=&daddr='. $spiellokal2[0].','.$spiellokal2[1] .'" target="_blank">'. JText::_('CLM_ROUTE') .'</a>'; 
					} else {
						echo  str_replace ( "," , "<br />", $verein[0]->adresse ) . 
						'<br><a href="http://maps.google.com/maps?hl=de&saddr=&daddr='. $verein[0]->adresse .'" target="_blank">'. JText::_('CLM_ROUTE') .'</a>'; 
					}
			    } ?>
                </div>
            </div>
            <div class="clr"></div>
            
            <?php if ($verein[0]->termine ==! false) { ?>
            <br />
            <div class="column">
                <h4><?php echo JText::_('CLUB_EVENTS') ?></h4>
                <?php echo str_replace ( "," , "<br />", $verein[0]->termine); ?>            </div>
            <?php } ?>
            
            <?php if ($verein[0]->homepage ==! false) { ?>
            <div class="column">
                <h4><?php echo JText::_('CLUB_HOMEPAGE') ?></h4>
                <a href="<?php echo $verein[0]->homepage; ?>"><?php echo $verein[0]->homepage; ?></a>            </div>
            <?php } ?>
        </div>
        </td>
        </tr>
    </table>
<?php } ?>
    
    <table cellpadding="0" cellspacing="0" width="100%">
    	<tr>
        <td width="50%" valign="top">
            <div class="column2">
                <div class="column">
                <span class="right">
                <form name="form1">
                    <select name="select" onchange="goto(this.form)" class="selectteam">
						<?php foreach ($saisons as $saisons) { ?>
                            <option value="<?php echo JURI::base(); ?>index.php?option=com_clm&view=verein&zps=<?php echo $zps; ?>&saison=<?php echo $saisons->id; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>"
                            <?php if ($saisons->id == $sid) { echo 'selected="selected"'; } ?>><?php echo $saisons->name; ?> </option>
                        <?php } ?>
                    </select>
                </form>
                </span>
                <?php if (isset($mannschaft[0]->name)){ ?>
                      <h4><?php echo JText::_('CLUB_TEAMS') ?></h4>
                        <ul>
                        <?php $cnt = 0;
                        foreach ($mannschaft as $mannschaft) { $cnt++;?>
                        <li><a href="index.php?option=com_clm&view=mannschaft&saison=<?php echo $sid; ?>&liga=<?php echo $mannschaft->liga; ?>&tlnr=<?php echo $mannschaft->tln_nr; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>"><?php echo $mannschaft->name; ?></a>
                         - <a href="index.php?option=com_clm&view=rangliste&saison=<?php echo $sid; ?>&liga=<?php echo $mannschaft->liga; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>"><?php echo $mannschaft->liga_name; ?><br></a></li><?php } ?>
                      </ul>
                    <br />
                <?php } ?>
                <?php if (isset($turniere[0]->name)){ ?>
                	<h4><?php echo JText::_('CLUB_TOURN') ?></h4>
                        <ul>
                        <?php $cnt = 0;
                        foreach ($turniere as $turniere) { $cnt++;?>
                        <li><a href="index.php?option=com_clm&view=turnier_info&turnier=<?php echo $turniere->id; ?>"><?php echo $turniere->name; ?></a><?php } ?>
                      </ul>
                </div>
				<?php } ?>
            </div>
        </td>
    	</tr>
    </table>
<div class="clr"></div>

<?php require_once(JPATH_COMPONENT.DS.'includes'.DS.'copy.php'); ?>

</div>
</div>