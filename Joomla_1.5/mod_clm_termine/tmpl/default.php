<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2011 Fjodor Schäfer. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Fjodor Schäfer
 * @email ich@vonfio.de
*/


defined('_JEXEC') or die('Restricted access'); 

$liga	= JRequest::getVar( 'liga' );
$runde	= JRequest::getVar( 'runde' );
$view	= JRequest::getVar( 'view' );
$dg		= JRequest::getVar( 'dg' );
$itemid	= JRequest::getVar( 'Itemid','' );
$start	= JRequest::getVar( 'start','1');
 
?>
<style type="text/css">
<?php 

	$document = &JFactory::getDocument();

	$cssDir = JURI::base().'modules/mod_clm_termine';
	//	$cssDir = JURI::base().'components'.DS.'com_clm'.DS.'includes';

	$document->addStyleSheet( $cssDir.'/mod_clm_termine.css', 'text/css', null, array() );

?>
</style>

<?php 

if ($par_liste == 0) { 
?>

<ul class="menu">

<?php 	

$arrWochentag = array( "Monday" => "Montag", "Tuesday" => "Dienstag", "Wednesday" => "Mittwoch", "Thursday" => "Donnerstag", "Friday" => "Freitag", "Saturday" => "Samstag", "Sunday" => "Sonntag", );
$count = 0; 
if ($start == '1') $start = date("Y-m-d");
for ($t = 0; $t < $par_anzahl; $t++) {
if ($runden[$t]->datum >= $start) { 

		// Veranstaltung verlinken
		if ($runden[$t]->ligarunde == 'event') { 
			$linkname = "index.php?option=com_clm&amp;view=termine&amp;nr=". $runden[$t]->id ."&amp;layout=termine_detail"; 
		} elseif ($runden[$t]->ligarunde != 0) { 
			//$linkname = "index.php?option=com_clm&amp;view=runde&amp;saison=" . $runden[$t]->sid . "&amp;liga=" .  $runden[$t]->typ_id ."&amp;runde=" . $runden[$t]->nr ."&amp;dg=" . $runden[$t]->durchgang;
			if (($runden[$t]->durchgang > 1) AND ($runden[$t]->nr > $runden[$t]->runden))
				$linkname = "index.php?option=com_clm&amp;view=runde&amp;saison=" . $runden[$t]->sid . "&amp;liga=" .  $runden[$t]->typ_id ."&amp;runde=" . ($runden[$t]->nr - $runden[$t]->runden) ."&amp;dg=2";
			else 
				$linkname = "index.php?option=com_clm&amp;view=runde&amp;saison=" . $runden[$t]->sid . "&amp;liga=" .  $runden[$t]->typ_id ."&amp;runde=" . $runden[$t]->nr ."&amp;dg=1";
			
		} else {
			$linkname = "index.php?option=com_clm&amp;view=turnier_runde&amp;runde=" . $runden[$t]->nr . "&amp;turnier=" . $runden[$t]->typ_id; }
		$linkname .= "&amp;start=". $runden[$t]->datum; 
		// Datumsberechnungen
		$datum[$t] = strtotime($runden[$t]->datum);
		$datum_arr[$t] = explode("-",$runden[$t]->datum);

		$datum_link = '<a href="'. $linkname;
		if ($itemid <>'') { $datum_link .= '&Itemid='.$itemid; }
		$datum_link .= '">'. $arrWochentag[date("l",$datum[$t])]. ',&nbsp;' . $datum_arr[$t][2].'.'.$datum_arr[$t][1].'.'.$datum_arr[$t][0];
		if ($runden[$t]->enddatum > $runden[$t]->datum) { 
			$enddatum[$t] = strtotime($runden[$t]->enddatum);
			$enddatum_arr[$t] = explode("-",$runden[$t]->enddatum); 
			$datum_link .= ' - '. $arrWochentag[date("l",$enddatum[$t])]. ',&nbsp;' . $enddatum_arr[$t][2].'.'.$enddatum_arr[$t][1].'.'.$enddatum_arr[$t][0]; }
		$datum_link .= "</a>\n";
 
    echo '<li>'; 

		if ($par_datum == 1) { // Parameter prüfen: Datum
			if (($datum[$t] == $datum[$t-1]) AND ($enddatum[$t] == $enddatum[$t-1])) { echo ''; }      //klkl
				else { 
					if ($par_datum_link == 1) { // Parameter prüfen: Datum verlinken
						echo $datum_link;
					} else {  
						echo $arrWochentag[date("l",$datum[$t])]. ",&nbsp;" . $datum_arr[$t][2].".".$datum_arr[$t][1].".".$datum_arr[$t][0]; 
							if ($runden[$t]->enddatum > $runden[$t]->datum) { //klkl
							echo ' - '.$arrWochentag[date("l",$enddatum[$t])]. ',&nbsp;' . $enddatum_arr[$t][2].'.'.$enddatum_arr[$t][1].'.'.$enddatum_arr[$t][0]; }
					} 	
				 } 
		} else { } 
		if (($par_name == 1) OR ($par_typ == 1) AND (($runden[$t]->name <>'') AND ($runden[$t]->typ <>'')) ) {

			if ($par_termin_link == 1 ) {
				echo '<a href="'. $linkname;
				if ($itemid <>'') { echo "&Itemid=".$itemid; }
				echo '">';
			}

				if ($par_name == 1) { echo $runden[$t]->name ."\n"; } // Parameter prüfen: Terminname 
				if (($par_name == 1) AND ($par_typ == 1) AND ($runden[$t]->typ <>'') ) { echo "&nbsp;-&nbsp;"; }
				if ($par_typ == 1) { echo $runden[$t]->typ ."\n"; }  // Parameter prüfen: Ort / Liga / Turnier
			if ($par_termin_link == 1 ) {
				echo "</a>\n";
			}

			echo '<br />';
		} else { 
			echo '<br />'; 
		}

    echo "</li>\n";

} 
} ?>

</ul>


<?php
} else { 

// Termine als Timestamp zu einem Array machen
$datum_stamp	= array ();
$datumend_stamp	= array ();
// Termin Details
$event_desc		= array ();
for ( $a = 0; $a < count ($runden); $a++ ) {

	// Veranstaltung verlinken
	if ($runden[$a]->ligarunde == 'event') { 
 		$linkname = "index.php?option=com_clm&amp;view=termine&amp;nr=". $runden[$a]->id ."&amp;layout=termine_detail"; 
 	} elseif ($runden[$a]->ligarunde != 0) { 
 		//$linkname = "index.php?option=com_clm&amp;view=runde&amp;saison=". $runden[$a]->sid ."&amp;liga=".  $runden[$a]->typ_id ."&amp;runde=". $runden[$a]->nr ."&amp;dg=". $runden[$a]->durchgang; 
		if (($runden[$a]->durchgang > 1) AND ($runden[$a]->nr > $runden[$a]->runden))
			$linkname = "index.php?option=com_clm&amp;view=runde&amp;saison=" . $runden[$a]->sid . "&amp;liga=" .  $runden[$a]->typ_id ."&amp;runde=" . ($runden[$a]->nr - $runden[$a]->runden) ."&amp;dg=2";
		else 
			$linkname = "index.php?option=com_clm&amp;view=runde&amp;saison=" . $runden[$a]->sid . "&amp;liga=" .  $runden[$a]->typ_id ."&amp;runde=" . $runden[$a]->nr ."&amp;dg=1";
 	} else {
 		$linkname = "index.php?option=com_clm&amp;view=turnier_runde&amp;runde=". $runden[$a]->nr ."&amp;turnier=". $runden[$a]->typ_id; 
	}
	$linkname .= "&amp;start=". $runden[$a]->datum; 
	$title			= $runden[$a]->name;
	$ende			= strtotime($runden[$a]->enddatum); 
	$anfang 		= strtotime($runden[$a]->datum);
		
	
	$datum_stamp[] 		= 	strtotime($runden[$a]->datum); 
	$event_desc[]		= 	array ($linkname , $title, $anfang, $ende  );  
	while ($ende > $anfang) {
		$anfang = mktime(0, 0, 0, date("m",$anfang)  , date("d",$anfang)+1, date("Y",$anfang));
		$datum_stamp[] 		= 	$anfang; 
		$event_desc[]		= 	array ($linkname , $title, $anfang, $ende  );  
	}
}
array_multisort ($datum_stamp, $event_desc);

// Mehrdimensionaler Array mit allen Information. Das Timestamp ist der Key
$event	= array_combine ($datum_stamp, $event_desc);

function monthBack( $timestamp ){
    return mktime(0,0,0, date("m",$timestamp)-1,date("d",$timestamp),date("Y",$timestamp) );
}
function yearBack( $timestamp ){
    return mktime(0,0,0, date("m",$timestamp),date("d",$timestamp),date("Y",$timestamp)-1 );
}
function monthForward( $timestamp ){
    return mktime(0,0,0, date("m",$timestamp)+1,date("d",$timestamp),date("Y",$timestamp) );
}
function yearForward( $timestamp ){
    return mktime(0,0,0, date("m",$timestamp),date("d",$timestamp),date("Y",$timestamp)+1 );
}

function getCalender($date,$headline = array('Mo','Di','Mi','Do','Fr','Sa','So'), $event, $datum_stamp ) {
    $sum_days = date('t',$date);
    $LastMonthSum = date('t',mktime(0,0,0,(date('m',$date)-1),0,date('Y',$date)));
	$linkname_tl = "index.php?option=com_clm&amp;view=termine&amp;Itemid=1"; 
    
    foreach( $headline as $key => $value ) {
        echo "<div class=\"tag kal_top\">".$value."</div>\n";
    }
    $istamp = 0;
    
    for( $i = 1; $i <= $sum_days; $i++ ) {
        $day_name = date('D',mktime(0,0,0,date('m',$date),$i,date('Y',$date)));
        $day_number = date('w',mktime(0,0,0,date('m',$date),$i,date('Y',$date)));
        
        $stamp = mktime(0,0,0,date('m',$date),$i,date('Y',$date));
		
		// Letzter Monat
        if( $i == 1) {
            $s = array_search($day_name,array('Mon','Tue','Wed','Thu','Fri','Sat','Sun'));
            for( $b = $s; $b > 0; $b-- ) {
                $x = $LastMonthSum-$b;
                echo "<div class=\"tag before\">".sprintf("%02d",$x)."</div>\n";
            }
        } 
        
		// Aktueller Tag
        if (date('Ymd') == date('Ymd',$stamp)) {
            echo "<div id=\"".$stamp."\" class=\"tag current\">"."<a title=\"heute\" href=\"". $linkname_tl."&amp;start=".date('Y-m',$date)."-01"."\" ><span title=\"" . "heute". "\" class=\"CLMTooltip\">" . sprintf("%02d",$i) . "</span></a>"."</div>\n";
        } 
		// Termin !
		elseif( array_key_exists($stamp, $event)) {
			while ($datum_stamp[$istamp] < $stamp) { $istamp++; 
			}
			echo "<div id=\"".$stamp."\" class=\"tag event\">";
			if ($datum_stamp[$istamp] != $datum_stamp[$istamp+1]) {
				echo "<a href=\"". $event[$stamp][0]; if ($itemid <>'') { echo "&Itemid=".$itemid; } echo "\" ><span title=\"" . $event[$stamp][1]. "\" class=\"CLMTooltip\">" . sprintf("%02d",$i) . "</span></a>";
			} else {
				echo "<a href=\"". $linkname_tl."&amp;start=".date('Y-m-d',$stamp); 
				if ($itemid <>'') { echo "&Itemid=".$itemid; } echo "\" ><span title=\"" . "mehrere Termine am Tag!". "\" class=\"CLMTooltip\">" . sprintf("%02d",$i) . "</span></a>";
			}
			echo "</div>\n";
        } 
		// Normaler Tag
		else {
            echo "<div id=\"".$stamp."\" class=\"tag normal\">".sprintf("%02d",$i)."</div>\n";
        }
        
		// Nächster Monat
        if( $i == $sum_days) {
            $next_sum = (6 - array_search($day_name,array('Mon','Tue','Wed','Thu','Fri','Sat','Sun')));
            for( $c = 1; $c <=$next_sum; $c++) {
                echo "<div class=\"tag after\"> ".sprintf("%02d",$c)." </div>\n"; 
            }
        }
    }
}

if( isset($_REQUEST['timestamp'])) { $date = $_REQUEST['timestamp']; }
else { $date = time(); }
if ($start != '1') {
	$start_arr = explode("-",$start);
    $date = mktime(0,0,0,$start_arr[1],$start_arr[2],$start_arr[0]);
}

$arrMonth = array(
    "January" => "Januar",
    "February" => "Februar",
    "March" => "M&auml;rz",
    "April" => "April",
    "May" => "Mai",
    "June" => "Juni",
    "July" => "Juli",
    "August" => "August",
    "September" => "September",
    "October" => "Oktober",
    "November" => "November",
    "December" => "Dezember"
);
    
$headline = array('Mo','Di','Mi','Do','Fr','Sa','So');
$linkname_tl = "index.php?option=com_clm&amp;view=termine&amp;Itemid=1"; 
$htext = $arrMonth[date('F',$date)].' '.date('Y',$date);

?>
<?php // URI holen  $uri     = &JFactory::getUri();  
// URL :  $uri->toString(); ?>
<center>
<div class="kalender">
    <div class="kal_pagination">
        <a href="?timestamp=<?php echo yearBack($date); ?>" class="last">&laquo;</a> 
        <a href="?timestamp=<?php echo monthBack($date); ?>" class="last">&laquo;</a> 
        <span><a title="<?php echo 'Termine '.$htext; ?>" href="<?php echo $linkname_tl.'&amp;start='.date('Y-m',$date).'-01'; ?>"><?php echo $htext ?></a></span>
        <a href="?timestamp=<?php echo monthForward($date); ?>" class="next">&raquo;</a>
        <a href="?timestamp=<?php echo yearForward($date); ?>" class="next">&raquo;</a>
        <div class="clear"></div>  
    </div>
    <?php getCalender($date,$headline,$event,$datum_stamp); ?>
    <div class="clear"></div>
</div>
</center>
<?php } ?>