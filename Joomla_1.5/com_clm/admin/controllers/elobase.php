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

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.controller' );

class CLMControllerElobase extends JController
{
	/**
	 * Constructor
	 */
function __construct( $config = array() )
	{
		parent::__construct( $config );
		// Register Extra tasks
		$this->registerTask( 'elobase_start','elobase_start');
	}

function display()
	{

	$db		=& JFactory::getDBO();

	$eb_Umlaute_1 	= array("ö","ü","ä","ß","Ö","Ü","Ä","é");
	$eb_Umlaute_2 	= array(chr(148),chr(129),chr(132),chr(225),chr(153),chr(154),chr(142),chr(130));
	$Monatstage 	= array(0,31,28,31,30,31,30,31,31,30,31,30,31);

	// Konfigurationsparameter auslesen
	$config		= &JComponentHelper::getParams( 'com_clm' );
	$erst		= $config->get('erstauswerter',705);
	$Verband 	= $config->get('lv','000');

	$db 	= & JFactory::getDBO();
	$query	=' SELECT a.name FROM #__clm_user as a'
		.' LEFT JOIN #__clm_saison AS s ON s.id = a.sid'
		.' WHERE a.user_clm > 70 AND s.archiv = 0 AND s.published = 1 '
		.' AND jid = '.$erst
		;
	$db->setQuery( $query );
	$Username	= $db->loadResult();

	$MaxLigen 	= 10;
//	$Saison 	= CLMControllerElobase::Abfrage('SELECT * FROM #_clm_saison ORDER BY id ASC Limit 0,3');

	$query = 'SELECT * FROM #__clm_saison '
		.' WHERE published = 1 and archiv = 0 '
		.' ORDER BY id ASC Limit 0,3 ';
	$db->setQuery( $query );
	$Saison = $db->loadObjectList();
//	$rnd_filter_dg	= $rnd_filter[0]->durchgang;

	foreach ($Saison as $Ligen)
	{
	$Jahre[]=(int) substr($Ligen->name,0,strpos($Ligen->name,'/')-1);
		if (strpos($Ligen->name,'/'))
		{
		$Jahre[]=(int) substr($Ligen->name,strpos($Ligen->name,'/')+1);
		}
	}
	sort($Jahre);
	for ($i=sizeof($Jahre)-2;$i>0;$i--)
		if ($Jahre[$i+1]==$Jahre[$i]) array_splice($Jahre,$i,1);

	$query = ' SELECT * FROM #__clm_liga '
		.' WHERE published =1 AND sid ='.$Saison[0]->id;
	if(isset($Saison[1])) {
		$query = $query.' OR sid ='.$Saison[1]->id;
		}
	$db->setQuery( $query );
	$Ligen = $db->loadObjectList();

	if (sizeof($Ligen)<9) $MaxLigen=sizeof($Ligen);

	require_once(JPATH_COMPONENT.DS.'views'.DS.'elobase.php');
	CLMViewElobase::Elobase( $MaxLigen, $Username, $Verband, $Ligen, $Jahre );
	}

function CLM2EB2()
	{
	global $mainframe;

	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$dbSpieler=array(
		array('ANR','C',2),
		array('APLATZ','C',2),
		array('PKZ','C',3),
		array('ZPSCODE','C',8),
		array('SNAME','C',40),
		array('SGESCHL','C',1),
		array('VERBAND','C',3),
		array('VNAME','C',40),
		array('VABK','C',4),
		array('AERFOLG','C',2),
		array('AEFAKTOR','C',1),
		array('APARTIEN','C',1),
		array('APUNKTE','C',1),
		array('ASONDER','C',1),
		array('AELOSTA','C',3),
		array('AELOEND','C',3),
		array('AGESPAR','C',1),
		array('AGESPKT','C',1),
		array('APKTSUM','C',1),
		array('AZUSATZ','C',1),
		array('STITEL','C',1),
		array('AWE1000','C',2),
		array('ALEISTUNG','C',2),
		array('ANIVEAU','C',2),
		array('AKTPARTIEN','C',1),
		array('AKTPUNKTE','C',1),
		array('AKTWE1000','C',2));

	$dbElo=array(
		array('ANR','C',2),
		array('APKT','C',1),
		array('AELOGEG','C',2));

	$dbGegner=array(
		array('ARUNDE','C',1),
		array('APAAR','C',1),
		array('ABRETT','C',1),
		array('AERG','C',1),
		array('ANR1','C',2),
		array('ANR2','C',2),
		array('AERGTUR','C',1),
		array('ALIGA','C',1));

	$dbMann=array(
		array('AMANNNR','C',1),
		array('AMANNPL','C',1),
		array('AMANNNAM','C',40),
		array('AMANNZPS','C',5),
		array('ALIGA','C',1),
		array('AANZAHL','C',1),
		array('ABRETTER','C',1),
		array('ARUNDEN','C',1));

	require_once(JPATH_COMPONENT.DS.'controllers'.DS.'class.dbase.php');

	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');

	$db 		=& JFactory::getDBO();

	$config 	= &JComponentHelper::getParams( 'com_clm' );
	$Verband 	= $config->get('lv','000');

	$Jahr 		= JRequest::getVar('Jahr');
	$Monat 		= JRequest::getVar('Monat');
	$Tag 		= JRequest::getVar('Tag');
	$Turniername	= JRequest::getVar('Turniername');
	$LiId 		= JRequest::getVar('LiId', array(), '', 'array');
	JArrayHelper::toInteger($LiId);
	$LiAs 		= JRequest::getVar('LiAs', array(), '', 'array');
	$LiNa 		= JRequest::getVar('LiNa', array(), '', 'array');

	$path 		= "components/$option/elobase/";

	jimport('joomla.filesystem.file');
	$folder		= JPath::clean(JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'elobase');

	// ggf. Verzeichnis erstellen //
	if (!file_exists($folder)) {
		JFolder::create( $folder);
		            }

	if (!$LiId) {
	JError::raiseWarning( 500, JText::_( 'ELOBASE_KEINE' ) );
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
		}

/*	if (!$Turniername AND count($LiId) == 1) {
		$Turniername=$LiAs[$LiId[0]];
		}
	else {
	JError::raiseWarning( 500, JText::_( 'ELOBASE_TURNIERNAME' ) );
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section );
		}
*/
  for ($i=1;$i<=4;$i++) $DBF[$i]=new dBase();
  if ($Jahr==0)
   {
    $dag=@getdate();
    $Jahr=$dag['year'];
    if ($Monat==0)
     {
      $Monat=$dag['mon'];
      if ($Tag==0)
       {
        $Tag=$dag['mday'];
       }
     }
    unset($dag);
   }
  for ($i=0;$i< count($LiId);$i++)
   {
    $LiID[$i]=$LiId[$i];
    if ($_POST['LiNa'][$i]=='')
          $LigaName[$i]=$LiAs[$LiID[$i]];
    Else $LigaName[$i]=$LiNa[$i];
   }
  // Wenn man den Code verfolgt, stellt man fest, da� EloStart zwei Mal gebraucht wird.
  // Das liegt daran, da� ohne Auswertung die Datenbanken EloStart und EloEnde identisch sind.  

	if(!$DBF[1]->create($path."A_HAUPT.dbf",$dbSpieler)) {
		$msg = JText::_( 'ELOBASE_HAUPT' );
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg );
		}
	elseif(!$DBF[2]->create($path."A_ELOSTA.dbf",$dbElo)) {
		$msg = JText::_( 'ELOBASE_ELOSTA' );
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg );
		}
	elseif(!$DBF[3]->create($path."A_GEGNER.dbf",$dbGegner)) {
		$msg = JText::_( 'ELOBASE_GEGNER' );
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg );
		}
	elseif(!$DBF[4]->create($path."A_MANN.dbf",$dbMann)) {
		$msg = JText::_( 'ELOBASE_MANN' );
		$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg );
		}
  $DBF[2]->unload();
  // Diese Spielerdaten sind unerheblich, oder werden als nicht belegt deklariert.
  $DBF[1]->set_field('VERBAND',$Verband);
  $DBF[1]->set_field('VABK','    ');
  $DBF[1]->set_field('AERFOLG',chr(0).chr(0));
  $DBF[1]->set_field('AEFAKTOR',chr(0));
  $DBF[1]->set_field('APARTIEN',chr(0));
  $DBF[1]->set_field('APUNKTE',chr(255));
  $DBF[1]->set_field('ASONDER',"N");
  $DBF[1]->set_field('AELOSTA','   ');
  $DBF[1]->set_field('AELOEND','   ');
  $DBF[1]->set_field('AGESPAR',chr(0));
  $DBF[1]->set_field('AGESPKT',chr(0));
  $DBF[1]->set_field('APKTSUM',chr(0));
  $DBF[1]->set_field('AZUSATZ',' ');
  $DBF[1]->set_field('STITEL',' ');
  $DBF[1]->set_field('AWE1000',chr(0).chr(0));
  $DBF[1]->set_field('ALEISTUNG',chr(0).chr(0));
  $DBF[1]->set_field('ANIVEAU',chr(0).chr(0));
  $DBF[1]->set_field('AKTPARTIEN',chr(0));
  $DBF[1]->set_field('AKTPUNKTE',chr(255));
  $DBF[1]->set_field('AKTWE1000',chr(0).chr(0));
  $ebData=array('nSpieler'=>0,'MaxRunden'=>0,'nMannschaften'=>0,'Verband'=>$Verband);
