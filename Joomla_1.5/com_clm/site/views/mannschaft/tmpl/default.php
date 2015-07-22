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
JHtml::_('behavior.tooltip', '.CLMTooltip', $params);

// Variablen ohne foreach setzen
//$aufstellung	=$this->aufstellung;
$mannschaft	=$this->mannschaft;
$count		=$this->count;
$bp			=$this->bp;
$sumbp		=$this->sumbp;
$plan		=$this->plan;
$termin		=$this->termin;
$einzel		=$this->einzel;
$saison 	=$this->saison;
$sub_liga	=$this->sub_liga;
$sub_msch	=$this->sub_msch;
$sub_rnd	=$this->sub_rnd;

// Variblen aus URL holen
$sid 		= JRequest::getInt('saison','1');
$lid		= JRequest::getInt('liga','1'); 
$liga 		= JRequest::getInt( 'liga', '1' );
$tln 		= JRequest::getInt('tlnr');
$itemid 	= JRequest::getInt('Itemid','1');
$option 	= JRequest::getCmd( 'option' );

$sql = ' SELECT `sieg`, `remis`, `nieder`, `antritt` FROM #__clm_liga'
		. ' WHERE `id` = "' . $liga . '"';
$db =& JFactory::getDBO ();
$db->setQuery ($sql);
$ligapunkte = $db->loadObject ();

if ($saison[0]->dsb_datum  > 0) $hint_dwzdsb = JText::_('DWZ_DSB_COMMENT_RUN').' '.utf8_decode(JText::_('ON_DAY')).' '.JHTML::_('date',  $saison[0]->dsb_datum, JText::_('%d. %B %Y'));  
if (($saison[0]->dsb_datum == 0) || (!isset($saison))) $hint_dwzdsb = JText::_('DWZ_DSB_COMMENT_UNCLEAR'); 

if ( !$mannschaft OR $mannschaft[0]->lpublished == 0) {
	$msg = JText::_('NOT_PUBLISHED').JText::_('GEDULD');
	$link = 'index.php?option='.$option.'&view=info&Itemid='.$itemid;
	$mainframe->redirect( $link, $msg );
	 }
if ( $mannschaft[0]->published == 0) {
	$msg = JText::_('TEAM_NOT_PUBLISHED').JText::_('GEDULD');
	$link = 'index.php?option='.$option.'&view=info&Itemid='.$itemid;
	$mainframe->redirect( $link, $msg );
	}