echo 'Es gibt '.sizeof($LiID).' Ligen.<br>';
for ($LigaNummer=0;$LigaNummer<sizeof($LiID);$LigaNummer++)
 {
  $Liga[$LigaNummer]=array($LigaNummer,$LiID[$LigaNummer],$LigaName[$LigaNummer]);
  CLMControllerElobase::LigaLoop($Liga[$LigaNummer],$ebData,$DBF);
 }
//  Testexit(__FILE__,__LINE__);
$DBF[1]->unload();
$DBF[3]->unload();
$DBF[4]->unload();
$Woche=  CLMControllerElobase::eb_Kalenderwoche($Tag,$Monat,$Jahr);
if ($Tag<10) $Tag='0'.$Tag;
if ($Monat<10) $Monat='0'.$Monat;
if ($Woche<10) $Woche='0'.$Woche;
if ($Jahr<26) $Jahr+=2000;
Elseif ($Jahr<100) $Jahr+=1900;
if ($Jahr>1999)                                            //2000ff)
  $Code=chr(((int)($Jahr/10)-135)).($Jahr % 10);           
Else if ($Jahr>1899) $Code=($Jahr % 100);                 //1900-1999
else $Code=' ';                                           //Der Rest: 100-1899
$Code.=$Woche.'-'.$Verband.'-'.$_POST['Code'];
$pieces=array('              ELOBASE Version 11.08',
'Turnier.....: '.$Turniername,
'Code........: '.$Code,
'Info........: ',
'Turnier-Typ : MV',
'Turnier-Ende: '.$Tag.'.'.$Monat.'.'.$Jahr,
'Erstauswert.: '.$_POST['Auswerter'],
'Letzte '.chr(142).'nd..: '.@date('d.m.Y, H:i'),
'Spieler.....: '.$ebData['nSpieler'],
'Runden......: '.$ebData['MaxRunden'],
'Gruppen/Mann: '.sizeof($_POST['LiId']).'/'.$ebData['nMannschaften'],
'Gew. Partien: 0',
'Berechnet am: ',
'Gespeich. am: '.@date('d.m.Y, H:i'),
'Ersterst. am: '.@date('d.m.Y, H:i'),
'Weitere Ausw: ',
'Schiedsrich.: ',
'Veranstalter: ',
'Notiz.......: Importiert durch CLM2EB V0.99'.chr(254).chr(254),
'MM-Wertung..: ',
chr(26));
$Intro=join(chr(13).chr(10),$pieces);
unset ($pieces);
$OffsetInfo=array(30+strlen($Intro),filesize($path.'A_HAUPT.dbf'));
$OffsetInfo[2]=$OffsetInfo[0]+$OffsetInfo[1];
$OffsetInfo[3]=filesize($path.'A_ELOSTA.dbf');
$OffsetInfo[4]=$OffsetInfo[2]+$OffsetInfo[3];
$OffsetInfo[5]=filesize($path.'A_ELOSTA.dbf');
$OffsetInfo[6]=$OffsetInfo[4]+$OffsetInfo[5];
$OffsetInfo[7]=filesize($path.'A_GEGNER.dbf');
$OffsetInfo[8]=$OffsetInfo[6]+$OffsetInfo[7];
$OffsetInfo[9]=filesize($path.'A_MANN.dbf');
$Code[8]='.';

/*
	// Datei schreiben ggf. Fehlermeldung absetzen
	jimport('joomla.filesystem.file');
	if (!JFile::write( $write, $buffer )) {
	JError::raiseWarning( 500, JText::_( 'ELOBASE_FEHLER' ) );
		}
*/

$Out=fopen($path.$Code,'wb');
fwrite($Out,$Intro);

for ($i=0;$i<10;$i++)
 {
  fwrite($Out,CLMControllerElobase::eb_pack($OffsetInfo[$i],3));
 }
$In=fopen($path.'A_HAUPT.dbf','rb');
fwrite($Out,fread($In,filesize($path.'A_HAUPT.dbf')));
fclose($In);
$In=fopen($path.'A_ELOSTA.dbf','rb');
fwrite($Out,fread($In,filesize($path.'A_ELOSTA.dbf')));
fclose($In);
$In=fopen($path.'A_ELOSTA.dbf','rb');
fwrite($Out,fread($In,filesize($path.'A_ELOSTA.dbf')));
fclose($In);
$In=fopen($path.'A_GEGNER.dbf','rb');
fwrite($Out,fread($In,filesize($path.'A_GEGNER.dbf')));
fclose($In);
$In=fopen($path.'A_MANN.dbf','rb');
fwrite($Out,fread($In,filesize($path.'A_MANN.dbf')));
fclose($In);