if ($mannschaft[0]->lpublished != 0 AND $mannschaft[0]->published != 0) {

// Stylesheet laden
require_once(JPATH_COMPONENT.DS.'includes'.DS.'css_path.php');
require_once(JPATH_COMPONENT.DS.'includes'.DS.'image_path.php'); 

	// Browsertitelzeile setzen
	$doc =& JFactory::getDocument();
	$daten['title'] = $mannschaft[0]->name.' - '.$mannschaft[0]->liga_name;
	$doc->setHeadData($daten);

	// Konfigurationsparameter auslesen
	$config	= &JComponentHelper::getParams( 'com_clm' );
	$telefon= $config->get('man_tel',1);
	$mobil	= $config->get('man_mobil',1);
	$mail	= $config->get('man_mail',1);
	$man_manleader	= $config->get('man_manleader',1);
	$man_spiellokal	= $config->get('man_spiellokal',1);
	$man_spielplan	= $config->get('man_spielplan',1);
	$fixth_msch = $config->get('fixth_msch',1);
	$googlemaps_msch   = $config->get('googlemaps_msch',1);
	$googlemaps   = $config->get('googlemaps',0);
	$googlemaps_rtype   = $config->get('googlemaps_rtype',0);
	// Aufbereitung Googledaten 1. Spiellokal
	$spiellokal1G = explode(",", $mannschaft[0]->lokal); 
    if ($googlemaps_rtype == 1) {
        $google_address = $spiellokal1G[0].','.$spiellokal1G[1].','.$spiellokal1G[2]; }
    elseif ($googlemaps_rtype == 2) {
        $google_address = $spiellokal1G[1].','.$spiellokal1G[2]; }
    elseif ($googlemaps_rtype == 3) {
        $google_address = $spiellokal1G[0].','.$spiellokal1G[1]; }
	else $google_address = $mannschaft[0]->lokal;
require_once(JPATH_COMPONENT.DS.'includes'.DS.'googlemaps.php');
	
	// Userkennung holen
	$user	=& JFactory::getUser();
	$jid	= $user->get('id');
?>

<div id="clm">
<div id="mannschaft">

    <div class="componentheading"><?php echo $mannschaft[0]->name; ?> - <?php echo $mannschaft[0]->liga_name; ?></div>

<?php require_once(JPATH_COMPONENT.DS.'includes'.DS.'submenu.php'); ?>
    
    <div class="clmbox"><a href="index.php?option=com_clm&view=verein&saison=<?php echo $sid; ?>&zps=<?php echo $mannschaft[0]->zps; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>"><?php echo JText::_('TEAM_DETAILS') ?></a> | <a href="index.php?option=com_clm&view=dwz&saison=<?php echo $sid; ?>&zps=<?php echo $mannschaft[0]->zps; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>"><?php echo JText::_('TEAM_OVERVIEW') ?></a>
    <div id="pdf">
	<?php if ($jid !="0") { ?>
    <div class="pdf"><a href="index.php?option=com_clm&view=mannschaft&format=clm_pdf&layout=team&o_nr=1&saison=<?php echo $mannschaft[0]->sid ?>&liga=<?php echo $mannschaft[0]->liga ?>&tlnr=<?php echo $mannschaft[0]->tln_nr ?>"><img src="<?php echo $imageDir; ?>pdf_button.png" alt="PDF"  title="<?php echo JText::_('TEAM_PDF_1') ?>" class="CLMTooltip" /></a>
    </div>
	<?php } ?>
	<div class="pdf"><a href="index.php?option=com_clm&view=mannschaft&format=clm_pdf&layout=team&o_nr=0&saison=<?php echo $mannschaft[0]->sid ?>&liga=<?php echo $mannschaft[0]->liga ?>&tlnr=<?php echo $mannschaft[0]->tln_nr ?>"><img src="<?php echo $imageDir; ?>pdf_button.png" alt="PDF"  title="<?php echo JText::_('TEAM_PDF') ?>" class="CLMTooltip" /></a>
    </div>
	</div></div>
    <div class="clr"></div>
    
    <div class="teamdetails">
        <div id="leftalign">
    <?php if ($man_manleader =="1") { ?>
        <b><?php echo JText::_('TEAM_LEADER') ?></b><br>
        <?php if ( $mannschaft[0]->mf_name <> '' ) {
        echo $mannschaft[0]->mf_name; ?><br>
        <?php if ($mail=="1" OR ($mail =="0" AND $jid !="0")) { echo JHTML::_( 'email.cloak', $mannschaft[0]->email );} 
              else { echo JText::_('TEAM_MAIL'); echo JText::_('TEAM_REGISTERED');} ?><br> 
        
        <?php if ($mannschaft[0]->tel_fest !='') { 
              if ($telefon =="1" OR ($telefon =="0" AND $jid !="0")) { echo JText::_('TEAM_FON'); echo " ".$mannschaft[0]->tel_fest; }
              if ($telefon =="0" AND $jid =="0") { echo JText::_('TEAM_FON'); echo JText::_('TEAM_REGISTERED'); }
              ?><br>
        <?php } else { echo JText::_('TEAM_NO_FONE');  } ?>
        
        <?php if ($mannschaft[0]->tel_mobil <> '') { 
              if ($mobil =="1" OR ($mobil =="0" AND $jid !="0")) { echo JText::_('TEAM_MOBILE');echo " ".$mannschaft[0]->tel_mobil; }
              if ($mobil =="0" AND $jid =="0") { echo JText::_('TEAM_MOBILE');echo JText::_('TEAM_REGISTERED'); }
        }
        else { echo JText::_('TEAM_NO_MOBILE') ; }
                                }
        else { ?><?php echo JText::_('TEAM_NOT_SET') ?><?php }} ?>
        </div>
        <div id="rightalign">
    <?php if ($man_spiellokal =="1") { ?>
        <?php echo JText::_('TEAM_LOCATION'); ?>
        <?php if ( ($mannschaft[0]->lokal ==! false) and ($googlemaps_msch == "1") and ($googlemaps == "1") ) { ?>&nbsp;(&nbsp;
        <a href="index.php?option=com_clm&view=mannschaft&saison=<?php echo $mannschaft[0]->sid ?>&liga=<?php echo $mannschaft[0]->liga ?>&tlnr=<?php echo $mannschaft[0]->tln_nr ?>#google"><?php echo JText::_('TEAM_KARTE') ?></a>&nbsp;)<?php } ?>
        <br />
        <div style="float:left; width: 50%;">
            <?php $spiellokal1 = explode(",", $mannschaft[0]->lokal); 
				$spiellokal2 = explode(",", $mannschaft[0]->adresse); ?>
            <?php
            // 1. Spiellokal
            if($spiellokal1[0] ==! false ) { echo $spiellokal1[0]; } 
            if(isset($spiellokal1[1])) { echo "<br>".$spiellokal1[1];}
            if(isset($spiellokal1[2])) { echo "<br>".$spiellokal1[2];}
            
            // Routenplaner
            if($spiellokal1[0] ==! false ) { 
				if ($googlemaps_rtype == 1) {
					echo '<br><a href="http://maps.google.com/maps?hl=de&saddr=&daddr=' . $spiellokal1[0].','. $spiellokal1[1].','.$spiellokal1[2].'" target="_blank">'. JText::_('CLM_ROUTE') .'</a>'; }
				elseif ($googlemaps_rtype == 2) {
					echo '<br><a href="http://maps.google.com/maps?hl=de&saddr=&daddr=' . $spiellokal1[1].','.$spiellokal1[2].'" target="_blank">'. JText::_('CLM_ROUTE') .'</a>'; }
				elseif ($googlemaps_rtype == 3) {
					echo '<br><a href="http://maps.google.com/maps?hl=de&saddr=&daddr=' . $spiellokal1[0].','.$spiellokal1[1].'" target="_blank">'. JText::_('CLM_ROUTE') .'</a>'; }
				else { echo '<br><a href="http://maps.google.com/maps?hl=de&saddr=&daddr=' . $spiellokal1[0].','.$spiellokal1[1].','.$spiellokal1[2].'" target="_blank">'. JText::_('CLM_ROUTE') .'</a>'; }
            } ?>
            </div>
            <div style="float:right; width: 50%;">
            <?php	
            // 2. Spiellokal
            if($spiellokal2[0] ==! false ) { echo $spiellokal2[0]; }
            if(isset($spiellokal2[1])) { echo "<br />".$spiellokal2[1];}
            if(isset($spiellokal2[2])) { echo "<br />".$spiellokal2[2];}
            ?>
        </div>
    <?php } ?>
    <div class="clr"></div>
        
        <?php
		// Termine
        if($mannschaft[0]->termine ==! false ) { echo "<br />". str_replace ( "," , "<br />", $mannschaft[0]->termine ); } 
				
		// Homepage
        if($mannschaft[0]->homepage ==! false) { ?><br><a href="<?php echo $mannschaft[0]->homepage; ?>" target="blank"><?php echo $mannschaft[0]->homepage; ?></a><?php }
        ?>
        </div>
    <div class="clr"></div>
    
    <?php if ( $mannschaft[0]->bemerkungen <> '') { ?>
    <br /><b><?php echo JText::_('TEAM_NOTICE') ?></b><br />
    <?php echo $mannschaft[0]->bemerkungen; ?>
    <?php	} ?>
    </div>
    
    <?php
	if ($mannschaft[0]->anzeige_ma == 1){ ?>
    <div id="wrong"><?php echo JText::_('TEAM_FORMATION_BLOCKED') ?></div><br>
    <?php }  elseif  (!$count){ ?>
    <div id="wrong"><?php echo JText::_('TEAM_NO_FORMATION') ?></div><br>
    <?php }  else {
    
    ?>
    <h4><?php echo JText::_('TEAM_FORMATION') ?></h4>
    <table cellpadding="0" cellspacing="0" id="mannschaft" <?php if ($fixth_msch =="1") { ?>class="tableWithFloatingHeader"<?php } ?>>
    
    <tr>
    <?php if($mannschaft[0]->lrang > 0) { ?><th class="nr"><?php echo JText::_('TEAM_RANK') ?></th><?php }
        else { ?><th class="nr"><?php echo JText::_('DWZ_NR') ?></th><?php } ?>
        <th class="name"><?php echo JText::_('DWZ_NAME') ?></th>
        <th class="dwz"><a title="<?php echo $hint_dwzdsb; ?>" class="CLMTooltip"><?php echo JText::_('LEAGUE_STAT_DWZ') ?></a></th>
	 <?php 
    // erster Durchgang
    for ($b=0; $b<$mannschaft[0]->runden; $b++) { ?>
        <th class="rnd"><a href="index.php?option=com_clm&view=runde&saison=<?php echo $sid; ?>&liga=<?php echo $liga; ?>&runde=<?php echo $b+1; ?>&dg=1<?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>"><?php echo $b+1; ?></th></a><?php } 
    
    // zweiter Durchgang
    if ($mannschaft[0]->dg >1) {
    for ($b=0; $b<$mannschaft[0]->runden; $b++) { ?>
        <th class="rnd"><a href="index.php?option=com_clm&view=runde&saison=<?php echo $sid; ?>&liga=<?php echo $liga; ?>&runde=<?php echo $b+1; ?>&dg=2<?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>"><?php echo $b+1; ?></th></a><?php 
                            }
                }
    ?>
        <th class="punkte"><?php echo JText::_('TEAM_POINTS') ?></th>
        <th class="spiele"><?php echo JText::_('TEAM_GAMES') ?></th>
        <th class="prozent"><?php echo JText::_('LEAGUE_STAT_PERCENT') ?></th>
    </tr>
    <?php
    $y = 1;
    // Teilnehmerschleife
    $ie = 0;
	$sumspl = 0;
	$sumgespielt = 0;
for ($x=0; $x< 100; $x++){
	// Überlesen von Null-Sätzen 
	while (($count[$x]) and ($count[$x]->mgl_nr == "0"))  {
		$x++; }
	if (!$count[$x]) break;
    if ($y%2 == 0) { $zeilenr = zeile2; }
        else { $zeilenr = zeile1; } ?>
        
    <tr class="<?php echo $zeilenr; ?>">
    <?php if($mannschaft[0]->lrang > 0) { ?><td class="nr" ><?php echo $count[$x]->rmnr.' - '.$count[$x]->rrang; ?></td><?php }
        else { ?><td class="nr" ><?php echo $y; ?></td><?php } ?>
    <td class="name"><a href="index.php?option=com_clm&view=spieler&saison=<?php echo $sid; ?>&zps=<?php echo $count[$x]->zps; ?>&mglnr=<?php echo $count[$x]->mgl_nr; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>"><?php echo $count[$x]->name; ?></a></td>
    <td class="dwz"><?php if ($count[$x]->dwz >0) { echo $count[$x]->dwz;} ?></td>
    <?php
	//keine Ergebnisse zum Spieler
    if (($count[$x]->zps !== $einzel[$ie]->zps)||($count[$x]->mgl_nr !== $einzel[$ie]->spieler)) {
        for ($z=0; $z< $mannschaft[0]->dg*$mannschaft[0]->runden; $z++)
        { ?>
        <td class="rnd">&nbsp;</td>
    <?php	} ?>
    <td class="punkte">&nbsp;</td>
    <td class="spiele">&nbsp;</td>
    <td class="prozent">&nbsp;</td>
    </tr>
    <?php
    $y++;
	continue; 
	}
    //Spieler mit Ergebnissen
    $pkt = 0;
    $spl = 0;
    $gespielt = 0;
  for ($c=0; $c<$mannschaft[0]->dg; $c++) {
	for ($b=0; $b<$mannschaft[0]->runden; $b++) {
	    if (($einzel[$ie])&&($einzel[$ie]->dg==$c+1)&&($einzel[$ie]->runde==$b+1)&&($count[$x]->zps==$einzel[$ie]->zps)&&($count[$x]->mgl_nr==$einzel[$ie]->spieler)) {
		
			$search = array ('.0', '0.5');
			$replace = array ('', '&frac12;');
			$punkte_text = str_replace ($search, $replace, $einzel[$ie]->punkte);
			
			if ($einzel[$ie]->kampflos == 0) {
				$dr_einzel = $punkte_text;
			}
			else {
				if ($config->get('fe_display_lose_by_default',0) == 0) {
					if($einzel[$ie]->punkte == 0) {
						$dr_einzel = "-";
					} else {
						$dr_einzel = "+";
					}
				} elseif ($config->get('fe_display_lose_by_default',0) == 1) {
					$dr_einzel =  $punkte_text.' (kl)';
				} else {
					$dr_einzel = $punkte_text;
				}
			}

			?>
				<td class="rnd" style="white-space: nowrap;"><?php echo $dr_einzel; ?></td>
			<?php
			if ($einzel[$ie]->kampflos == 0) {
				$gespielt++;
    	    	$sumgespielt++;
    	    }
        	$spl++;	
        	$sumspl++;
			$pkt += $einzel[$ie]->punkte;
			$ie++;
		}
        else { ?>
        <td class="rnd">&nbsp;</td>
    <?php	     }
        }
			}

//    $prozent = round(100*($punkte/$spl));
	if(($gespielt * $ligapunkte->sieg) != 0) {
//		$prozent = round (100 * ($pkt - $gespielt * $ligapunkte->antritt) / ($gespielt * $ligapunkte->sieg), 1);
		$prozent = round (100 * ($pkt - $spl * $ligapunkte->antritt) / ($spl * $ligapunkte->sieg), 1);
	} else {
		$prozent = '';
		$gespielt = '';
		$pkt = '';
	}
    ?>
    <td class="punkte"><?php echo $pkt; ?></td>
    <td class="spiele"><?php echo $spl; //echo $gespielt; ?></td>
    <td class="prozent"><?php echo $prozent ?></td>
    
    </tr>
    <?php
    $y++;
                    }
	while ($einzel[$ie]) {
		$ztext = "    "."Ergebnis übersprungen, da Spieler nicht in Aufstellung ";
		$ztext .= ' Verein:'.$einzel[$ie]->zps.' Mitglied:'.$einzel[$ie]->spieler;
		$ztext .= ' Durchgang:'.$einzel[$ie]->dg.' Runde:'.$einzel[$ie]->runde;
		$ztext .= ' Brett:'.$einzel[$ie]->brett.' Erg:'.$einzel[$ie]->punkte; 	
		$zcolspan = 6 + ($mannschaft[0]->dg * $mannschaft[0]->runden);
		?>
		<tr class="<?php echo $zeilenr; ?>">
			<td class="name" colspan ="<?php echo $zcolspan; ?>"><?php echo $ztext; ?></td>
		</tr> 
		<?php $ie++;
	}
	
    ?>
    <tr class="ende">
    <td colspan="3">Gesamt</td>
    <?php	$spl = 0; $gespielt = 0;
    // erster Durchgang
        for ($z=0; $z< $mannschaft[0]->runden; $z++) { 			
			while ((isset($bp[$spl]->tln_nr)) AND ($bp[$spl]->tln_nr != $mannschaft[0]->tln_nr)) { $spl++; } 
			if (isset($bp[$spl]->runde) AND $bp[$spl]->runde == $z+1) { ?>
    <td class="rnd"><?php echo str_replace ('.0', '', $bp[$spl]->brettpunkte); ?></td>
    <?php 
        $spl++;
                        }
         else { ?>
    <td class="rnd">&nbsp;</td>
    <?php 		}
        }
    // zweiter Durchgang
    if ( $mannschaft[0]->dg > 1) {
        for ($z=0; $z< $mannschaft[0]->runden; $z++)
        {
        if ($bp[$spl]->runde == $z+1) { ?>
    <td class="rnd"><?php echo $bp[$spl]->brettpunkte; ?></td>
    <?php 	$spl++;			}
         else { ?>
    <td class="rnd">&nbsp;</td>
    <?php 		}}}
    ?>
    <td class="punkte"><?php echo str_replace ('.0', '', $sumbp[0]->summe); ?></td>
    <td class="spiele"><?php echo $sumspl; //echo $sumgespielt; ?></td>
    <?php if ($sumgespielt < 1) { ?>
    <td class="spiele">&nbsp;</td>
    <?php } else { ?>
    <td class="prozent"><?php echo round(100*($sumbp[0]->summe - $sumspl * $ligapunkte->antritt) / ($sumspl * $ligapunkte->sieg), 1); //echo round(100*($sumbp[0]->summe - $sumgespielt * $ligapunkte->antritt) / ($sumgespielt * $ligapunkte->sieg), 1); ?></td>
    <?php } ?>
    </tr>
    
    </table><br>
    <?php } ?>
    
    <?php if ($man_spielplan =="1") { ?>
    <h4><?php echo JText::_('TEAM_PLAN') ?></h4>
    
    <table cellpadding="0" cellspacing="0" class="spielplan">
    <tr>
        <th><?php echo JText::_('TEAM_ROUNDS') ?></th>
        <th><?php echo JText::_('TEAM_PAIR') ?></th>
        <th><?php echo JText::_('TEAM_DATE') ?></th>
        <th><?php echo JText::_('TEAM_HOME') ?></th>
        <th><?php echo JText::_('TEAM_GUEST') ?></th>
    </tr>
    <?php 
    $cnt = 0;
    foreach ($plan as $plan) { 
    $datum =& JFactory::getDate($plan->datum);?>
    <tr>
    <td><a href="index.php?option=com_clm&view=runde&saison=<?php echo $sid; ?>&liga=<?php echo $liga; ?>&runde=<?php echo $plan->runde; ?>&dg=<?php echo $plan->dg; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>"><?php echo $plan->runde; ?></a></td>
    <td><?php echo $plan->paar; ?></td>
    <td><?php while (isset($termin[$cnt]->nr) AND ($plan->runde + $mannschaft[0]->runden*($plan->dg -1)) > $termin[$cnt]->nr) { 
			$cnt++; }
		    if (isset($termin[$cnt]->nr) AND ($plan->runde + $mannschaft[0]->runden*($plan->dg -1))== $termin[$cnt]->nr) { echo JHTML::_('date',  $termin[$cnt]->datum, JText::_('%d.%m.%Y')); $cnt++ ;} ?></td>
    <?php if ($plan->tln_nr == $tln) { ?>
        <td><?php echo $plan->hname; ?></td>
        <td>
        <?php if ($plan->gpublished == 1) { ?>
        <a href="index.php?option=com_clm&view=mannschaft&saison=<?php echo $sid; ?>&liga=<?php echo $liga ?>&tlnr=<?php echo $plan->gegner; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>"><?php echo $plan->gname; ?></a>
        <?php } 
        else { echo $plan->gname; } ?>
        </td>
    <?php 	} else { ?>
        <td>
        <?php if ($plan->hpublished == 1) { ?>
        <a href="index.php?option=com_clm&view=mannschaft&saison=<?php echo $sid; ?>&liga=<?php echo $liga ?>&tlnr=<?php echo $plan->tln_nr; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>"><?php echo $plan->hname; ?></a>
        <?php } 
        else { echo $plan->hname; } ?>
        </td>
        <td><?php echo $plan->gname; ?></td>
    <?php } ?>
    </tr>
    <?php } ?>
    </table>
    <?php }} ?>
    <br>
    
    <?php if ( ($mannschaft[0]->lokal ==! false) and ($googlemaps_msch == "1")  and ($googlemaps == "1") ) { ?>
    <a name="google"></a><h4><?php echo JText::_('GOOGLE_MAPS') ?></h4>
    <!-- Google Maps-->
    <center><div style="border:1px solid #CCC;"><div id="map" style="width: 100%; height: 300px;"><script>load();</script>map</div></div></center>
    <br>
    <?php } ?>
    <?php echo '<div class="hint">'.$hint_dwzdsb.'</div><br>'; ?>

    <?php require_once(JPATH_COMPONENT.DS.'includes'.DS.'copy.php'); ?>

<div class="clr"></div>
</div>
</div>