fclose($Out);

	$msg = count($LiAs).'_!_'.$Turniername.'_!_'.$LiId[0].'_*_'.$LiId[1].'_*_'.$Verband.'_!_'.$Jahr.'_'.$Monat.'_'.$Tag;

	$msg =JText::_( 'ELOBASE_FILE_SUCCESS' );
	$mainframe->redirect( 'index.php?option='. $option.'&section='.$section, $msg );
	}


function eb_pack($Value,$Bytes=1)
 {
  $s=chr($Value % 256);
  for ($i=1;$i<$Bytes;$i++)
   {
    $Value/=256;
    $s=chr($Value % 256).$s;
   }
   return $s;
 }

function eb_Kalenderwoche($Tag=0,$Monat=0,$Jahr=0)
 {
  if ($Jahr==0)
   {
    $dag=getdate();
    $Jahr=$dag['year'];
    if ($Monat==0)
     {
      $Monat=$dag['mon'];
      if ($Tag==0) $Tag=$dag['mday'];
     }
    unset($dag);
   }
  $r=mySQL_query('SELECT WEEK(\''.$Jahr.'-'.$Monat.'-'.$Tag.'\',1)');
  return mysql_result($r,0);
 }

function eb_pkz2elobase ($string)
 {
  // PKZ zerlegen
  $days = substr($string,0,5); // erste 5 Stellen als Zahl
  $number = substr($string,5,3); // letzte 3 Stellen als Zahl
  $pkznumber = $string; // komplette PKZ als Zahl
  if($pkznumber <= 1835007) // kein Geburtstag oder nur Geburtsjahr
   {
    return   CLMControllerElobase::eb_pack($string,3);  
   }
  else // $days und $number konvertieren (kompl.Geburtsdatum)
   {
    return   CLMControllerElobase::eb_pack($days,2).CLMControllerElobase::eb_pack($number);  
   }
 }

/* Code von Frank Hoppe Genehmigung steht noch aus. */
function eb_elobase2pkz ($string)
  {
  	// Die PKZ wird in ELOBASE in 3 Bytes gespeichert. Hier erfolgt
  	// die Umwandlung in eine lesbare PKZ und das Geburtsdatum.
  	
    // Art der PKZ feststellen, dazu erste 2 Bytes pr�fen
    $Temp = hexdec(bin2hex(substr($string,0,2)));
    if($Temp < 7168 && $Temp > 255)
    {
    	// 7168 = 17.08.1899
    	// Die PKZ ist eine lfd.Nr. und enth�lt nur das Geburtsjahr
      $lfdnr = sprintf("%08d",hexdec(bin2hex($string)));
      $Jahr = 1900 + substr($lfdnr,0,4);
      return $lfdnr;
    }
    elseif($Temp <= 255)
    {
      // 1.Byte hat Wert 0, dann PKZ = lfd.Nr. und kein Geburtstag
      return sprintf("%08d",hexdec(bin2hex($string)));
    }
    else
    {
    	// Byte 1+2 = Tagez�hler, Byte 3 = lfd.Nummer
      $Anzahl = hexdec(bin2hex(substr($string,0,2)));
      $LfdNr = hexdec(bin2hex(substr($string,2,1)));
      return sprintf("%05d",$Anzahl) . sprintf("%03d",$LfdNr);
    }	
  }

function eb_elobase2Jahr ($string)
  {
    $Temp = hexdec(bin2hex(substr($string,0,2)));
    if($Temp < 7168 && $Temp > 255)
    {
      $lfdnr = sprintf("%08d",hexdec(bin2hex($string)));
      $Jahr = 1900 + substr($lfdnr,0,4);
      return $Jahr;
    }
    elseif($Temp <= 255)
      return 0;
    else
    {
      $Anzahl = hexdec(bin2hex(substr($string,0,2)));
      $LfdNr = hexdec(bin2hex(substr($string,2,1)));
      return eb_birthyear($Anzahl);
    }	
  }

  function eb_birthyear ($days)
  {
    $days = $days + 2407716; // 2407716 = 01.01.1880
    $days         -=     1721119;
    $century     =    floor(( 4 * $days -  1) /  146097);
    $days        =    floor(4 * $days - 1 - 146097 * $century);
    $day        =    floor($days /  4);

    $year        =    floor(( 4 * $day +  3) /  1461);
    $day        =    floor(4 * $day +  3 -  1461 * $year);
    $day        =    floor(($day +  4) /  4);
    $month        =    floor(( 5 * $day -  3) /  153);
    if($month < 10)
        $month +=3;
    else
    {
        $month -=9;
        if($year++ == 99)
        {
            $year = 0;
            $century++;
        }
    }

    $century = sprintf("%02d",$century);
    $year = sprintf("%02d",$year);

    return "$century$year";
  }

/*  function Geburtstag ()
   {
    return $this->Geburtstag;
   }
  function Gegner ()
  {
    // Mu� nach Erfolg aufgerufen werden
    return $this->Gegner;
  }
*/

  function eb_Ascii2Ansi ($string)
   {
    //global $eb_Umlaute_1,$eb_Umlaute_2;
	$eb_Umlaute_1 	= array("ö","ü","ä","ß","Ö","Ü","Ä","é");
	$eb_Umlaute_2 	= array(chr(148),chr(129),chr(132),chr(225),chr(153),chr(154),chr(142),chr(130));
    for($y=0;$y<count($eb_Umlaute_1);$y++)
     {
      $string = str_replace($eb_Umlaute_2[$y],$eb_Umlaute_1[$y],$string);
     }
    return $string;
   }

  function eb_Ansi2Ascii ($string)
   {
    //global $eb_Umlaute_1,$eb_Umlaute_2;
	$eb_Umlaute_1 	= array("ö","ü","ä","ß","Ö","Ü","Ä","é");
	$eb_Umlaute_2 	= array(chr(148),chr(129),chr(132),chr(225),chr(153),chr(154),chr(142),chr(130));
    for($y=0;$y<count($eb_Umlaute_1);$y++)
     {
      $string = str_replace($eb_Umlaute_1[$y],$eb_Umlaute_2[$y],$string);
     }
    return $string;
   }

// Ende des Hoppe'schen Codes

function HoleDatum($LiID,$Tag=0,$Monat=0,$Jahr=0)
 {
$JOSPrefix='#_';
$CLMPrefix='clm_';

  $A=CLMControllerElobase::Abfrage('SELECT datum FROM '.$JOSPrefix.$CLMPrefix.'runden_termine WHERE liga='.$LiID.' ORDER BY datum desc limit 1');
  if ($A===false) return false;
  $Y=substr($A[0]['datum'],0,4);
  $M=substr($A[0]['datum'],5,2);
  $T=substr($A[0]['datum'],8,2);
  if ($Y<$Jahr) return array($Tag,$Monat,$Jahr);
  if (($Y==$Jahr)&&($M<$Monat)) return array($Tag,$Monat,$Jahr);
  if (($Y==$Jahr)&&($M==$Monat)&&($T<$Tag)) return array($Tag,$Monat,$Jahr);
  return array($T,$M,$Y);
 }

function MSchreiben(&$Mannschaft,&$DBF)
 {
  $DBF[4]->set_field('AMANNNR',chr($Mannschaft['tln_nr']));
  $DBF[4]->set_field('AMANNNAM',CLMControllerElobase::eb_Ansi2Ascii($Mannschaft['name']));
  $DBF[4]->set_field('AMANNZPS',$Mannschaft['zps']);
  return $DBF[4]->add_record();
 }

function MannLoop(&$Mannschaft,$LigaID,&$DBF)
 {
$JOSPrefix='#_';
$CLMPrefix='clm_';

  CLMControllerElobase::MSchreiben($Mannschaft,$DBF);
  $Mannschaft['AlleSpieler']=CLMControllerElobase::Abfrage('SELECT zps, mgl_nr FROM '.$JOSPrefix.$CLMPrefix.'meldeliste_spieler where lid='.$LigaID.' and zps = "'.$Mannschaft['zps'].'" and mnr='.$Mannschaft['man_nr'].' order by snr');
  if ($Mannschaft['AlleSpieler'])
   {
    foreach($Mannschaft['AlleSpieler'] as &$EinSpieler)
     {
      $Daten=CLMControllerElobase::Abfrage('SELECT PKZ,Geburtsjahr,Geschlecht,Spielername from '.$JOSPrefix.$CLMPrefix.'dwz_spieler where ZPS="'.$EinSpieler['zps'].'" AND Mgl_Nr='.$EinSpieler['mgl_nr']);
      $EinSpieler['nEinsatz']=0;
      if (is_array($Daten)) $EinSpieler=array_merge($EinSpieler,$Daten[0]);
     }
   }
 }

function SpielerSchreiben($Mannschaft,$MSpieler,$LigaNummer,$Einsaetze,$PKZ,$zpsV,$zpsS,$SName,$Sex,$Verein,&$DBF)
 {
  $DBF[1]->set_field('ANR',chr($Mannschaft).chr($MSpieler));
  $DBF[1]->set_field('APLATZ',chr($LigaNummer).chr($Einsaetze));
  $DBF[1]->set_field('PKZ',  CLMControllerElobase::eb_pkz2elobase($PKZ));
  if ($zpsS>999) $ZPSStr='***';
  Else if ($zpsS>99) $ZPSStr=$zpsS;
  Else if ($zpsS>9) $ZPSStr='0'.$zpsS;
  Else $ZPSStr='00'.$zpsS;
  $DBF[1]->set_field('ZPSCODE',$zpsV.$ZPSStr);
  $DBF[1]->set_field('SNAME',  CLMControllerElobase::eb_ansi2ascii($SName));
  $DBF[1]->set_field('SGESCHL',$Sex);
  $DBF[1]->set_field('VNAME',  CLMControllerElobase::eb_ansi2ascii($Verein));
  $DBF[1]->add_record();
 }

function SpielerEintragen(&$Mannschaft,&$Liga,&$DBF,&$ebData)
 {

  $LigaNummer=$Liga[0];
  $MSpieler=0;
  if ($Mannschaft['AlleSpieler']===false) return;
  for ($i=Sizeof($Mannschaft['AlleSpieler'])-1;$i>=0;$i--)
    if ($Mannschaft['AlleSpieler'][$i]['nEinsatz']===0) array_splice($Mannschaft['AlleSpieler'],$i,1);
  foreach($Mannschaft['AlleSpieler'] as &$EinSpieler)
   {
    $MSpieler++;
    $EinSpieler['LfdNummer']= 0;
    $Liga['Spieler2Nummer'][$EinSpieler['zps'].'-'.$EinSpieler['mgl_nr']]=0;
   }
 }

function SpielerNachtragen($zpsV,$zpsS,&$Liga,$mnr,&$ebData,&$DBF)
 {

  $JOSPrefix='#_';
$CLMPrefix='clm_';

  $Daten=  CLMControllerElobase::Abfrage('SELECT PKZ,Geburtsjahr,Geschlecht,Spielername from '.$JOSPrefix.$CLMPrefix.'dwz_spieler where ZPS="'.$zpsV.'" AND Mgl_Nr='.$zpsS);
  if (is_array($Daten))
  $Liga['Mannschaften'][$mnr-1]['AlleSpieler'][]=array_merge(array('zps'=>$zpsV,'mgl_nr'=>$zpsS,'nEinsatz'=>0),$Daten[0],array('LfDNummer'=>$ebData['nSpieler']));
  $Liga['Spieler2Nummer'][$zpsV.'-'.$zpsS]=$ebData['nSpieler'];
  $Liga['Mannschaften'][$mnr-1]['AlleSpieler'][]=array('zps'=>$zpsV,'mgl_nr'=>$zpsS,'nEinsatz'=>1,'PKZ'=>$Daten[0]['PKZ'],
   'Geburtsjahr'=>$Daten[0]['Geburtsjahr'],'Geschlecht'=>$Daten[0]['Geschlecht'],'Spielername'=>$Daten[0]['Spielername'],'LfdNummer'=>0);
 }

function AlleSpielerSchreiben(&$Liga,&$ebData,&$DBF)
 {
  foreach ($Liga['Mannschaften'] as &$EineMannschaft)
  if (is_array($EineMannschaft['AlleSpieler']))
   {
    $MSpieler=0;
    foreach ($EineMannschaft['AlleSpieler'] as &$EinSpieler)
     {
      $MSpieler++;
      $EinSpieler['LfdNummer']=++$ebData['nSpieler'];
      $Liga['Spieler2Nummer'][$EinSpieler['zps'].'-'.$EinSpieler['mgl_nr']]=$EinSpieler['LfdNummer'];
        CLMControllerElobase::SpielerSchreiben($EineMannschaft['tln_nr'],
                       $MSpieler,
                       $Liga[0]+1,
                       $EinSpieler['nEinsatz'],
                       ($EinSpieler['PKZ']!='')?$EinSpieler['PKZ']:$EinSpieler['Geburtsjahr'],
                       $EinSpieler['zps'],
                       $EinSpieler['mgl_nr'],
                       $EinSpieler['Spielername'],
                       $EinSpieler['Geschlecht'],
                       $EineMannschaft['name'],
                       $DBF);
     }
   }
 }

function LigaLoop(&$Liga,&$ebData,&$DBF)
 {
$JOSPrefix='#_';
$CLMPrefix='clm_';

  //global $JOSPrefix,$CLMPrefix;
  echo 'Verarbeite "'.$Liga[2].'"<br>';
  flush();
  $Liga['nSpieler']=0;  
  $Liga['nMannschaften']=0;
  $Liga['nBretter']=0;
  $Liga['nRunden']=0;
  $Liga['Mannschaften']=CLMControllerElobase::Abfrage('SELECT id,teil,Stamm,runden,durchgang FROM '.$JOSPrefix.$CLMPrefix.'liga WHERE id='.$Liga[1]);
  $Liga['Runden']=$Liga['Mannschaften'][0]['runden'];
  $Liga['nMannschaften']=$Liga['Mannschaften'][0]['teil'];
  $Liga['nBretter']=$Liga['Mannschaften'][0]['Stamm'];
  $Liga['nRunden']=$Liga['Mannschaften'][0]['runden']*$Liga['Mannschaften'][0]['durchgang'];
  if (!($Liga['nRunden']!==0)) $Liga['nRunden']=1;
  if ($Liga['nRunden']>$ebData['MaxRunden']) $ebData['MaxRunden']=$Liga['nRunden'];
  $ebData['nMannschaften']+=$Liga['nMannschaften'];
  $DBF[4]->set_field('AMANNNR',chr(0));
  $DBF[4]->set_field('AMANNPL',chr($Liga[0]+49));
  $DBF[4]->set_field('AMANNNAM',CLMControllerElobase::eb_Ansi2Ascii($Liga[2]));
  $DBF[4]->set_field('AMANNZPS',$ebData['Verband'].'  ');
  $DBF[4]->set_field('ALIGA',chr($Liga[0]+1));
  $DBF[4]->set_field('AANZAHL',chr($Liga['nMannschaften']));
  $DBF[4]->set_field('ABRETTER',chr($Liga['nBretter']));
  $DBF[4]->set_field('ARUNDEN',chr($Liga['nRunden']));
  $DBF[4]->add_record();
// F�r einen Mannschaftseintrag sind diese Daten konstant:
  $DBF[4]->set_field('AMANNPL',chr(0));
  $DBF[4]->set_field('AANZAHL',chr(0));
  $DBF[4]->set_field('ABRETTER',chr(0));
  $DBF[4]->set_field('ARUNDEN',chr(0));
  $Liga['Mannschaften']=CLMControllerElobase::Abfrage('SELECT * FROM '.$JOSPrefix.$CLMPrefix.'mannschaften where liga='.$Liga[1].' ORDER BY tln_nr ASC');
  foreach ($Liga['Mannschaften'] as &$Mannschaft)
   {
    set_time_limit(60);
    CLMControllerElobase::MannLoop($Mannschaft,$Liga[1],$DBF);
   }
  $Liga['BreErgebnisse']=CLMControllerElobase::Abfrage('SELECT runde,paar,dg,tln_nr,brett,zps,spieler,gzps,gegner,ergebnis,kampflos,heim FROM '.$JOSPrefix.$CLMPrefix.'rnd_spl WHERE lid='.$Liga[1].' ORDER BY runde,paar,dg,paar,brett');
  foreach ($Liga['BreErgebnisse'] as $EinBrett)
   {
    $i=0;
    while (($i<sizeOf($Liga['Mannschaften']))&&($Liga['Mannschaften'][$i]['tln_nr']!=$EinBrett['tln_nr'])) $i++;
    $j=0;
    while (($j<sizeof($Liga['Mannschaften'][$i]['AlleSpieler'])) &&
          ($Liga['Mannschaften'][$i]['AlleSpieler'][$j]['zps'].$Liga['Mannschaften'][$i]['AlleSpieler'][$j]['mgl_nr']!=
           $EinBrett['zps'].$EinBrett['spieler'])) $j++;
    if (($j<sizeof($Liga['Mannschaften'][$i]['AlleSpieler'])) &&
       ($Liga['Mannschaften'][$i]['AlleSpieler'][$j]['zps'].$Liga['Mannschaften'][$i]['AlleSpieler'][$j]['mgl_nr']==
           $EinBrett['zps'].$EinBrett['spieler']))
    $Liga['Mannschaften'][$i]['AlleSpieler'][$j]['nEinsatz']++;
   }
  foreach ($Liga['Mannschaften'] as &$Mannschaft)
    if ($Liga['Mannschaften'][$i]['AlleSpieler']) CLMControllerElobase::SpielerEintragen($Mannschaft,$Liga,$DBF,$ebData);
  foreach ($Liga['BreErgebnisse'] as $EinBrett)
  if(!isset($Liga['Spieler2Nummer'][$EinBrett['zps'].'-'.$EinBrett['spieler']]))
    CLMControllerElobase::SpielerNachtragen($EinBrett['zps'],$EinBrett['spieler'],$Liga,$EinBrett['tln_nr'],$ebData,$DBF);
  CLMControllerElobase::AlleSpielerSchreiben($Liga,$ebData,$DBF);
  foreach ($Liga['BreErgebnisse'] as $EinBrett)
   {
    $Runde=$EinBrett['runde']+($EinBrett['dg']-1)*$Liga['Runden'];
    $Liga['EndErgebnis'][$Runde][$EinBrett['paar']][0][0]=' ';
    if ($EinBrett['heim']==1) $Liga['EndErgebnis'][$Runde][$EinBrett['paar']][0][1]=chr($EinBrett['tln_nr']).chr(0);
    else $Liga['EndErgebnis'][$Runde][$EinBrett['paar']][0][2]=chr($EinBrett['tln_nr']).chr(0);
   }
  foreach ($Liga['BreErgebnisse'] as $EinBrett)
   {
    if ($EinBrett['heim']==0) continue;
    $Runde=$EinBrett['runde']+($EinBrett['dg']-1)*$Liga['Runden'];
    switch ($EinBrett['ergebnis'])
     {
      case '0': $Erg='0';break;
      case '1': $Erg='1';break;
      case '2': $Erg='5';break;
      case '4': $Erg='-';break;
      case '5': $Erg='+';break;
      case '6': $Erg='*';break;
      default : $Erg=' ';
     }
    $Heim=CLMControllerElobase::eb_pack($Liga['Spieler2Nummer'][$EinBrett['zps'].'-'.$EinBrett['spieler']],2);
    $Gast=CLMControllerElobase::eb_pack($Liga['Spieler2Nummer'][$EinBrett['gzps'].'-'.$EinBrett['gegner']],2);
    $Liga['EndErgebnis'][$Runde][$EinBrett['paar']][(int) $EinBrett['brett']]=array($Erg,$Heim,$Gast);
   }
  $Liga['ManErgebnisse']=CLMControllerElobase::Abfrage('SELECT runde,paar,dg,tln_nr,gegner,brettpunkte FROM '.$JOSPrefix.$CLMPrefix.'rnd_man WHERE lid='.$Liga[1].' AND heim=1 AND !ISNULL(brettpunkte)ORDER BY runde,dg,paar');
  foreach ($Liga['ManErgebnisse'] as $ManErgebnisse);
    $Runde=$ManErgebnisse['runde']+($ManErgebnisse['dg']-1)*$Liga['Runden'];
  $Runde=0;
  $DBF[3]->set_field('ALIGA',chr($Liga[0]+1));
  $DBF[3]->set_field('AERGTUR',' ');
  foreach ($Liga['EndErgebnis'] as $EineRunde)
   {
    $Runde++;
    $Paarung=0;
    $DBF[3]->set_field('ARUNDE',chr($Runde));
    foreach($EineRunde as $EinePaarung)
     {
      $Paarung++;
      $DBF[3]->set_field('APAAR',chr($Paarung));
      for ($Brett=0;$Brett<=$Liga['nBretter'];$Brett++)
      if (isset ($EinePaarung[$Brett]))
       {
        $DBF[3]->set_field('ABRETT',chr($Brett));
        $DBF[3]->set_field('AERG',$EinePaarung[$Brett][0]);
        $DBF[3]->set_field('ANR1',$EinePaarung[$Brett][1]);
        $DBF[3]->set_field('ANR2',$EinePaarung[$Brett][2]);
        $DBF[3]->add_record();
       }
     }
   }
 }

function Abfrage($query,$db=False)
 {
  $r=array();
  if (!$db) $result = mysql_query($query);
  Else $result = @ mysql_query($query,$db);
  if (!$result)
   {
    echo ("Abfrage konnte nicht ausgeführt werden: \n" . mysql_error().'\nDie Abfrage war: "'.$query.'"');
    die();
   }
  if (mysql_num_rows($result) > 0)
   {
    while ($row = mysql_fetch_assoc($result))
     {
      $r[]=$row;
     }
    unset($row);
    mysql_free_result($result);
    unset($result);
    return $r;

   }
  else
   {
    unset($result);
    return false;
   }
 }

function download ()
	{
	global $mainframe;
	$option		= JRequest::getCmd('option');

	jimport( 'joomla.filesystem.folder' );
	$filesDir 	= 'components'.DS.$option.DS.'elobase';

	$ex_dbf		= JFolder::files( $filesDir, 'dbf$',true, false);
	$ex_dbf[]	="index.html";
	$files		= JFolder::files( $filesDir, '',true, false, $ex_dbf );
		$count = count($files);
		$sql = array();
	for ($x=0; $x< $count; $x++ ) {
		$sql[] = $files[$x];
					}
	return $sql;
	}

}