<?php

// No direct access
defined('_JEXEC') or die('Restricted access');

class SimpleClmInstaller {

	/**
	 * gibt die Datenbank zurück
	*/
	function _getDB() {
		$database = JFactory::getDBO();
		return $database;
	
	}	
	/**
	 * gibt den Inhalt einer Tabelle zum Debuggen zurück
	*/
	function _debugDB($table) {
		echo '<br/>';
		foreach ($table as $key => $value) {
			echo $key.'-';
		}
		echo '<br/>';
	}	
	
	/**
	 * Ermittelt die Collation der DB (UTF8 oder nicht)
	 *
	 */
	function _isUtf8($collation) {
		$utf8 = '';
		if ( substr($collation, 0, 4) == 'utf8' ) {
			$utf8 = " CHARACTER SET `utf8` COLLATE `" . $collation . "`";
		}
		return $utf8;
	}
	
	/**
	 * Installation der Datenbank
	 * Nur erforderlich bei der ersten Installation
	 * Die Erstinstallation erfolgt auf Stand 0.92
	 * alle danach erfolgten Änderungen werden angehängt
	 */
	 function dbinstall($collation) {

		// UTF8-Test
		$utf8 = SimpleClmInstaller::_isUtf8($collation);
				
		// INIT
		$tableCreate = array();
		$tableInsert = array();
		
		// alle Tabellen werden als key->value-Paar gespeichert
				
				
		 $tableCreate['dwz_verbaende'] = "CREATE TABLE IF NOT EXISTS `dwz_verbaende` (
		  `Verband`            char(3)      NOT NULL default '',
		  `LV`                 char(1)      NOT NULL default '',
		  `Uebergeordnet`      char(3)      NOT NULL default '',
		  `Verbandname`        varchar(45)  NOT NULL default '',
		  PRIMARY KEY (`Verband`)
		) TYPE=MyISAM" . $utf8 . ";";
		
		 $tableCreate['dwz_vereine'] = "CREATE TABLE IF NOT EXISTS `dwz_vereine` (
		  `ZPS`                varchar(5)   NOT NULL default '',
		  `LV`                 char(1)      NOT NULL default '',
		  `Verband`            char(3)      NOT NULL default '',
		  `Vereinname`         varchar(40)  NOT NULL default '',
		  PRIMARY KEY (`ZPS`)
		) TYPE=MyISAM" . $utf8 . ";";
		
		 $tableCreate['dwz_spieler'] = "CREATE TABLE IF NOT EXISTS `dwz_spieler` (
		  `ZPS`                varchar(5)   NOT NULL default '',
		  `Mgl_Nr`             char(4)      NOT NULL default '',
		  `Status`             char(1)               default NULL,
		  `Spielername`        varchar(40)  NOT NULL default '',
		  `Spielername_G`      varchar(40)  NOT NULL default '',
		  `Geschlecht`         char(1)               default NULL,
		  `Spielberechtigung`  char(1)      NOT NULL default '',
		  `Geburtsjahr`        year(4)      NOT NULL default '0000',
		  `Letzte_Auswertung`  mediumint(6) unsigned default NULL,
		  `DWZ`                smallint(4)  unsigned default NULL,
		  `DWZ_Index`          smallint(3)  unsigned default NULL,
		  `FIDE_Elo`           smallint(4)  unsigned default NULL,
		  `FIDE_Titel`         char(2)               default NULL,
		  `FIDE_ID`            int(8)       unsigned default NULL,
		  `FIDE_Land`          char(3)               default NULL,
		  PRIMARY KEY  (`ZPS`,`Mgl_Nr`),
		  KEY `FIDE_ID` (`FIDE_ID`),
		  KEY `Spielername_G` (`Spielername_G`)
		) TYPE=MyISAM" . $utf8 . ";";
		
		 $tableCreate['#__clm_dwz_spieler'] = "CREATE TABLE IF NOT EXISTS `#__clm_dwz_spieler` (
			`id` int(11) NOT NULL auto_increment,
			`sid` mediumint(6) unsigned default NULL,
			`PKZ` varchar(9) default NULL,
			`ZPS` varchar(5) NOT NULL default '',
			`Mgl_Nr` varchar(4) NOT NULL default '',
			`Status` char(1) default NULL,
			`Spielername` varchar(40) NOT NULL default '',
			`Spielername_G` varchar(40) NOT NULL default '',
			`Geschlecht` char(1) default NULL,
			`Spielberechtigung` char(1) NOT NULL default '',
			`Geburtsjahr` year(4) NOT NULL default '0000',
			`Letzte_Auswertung` mediumint(6) unsigned default NULL,
			`DWZ` smallint(4) unsigned default NULL,
			`DWZ_Index` smallint(3) unsigned default NULL,
			`FIDE_Elo` smallint(4) unsigned default NULL,
			`FIDE_Titel` char(2) default NULL,
			`FIDE_ID` int(8) unsigned default NULL,
			`FIDE_Land` char(3) default NULL,
			`DWZ_neu` smallint(4) unsigned NOT NULL default '0',
			`I0` smallint(4) unsigned NOT NULL default '0',
			`Punkte` decimal(4,1) unsigned NOT NULL default '0.0',
			`Partien` tinyint(3) NOT NULL default '0',
			`We` decimal(6,3) NOT NULL default '0.000',
			`Leistung` smallint(4) NOT NULL default '0',
			`EFaktor` tinyint(2) NOT NULL default '0',
			`Niveau` smallint(4) NOT NULL default '0',
			PRIMARY KEY  (`id`),
			KEY `sid` (`sid`),
			KEY `ZPS` (`ZPS`)
		) ENGINE=MyISAM" . $utf8 . ";";
		
		 $tableCreate['#__clm_dwz_vereine'] = "CREATE TABLE IF NOT EXISTS `#__clm_dwz_vereine` (
		  `id` int(11) unsigned NOT NULL auto_increment,
		  `sid` mediumint(6) unsigned default NULL,
		  `ZPS` varchar(5) NOT NULL default '',
		  `LV` char(1) NOT NULL default '',
		  `Verband` varchar(3) NOT NULL default '',
		  `Vereinname` varchar(40) NOT NULL default '',
		  PRIMARY KEY  (`id`)
		) ENGINE=MyISAM" . $utf8 . ";";
		
		 $tableCreate['#__clm_ergebnis'] = "CREATE TABLE IF NOT EXISTS `#__clm_ergebnis` (
		  `id` int(11) NOT NULL auto_increment,
		  `eid` mediumint(5) unsigned default NULL,
		  `erg_text` varchar(10) NOT NULL default '',
		  PRIMARY KEY  (`id`)
		) ENGINE=MyISAM  " . $utf8 . ";";
		
		$tableInsert['#__clm_ergebnis'] = "REPLACE INTO `#__clm_ergebnis` (`id`, `eid`, `erg_text`) VALUES
		(1, 0, '0-1'),
		(2, 1, '1-0'),
		(3, 2, '0,5-0,5'),
		(4, 3, '0-0'),
		(5, 4, '-/+'),
		(6, 5, '+/-'),
		(7, 6, '-/-'),
		(8, 7, '---'),
		(9, 8, 'spielfrei');";
		
		 $tableCreate['#__clm_liga'] = "CREATE TABLE IF NOT EXISTS `#__clm_liga` (
		  `id` int(11) NOT NULL auto_increment,
		  `name` varchar(100) NOT NULL default '',
		  `sid` mediumint(3) unsigned default NULL,
		  `teil` mediumint(5) unsigned default NULL,
		  `stamm` mediumint(5) unsigned default NULL,
		  `ersatz` mediumint(5) unsigned default NULL,
		  `rang` tinyint(1) unsigned default 0,
		  `sl` mediumint(5) unsigned default NULL,
		  `runden` mediumint(5) unsigned default NULL,
		  `durchgang` mediumint(5) unsigned default NULL,
		  `mail` tinyint(1) unsigned default NULL,
		  `sl_mail` tinyint(1) unsigned default NULL,
		  `heim` tinyint(1) unsigned default NULL,
		  `order` tinyint(1) unsigned default NULL,
		  `rnd` tinyint(1) unsigned default NULL,
		  `auf` tinyint(1) NOT NULL,
		`auf_evtl` tinyint(1) NOT NULL,
		  `ab` tinyint(1) NOT NULL,
		`ab_evtl` tinyint(1) NOT NULL,
		`sieg_bed` tinyint(2) unsigned default NULL,
		`runden_modus` tinyint(2) unsigned default NULL,
		`man_sieg` decimal(4,2) unsigned default '1.0',
		`man_remis` decimal(4,2) unsigned default '0.5',
		`man_nieder` decimal(4,2) unsigned default '0.0',
		`man_antritt` decimal(4,2) unsigned default '0.0',
		`sieg` decimal(2,1) unsigned default '1.0',
		`remis` decimal(2,1) unsigned default '0.5',
		`nieder` decimal(2,1) unsigned default '0.0',
		`antritt` decimal(2,1) unsigned default '0.0',
		  `published` mediumint(3) unsigned default NULL,
		  `bemerkungen` text,
		  `bem_int` text,
		  `checked_out` tinyint(3) unsigned NOT NULL default '0',
		  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
		  `ordering` int(11) NOT NULL default '0',
		  `b_wertung` tinyint(1) unsigned default '0',
		  `liga_mt` tinyint(1) unsigned default '0',
		  `tiebr1` tinyint(2) unsigned NOT NULL default '0',
		  `tiebr2` tinyint(2) unsigned NOT NULL default '0',
		  `tiebr3` tinyint(2) unsigned NOT NULL default '0',		  
		  `ersatz_regel` tinyint(1) unsigned default '0',
		  `anzeige_ma` tinyint(1) unsigned default '0',
		`params` text NOT NULL,
		  PRIMARY KEY  (`id`),
		  KEY `published` (`published`)
		) ENGINE=MyISAM" . $utf8 . ";";
		
		 $tableCreate['#__clm_log'] = "CREATE TABLE IF NOT EXISTS `#__clm_log` (
		  `id` int(11) NOT NULL auto_increment,
		  `aktion` varchar(100) NOT NULL default '',
		  `jid_aktion` int(11) unsigned default NULL,
		  `sid` int(11) unsigned default NULL,
		`catid` smallint(6) unsigned default NULL,
		  `lid` int(11) unsigned default NULL,
		`tid` int(11) unsigned default NULL,
		  `rnd` int(11) unsigned default NULL,
		  `paar` int(11) unsigned default NULL,
		  `dg` int(11) unsigned default NULL,
		  `zps` varchar(5) default NULL,
		  `man` int(11) unsigned default NULL,
		  `mgl_nr` int(11) unsigned default NULL,
		  `jid` int(11) unsigned default NULL,
		  `cids` varchar(100) NOT NULL default '',
		  `datum` datetime NOT NULL default '0000-00-00 00:00:00',
		  `nr_aktion` smallint(6) ,
		  PRIMARY KEY  (`id`)
		) ENGINE=MyISAM" . $utf8 . ";";
		
		 $tableCreate['#__clm_mannschaften'] = "CREATE TABLE IF NOT EXISTS `#__clm_mannschaften` (
			`id` int(11) NOT NULL auto_increment,
			`sid` int(11) NOT NULL default '0',
			`name` varchar(100) NOT NULL default '',
			`liga` mediumint(5) unsigned default NULL,
			`zps` varchar(5) default NULL,
			`liste` mediumint(3) NOT NULL default '0',
			`edit_liste` mediumint(3) NOT NULL default '0',
			`man_nr` mediumint(5) unsigned default NULL,
			`tln_nr` mediumint(5) unsigned default NULL,
			`mf` mediumint(5) unsigned default NULL,
			`sg_zps` varchar(5) default NULL,
			`datum` datetime NOT NULL default '0000-00-00 00:00:00',
			`edit_datum` datetime NOT NULL default '0000-00-00 00:00:00',
			`lokal` text NOT NULL,
			`termine` text,
			`adresse` text,
			`homepage` text,
			`bemerkungen` text NOT NULL,
			`bem_int` text NOT NULL,
			`published` tinyint(1) NOT NULL default '0',
			`checked_out` tinyint(3) unsigned NOT NULL default '0',
			`checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
			`ordering` int(11) NOT NULL default '0',
			`summanpunkte` decimal(4,1) default NULL,
			`sumbrettpunkte` decimal(4,1) default NULL,
			`sumwins` tinyint(2) default NULL,
			`sumtiebr1` decimal(6,3) default '0.000',
			`sumtiebr2` decimal(6,3) default '0.000',
			`sumtiebr3` decimal(6,3) default '0.000',
			`rankingpos` tinyint(3) unsigned NOT NULL default '0',
			`sname` varchar(20) NOT NULL default '',
			PRIMARY KEY  (`id`),
			KEY `published` (`published`),
			KEY `sid` (`sid`)		
		) ENGINE=MyISAM ROW_FORMAT=COMPRESSED" . $utf8 . ";";
		
		 $tableCreate['#__clm_meldeliste_spieler'] = "CREATE TABLE IF NOT EXISTS `#__clm_meldeliste_spieler` (
		  `id` int(11) NOT NULL auto_increment,
		  `sid` mediumint(3) unsigned default NULL,
		  `lid` mediumint(3) unsigned default NULL,
		  `mnr` mediumint(5) unsigned NOT NULL default '0',
		  `snr` mediumint(5) unsigned default NULL,
		  `mgl_nr` mediumint(5) unsigned NOT NULL default '0',
		  `zps` varchar(5) NOT NULL default '0',
		  `status` mediumint(5) NOT NULL default '0',
		  `ordering` int(11) NOT NULL default '0',
		  `DWZ` smallint(4) unsigned NOT NULL default '0',
		  `I0` smallint(4) unsigned NOT NULL default '0',
		  `Punkte` decimal(4,1) unsigned NOT NULL default '0.0',
		  `Partien` tinyint(3) NOT NULL default '0',
		  `We` decimal(6,3) NOT NULL default '0.000',
		  `Leistung` smallint(4) NOT NULL default '0',
		  `EFaktor` tinyint(2) NOT NULL default '0',
		  `Niveau` smallint(4) NOT NULL default '0',
		  `sum_saison` decimal(5,1) NOT NULL default '0.0',
		`gesperrt` tinyint(1) unsigned DEFAULT NULL,
		  PRIMARY KEY  (`id`)
		) ENGINE=MyISAM " . $utf8 . ";";
		
		$tableCreate['#__clm_params'] = "CREATE TABLE IF NOT EXISTS `#__clm_params` (
			  `params` text NOT NULL,
			  `id` tinyint(1) NOT NULL,
			  PRIMARY KEY (`id`)			  
			) ENGINE=MyISAM" . $utf8 . ";";
		 
		 $tableCreate['#__clm_rnd_man'] = "CREATE TABLE IF NOT EXISTS `#__clm_rnd_man` (
		  `id` int(11) NOT NULL auto_increment,
		  `sid` mediumint(5) unsigned default NULL,
		  `lid` mediumint(5) unsigned default NULL,
		  `runde` mediumint(5) unsigned default NULL,
		  `paar` mediumint(5) unsigned default NULL,
		  `dg` tinyint(1) unsigned default NULL,
		  `heim` tinyint(1) unsigned default NULL,
		  `tln_nr` mediumint(5) unsigned NOT NULL default '0',
		  `gegner` mediumint(5) unsigned NOT NULL default '0',
		  `brettpunkte` decimal(5,1) unsigned default NULL,
		  `manpunkte` mediumint(5) unsigned default NULL,
		  `bp_sum` decimal(5,1) unsigned default NULL,
		  `mp_sum` mediumint(5) unsigned default NULL,
		  `gemeldet` mediumint(5) unsigned default NULL,
		  `editor` mediumint(5) unsigned default NULL,
		  `dwz_editor` mediumint(5) unsigned default NULL,
		  `zeit` datetime NOT NULL default '0000-00-00 00:00:00',
		  `edit_zeit` datetime NOT NULL default '0000-00-00 00:00:00',
		  `dwz_zeit` datetime NOT NULL default '0000-00-00 00:00:00',
		  `published` tinyint(1) unsigned NOT NULL default '0',
		  `checked_out` tinyint(3) unsigned NOT NULL default '0',
		  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
		  `ordering` int(11) NOT NULL default '0',
		  `wertpunkte` decimal(5,1) unsigned default NULL,
		  `ko_decision` tinyint(1) unsigned NOT NULL default '0',
		  `comment` text NOT NULL,
		  PRIMARY KEY  (`id`),
		  KEY `published` (`published`)
		) ENGINE=MyISAM   " . $utf8 . ";";
		
		 $tableCreate['#__clm_rnd_spl'] = "CREATE TABLE IF NOT EXISTS `#__clm_rnd_spl` (
		  `id` int(11) NOT NULL auto_increment,
		  `sid` mediumint(5) unsigned default NULL,
		  `lid` mediumint(5) unsigned default NULL,
		  `runde` mediumint(5) unsigned default NULL,
		  `paar` mediumint(5) unsigned default NULL,
		  `dg` tinyint(1) unsigned default NULL,
		  `tln_nr` mediumint(5) unsigned default NULL,
		  `brett` mediumint(5) unsigned default NULL,
		  `heim` tinyint(1) unsigned default NULL,
		  `weiss` tinyint(1) unsigned default NULL,
		  `spieler` mediumint(5) unsigned default NULL,
		  `zps` varchar(5) default NULL,
		  `gegner` mediumint(5) unsigned default NULL,
		  `gzps` varchar(5) default NULL,
		  `ergebnis` mediumint(5) unsigned default NULL,
		  `kampflos` tinyint(1) unsigned default NULL,
		  `punkte` decimal(5,1) unsigned default NULL,
		  `gemeldet` mediumint(5) unsigned default NULL,
		  `dwz_edit` mediumint(5) unsigned default NULL,
		  `dwz_editor` mediumint(5) unsigned default NULL,
		  PRIMARY KEY  (`id`)
		) ENGINE=MyISAM   " . $utf8 . ";";
		
		 $tableCreate['#__clm_runden_termine'] = "CREATE TABLE IF NOT EXISTS `#__clm_runden_termine` (
		  `id` int(11) NOT NULL auto_increment,
		  `sid` mediumint(5) unsigned default NULL,
		  `name` varchar(100) NOT NULL default '',
		  `liga` mediumint(5) unsigned default NULL,
		  `nr` mediumint(5) unsigned default NULL,
		  `datum` date NOT NULL default '0000-00-00',
		`meldung` tinyint(1)  NOT NULL default '0',
		  `sl_ok` tinyint(1) NOT NULL default '0',
		  `published` tinyint(1) NOT NULL default '0',
		  `bemerkungen` text,
		  `bem_int` text,
		  `gemeldet` mediumint(3) unsigned default NULL,
		`dwz` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0',
		  `editor` mediumint(3) unsigned default NULL,
		  `zeit` datetime NOT NULL default '0000-00-00 00:00:00',
		  `edit_zeit` datetime NOT NULL default '0000-00-00 00:00:00',
		  `checked_out` tinyint(3) unsigned NOT NULL default '0',
		  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
		  `ordering` int(11) NOT NULL default '0',
		  PRIMARY KEY  (`id`),
		  KEY `published` (`published`)
		) ENGINE=MyISAM ROW_FORMAT=COMPRESSED" . $utf8 . ";";
		
		 $tableCreate['#__clm_saison'] = "CREATE TABLE IF NOT EXISTS `#__clm_saison` (
		  `id` int(11) NOT NULL auto_increment,
		  `name` varchar(100) NOT NULL default '',
		  `published` tinyint(1) NOT NULL default '0',
		  `archiv` tinyint(1) NOT NULL default '0',
		  `bemerkungen` text,
		  `bem_int` text,
		  `checked_out` tinyint(3) unsigned NOT NULL default '0',
		  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
		  `ordering` int(11) NOT NULL default '0',
		  `datum` date NOT NULL default '0000-00-00', 
		  PRIMARY KEY  (`id`),
		  KEY `published` (`published`)
		) ENGINE=MyISAM ROW_FORMAT=COMPRESSED" . $utf8 . ";";
		
		$tableInsert['#__clm_saison'] = "REPLACE #__clm_saison set id=1,name=concat(year(now())-(month(now())<7),'/',year(now())+(month(now())>6)),published=1;";
		
		 $tableCreate['#__clm_swt_liga'] = "CREATE TABLE IF NOT EXISTS `#__clm_swt_liga` (
			`swt_id` mediumint(3) NOT NULL auto_increment,
			`Liga` varchar(33) NOT NULL default '',
			`clm_id` mediumint(3) unsigned default NULL,
			`Mannschaften` smallint(3) unsigned default NULL,
			`Runden` smallint(3) unsigned default NULL,
			`gesp_Runden` smallint(3) unsigned default NULL,
			`Spieler` smallint(3) unsigned default NULL,
			`Bretter` smallint(3) unsigned default NULL,
			`Turnierart` smallint(3) unsigned default NULL,
			`Durchgaenge` smallint(3) unsigned default NULL,
			`akt_DG` smallint(3) unsigned default NULL,
			`import_datum` datetime NOT NULL default '0000-00-00 00:00:00',
			`import_anzahl` smallint(3) unsigned default NULL,
			PRIMARY KEY  (`swt_id`)
		) ENGINE=MyISAM" . $utf8 . ";";
		
		$tableCreate['#__clm_swt_man'] = "CREATE TABLE IF NOT EXISTS `#__clm_swt_man` (
				  `swt_id` int(3) unsigned DEFAULT NULL,
				  `name` varchar(33) NOT NULL DEFAULT '',
				  `zps` varchar(7) NOT NULL DEFAULT '',
				  `tln_nr` smallint(3) unsigned DEFAULT NULL
				) ENGINE=MyISAM" . $utf8 . ";";
		
		$tableCreate['#__clm_swt_rnd_man'] = "CREATE TABLE IF NOT EXISTS `#__clm_swt_rnd_man` (
			  `liga_swt` mediumint(5) unsigned DEFAULT NULL,
			  `runde` mediumint(5) unsigned DEFAULT NULL,
			  `paar` mediumint(5) unsigned DEFAULT NULL,
			  `dg` tinyint(1) unsigned DEFAULT NULL,
			  `heim` tinyint(1) unsigned DEFAULT NULL,
			  `tln_nr` mediumint(5) unsigned NOT NULL DEFAULT '0',
			  `gegner` mediumint(5) unsigned NOT NULL DEFAULT '0',
			  `brettpunkte` decimal(5,1) unsigned DEFAULT NULL,
			  `manpunkte` mediumint(5) unsigned DEFAULT NULL,
			  `bp_sum` decimal(5,1) unsigned DEFAULT NULL,
			  `mp_sum` mediumint(5) unsigned DEFAULT NULL
			) ENGINE=MyISAM " . $utf8 . ";";
		 
		 $tableCreate['#__clm_swt_rnd_spl'] = "CREATE TABLE IF NOT EXISTS `#__clm_swt_rnd_spl` (
		  `Nr` smallint(3) unsigned DEFAULT NULL,
		`swt_id` mediumint(5) unsigned DEFAULT NULL,
		  `Runde` smallint(3) unsigned default NULL,
		  `DG` smallint(3) unsigned default NULL,
		  `Weiss` smallint(3) unsigned default NULL,
		  `Gegner` mediumint(3) unsigned default NULL,
		  `Ergebnis` decimal(2,1) unsigned default NULL,
		  `kampflos` smallint(3) unsigned default NULL,
		  `Paarung` smallint(3) unsigned default NULL,
		  `Mannschaft` smallint(3) unsigned default NULL,
		  `Summe` decimal(3,1) unsigned default NULL,
		`Brett` mediumint(3) unsigned default NULL,
		`clm_ergebnis` tinyint(1) default NULL,
		KEY `Nr` (`Nr`),
		KEY `swt_id` (`swt_id`),
		KEY `Runde` (`Runde`)
		) ENGINE=MyISAM" . $utf8 . ";";
		
		 $tableCreate['#__clm_swt_spl'] = "CREATE TABLE IF NOT EXISTS `#__clm_swt_spl` (
		`mnr` mediumint(5) unsigned NOT NULL default '0',
		`snr` mediumint(5) unsigned default NULL,
		`mgl_nr` mediumint(5) unsigned NOT NULL default '0',
		`clm_zps` varchar(5) NOT NULL default '0',
		`Nr` smallint(3) unsigned NOT NULL default '0',
		`Name` varchar(33) NOT NULL default '',
		`Verein` varchar(33) NOT NULL default '',
		`Liga` smallint(4) unsigned default NULL,
		`MaNr` smallint(4) unsigned default NULL,
		`BrNr` smallint(4) unsigned default NULL,
		`Titel` varchar(4) NOT NULL default '',
		`ELO` smallint(4) unsigned default NULL,
		`DWZ` smallint(4) unsigned default NULL,
		`ZPS` varchar(5) default NULL,
		`Mgl` mediumint(4) unsigned default NULL,
		`Land` varchar(4) NOT NULL default '',
		`Verband` varchar(4) NOT NULL default '',
		`GebJahr` smallint(5) unsigned default NULL,
		`FideKennz` mediumint(9) unsigned default NULL,
		`Status` char(2) NOT NULL default '',
		`aktiv` smallint(4) unsigned default NULL,
		`liga_swt` mediumint(5) unsigned NOT NULL default '0',
		PRIMARY KEY  (`Nr`,`liga_swt`)
		 ) ENGINE=MyISAM" . $utf8 . ";";
		
		
		$tableCreate['#__clm_swt_spl_nach'] = "CREATE TABLE IF NOT EXISTS `#__clm_swt_spl_nach` (
		`liga_swt` mediumint(5) unsigned NOT NULL DEFAULT '0',
		`mgl_nr` mediumint(5) unsigned NOT NULL DEFAULT '0',
		`clm_zps` varchar(5) NOT NULL DEFAULT '0',
		`Nr` mediumint(4) unsigned NOT NULL DEFAULT '0',
		PRIMARY KEY (`liga_swt`,`Nr`)
		) ENGINE=MyISAM " . $utf8 . ";";
		
		$tableCreate['#__clm_swt_spl_tmp'] = "CREATE TABLE IF NOT EXISTS `#__clm_swt_spl_tmp` (
		  `lid` mediumint(5) unsigned DEFAULT NULL,
		  `liga_swt` mediumint(5) unsigned NOT NULL DEFAULT '0',
		  `mnr` mediumint(5) unsigned NOT NULL DEFAULT '0',
		  `snr` mediumint(5) unsigned DEFAULT NULL,
		  `mgl_nr` mediumint(5) unsigned NOT NULL DEFAULT '0',
		  `clm_zps` varchar(5) DEFAULT NULL,
		  `Nr` mediumint(4) unsigned NOT NULL DEFAULT '0',
		  `Name` varchar(33) DEFAULT NULL,
		  `ZPS` varchar(5) DEFAULT NULL,
		  `Status` varchar(2) DEFAULT NULL,
		  PRIMARY KEY (`Nr`,`liga_swt`)
		) ENGINE=MyISAM" . $utf8 . ";";
		
		
		 $tableCreate['#__clm_termine'] = "CREATE TABLE IF NOT EXISTS `#__clm_termine` (
		  `id` int(11) NOT NULL auto_increment,
		  `name` varchar(100) NOT NULL default '',
		  `beschreibung` text,
		  `address` varchar(100) NOT NULL default '',
		  `category` varchar(33) NOT NULL default '',
		  `host` varchar(5) default NULL,
		  `startdate` date NOT NULL default '0000-00-00 00:00:00',
		  `enddate` date NOT NULL default '0000-00-00 00:00:00',
		  `attached_file` varchar(256) NULL default '',
		  `attached_file_description` varchar(128) NULL default '',
		  `published` mediumint(3) unsigned default NULL,
		  `checked_out` tinyint(3) unsigned NOT NULL default '0',
		  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
		  `ordering` int(11) NOT NULL default '0',
		  `event_link` varchar(500) NOT NULL default '',
		  PRIMARY KEY  (`id`),
		  KEY `published` (`published`)
		) ENGINE=MyISAM" . $utf8 . ";";
		
		 $tableCreate['#__clm_turniere'] = "CREATE TABLE IF NOT EXISTS `#__clm_turniere` (
		  `id` int(11) NOT NULL auto_increment,
		  `name` varchar(100) NOT NULL default '',
		  `sid` mediumint(3) unsigned default NULL,
		`dateStart` DATE NOT NULL,
		`dateEnd` DATE NOT NULL,
		`catidAlltime` SMALLINT(6) UNSIGNED NOT NULL DEFAULT '0',
		`catidEdition` SMALLINT(6) UNSIGNED NOT NULL DEFAULT '0',
		  `typ` tinyint(1) unsigned default NULL,
		`tiebr1` tinyint(2) unsigned NOT NULL default '0',
		`tiebr2` tinyint(2) unsigned NOT NULL default '0',
		`tiebr3` tinyint(2) unsigned NOT NULL default '0',
		  `rnd` tinyint(1) unsigned default NULL,
		  `teil` mediumint(5) unsigned default NULL,
		  `runden` mediumint(5) unsigned default NULL,
		  `dg` mediumint(5) unsigned default NULL,
		  `tl` mediumint(5) unsigned default NULL,
		  `bezirk` varchar(8) default NULL,
		`bezirkTur` enum('0','1') NOT NULL default '1',
		`vereinZPS` varchar(5) default NULL,
		  `published` tinyint(1) NOT NULL DEFAULT '0',
		`started` tinyint(1) NOT NULL DEFAULT '0',
		`finished` tinyint(1) NOT NULL DEFAULT '0',
		`invitationText` text,
		  `bemerkungen` text,
		  `bem_int` text,
		  `checked_out` tinyint(3) unsigned NOT NULL default '0',
		  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
		  `ordering` int(11) NOT NULL default '0',
		`params` text NOT NULL,
		  PRIMARY KEY  (`id`),
		  KEY `published` (`published`)
		) ENGINE=MyISAM " . $utf8 . ";";
		
		 $tableCreate['#__clm_turniere_rnd_spl'] = "CREATE TABLE IF NOT EXISTS `#__clm_turniere_rnd_spl` (
		  `id` int(11) NOT NULL auto_increment,
		  `sid` mediumint(5) unsigned default NULL,
		  `turnier` mediumint(5) unsigned default NULL,
		  `runde` mediumint(5) unsigned default NULL,
		  `paar` mediumint(5) unsigned default NULL,
		  `brett` mediumint(5) unsigned default NULL,
		  `dg` tinyint(1) unsigned default NULL,
		  `tln_nr` mediumint(5) unsigned default NULL,
		  `heim` tinyint(1) unsigned default NULL,
		  `spieler` mediumint(5) unsigned default NULL,
		  `gegner` mediumint(5) unsigned default NULL,
		  `ergebnis` mediumint(5) unsigned default NULL,
			`tiebrS` tinyint(2) unsigned NOT NULL DEFAULT '0',
			`tiebrG` tinyint(2) unsigned NOT NULL DEFAULT '0',
			`kampflos` tinyint(1) unsigned default NULL,
			`pgn` text NOT NULL,
			`ordering` int(11) NOT NULL default '0',
		  PRIMARY KEY  (`id`)
		) ENGINE=MyISAM" . $utf8 . ";";
		
		 $tableCreate['#__clm_turniere_rnd_termine'] = "CREATE TABLE IF NOT EXISTS `#__clm_turniere_rnd_termine` (
		  `id` int(11) NOT NULL auto_increment,
		  `sid` mediumint(5) unsigned default NULL,
		  `name` varchar(100) NOT NULL default '',
		  `turnier` mediumint(5) unsigned default NULL,
		  `dg` tinyint(1) unsigned default NULL,
		  `nr` mediumint(5) unsigned default NULL,
		  `datum` date NOT NULL default '0000-00-00',
		  `abgeschlossen` mediumint(3) NOT NULL default '0',
		  `tl_ok` tinyint(1) NOT NULL default '0',
		  `published` tinyint(1) NOT NULL default '0',
		  `bemerkungen` text,
		  `bem_int` text,
		  `gemeldet` mediumint(3) unsigned default NULL,
		  `editor` mediumint(3) unsigned default NULL,
		  `zeit` datetime NOT NULL default '0000-00-00 00:00:00',
		  `edit_zeit` datetime NOT NULL default '0000-00-00 00:00:00',
		  `checked_out` tinyint(3) unsigned NOT NULL default '0',
		  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
		  `ordering` int(11) NOT NULL default '0',
		  PRIMARY KEY  (`id`),
		  KEY `published` (`published`)
		) ENGINE=MyISAM" . $utf8 . ";";
		 $tableCreate['#__clm_turniere_sonderranglisten'] = "CREATE TABLE IF NOT EXISTS `#__clm_turniere_sonderranglisten` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `turnier` int(11) NOT NULL,
		  `name` varchar(100) NOT NULL,
		  `use_rating_filter` enum('0','1') DEFAULT '0',
		  `rating_type` tinyint(1) DEFAULT '0',
		  `rating_higher_than` smallint(4) DEFAULT '0',
		  `rating_lower_than` smallint(4) DEFAULT '3000',
		  `use_birthYear_filter` enum('0','1') DEFAULT '0',
		  `birthYear_younger_than` year(4) DEFAULT '0000',
		  `birthYear_older_than` year(4) DEFAULT '0000',
		  `use_sex_filter` enum('0','1') DEFAULT '0',
		  `sex` enum('','M','W') DEFAULT NULL,
		  `published` tinyint(1) NOT NULL DEFAULT '0',
		  `checked_out` tinyint(3) unsigned NOT NULL,
		  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		  `ordering` int(11) NOT NULL DEFAULT '0',
		  `use_zps_filter` enum('0','1') DEFAULT '0',
		  `zps_higher_than` varchar(5) DEFAULT '',
		  `zps_lower_than` varchar(5) DEFAULT 'ZZZZZ',
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM" . $utf8 . ";";
		
		 $tableCreate['#__clm_turniere_tlnr'] = "CREATE TABLE IF NOT EXISTS `#__clm_turniere_tlnr` (
		  `id` int(11) NOT NULL auto_increment,
		  `sid` mediumint(3) unsigned default NULL,
		  `turnier` mediumint(4) unsigned default NULL,
		  `snr` mediumint(5) unsigned default NULL,
		  `name` varchar(150) default NULL,
		`birthYear` year(4) NOT NULL DEFAULT '0000',
		`geschlecht` CHAR(1) DEFAULT NULL,		
		  `verein` varchar(150) default NULL,
		  `twz` smallint(4) unsigned default NULL,
		`NATrating` smallint(4) UNSIGNED NULL,
		`FIDEelo` smallint(4) UNSIGNED NULL,
		`FIDEid` int(8) UNSIGNED NULL,
		`FIDEcco` char(3) NULL,
		  `titel` char(3) default NULL,
		  `mgl_nr` mediumint(5) unsigned NOT NULL default '0',
		  `zps` varchar(5) NOT NULL default '0',
		  `status` mediumint(5) NOT NULL default '0',
		`rankingPos` smallint(5) UNSIGNED NOT NULL default '0',
		`tlnrStatus` tinyint(1) UNSIGNED NOT NULL default '1',
		`anz_spiele` tinyint(2) UNSIGNED NOT NULL default '0',
		  `sum_punkte` decimal(4,1) default NULL,
		  `sum_bhlz` decimal(5,2) default NULL,
		  `sum_busum` decimal(6,2) default NULL,
		  `sum_sobe` decimal(5,2) default NULL,
		  `sum_wins` tinyint(1) unsigned NOT NULL default '0',
		`sumTiebr1` DECIMAL(8, 3) NOT NULL DEFAULT '0',
		`sumTiebr2` DECIMAL(8, 3) NOT NULL DEFAULT '0',
		`sumTiebr3` DECIMAL(8, 3) NOT NULL DEFAULT '0',
		`koStatus` enum( '0', '1' ) NOT NULL default '1',
		`koRound` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0',
		  `DWZ` smallint(4) unsigned NOT NULL default '0',
		  `I0` smallint(4) unsigned NOT NULL default '0',
		  `Punkte` decimal(4,1) unsigned NOT NULL default '0.0',
		  `Partien` tinyint(3) NOT NULL default '0',
		  `We` decimal(6,3) NOT NULL default '0.000',
		  `Leistung` smallint(4) NOT NULL default '0',
		  `EFaktor` tinyint(2) NOT NULL default '0',
		  `Niveau` smallint(4) NOT NULL default '0',
		  `published` tinyint(1) NOT NULL default '0',
		  `checked_out` tinyint(3) unsigned NOT NULL default '0',
		  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
		  `ordering` int(11) NOT NULL default '0',
		  PRIMARY KEY  (`id`,`zps`,`mgl_nr`,`status`)
		) ENGINE=MyISAM" . $utf8 . ";";
		
		 $tableCreate['#__clm_user'] = "CREATE TABLE IF NOT EXISTS `#__clm_user` (
		  `id` int(11) NOT NULL auto_increment,
		  `sid` smallint(3) unsigned default NULL,
		  `jid` mediumint(5) unsigned default NULL,
		  `name` text NOT NULL,
		  `username` varchar(150) NOT NULL default '',
		  `aktive` tinyint(3) NOT NULL default '0',
		  `email` varchar(100) NOT NULL default '',
		  `tel_fest` varchar(30) NOT NULL default '',
		  `tel_mobil` varchar(30) NOT NULL default '',
		  `usertype` varchar(75) NOT NULL default '',
		  `user_clm` smallint(3) unsigned default NULL,
		  `zps` varchar(5) default NULL,
		  `mglnr` varchar(4) default NULL,
 		  `mannschaft` smallint(3) unsigned default NULL,
		  `published` smallint(3) unsigned default NULL,
		  `bemerkungen` text NOT NULL,
		  `bem_int` text NOT NULL,
		  `checked_out` tinyint(3) unsigned NOT NULL default '0',
		  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
		  `ordering` int(11) NOT NULL default '0',
		  `block` tinyint(4) NOT NULL default '0',
		   `activation` varchar(100) NOT NULL default '',
		  PRIMARY KEY  (`id`),
		  KEY `published` (`published`)
		) ENGINE=MyISAM" . $utf8 . ";";
		
		 $tableCreate['#__clm_usertype'] = "CREATE TABLE IF NOT EXISTS `#__clm_usertype` (
		  `id` int(11) NOT NULL auto_increment,
		  `name` varchar(200) NOT NULL default '',
		  `user_clm` smallint(3) unsigned default NULL,
			`type` varchar(4) NOT NULL default 'USER',
			`group` varchar(15) default '0',
			`published` int(1) NOT NULL default '0',
			`ordering` int(11) NOT NULL default '0',
			`fe_params` text NOT NULL,
			`be_params` text NOT NULL,
		  PRIMARY KEY  (`id`)
		) ENGINE=MyISAM" . $utf8 . ";";
		
		$tableInsert['#__clm_usertype'] = "REPLACE `#__clm_usertype` (`id`, `name`, `user_clm`, `type`, `group`, `published`, `ordering`, `fe_params`, `be_params`) VALUES
		(1, 'Administrator', 100, 'USER', 'admin', 1, 0, '', ''),
		(2, 'DV Referent', 90, 'USER', 'dv', 1, 0, '', ''),
		(3, 'Spielleiter', 89, 'USER', 'dv', 1, 0, '', ''),
		(4, 'DWZ Referent', 80, 'USER', 'dv', 1, 0, '', ''),
		(5, 'Staffelleiter', 70, 'USER', 'sl', 1, 0, '', ''),
		(6, 'Turnierleiter', 69, 'USER', 'tl', 1, 0, '', ''),
		(7, 'Damenwart', 68, 'USER', 'tl', 1, 0, '', ''),
		(8, 'Jugendwart', 67, 'USER', 'tl', 1, 0, '', ''),
		(9, 'Vereinsspielleiter', 60, 'USER', 'tl', 1, 0, '', ''),
		(10, 'Vereinsleiter', 50, 'USER', 'vl', 1, 0, '', ''),
		(11, 'Vereinsjugendwart', 40, 'USER', 'vw', 1, 0, '', ''),
		(12, 'Vereinsdamenwart', 39, 'USER', 'vw', 1, 0, '', ''),
		(13, 'Mannschaftsführer', 30, 'USER', 'mf', 1, 0, '', ''),
		(14, 'Spieler', 20, 'USER', 'spl', 1, 0, '', '');";
		
		 $tableCreate['#__clm_vereine'] = "CREATE TABLE IF NOT EXISTS `#__clm_vereine` (
		  `id` int(11) NOT NULL auto_increment,
		  `name` varchar(100) NOT NULL default '',
		  `sid` mediumint(5) unsigned default NULL,
		  `zps` varchar(5) default NULL,
		  `vl` mediumint(5) unsigned default NULL,
		  `lokal` varchar(200) NOT NULL default '',
		  `homepage` varchar(200) NOT NULL default '',
		  `adresse` varchar(200) NOT NULL default '',
		  `vs` varchar(200) NOT NULL default '',
		  `vs_mail` varchar(200) NOT NULL default '',
		  `vs_tel` varchar(200) NOT NULL default '',
		  `tl` varchar(200) NOT NULL default '',
		  `tl_mail` varchar(200) NOT NULL default '',
		  `tl_tel` varchar(200) NOT NULL default '',
		  `jw` varchar(200) NOT NULL default '',
		  `jw_mail` varchar(200) NOT NULL default '',
		  `jw_tel` varchar(200) NOT NULL default '',
		  `pw` varchar(200) NOT NULL default '',
		  `pw_mail` varchar(200) NOT NULL default '',
		  `pw_tel` varchar(200) NOT NULL default '',
		  `kw` varchar(200) NOT NULL default '',
		  `kw_mail` varchar(200) NOT NULL default '',
		  `kw_tel` varchar(200) NOT NULL default '',
		  `sw` varchar(200) NOT NULL default '',
		  `sw_mail` varchar(200) NOT NULL default '',
		  `sw_tel` varchar(200) NOT NULL default '',
		  `termine` varchar(200) NOT NULL default '',
		  `published` mediumint(3) unsigned default NULL,
		  `bemerkungen` text NOT NULL,
		  `bem_int` text NOT NULL,
		  `checked_out` tinyint(3) unsigned NOT NULL default '0',
		  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
		  `ordering` int(11) NOT NULL default '0',
		  PRIMARY KEY  (`id`),
		  KEY `published` (`published`)
		) ENGINE=MyISAM" . $utf8 . ";";
		
		 $tableCreate['#__clm_rangliste_id'] = "CREATE TABLE IF NOT EXISTS `#__clm_rangliste_id` (
		  `id` int(10) unsigned NOT NULL auto_increment,
		  `gid` int(10) unsigned NOT NULL default '0',
		  `sid` mediumint(5) NOT NULL,
		  `zps` varchar(5) NOT NULL default '00000',
		  `rang` tinyint(1) NOT NULL,
		  `published` mediumint(3) unsigned default NULL,
		  `bemerkungen` text NOT NULL,
		  `bem_int` text NOT NULL,
		  `checked_out` tinyint(3) unsigned NOT NULL default '0',
		  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
		  `ordering` int(11) NOT NULL default '0',
		  PRIMARY KEY  (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8" . $utf8 . ";";
		
		 $tableCreate['#__clm_rangliste_name'] = "CREATE TABLE IF NOT EXISTS `#__clm_rangliste_name` (
		  `id` int(10) unsigned NOT NULL auto_increment,
		  `Gruppe` varchar(30) character set utf8 collate utf8_bin NOT NULL,
		  `Meldeschluss` date default '2009-06-30',
		  `geschlecht` varchar(1) default NULL,
		  `alter_grenze` varchar(1) default NULL,
		  `alter` smallint(3) default NULL,
		  `sid` mediumint(3) unsigned default '0',
		  `user` mediumint(3) unsigned default '0',
		  `user_clm` mediumint(3) unsigned default '0',
		  `bemerkungen` text,
		  `bem_int` text,
		  `checked_out` tinyint(3) unsigned default '0',
		  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
		  `ordering` int(11) NOT NULL default '0',
		  `published` tinyint(1) NOT NULL default '0',
		  PRIMARY KEY  (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8" . $utf8 . ";";
		
		$tableInsert['#__clm_rangliste_name'] ="INSERT INTO `#__clm_rangliste_name` (`id`, `Gruppe`, `Meldeschluss`, `geschlecht`, `alter_grenze`, `alter`, `sid`, `user`, `user_clm`, `bemerkungen`, `bem_int`, `checked_out`, `checked_out_time`, `ordering`, `published`) VALUES
		(1, 'Testrangliste', '2011-12-31', '0', '0', 0, 1, 62, 100, '', '', 0, '0000-00-00 00:00:00', 1, 1);";
		
		 $tableCreate['#__clm_rangliste_spieler'] = "CREATE TABLE IF NOT EXISTS `#__clm_rangliste_spieler` (
		  `Gruppe` tinyint(3) unsigned NOT NULL,
		  `ZPS` varchar(5) NOT NULL default '00000',
		  `Mgl_Nr` smallint(5) unsigned NOT NULL default '0',
		  `PKZ` int(10) unsigned NOT NULL default '0',
		  `Rang` int(10) unsigned NOT NULL default '0',
		  `man_nr` tinyint(3) unsigned NOT NULL default '1',
		  `sid` mediumint(3) unsigned default '0',
		  PRIMARY KEY  USING BTREE (`Gruppe`,`ZPS`,`man_nr`,`Rang`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8" . $utf8 . ";";

		$tableCreate['#__clm_categories'] = "CREATE TABLE IF NOT EXISTS `#__clm_categories` (
			`id` smallint(6) NOT NULL auto_increment,
			`parentid` smallint(6) unsigned default NULL,
			`name` varchar(100) NOT NULL default '',
			`sid` mediumint(3) unsigned default NULL,
			`dateStart` date NOT NULL,
			`dateEnd` date NOT NULL,
			`tl` mediumint(5) unsigned default NULL,
			`bezirk` varchar(8) default NULL,
			`bezirkTur` enum('0','1') NOT NULL default '1',
			`vereinZPS` varchar(5) default NULL,
			`published` mediumint(3) unsigned default NULL,
			`started` tinyint(1) NOT NULL default '0',
			`finished` tinyint(1) NOT NULL default '0',
			`invitationText` text,
			`bemerkungen` text,
			`bem_int` text,
			`checked_out` tinyint(3) unsigned NOT NULL default '0',
			`checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
			`ordering` int(11) NOT NULL default '0',
			`params` text NOT NULL,
		  PRIMARY KEY  (`id`),
		  KEY `published` (`published`)
		) ENGINE=MyISAM " . $utf8 . ";";

		$tableCreate['#__clm_access_points'] = "CREATE TABLE IF NOT EXISTS `#__clm_access_points` (
			`id` int(11) NOT NULL auto_increment,
			`area` char(2) NOT NULL default 'BE',
			`accesstopic` varchar(20) NOT NULL,
			`accesspoint` varchar(20) NOT NULL,
			`rule` char(3) NOT NULL default 'NY',
			`published` int(1) NOT NULL default '0',
			`ordering` int(11) NOT NULL default '0',
		  PRIMARY KEY  (`id`)
		) ENGINE=MyISAM " . $utf8 . ";";
		
		$database = SimpleClmInstaller::_getDB();
		
		
		foreach ($tableCreate as $key => $sql) {
			$database->setQuery($sql);
			if ( !$database->query() ) {
				echo "Fehler beim Anlegen der Tabelle ".$key;
				return false;
			}
		}
		foreach ($tableInsert as $key => $sql) {
			$database->setQuery($sql);
			if ( !$database->query() ) {
				echo "Fehler beim Füllen der Tabelle ".$key;
				return false;
			}
		}
		
		return "* Tabellen erfolgreich angelegt!<br />";
		}
		
		/**
	 * Aktualiserung der Datenbank auf die neueste Version
	 * Dadurch wird die DB konsistent gehalten
	 *
	 * Alle Änderungen ab 0.92
	 * @return gibt Fehler
	 *
	 */
	function dbupgrade($collation) {

		// UTF8-Test
		$utf8 = SimpleClmInstaller::_isUtf8($collation);

		$database = SimpleClmInstaller::_getDB();
		$string = '';
		// ---------------------------------------------------------------------------
		// DB TABELLE #__clm_liga
		// ---------------------------------------------------------------------------
		$database->setQuery ("SHOW COLUMNS FROM #__clm_liga");
		$fields = $database->loadObjectList();
		$fieldtypes = array();
		foreach ($fields as $field) {
			$fieldtypes[$field->Field] = $field->Type;
		}
		
		// % -> Tabellendefinition angepasst
		//0.963
		if (isset($fieldtypes['color_auf'])) {
			$sql = "ALTER TABLE `#__clm_liga` CHANGE `color_auf` `auf_evtl` tinyint(1) NOT NULL ;";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte Color_auf geändert zu auf_evtl in Tabelle clm_ligaS<br />";
			} else {
				echo "<font color='red'>* Fehler beim Ändern Spalte Color_auf</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		
		if (isset($fieldtypes['color_ab'])) {
			$sql = "ALTER TABLE `#__clm_liga` CHANGE `color_ab` `ab_evtl` tinyint(1) NOT NULL ;";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte Color_ab geändert zu auf_evtl in Tabelle clm_liga<br />";
			} else {
				echo "<font color='red'>* Fehler beim Ändern Spalte Color_ab</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		
		// 0964
		if (!isset($fieldtypes['sieg_bed'])) {
			$sql = "ALTER TABLE `#__clm_liga` ADD `sieg_bed` tinyint(2) unsigned default NULL;";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte sieg_bed zu Tabelle clm_liga hinzugefügt<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen Spalte sieg_bed</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		
		if (!isset($fieldtypes['runden_modus'])) {
			$sql = "ALTER TABLE `#__clm_liga` ADD `runden_modus` tinyint(2) unsigned default NULL;";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte runden_modus zu Tabelle clm_liga hinzugefügt<br />";
			} else {
				echo "<font color='red'>* Fehler bei hinzufügen Spalte runden_modus</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		
		if (!isset($fieldtypes['man_sieg'])) {
			$sql = "ALTER TABLE `#__clm_liga` ADD `man_sieg` decimal(4,2) unsigned default '1.0' ;";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte man_sieg zu Tabelle clm_liga hinzugefügt<br />";
			} else {
				echo "<font color='red'>* Fehler bei hinzufügen Spalte man_sieg</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		
		if (!isset($fieldtypes['man_remis'])) {
			$sql = "ALTER TABLE `#__clm_liga` ADD `man_remis` decimal(4,2) unsigned default '0.5' ;";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte man_remis zu Tabelle clm_liga hinzugefügt<br />";
			} else {
				echo "<font color='red'>* Fehler bei hinzufügen Spalte man_remis</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		
		if (!isset($fieldtypes['man_nieder'])) {
			$sql = "ALTER TABLE `#__clm_liga` ADD `man_nieder` decimal(4,2) unsigned default '0.0' ;";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte man_nieder zu Tabelle clm_liga hinzugefügt<br />";
			} else {
				echo "<font color='red'>* Fehler bei hinzufügen Spalte man_nieder</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		
		if (!isset($fieldtypes['man_antritt'])) {
			$sql = "ALTER TABLE `#__clm_liga` ADD `man_antritt` decimal(4,2) unsigned default '0.0' ;";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte man_antritt zu Tabelle clm_liga hinzugefügt<br />";
			} else {
				echo "<font color='red'>* Fehler bei hinzufügen Spalte man_antritt</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		
		if (!isset($fieldtypes['sieg'])) {
			$sql = "ALTER TABLE `#__clm_liga` ADD `sieg` decimal(2,1) unsigned default '1.0' ;";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte sieg zu Tabelle clm_liga hinzugefügt<br />";
			} else {
				echo "<font color='red'>* Fehler bei hinzufügen Spalte sieg</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		
		if (!isset($fieldtypes['remis'])) {
			$sql = "ALTER TABLE `#__clm_liga` ADD `remis` decimal(2,1) unsigned default '0.5' ;";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte remis zu Tabelle clm_liga hinzugefügt<br />";
			} else {
				echo "<font color='red'>* Fehler bei hinzufügen Spalte remis</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		
		if (!isset($fieldtypes['nieder'])) {
			$sql = "ALTER TABLE `#__clm_liga` ADD `nieder` decimal(2,1) unsigned default '0.0' ;";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte nieder zu Tabelle clm_liga hinzugefügt<br />";
			} else {
				echo "<font color='red'>* Fehler bei hinzufügen Spalte nieder</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		
		if (!isset($fieldtypes['antritt'])) {
			$sql = "ALTER TABLE `#__clm_liga` ADD `antritt` decimal(2,1) unsigned default '0.0' ;";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte antritt zu Tabelle clm_liga hinzugefügt<br />";
			} else {
				echo "<font color='red'>* Fehler bei hinzufügen Spalte antritt</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		
		// 1.1.3
		if (!isset($fieldtypes['b_wertung'])) {
			$sql = "ALTER TABLE `#__clm_liga` ADD `b_wertung` tinyint(1) unsigned default '0';";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte b_wertung zu Tabelle clm_liga hinzugefügt<br />";
				$string .= "        -->Pflege über Liga-Manager nötig, wo Berliner Wertung angewandt wird<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen Spalte b_wertung</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		// 1.1.17
		if (!isset($fieldtypes['liga_mt'])) {
			$sql = "ALTER TABLE `#__clm_liga` ADD `liga_mt` tinyint(1) unsigned default '0';";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte liga_mt zu Tabelle clm_liga hinzugefügt<br />";
				$string .= "        -->Darstellungsmodul mod_clm Mindest-Version 1.1.0 für Ligen sollte installiert werden<br />";
				$string .= "        -->sowie ein Kopie davon für die Darstellung der Mannschaftsturniere<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen Spalte liga_mt</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		// 1.1.17
		if (!isset($fieldtypes['tiebr1'])) {
			$sql = "ALTER TABLE `#__clm_liga` ADD `tiebr1` tinyint(2) unsigned NOT NULL default '0';";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte tiebr1 zu Tabelle clm_liga hinzugefügt<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen Spalte tiebr1</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		// 1.1.17
		if (!isset($fieldtypes['tiebr2'])) {
			$sql = "ALTER TABLE `#__clm_liga` ADD `tiebr2` tinyint(2) unsigned NOT NULL default '0';";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte tiebr2 zu Tabelle clm_liga hinzugefügt<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen Spalte tiebr2</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		// 1.1.17
		if (!isset($fieldtypes['tiebr3'])) {
			$sql = "ALTER TABLE `#__clm_liga` ADD `tiebr3` tinyint(2) unsigned NOT NULL default '0';";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte tiebr3 zu Tabelle clm_liga hinzugefügt<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen Spalte tiebr3</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		// 1.1.19
		if (!isset($fieldtypes['ersatz_regel'])) {
			$sql = "ALTER TABLE `#__clm_liga` ADD `ersatz_regel` tinyint(1) unsigned default '0';";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte ersatz_regel zu Tabelle clm_liga hinzugefügt<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen Spalte ersatz_regel zu Tabelle clm_liga</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		if (!isset($fieldtypes['anzeige_ma'])) {
			$sql = "ALTER TABLE `#__clm_liga` ADD `anzeige_ma` tinyint(1) unsigned default '0';";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte anzeige_ma zu Tabelle clm_liga hinzugefügt<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen Spalte anzeige_ma zu Tabelle clm_liga</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		// 1.2.3
		if (!isset($fieldtypes['params'])) {
			$sql = "ALTER TABLE `#__clm_liga` ADD `params` text not null;";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte params zu Tabelle clm_liga hinzugefügt<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen Spalte params zu Tabelle clm_liga</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		
		// ---------------------------------------------------------------------------
		// DB TABELLE #__clm_runden_termine
		// ---------------------------------------------------------------------------
		$database->setQuery ("SHOW COLUMNS FROM #__clm_runden_termine");
		$fields = $database->loadObjectList();
		$fieldtypes = array();
		foreach ($fields as $field) {
			$fieldtypes[$field->Field] = $field->Type;
		}

		// 0.963
		
		if (isset($fieldtypes['abgeschlossen'])) {
			$sql = "ALTER TABLE `#__clm_runden_termine` CHANGE `abgeschlossen` `meldung` tinyint(1)  NOT NULL default '0' ;";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte abgeschlossen geändert zu meldung in Tabelle clm_runden_termine<br />";
			} else {
				echo "<font color='red'>* Fehler beim Ändern Spalte abgeschlossen</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		

		// ---------------------------------------------------------------------------
		// DB TABELLE #__clm_swt_man (neu mit 0.963 // %
		// ---------------------------------------------------------------------------
		$database->setQuery ("SHOW COLUMNS FROM #__clm_swt_man");
		$fields = $database->loadObjectList();
		if ( !count($fields) ) {
		//Tabelle wird neu angelegt
			$sql = "CREATE TABLE IF NOT EXISTS `#__clm_swt_man` (
				  `swt_id` int(3) unsigned DEFAULT NULL,
				  `name` varchar(33) NOT NULL DEFAULT '',
				  `zps` varchar(7) NOT NULL DEFAULT '',
				  `tln_nr` smallint(3) unsigned DEFAULT NULL
				) ENGINE=MyISAM" . $utf8 . ";";
		$database->setQuery($sql);
		if ( !$database->query() ) {
			echo "Fehler beim Anlegen Tabelle clm_swt_man";
			return false;
			}
		}
		
		// ---------------------------------------------------------------------------
		// DB TABELLE #__clm_rnd_man (neu mit 0.963 // %
		// ---------------------------------------------------------------------------
		$database->setQuery ("SHOW COLUMNS FROM #__clm_swt_rnd_man");
		$fields = $database->loadObjectList();
		if ( !count($fields) ) {
		//Tabelle wird neu angelegt
		$sql = "CREATE TABLE IF NOT EXISTS `#__clm_swt_rnd_man` (
			  `liga_swt` mediumint(5) unsigned DEFAULT NULL,
			  `runde` mediumint(5) unsigned DEFAULT NULL,
			  `paar` mediumint(5) unsigned DEFAULT NULL,
			  `dg` tinyint(1) unsigned DEFAULT NULL,
			  `heim` tinyint(1) unsigned DEFAULT NULL,
			  `tln_nr` mediumint(5) unsigned NOT NULL DEFAULT '0',
			  `gegner` mediumint(5) unsigned NOT NULL DEFAULT '0',
			  `brettpunkte` decimal(5,1) unsigned DEFAULT NULL,
			  `manpunkte` mediumint(5) unsigned DEFAULT NULL,
			  `bp_sum` decimal(5,1) unsigned DEFAULT NULL,
			  `mp_sum` mediumint(5) unsigned DEFAULT NULL
			) ENGINE=MyISAM " . $utf8 . ";";
		$database->setQuery($sql);
		if ( !$database->query() ) {
			echo "Fehler beim Anlegen Tabelle clm_swt_rnd_man";
			return false;
			}
		}
		
		
		// ---------------------------------------------------------------------------
		// DB TABELLE #__clm_swt_rnd_spl
		// ---------------------------------------------------------------------------
		$database->setQuery ("SHOW COLUMNS FROM #__clm_swt_rnd_spl");
		$fields = $database->loadObjectList();
		$fieldtypes = array();
		foreach ($fields as $field) {
			$fieldtypes[$field->Field] = $field->Type;
		}
		// geändert mit 0963 // %
		if ($fieldtypes['Nr'] != 'smallint(3) unsigned' ) {
			$sql = "ALTER TABLE `#__clm_swt_rnd_spl` CHANGE `Nr` `Nr` smallint(3) unsigned DEFAULT NULL ;";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte Nr geändert zu Typ smallint(3) in Tabelle clm_swt_rnd_spl<br />";
			} else {
				echo "<font color='red'>* Fehler beim Ändern Spalte Nr </font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		
		if (isset($fieldtypes['lid'])) {
			$sql = "ALTER TABLE `#__clm_swt_rnd_spl` CHANGE `lid` `swt_id` mediumint(5) unsigned DEFAULT NULL;";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte lid geändert zu swt_id in Tabelle clm_swt_rnd_spl<br />";
				$sql = "ALTER TABLE `#__clm_swt_rnd_spl` DROP PRIMARY KEY;";
				$database->setQuery($sql);
				if ( $database->query() ) {
				$string .= "* Schlüssel zu Tabelle clm_swt_rnd_spl gelöscht<br />";
				} else {
					echo "<font color='red'>* Fehler bei Löschen Schlüssel Nr</font><br />";
					SimpleClmInstaller::_debugDB($fieldtypes);
					return false;
				}
				$sql = "ALTER TABLE `#__clm_swt_rnd_spl` ADD Index (swt_id), ADD INDEX (Runde) ;";
				$database->setQuery($sql);
				if ( $database->query() ) {
				$string .= "* Index zu Tabelle clm_swt_rnd_spl hinzugefügt<br />";
				} else {
					echo "<font color='red'>* Fehler bei Hinzufügen Index Tabelle clm_swt_spl</font><br />";
					SimpleClmInstaller::_debugDB($fieldtypes);
					return false;
				}
			} else {
				echo "<font color='red'>* Fehler beim Ändern Spalte lid </font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		
		if (isset($fieldtypes['Spieler'])) {
			$sql = "ALTER TABLE `#__clm_swt_rnd_spl` DROP COLUMN `Spieler` ;";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte Spieler entfernt in Tabelle clm_swt_rnd_spl<br />";
			} else {
				echo "<font color='red'>* Fehler beim Entfernen Spalte Spieler </font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		
		if (!isset($fieldtypes['Brett'])) {
			$sql = "ALTER TABLE `#__clm_swt_rnd_spl` ADD `Brett` mediumint(3) unsigned default NULL;";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte Brett hinzugefügt zu Tabelle clm_swt_rnd_spl<br />";
			} else {
				echo "<font color='red'>* Fehler beim hinzufügen Spalte Brett</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		
		if (!isset($fieldtypes['clm_ergebnis'])) {
			$sql = "ALTER TABLE `#__clm_swt_rnd_spl` ADD `clm_ergebnis` tinyint(1) default NULL;";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte clm_ergebnis hinzugefügt zu Tabelle clm_swt_rnd_spl<br />";
			} else {
				echo "<font color='red'>* Fehler beim hinzufügen Spalte clm_ergebnis</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		
		// ---------------------------------------------------------------------------
		// DB TABELLE #__clm_swt_spl
		// ---------------------------------------------------------------------------
		$database->setQuery ("SHOW COLUMNS FROM #__clm_swt_spl");
		$fields = $database->loadObjectList();
		$fieldtypes = array();
		foreach ($fields as $field) {
			$fieldtypes[$field->Field] = $field->Type;
		}
		// geändert mit 0963

		if (isset($fieldtypes['id'])) {
			$sql = "ALTER TABLE `#__clm_swt_spl` CHANGE `id` `id` int(11) NOT NULL ;";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte id geändert zu Typ int(11) in Tabelle clm_swt_spl<br />";
			} else {
				echo "<font color='red'>* Fehler beim Ändern Spalte id </font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
			$sql = "ALTER TABLE `#__clm_swt_spl` DROP PRIMARY KEY ;";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Schlüssel zu Tabelle clm_swt_spl gelöscht<br />";
			} else {
				echo "<font color='red'>* Fehler bei Löschen Schlüssel id </font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
			$sql = "ALTER TABLE `#__clm_swt_spl` DROP COLUMN `id` ;";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte id entfernt in Tabelle clm_swt_spl<br />";
			} else {
				echo "<font color='red'>* Fehler beim Entfernen Spalte id </font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		
		if (!isset($fieldtypes['liga_swt'])) {
			$sql = "ALTER TABLE `#__clm_swt_spl` CHANGE `Nr` `Nr` smallint(3) unsigned DEFAULT NULL ;";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte Nr geändert zu Typ smallint(3) in Tabelle clm_swt_rnd_spl<br />";
			} else {
				echo "<font color='red'>* Fehler beim Ändern Spalte Nr </font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
			$sql = "ALTER TABLE `#__clm_swt_spl` ADD `liga_swt` mediumint(5) unsigned NOT NULL DEFAULT '0' ;";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte liga_swt hinzugefügt in Tabelle clm_swt_spl<br />";
			} else {
				echo "<font color='red'>* Fehler beim hinzufügen Spalte liga_swt</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
			$sql = "ALTER TABLE `#__clm_swt_spl`  ADD PRIMARY KEY (`Nr` ,`liga_swt`) ;";
			$database->setQuery($sql);
				if ( $database->query() ) {
					$string .= "* Schlüssel geändert in  Tabelle clm_swt_spl<br />";
				} else {
				echo "<font color='red'>* Fehler beim Ändern Schlüssel in Tabelle clm_swt_spl</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		
		if (isset($fieldtypes['sid'])) {
			$sql = "ALTER TABLE `#__clm_swt_spl` DROP COLUMN `sid` ;";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte sid entfernt in Tabelle clm_swt_spl<br />";
			} else {
				echo "<font color='red'>* Fehler beim Entfernen Spalte sid </font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		
		if (isset($fieldtypes['lid'])) {
			$sql = "ALTER TABLE `#__clm_swt_spl` DROP COLUMN `lid` ;";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte lid entfernt in Tabelle clm_swt_spl<br />";
			} else {
				echo "<font color='red'>* Fehler beim Entfernen Spalte lid </font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		
		// ---------------------------------------------------------------------------
		// DB TABELLE #__clm_swt_spl_nach (neu mit 0.963
		// ---------------------------------------------------------------------------
		$database->setQuery ("SHOW COLUMNS FROM #__clm_swt_spl_nach");
		$fields = $database->loadObjectList();
		if ( !count($fields) ) { // %
		//Tabelle wird neu angelegt
		$sql = "CREATE TABLE IF NOT EXISTS `#__clm_swt_spl_nach` (
  				`liga_swt` mediumint(5) unsigned NOT NULL DEFAULT '0',
				  `mgl_nr` mediumint(5) unsigned NOT NULL DEFAULT '0',
 				 `clm_zps` varchar(5) NOT NULL DEFAULT '0',
 				 `Nr` mediumint(4) unsigned NOT NULL DEFAULT '0',
 				 PRIMARY KEY (`liga_swt`,`Nr`)
				) ENGINE=MyISAM " . $utf8 . ";";
		$database->setQuery($sql);
		if ( !$database->query() ) {
			echo "Fehler beim Anlegen Tabelle clm_swt_spl_nach";
			return false;
			}
		}
		
		// ---------------------------------------------------------------------------
		// DB TABELLE #__clm_swt_spl_tpm (neu mit 0.963
		// ---------------------------------------------------------------------------
		$database->setQuery ("SHOW COLUMNS FROM #__clm_swt_spl_tmp");
		$fields = $database->loadObjectList();
		if ( !count($fields) ) { // %
		//Tabelle wird neu angelegt
		$sql = "CREATE TABLE IF NOT EXISTS `#__clm_swt_spl_tmp` (
			  `lid` mediumint(5) unsigned DEFAULT NULL,
			  `liga_swt` mediumint(5) unsigned NOT NULL DEFAULT '0',
			  `mnr` mediumint(5) unsigned NOT NULL DEFAULT '0',
			  `snr` mediumint(5) unsigned DEFAULT NULL,
			  `mgl_nr` mediumint(5) unsigned NOT NULL DEFAULT '0',
			  `clm_zps` varchar(5) DEFAULT NULL,
			  `Nr` mediumint(4) unsigned NOT NULL DEFAULT '0',
			  `Name` varchar(33) DEFAULT NULL,
			  `ZPS` varchar(5) DEFAULT NULL,
			  `Status` varchar(2) DEFAULT NULL,
			  PRIMARY KEY (`Nr`,`liga_swt`)
			) ENGINE=MyISAM" . $utf8 . ";";
		$database->setQuery($sql);
		if ( !$database->query() ) {
			echo "Fehler beim Anlegen Tabelle clm_swt_spl_tmp";
			return false;
			}
		}
		
		// ---------------------------------------------------------------------------
		// DB TABELLE #__clm_swt_liga
		// ---------------------------------------------------------------------------
		$database->setQuery ("SHOW COLUMNS FROM #__clm_swt_liga");
		$fields = $database->loadObjectList();
		$fieldtypes = array();
		foreach ($fields as $field) {
			$fieldtypes[$field->Field] = $field->Type;
		}

		// 0.963
		if (!isset($fieldtypes['swt_id'])) {
			$sql = "ALTER TABLE `#__clm_swt_liga` DROP PRIMARY KEY ;";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Schlüssel zu Tabelle clm_swt_liga gelöscht<br />";
			} else {
				echo "<font color='red'>* Fehler bei Löschen Schlüssel Liga</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
			$sql = "ALTER TABLE `#__clm_swt_liga` ADD `swt_id` mediumint(3) NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`swt_id`);";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte swt_id zu Tabelle clm_swt_liga hinzugefügt<br />";
			} else {
				echo "<font color='red'>* Fehler bei hinzufügen Spalte swt_id</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}

		}
		
	
		// ---------------------------------------------------------------------------
		// DB TABELLE #__clm_meldeliste_spieler
		// ---------------------------------------------------------------------------
		$database->setQuery ("SHOW COLUMNS FROM #__clm_meldeliste_spieler");
		$fields = $database->loadObjectList();
		$fieldtypes = array();
		foreach ($fields as $field) {
			$fieldtypes[$field->Field] = $field->Type;
		}

		// 0.965
		if (!isset($fieldtypes['gesperrt'])) {
			$sql = "ALTER TABLE `#__clm_meldeliste_spieler` ADD `gesperrt` tinyint(1) unsigned DEFAULT NULL ";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte gesperrt zu Tabelle clm_meldeliste_spieler hinzugefügt<br />";
			} else {
				echo "<font color='red'>* Fehler bei hinzufügen Spalte gesperrt</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}

		}


		// ---------------------------------------------------------------------------
		// DB TABELLE #__clm_params neu mit 0.97
		// ---------------------------------------------------------------------------
		$database->setQuery ("SHOW COLUMNS FROM #__clm_params");
		$fields = $database->loadObjectList();
		if ( !count($fields) ) { // %
		//Tabelle wird neu angelegt
		$sql = "CREATE TABLE IF NOT EXISTS `#__clm_params` (
			  `params` text NOT NULL,
			  `id` tinyint(1) NOT NULL,
			  PRIMARY KEY (`id`)			  
			) ENGINE=MyISAM" . $utf8 . ";";
		$database->setQuery($sql);
		if ( !$database->query() ) {
			echo "Fehler beim Anlegen Tabelle clm_params";
			return false;
			}
		}

		

		// ---------------------------------------------------------------------------
		// DB TABELLE #__clm_turniere_tlnr
		// ---------------------------------------------------------------------------
		$database->setQuery ("SHOW COLUMNS FROM #__clm_turniere_tlnr");
		$fields = $database->loadObjectList();
		$fieldtypes = array();
		foreach ($fields as $field) {
			$fieldtypes[$field->Field] = $field->Type;
		}

		// 097 v9 - v17
		if ($fieldtypes['sum_bhlz'] != 'decimal(5,2)') {
			$sql = "ALTER TABLE `#__clm_turniere_tlnr` CHANGE `sum_bhlz` `sum_bhlz` DECIMAL(5, 2) NULL DEFAULT NULL";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte sum_bhlz geändert zu Typ DECIMAL(5, 2) in Tabelle clm_turniere_tlnr<br />";
			} else {
				echo "<font color='red'>* Fehler bei ändern Spalte sum_bhlz</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		if ($fieldtypes['sum_busum'] != 'decimal(6,2)') {
			$sql = "ALTER TABLE `#__clm_turniere_tlnr` CHANGE `sum_busum` `sum_busum` DECIMAL(6, 2) NULL DEFAULT NULL";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte sum_busum geändert zu Typ DECIMAL(6, 2) in Tabelle clm_turniere_tlnr<br />";
			} else {
				echo "<font color='red'>* Fehler bei ändern Spalte sum_busum</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		if ($fieldtypes['sum_sobe'] != 'decimal(5,2)') {
			$sql = "ALTER TABLE `#__clm_turniere_tlnr` CHANGE `sum_sobe` `sum_sobe` DECIMAL(5, 2) NULL DEFAULT NULL";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte sum_sobe geändert zu Typ DECIMAL(5, 2) in Tabelle clm_turniere_tlnr<br />";
			} else {
				echo "<font color='red'>* Fehler bei ändern Spalte sum_sobe</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		if (!isset($fieldtypes['sum_wins'])) {
			$sql = "ALTER TABLE `#__clm_turniere_tlnr` ADD `sum_wins` TINYINT( 2 ) UNSIGNED NOT NULL DEFAULT '0' AFTER `sum_sobe`";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte sum_wins hinzugefügt<br />";
			} else {
				echo "<font color='red'>* Fehler bei Hinzufügen Spalte sum_wins</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		// 097 - v20
		if (!isset($fieldtypes['NATrating'])) {
			$sql = "ALTER TABLE `#__clm_turniere_tlnr` ADD `NATrating` SMALLINT(4) UNSIGNED NULL AFTER `twz` ,
						ADD `FIDEelo` SMALLINT(4) UNSIGNED NULL AFTER `NATrating` ,
						ADD `FIDEid` INT(8) UNSIGNED NULL AFTER `FIDEelo` ,
						ADD `FIDEcco` CHAR(3) NULL AFTER `FIDEid`,
						ADD `birthYear` YEAR(4) NOT NULL DEFAULT '0000' AFTER name";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalten für FIDE-Daten hinzugefügt<br />";
			} else {
				echo "<font color='red'>* Fehler bei Hinzufügen der Spalten für FIDE-Daten</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
	   // 097 - v22
		if (!isset($fieldtypes['rankingPos'])) {
			$sql = "ALTER TABLE `#__clm_turniere_tlnr`
						ADD `rankingPos` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0' AFTER `status`";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte für Ranglisten-Position hinzugefügt<br />";
			} else {
				echo "<font color='red'>* Fehler bei Hinzufügen der Spalte für Ranglisten-Position</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		
		// > 1.0.3
		if (!isset($fieldtypes['sumTiebr1'])) {
			$sql = "ALTER TABLE `#__clm_turniere_tlnr`
						ADD `sumTiebr1` DECIMAL(8, 3) NOT NULL DEFAULT '0' AFTER `sum_wins` ,
						ADD `sumTiebr2` DECIMAL(8, 3) NOT NULL DEFAULT '0' AFTER `sumTiebr1` ,
						ADD `sumTiebr3` DECIMAL(8, 3) NOT NULL DEFAULT '0' AFTER `sumTiebr2`";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalten für Feinwertungsinhalte hinzugefügt<br />";
			} else {
				echo "<font color='red'>* Fehler bei Hinzufügen der Spalte für Feinwertungsinhalte</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		
		// 1.14 Geburtsjahr und Geschlecht
		if (!isset($fieldtypes['geschlecht'])) {
			$sql = "ALTER TABLE `#__clm_turniere_tlnr`
						ADD `geschlecht` CHAR(1) DEFAULT NULL AFTER `birthYear`";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalten für Geschlecht hinzugefügt<br />";
				
				// Geschlecht für existierende Spieler aus #__clm_dwz_spieler importieren
				$sql = "UPDATE 
							`#__clm_turniere_tlnr` AS Ziel 
						INNER JOIN 
							`#__clm_dwz_spieler` AS Quelle
						ON 
							Ziel.sid = Quelle.sid AND
							Ziel.zps = Quelle.zps AND 
							Ziel.mgl_nr = Quelle.mgl_nr
						SET 
							Ziel.geschlecht = Quelle.Geschlecht, 
							Ziel.birthYear = Quelle.Geburtsjahr;";
				$database->setQuery($sql);
				if ( $database->query() ) {
					$string .= "* Geburtsjahr und Geschlecht der existierenden Turnierteilnehmer importiert<br />";
				} else {
					$sql = "UPDATE 
								`#__clm_turniere_tlnr` AS Ziel 
							INNER JOIN 
								`#__clm_dwz_spieler` AS Quelle
							ON 
								Ziel.sid = Quelle.sid AND
								Ziel.zps = Quelle.zps AND 
								Ziel.mgl_nr = Quelle.mgl_nr
							SET 
								Ziel.birthYear = Quelle.Geburtsjahr;";
					$database->setQuery($sql);
					if ( $database->query() ) {
						echo "<font color='red'>* Fehler bei Import von Geschlecht der existierenden Turnierteilnehmer</font><br />";
						$string .= "* Geburtsjahr der existierenden Turnierteilnehmer importiert<br />";
					} else {
						echo "<font color='red'>* Fehler bei Import von Geburtsjahr und Geschlecht der existierenden Turnierteilnehmer</font><br />";
						SimpleClmInstaller::_debugDB($fieldtypes);
						return false;
					}
				}
			} else {
				echo "<font color='red'>* Fehler bei Hinzufügen der Spalte für Geschlecht</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		//1.2.5
		if ($fieldtypes['sumTiebr1'] != 'decimal(8,3)') {
			$sql = "ALTER TABLE `#__clm_turniere_tlnr` CHANGE `sumTiebr1` `sumTiebr1` DECIMAL(8,3) NULL DEFAULT NULL";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte sumTiebr1 geändert zu Typ DECIMAL(8,3) in Tabelle clm_turniere_tlnr<br />";
			} else {
				echo "<font color='red'>* Fehler bei ändern Spalte sumTiebr1 in Tabelle clm_turniere_tlnr</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		if ($fieldtypes['sumTiebr2'] != 'decimal(8,3)') {
			$sql = "ALTER TABLE `#__clm_turniere_tlnr` CHANGE `sumTiebr2` `sumTiebr2` DECIMAL(8,3) NULL DEFAULT NULL";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte sumTiebr2 geändert zu Typ DECIMAL(8,3) in Tabelle clm_turniere_tlnr<br />";
			} else {
				echo "<font color='red'>* Fehler bei ändern Spalte sumTiebr2 in Tabelle clm_turniere_tlnr</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		if ($fieldtypes['sumTiebr3'] != 'decimal(8,3)') {
			$sql = "ALTER TABLE `#__clm_turniere_tlnr` CHANGE `sumTiebr3` `sumTiebr3` DECIMAL(8,3) NULL DEFAULT NULL";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte sumTiebr3 geändert zu Typ DECIMAL(8,3) in Tabelle clm_turniere_tlnr<br />";
			} else {
				echo "<font color='red'>* Fehler bei ändern Spalte sumTiebr3 in Tabelle clm_turniere_tlnr</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		//1.2.5
		if (!isset($fieldtypes['tlnrStatus'])) {
			$sql = "ALTER TABLE `#__clm_turniere_tlnr`
						ADD `tlnrStatus` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1' AFTER `rankingPos`";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte tlnrStatus in Tabelle clm_turniere_tlnr hinzugefügt<br />";
			} else {
				echo "<font color='red'>* Fehler bei Hinzufügen der Spalte tlnrStatus in Tabelle clm_turniere_tlnr</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		if (!isset($fieldtypes['anz_spiele'])) {
			$sql = "ALTER TABLE `#__clm_turniere_tlnr`
						ADD `anz_spiele` TINYINT(2) UNSIGNED NOT NULL DEFAULT '0' AFTER `tlnrStatus`";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte anz_spiele in Tabelle clm_turniere_tlnr hinzugefügt<br />";
			} else {
				echo "<font color='red'>* Fehler bei Hinzufügen der Spalte anz_spiele in Tabelle clm_turniere_tlnr</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		
		// ---------------------------------------------------------------------------
		// DB TABELLE #__clm_turniere
		// ---------------------------------------------------------------------------
		$database->setQuery ("SHOW COLUMNS FROM #__clm_turniere");
		$fields = $database->loadObjectList();
		$fieldtypes = array();
		foreach ($fields as $field) {
			$fieldtypes[$field->Field] = $field->Type;
		}

		// 097 v11
		if ($fieldtypes['bezirk'] != 'varchar(8)') {
			// alte Feld-Definition vorsorglich ändern
			$sql = "ALTER TABLE `#__clm_turniere` CHANGE `bezirk` `bezirk` VARCHAR(8) NULL DEFAULT NULL";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte bezirk geändert zu Typ varchar(8) in Tabelle clm_turniere<br />";
			} else {
				echo "<font color='red'>* Fehler bei ändern Spalte bezirk</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
			// Tabelle erweitern
			$sql = "ALTER TABLE `#__clm_turniere` ADD `bezirkTur` ENUM( '0', '1' ) NOT NULL DEFAULT '1' AFTER `bezirk`, "
					. "ADD `vereinZPS` VARCHAR( 5 ) NULL AFTER `bezirkTur`";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Tabelle clm_turniere um drei Felder erweitert<br />";
			} else {
				echo "<font color='red'>* Fehler beim Erweitern der Tabelle `#__clm_turniere`</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
			// alte Daten umschreiben
			$sql = "UPDATE `#__clm_turniere` SET `vereinZPS` = `bezirk`, `bezirkTur` = '0' WHERE `bezirk` != '1'";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Nicht-Bezirksveranstaltungen in clm_turniere umgeschrieben<br />";
			} else {
				echo "<font color='red'>* Fehler beim Umschreiben der Tabelle `#__clm_turniere`</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
			$sql = "UPDATE `#__clm_turniere` SET `bezirkTur` = '1' WHERE `bezirk` = '1'";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Bezirksveranstaltungen in clm_turniere umgeschrieben<br />";
			} else {
				echo "<font color='red'>* Fehler beim Umschreiben der Tabelle `#__clm_turniere`</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}


		}
		
		// 097 v13
		if (!isset($fieldtypes['invitationText'])) {
			$sql = "ALTER TABLE `#__clm_turniere` 
						ADD `invitationText` TEXT AFTER `published`";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte invitationText hinzugefügt in Tabelle clm_turniere<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen der Spalte invitationText</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		
		}
		if (!isset($fieldtypes['dateStart'])) {
			$sql = "ALTER TABLE `#__clm_turniere` 
						ADD `dateStart` DATE NOT NULL AFTER `sid` ,
						ADD `dateEnd` DATE NOT NULL AFTER `dateStart`";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalten dateStart und dateEnd hinzugefügt in Tabelle clm_turniere<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen der Spalten dateStart und dateEnd</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		
		// v17
		if (!isset($fieldtypes['tiebr1'])) {
			$sql = "ALTER TABLE `#__clm_turniere` 
						ADD `tiebr1` TINYINT( 2 ) UNSIGNED NOT NULL DEFAULT '0' AFTER `typ` ,
						ADD `tiebr2` TINYINT( 2 ) UNSIGNED NOT NULL DEFAULT '0' AFTER `tiebr1` ,
						ADD `tiebr3` TINYINT( 2 ) UNSIGNED NOT NULL DEFAULT '0' AFTER `tiebr2`";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalten für Feinwertung hinzugefügt in Tabelle clm_turniere<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen der Spalten für Fewinwertungen</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		
		// v19
		if (!isset($fieldtypes['started'])) {
			$sql = "ALTER TABLE `#__clm_turniere` 
						ADD `started` tinyint(1) NOT NULL DEFAULT '0' AFTER `published`,
						ADD `finished` tinyint(1) NOT NULL DEFAULT '0' AFTER `started`";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalten für Statusflags auf Turnierstart und -ende hinzugefügt in Tabelle clm_turniere<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen der Spalten für Statusflags</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		if ($fieldtypes['published'] != 'tinyint(1)') {
			// alte Feld-Definition vorsorglich ändern
			$sql = "ALTER TABLE `#__clm_turniere` CHANGE `published` `published` tinyint(1) NOT NULL DEFAULT '0'";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte published geändert zu Typ tinyint(1) in Tabelle clm_turniere<br />";
			} else {
				echo "<font color='red'>* Fehler bei ändern Spalte published</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		
		// v20
		if (!isset($fieldtypes['params'])) {
			$sql = "ALTER TABLE `#__clm_turniere` 
						ADD `params` TEXT NOT NULL";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte für Turnierparameter hinzugefügt in Tabelle clm_turniere<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen der Spalte für Turnierparameter</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		if (!isset($fieldtypes['catidAlltime'])) {
			$sql = "ALTER TABLE `#__clm_turniere` 
						ADD `catidAlltime` SMALLINT(6) UNSIGNED NOT NULL DEFAULT '0' AFTER `dateEnd`,
						ADD `catidEdition` SMALLINT(6) UNSIGNED NOT NULL DEFAULT '0' AFTER `catidAlltime`";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte für Turnierparameter hinzugefügt in Tabelle clm_turniere<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen der Spalte für Turnierparameter</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		
		
		// Anpassung / Angleichung Adressen für Mannschaften und Vereine
		// 097 v14

		// ---------------------------------------------------------------------------
		// DB TABELLE #__clm_turniere
		// ---------------------------------------------------------------------------
		$database->setQuery ("SHOW COLUMNS FROM #__clm_mannschaften");
		$fields = $database->loadObjectList();
		$fieldtypes = array();
		foreach ($fields as $field) {
			$fieldtypes[$field->Field] = $field->Type;
		}

		if (!isset($fieldtypes['termine'])) { // %
			$sql = "ALTER TABLE `#__clm_mannschaften` 
						ADD `homepage` TEXT AFTER `lokal` ,
						ADD `adresse` TEXT AFTER `lokal`, 
						ADD `termine` TEXT AFTER `lokal`";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalten für Adresse (Mannschaften) hinzugefügt in Tabelle clm_mannschaften<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen der Adressspalten</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		
		}


		// Anpassung #__clm_turniere_rnd_spl - tiebreak-Felder für KO-Modus
		// 097 v17
		$database->setQuery ("SHOW COLUMNS FROM #__clm_turniere_rnd_spl");
		$fields = $database->loadObjectList();
		$fieldtypes = array();
		foreach ($fields as $field) {
			$fieldtypes[$field->Field] = $field->Type;
		}
		if (!isset($fieldtypes['tiebrS'])) {
			$sql = "ALTER TABLE `#__clm_turniere_rnd_spl` 
						ADD `tiebrS` TINYINT( 2 ) UNSIGNED NOT NULL DEFAULT '0' AFTER `ergebnis`,
						ADD `tiebrG` TINYINT( 2 ) UNSIGNED NOT NULL DEFAULT '0' AFTER `tiebrS`";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalten für Enscheidung bei KO-System-Turnieren hinzugefügt in Tabelle clm_turniere_rnd_spl<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen der KO-Turnier-Spalten</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		
		// Anpassung #__clm_turniere_rnd_spl - pgn
		// 1.0.1+
		if (!isset($fieldtypes['pgn'])) {
			$sql = "ALTER TABLE `#__clm_turniere_rnd_spl` 
						ADD `pgn` TEXT NOT NULL AFTER `kampflos`";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalten für pgnhinzugefügt in Tabelle clm_turniere_rnd_spl<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen der KO-Turnier-Spalten</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		
		// Anpassung #__clm_turniere_tlnr - koStatus für KO-Modus
		// 097 v17
		$database->setQuery ("SHOW COLUMNS FROM #__clm_turniere_tlnr");
		$fields = $database->loadObjectList();
		$fieldtypes = array();
		foreach ($fields as $field) {
			$fieldtypes[$field->Field] = $field->Type;
		}
		if (!isset($fieldtypes['koStatus'])) {
			$sql = "ALTER TABLE `#__clm_turniere_tlnr` 
						ADD `koStatus` ENUM( '0', '1' ) NOT NULL DEFAULT '1' AFTER `sum_sobe`,
						ADD `koRound` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0' AFTER `koStatus`";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte für Enscheidung bei KO-System hinzugefügt in Tabelle clm_turniere_tlnr<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen der kostatus-Spalte</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}

		// Anpassung #__clm_runden_termine - DWZ Auswertung anzeigen im BE View
		// 097 v19
		$database->setQuery ("SHOW COLUMNS FROM #__clm_runden_termine");
		$fields = $database->loadObjectList();
		$fieldtypes = array();
		foreach ($fields as $field) {
			$fieldtypes[$field->Field] = $field->Type;
		}
		if (!isset($fieldtypes['dwz'])) { // %
			$sql = "ALTER TABLE `#__clm_runden_termine` 
						ADD `dwz` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0' AFTER `gemeldet`";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalten für Durchführung der DWZ Anzeige in Tabelle clm_runden_termine<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen der DWZ Anzeige-Spalten BE View</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		
		}

		// ---------------------------------------------------------------------------
		// DB TABELLE #__clm_log
		// ---------------------------------------------------------------------------
		$database->setQuery ("SHOW COLUMNS FROM #__clm_log");
		$fields = $database->loadObjectList();
		$fieldtypes = array();
		foreach ($fields as $field) {
			$fieldtypes[$field->Field] = $field->Type;
		}
		// 097 v20
		if (!isset($fieldtypes['tid'])) {
			$sql = "ALTER TABLE `#__clm_log` ADD `tid` int(11) UNSIGNED NULL AFTER `lid`";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte TurnierID hinzugefügt in Tabelle clm_log<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen der TurnierID-Spalte</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		if (!isset($fieldtypes['catid'])) {
			$sql = "ALTER TABLE `#__clm_log` ADD `catid` SMALLINT(6) UNSIGNED NULL DEFAULT NULL AFTER `sid`";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte KategorieID hinzugefügt in Tabelle clm_log<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen der Kategorie-Spalte</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		//1.0.6
		if (!isset($fieldtypes['nr_aktion'])) {					//klkl
			$sql = "ALTER TABLE `#__clm_log` ADD `nr_aktion` SMALLINT(5) AFTER `datum`";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte AktionsNummer hinzugefügt in Tabelle clm_log<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen der Spalte Aktionsnummer</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		
		// ---------------------------------------------------------------------------
		// DB TABELLE #__clm_categories (neu mit v20/21)
		// ---------------------------------------------------------------------------
		$database->setQuery ("SHOW COLUMNS FROM #__clm_categories");
		$fields = $database->loadObjectList();
		if ( !count($fields) ) {
			//Tabelle wird neu angelegt
			$sql = "CREATE TABLE IF NOT EXISTS `#__clm_categories` (
					`id` smallint(6) NOT NULL auto_increment,
					`parentid` smallint(6) unsigned default NULL,
					`name` varchar(100) NOT NULL default '',
					`sid` mediumint(3) unsigned default NULL,
					`dateStart` date NOT NULL,
					`dateEnd` date NOT NULL,
					`tl` mediumint(5) unsigned default NULL,
					`bezirk` varchar(8) default NULL,
					`bezirkTur` enum('0','1') NOT NULL default '1',
					`vereinZPS` varchar(5) default NULL,
					`published` mediumint(3) unsigned default NULL,
					`started` tinyint(1) NOT NULL default '0',
					`finished` tinyint(1) NOT NULL default '0',
					`invitationText` text,
					`bemerkungen` text,
					`bem_int` text,
					`checked_out` tinyint(3) unsigned NOT NULL default '0',
					`checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
					`ordering` int(11) NOT NULL default '0',
					`params` text NOT NULL,
				PRIMARY KEY  (`id`),
				KEY `published` (`published`)
				) ENGINE=MyISAM " . $utf8 . ";";
			$database->setQuery($sql);
			if ( !$database->query() ) {
				echo "Fehler beim Anlegen Tabelle clm_categories";
				return false;
			} else {
				$string .= "* Tabelle clm_categories hinzugefügt<br />";
			}
		}

		
		// ---------------------------------------------------------------------------
		// DB TABELLE #__clm_dwz_spieler
		// ---------------------------------------------------------------------------
		$database->setQuery ("SHOW COLUMNS FROM #__clm_dwz_spieler");
		$fields = $database->loadObjectList();
		$fieldtypes = array();
		foreach ($fields as $field) {
			$fieldtypes[$field->Field] = $field->Type;
		}
		// 1.01
		if (!isset($fieldtypes['DWZ_neu'])) {
			$sql = "ALTER TABLE `#__clm_dwz_spieler` ADD `DWZ_neu` smallint(4) unsigned NOT NULL default '0' AFTER `FIDE_Land`";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte DWZ_neu hinzugefügt in Tabelle clm_dwz_spieler<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen der DWZ_neu-Spalte</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		if (!isset($fieldtypes['I0'])) {
			$sql = "ALTER TABLE `#__clm_dwz_spieler` ADD `I0` smallint(4) unsigned NOT NULL default '0' AFTER `DWZ_neu`";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte I0 hinzugefügt in Tabelle clm_dwz_spieler<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen der I0-Spalte</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		if (!isset($fieldtypes['Punkte'])) {
			$sql = "ALTER TABLE `#__clm_dwz_spieler` ADD `Punkte` decimal(4,1) unsigned NOT NULL default '0.0' AFTER `I0`";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte Punkte hinzugefügt in Tabelle clm_dwz_spieler<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen der Punkte-Spalte</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		if (!isset($fieldtypes['Partien'])) {
			$sql = "ALTER TABLE `#__clm_dwz_spieler` ADD `Partien` tinyint(3) NOT NULL default '0' AFTER `Punkte`";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte Partien hinzugefügt in Tabelle clm_dwz_spieler<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen der Partien-Spalte</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		if (!isset($fieldtypes['We'])) {
			$sql = "ALTER TABLE `#__clm_dwz_spieler` ADD `We` decimal(6,3) NOT NULL default '0.000' AFTER `Partien`";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte We hinzugefügt in Tabelle clm_dwz_spieler<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen der We-Spalte</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		if (!isset($fieldtypes['Leistung'])) {
			$sql = "ALTER TABLE `#__clm_dwz_spieler` ADD `Leistung` smallint(4) NOT NULL default '0' AFTER `We`";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte Leistung hinzugefügt in Tabelle clm_dwz_spieler<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen der Leistung-Spalte</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		if (!isset($fieldtypes['EFaktor'])) {
			$sql = "ALTER TABLE `#__clm_dwz_spieler` ADD `EFaktor` tinyint(2) NOT NULL default '0' AFTER `Leistung`";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte EFaktor hinzugefügt in Tabelle clm_dwz_spieler<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen der EFaktor-Spalte</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		if (!isset($fieldtypes['Niveau'])) {
			$sql = "ALTER TABLE `#__clm_dwz_spieler` ADD `Niveau` smallint(4) NOT NULL default '0' AFTER `EFaktor`";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte Niveau hinzugefügt in Tabelle clm_dwz_spieler<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen der Niveau-Spalte</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		
		// Indices ergänzen? -> 1.0.3
		$database->setQuery ("SHOW INDEX FROM #__clm_dwz_spieler");
		$indices = $database->loadObjectList('Column_name');
		if (!isset($indices['sid'])) {
			$sql = "ALTER TABLE #__clm_dwz_spieler ADD INDEX ( `sid` )";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Index 'sid' hinzugefügt in Tabelle clm_dwz_spieler<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen Index 'sid' in Tabelle clm_dwz_spieler</font><br />";
				echo mysql_error()."<br /><br />";
				echo "Vorhandene Feldnamen in Indices: ";
				SimpleClmInstaller::_debugDB($indices);
				return false;
			}
		}
		if (!isset($indices['ZPS'])) {
			$sql = "ALTER TABLE #__clm_dwz_spieler ADD INDEX ( `ZPS` )";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Index 'ZPS' hinzugefügt in Tabelle clm_dwz_spieler<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen Index 'ZPS' in Tabelle clm_dwz_spieler</font><br />";
				echo mysql_error()."<br /><br />";
				echo "Vorhandene Feldnamen in Indices: ";
				SimpleClmInstaller::_debugDB($indices);
				return false;
			}
		}
		
		// ---------------------------------------------------------------------------
		// DB TABELLE #__clm_saison                           
		// ---------------------------------------------------------------------------
		$database->setQuery ("SHOW COLUMNS FROM #__clm_saison");
		$fields = $database->loadObjectList();
		$fieldtypes = array();
		foreach ($fields as $field) {
			$fieldtypes[$field->Field] = $field->Type;
		}
		// 1.0.6
		if (!isset($fieldtypes['datum'])) {
			$sql = "ALTER TABLE `#__clm_saison` ADD `datum` date NOT NULL default '0000-00-00' AFTER `ordering`";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte DSB-Datum hinzugefügt in Tabelle clm_saison<br />";
				$string .= "        -->Pflege über Saison-Manager wird empfohlen, aber nicht zwingend<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen der Spalte DSB-Datum</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		// ---------------------------------------------------------------------------
		// DB TABELLE #__clm_mannschaften                           
		// ---------------------------------------------------------------------------
		$database->setQuery ("SHOW COLUMNS FROM #__clm_mannschaften");
		$fields = $database->loadObjectList();
		$fieldtypes = array();
		foreach ($fields as $field) {
			$fieldtypes[$field->Field] = $field->Type;
		}
		// 1.1.17
		if (!isset($fieldtypes['summanpunkte'])) {
			$sql = "ALTER TABLE `#__clm_mannschaften` ADD `summanpunkte` decimal(4,1) default NULL AFTER `ordering`";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte summanpunkte hinzugefügt in Tabelle clm_mannschaften<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen der Spalte summanpunkte</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		if (!isset($fieldtypes['sumbrettpunkte'])) {
			$sql = "ALTER TABLE `#__clm_mannschaften` ADD `sumbrettpunkte` decimal(4,1) default NULL AFTER `summanpunkte`";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte sumbrettpunkte hinzugefügt in Tabelle clm_mannschaften<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen der Spalte sumbrettpunkte</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		if (!isset($fieldtypes['sumwins'])) {
			$sql = "ALTER TABLE `#__clm_mannschaften` ADD `sumwins` tinyint(2) default NULL AFTER `sumbrettpunkte`";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte sumwins hinzugefügt in Tabelle clm_mannschaften<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen der Spalte sumwins</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		if (!isset($fieldtypes['sumtiebr1'])) {
			$sql = "ALTER TABLE `#__clm_mannschaften` ADD `sumtiebr1` decimal(6,3) default '0.000' AFTER `sumwins`";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte sumtiebr1 hinzugefügt in Tabelle clm_mannschaften<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen der Spalte sumtiebr1</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		if (!isset($fieldtypes['sumtiebr2'])) {
			$sql = "ALTER TABLE `#__clm_mannschaften` ADD `sumtiebr2` decimal(6,3) default '0.000' AFTER `sumtiebr1`";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte sumtiebr2 hinzugefügt in Tabelle clm_mannschaften<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen der Spalte sumtiebr2</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		if (!isset($fieldtypes['sumtiebr3'])) {
			$sql = "ALTER TABLE `#__clm_mannschaften` ADD `sumtiebr3` decimal(6,3) default '0.000' AFTER `sumtiebr2`";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte sumtiebr3 hinzugefügt in Tabelle clm_mannschaften<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen der Spalte sumtiebr3</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		if (!isset($fieldtypes['rankingpos'])) {
			$sql = "ALTER TABLE `#__clm_mannschaften` ADD `rankingpos` tinyint(3) unsigned NOT NULL default '0' AFTER `sumtiebr3`";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte rankingpos hinzugefügt in Tabelle clm_mannschaften<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen der Spalte rankingpos</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		// 1.2.6
		if (!isset($fieldtypes['sname'])) {
			$sql = "ALTER TABLE `#__clm_mannschaften` ADD `sname` varchar(20) default '' AFTER `rankingpos`";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte sname hinzugefügt in Tabelle clm_mannschaften<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen der Spalte sname in Tabelle clm_mannschaften</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		
		// ---------------------------------------------------------------------------
		// DB TABELLE #__clm_rnd_man                            
		// ---------------------------------------------------------------------------
		$database->setQuery ("SHOW COLUMNS FROM #__clm_rnd_man");
		$fields = $database->loadObjectList();
		$fieldtypes = array();
		foreach ($fields as $field) {
			$fieldtypes[$field->Field] = $field->Type;
		}	 
		// 1.1.2
		if (!isset($fieldtypes['wertpunkte'])) {
			$sql = "ALTER TABLE `#__clm_rnd_man` ADD `wertpunkte` decimal(5,1) NULL default NULL AFTER `ordering`";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte wertpunkte hinzugefügt in Tabelle clm_rnd_man<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen der Spalte wertpunkte</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		// 1.1.17
		if (!isset($fieldtypes['ko_decision'])) {
			$sql = "ALTER TABLE `#__clm_rnd_man` ADD `ko_decision` tinyint(1) unsigned NOT NULL default '0' AFTER `wertpunkte`";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte ko_decision hinzugefügt in Tabelle clm_rnd_man<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen der Spalte ko_decision</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		if (!isset($fieldtypes['comment'])) {
			$sql = "ALTER TABLE `#__clm_rnd_man` ADD `comment` text NOT NULL AFTER `ko_decision`";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte comment hinzugefügt in Tabelle clm_rnd_man<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen der Spalte comment</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
					
		// ---------------------------------------------------------------------------
		// DB TABELLE #__clm_todo löschen (ab v 1.1.13)
		// ---------------------------------------------------------------------------
		$database->setQuery ("SHOW COLUMNS FROM #__clm_todo");
		$fields = $database->loadObjectList();
		if ( count($fields) ) {
			//Tabelle wird gelöscht
			$sql = "DROP TABLE IF EXISTS #__clm_todo ;";
			$database->setQuery($sql);
			if ( !$database->query() ) {
				echo "Fehler beim Löschen Tabelle clm_todo";
				return false;
			} else {
				$string .= "* Tabelle clm_todo gelöscht<br />";
			}
		}
		
		// ---------------------------------------------------------------------------
		// DB TABELLE #__clm_termine (neu mit v 1.1.13)
		// ---------------------------------------------------------------------------
		$database->setQuery ("SHOW COLUMNS FROM #__clm_termine");
		$fields = $database->loadObjectList();
		if ( !count($fields) ) {
			//Tabelle wird neu angelegt
			$sql = "CREATE TABLE IF NOT EXISTS `#__clm_termine` (
					`id` int(11) NOT NULL auto_increment,
				    `name` varchar(100) NOT NULL default '',
				    `beschreibung` text,
					`address` varchar(100) NOT NULL default '',
					`category` varchar(33) NOT NULL default '',
		  			`host` varchar(5) default NULL,
				    `startdate` date NOT NULL default '0000-00-00 00:00:00',
				    `enddate` date NOT NULL default '0000-00-00 00:00:00',
					`attached_file` varchar(256) NULL default '',
					`attached_file_description` varchar(128) NULL default '',
				    `published` mediumint(3) unsigned default NULL,
				    `checked_out` tinyint(3) unsigned NOT NULL default '0',
				    `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
				    `ordering` int(11) NOT NULL default '0',
					`event_link` varchar(500) NOT NULL default '',
				PRIMARY KEY  (`id`),
				KEY `published` (`published`)
				) ENGINE=MyISAM " . $utf8 . ";";
			$database->setQuery($sql);
			if ( !$database->query() ) {
				echo "Fehler beim Anlegen Tabelle clm_termine";
				return false;
			} else {
				$string .= "* Tabelle clm_termine hinzugefügt<br />";
			}
		}

		// ---------------------------------------------------------------------------
		// DB TABELLE #__clm_termine                            
		// ---------------------------------------------------------------------------
		$database->setQuery ("SHOW COLUMNS FROM #__clm_termine");
		$fields = $database->loadObjectList();
		$fieldtypes = array();
		foreach ($fields as $field) {
			$fieldtypes[$field->Field] = $field->Type;
		}	 
		// 1.1.18
		if (!isset($fieldtypes['event_link'])) {
			$sql = "ALTER TABLE `#__clm_termine` ADD `event_link` varchar(500) NOT NULL default '' AFTER `ordering`";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte event_link hinzugefügt in Tabelle clm_termine<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen der Spalte event_link in Tabelle clm_termine</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		} elseif  ($fieldtypes['event_link'] != 'varchar(500)') { // 1.2.4
			$sql = "ALTER TABLE `#__clm_termine` CHANGE `event_link` `event_link` varchar( 500 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL "; 
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte event_link updated in Tabelle clm_termine<br />";
			} else {
				echo "<font color='red'>* Fehler beim Update der Spalte event_link  in Tabelle clm_termine</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		
		// Indices ergänzen 
		// ---------------------------------------------------------------------------
		// DB TABELLE #__clm_mannschaften (1.1.15)
		// ---------------------------------------------------------------------------
		$database->setQuery ("SHOW INDEX FROM #__clm_mannschaften");
		$indices = $database->loadObjectList('Key_name');
		if (!isset($indices['liga_sid'])) {
			$sql = "ALTER TABLE #__clm_mannschaften ADD INDEX liga_sid ( `liga`, `sid` )";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Index 'liga_sid' hinzugefügt in Tabelle clm_mannschaften<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen Index 'liga_sid' in Tabelle clm_mannschaften</font><br />";
				echo mysql_error()."<br /><br />";
				echo "Vorhandene Indices: ";
				SimpleClmInstaller::_debugDB($indices);
				return false;
			}
		}
		
		// ---------------------------------------------------------------------------
		// DB TABELLE #__clm_rnd_man (1.1.15)
		// ---------------------------------------------------------------------------
		$database->setQuery ("SHOW INDEX FROM #__clm_rnd_man");
		$indices = $database->loadObjectList('Key_name');
		if (!isset($indices['lid_sid'])) {
			$sql = "ALTER TABLE #__clm_rnd_man ADD INDEX lid_sid ( `lid`, `sid` )";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Index 'lid_sid' hinzugefügt in Tabelle clm_rnd_man<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen Index 'lid_sid' in Tabelle clm_rnd_man</font><br />";
				echo mysql_error()."<br /><br />";
				echo "Vorhandene Indices: ";
				SimpleClmInstaller::_debugDB($indices);
				return false;
			}
		}
		
		// ---------------------------------------------------------------------------
		// DB TABELLE #__clm_rnd_spl (1.1.20)
		// ---------------------------------------------------------------------------
		$database->setQuery ("SHOW INDEX FROM #__clm_rnd_spl");
		$indices = $database->loadObjectList('Key_name');
		if (!isset($indices['sid_zps_spieler'])) {
			$sql = "ALTER TABLE #__clm_rnd_spl ADD INDEX sid_zps_spieler ( `sid`, `zps`, `spieler` )";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Index 'sid_zps_spieler' hinzugefügt in Tabelle clm_rnd_spl<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen Index 'sid_zps_spieler' in Tabelle clm_rnd_spl</font><br />";
				echo mysql_error()."<br /><br />";
				echo "Vorhandene Indices: ";
				SimpleClmInstaller::_debugDB($indices);
				return false;
			}
		}
		
		// ---------------------------------------------------------------------------
		// DB TABELLE #__clm_turniere_tlnr (1.1.15)
		// ---------------------------------------------------------------------------
		$database->setQuery ("SHOW INDEX FROM #__clm_turniere_tlnr");
		$indices = $database->loadObjectList('Key_name');
		if (!isset($indices['turnier_snr'])) {
			$sql = "ALTER TABLE #__clm_turniere_tlnr ADD INDEX turnier_snr ( `turnier`, `snr` )";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Index 'turnier_snr' hinzugefügt in Tabelle clm_turniere_tlnr<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen Index 'turnier_snr' in Tabelle clm_turniere_tlnr</font><br />";
				echo mysql_error()."<br /><br />";
				echo "Vorhandene Indices: ";
				SimpleClmInstaller::_debugDB($indices);
				return false;
			}
		}

		// ---------------------------------------------------------------------------
		// DB TABELLE #__clm_dwz_spieler (1.1.15)
		// ---------------------------------------------------------------------------
		$database->setQuery ("SHOW INDEX FROM #__clm_dwz_spieler");
		$indices = $database->loadObjectList('Key_name');
		if (!isset($indices['sid_zps_mglnr'])) {
			$sql = "ALTER TABLE #__clm_dwz_spieler ADD UNIQUE sid_zps_mglnr ( `sid`, `ZPS`, `Mgl_Nr` )";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Index 'sid_zps_mglnr' hinzugefügt in Tabelle clm_dwz_spieler<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen Index 'sid_zps_mglnr' in Tabelle clm_dwz_spieler</font><br />";
				echo mysql_error()."<br /><br />";
				echo "Vorhandene Indices: ";
				SimpleClmInstaller::_debugDB($indices);
				return false;
			}
		}
		
		// ---------------------------------------------------------------------------
		// DB TABELLE #__clm_meldeliste_spieler (1.1.15)
		// ---------------------------------------------------------------------------
		$database->setQuery ("SHOW INDEX FROM #__clm_meldeliste_spieler");
		$indices = $database->loadObjectList('Key_name');
		if (!isset($indices['sid_zps_mglnr'])) {
			$sql = "ALTER TABLE #__clm_meldeliste_spieler ADD INDEX sid_zps_mglnr ( `sid`, `zps`, `mgl_nr` )";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Index 'sid_zps_mglnr' hinzugefügt in Tabelle clm_meldeliste_spieler<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen Index 'sid_zps_mglnr' in Tabelle clm_meldeliste_spieler</font><br />";
				echo mysql_error()."<br /><br />";
				echo "Vorhandene Indices: ";
				SimpleClmInstaller::_debugDB($indices);
				return false;
			}
		}
		
		// ---------------------------------------------------------------------------
		// DB TABELLE #__clm_user (1.1.15)
		// ---------------------------------------------------------------------------
		$database->setQuery ("SHOW INDEX FROM #__clm_user");
		$indices = $database->loadObjectList('Key_name');
		if (!isset($indices['sid_jid'])) {
			$sql = "ALTER TABLE #__clm_user ADD INDEX sid_jid ( `sid`, `jid` )";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Index 'sid_jid' hinzugefügt in Tabelle clm_user<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen Index 'sid_jid' in Tabelle clm_user</font><br />";
				echo mysql_error()."<br /><br />";
				echo "Vorhandene Indices: ";
				SimpleClmInstaller::_debugDB($indices);
				return false;
			}
		}
		
		// ---------------------------------------------------------------------------
		// DB TABELLE #__clm_turniere_sonderranglisten (neu mit v 1.1.17)
		// ---------------------------------------------------------------------------
		$database->setQuery ("SHOW COLUMNS FROM #__clm_turniere_sonderranglisten");
		$fields = $database->loadObjectList();
		if ( !count($fields) ) {
			//Tabelle wird neu angelegt
			$sql = 	"CREATE TABLE IF NOT EXISTS `#__clm_turniere_sonderranglisten` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`turnier` int(11) NOT NULL,
				`name` varchar(100) NOT NULL,
				`use_rating_filter` enum('0','1') DEFAULT '0',
				`rating_type` tinyint(1) DEFAULT '0',
				`rating_higher_than` smallint(4) DEFAULT '0',
				`rating_lower_than` smallint(4) DEFAULT '3000',
				`use_birthYear_filter` enum('0','1') DEFAULT '0',
				`birthYear_younger_than` year(4) DEFAULT '0000',
				`birthYear_older_than` year(4) DEFAULT '0000',
				`use_sex_filter` enum('0','1') DEFAULT '0',
				`sex` enum('','M','W') DEFAULT NULL,
				`published` tinyint(1) NOT NULL DEFAULT '0',
				`checked_out` tinyint(3) unsigned NOT NULL,
				`checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`ordering` int(11) NOT NULL DEFAULT '0',
				`use_zps_filter` enum('0','1') DEFAULT '0',
				`zps_higher_than` varchar(5) DEFAULT '',
				`zps_lower_than` varchar(5) DEFAULT 'ZZZZZ',
				PRIMARY KEY (`id`)
				) ENGINE=MyISAM" . $utf8 . ";";
			$database->setQuery($sql);
			if ( !$database->query() ) {
				echo "Fehler beim Anlegen Tabelle clm_turniere_sonderranglisten";
				return false;
			} else {
				$string .= "* Tabelle clm_turniere_sonderranglisten hinzugefügt<br />";
			}
		}
		// ---------------------------------------------------------------------------
		// DB TABELLE #__clm_turniere_sonderranglisten                            
		// ---------------------------------------------------------------------------
		$database->setQuery ("SHOW COLUMNS FROM #__clm_turniere_sonderranglisten");
		$fields = $database->loadObjectList();
		$fieldtypes = array();
		foreach ($fields as $field) {
			$fieldtypes[$field->Field] = $field->Type;
		}	 
		// 1.1.18
		if (!isset($fieldtypes['use_zps_filter'])) {
			$sql = "ALTER TABLE `#__clm_turniere_sonderranglisten` ADD `use_zps_filter` enum('0','1') DEFAULT '0' AFTER `ordering`";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte use_zps_filter hinzugefügt in Tabelle clm_turniere_sonderranglisten<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen der Spalte use_zps_filter in Tabelle clm_turniere_sonderranglisten</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		if (!isset($fieldtypes['zps_higher_than'])) {
			$sql = "ALTER TABLE `#__clm_turniere_sonderranglisten` ADD `zps_higher_than` varchar(5) DEFAULT '' AFTER `use_zps_filter`";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte zps_higher_than hinzugefügt in Tabelle clm_turniere_sonderranglisten<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen der Spalte zps_higher_than in Tabelle clm_turniere_sonderranglisten</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		if (!isset($fieldtypes['zps_lower_than'])) {
			$sql = "ALTER TABLE `#__clm_turniere_sonderranglisten` ADD `zps_lower_than` varchar(5) DEFAULT 'ZZZZZ' AFTER `zps_higher_than`";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte zps_lower_than hinzugefügt in Tabelle clm_turniere_sonderranglisten<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen der Spalte zps_lower_than in Tabelle clm_turniere_sonderranglisten</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		
		// ---------------------------------------------------------------------------
		// DB TABELLE #__clm_user                            
		// ---------------------------------------------------------------------------
		$database->setQuery ("SHOW COLUMNS FROM #__clm_user");
		$fields = $database->loadObjectList();
		$fieldtypes = array();
		foreach ($fields as $field) {
			$fieldtypes[$field->Field] = $field->Type;
		}	 
		// 1.2.2
		if (!isset($fieldtypes['mglnr'])) {
			$sql = "ALTER TABLE `#__clm_user` ADD `mglnr` varchar(5) DEFAULT NULL AFTER `zps`";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte mglnr hinzugefügt in Tabelle clm_user<br />";
			} else {
				echo "<font color='red'>* Fehler beim Hinzufügen der Spalte mglnr in Tabelle clm_user</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		
		// ---------------------------------------------------------------------------
		// DB TABELLE #__clm_access_point (neu mit v 1.2.4)
		// ---------------------------------------------------------------------------
		$database->setQuery ("SHOW COLUMNS FROM #__clm_access_points");
		$fields = $database->loadObjectList();
		if ( !count($fields) ) {
			//Tabelle wird neu angelegt
			$sql = "CREATE TABLE IF NOT EXISTS `#__clm_access_points` (
					`id` int(11) NOT NULL AUTO_INCREMENT,
					`area` char(2) NOT NULL DEFAULT 'BE',
					`accesstopic` varchar(20) NOT NULL,
					`accesspoint` varchar(20) NOT NULL,
					`rule` char(3) NOT NULL DEFAULT 'NY',
					`published` int(1) NOT NULL DEFAULT '0',
					`ordering` int(11) NOT NULL DEFAULT '0',
					PRIMARY KEY (`id`)
					) ENGINE=MyISAM " . $utf8 . ";";
			$database->setQuery($sql);
			if ( !$database->query() ) {
				echo "Fehler beim Anlegen Tabelle clm_access_points";
				return false;
			} else {
				$string .= "* Tabelle clm_access_points hinzugefügt<br />";
			}
		}
		
		// ---------------------------------------------------------------------------
		// DB TABELLE #__clm_usertype
		// ---------------------------------------------------------------------------
		$database->setQuery ("SHOW COLUMNS FROM #__clm_usertype");
		$fields = $database->loadObjectList();
		$fieldtypes = array();
		foreach ($fields as $field) {
			$fieldtypes[$field->Field] = $field->Type;
		}

		// 1.2.4
		if (!isset($fieldtypes['type'])) {
			$sql = "ALTER TABLE `#__clm_usertype` ADD `type` VARCHAR( 4 ) NOT NULL DEFAULT 'USER' AFTER `user_clm`";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte type in Tabelle clm_usertype hinzugefügt<br />";
			} else {
				echo "<font color='red'>* Fehler bei Hinzufügen Spalte type in Tabelle clm_usertype</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		// 1.2.4
		if (!isset($fieldtypes['published'])) {
			$sql = "ALTER TABLE `#__clm_usertype` ADD `published` INT( 1 ) NOT NULL DEFAULT '0' AFTER `group`";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte published in Tabelle clm_usertype hinzugefügt<br />";
			} else {
				echo "<font color='red'>* Fehler bei Hinzufügen Spalte published in Tabelle clm_usertype</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		// 1.2.4
		if (!isset($fieldtypes['fe_params'])) {
			$sql = "ALTER TABLE `#__clm_usertype` ADD `fe_params` TEXT NOT NULL AFTER `ordering`";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte fe_params in Tabelle clm_usertype hinzugefügt<br />";
			} else {
				echo "<font color='red'>* Fehler bei Hinzufügen Spalte fe_params in Tabelle clm_usertype</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}
		// 1.2.4
		if (!isset($fieldtypes['be_params'])) {
			$sql = "ALTER TABLE `#__clm_usertype` ADD `be_params` TEXT NOT NULL AFTER `fe_params`";
			$database->setQuery($sql);
			if ( $database->query() ) {
				$string .= "* Spalte be_params in Tabelle clm_usertype hinzugefügt<br />";
			} else {
				echo "<font color='red'>* Fehler bei Hinzufügen Spalte be_params in Tabelle clm_usertype</font><br />";
				SimpleClmInstaller::_debugDB($fieldtypes);
				return false;
			}
		}

 
 
		// Termination
		if ( $string == '' ) {
			$string = "* Alles in Ordnung, kein DB-Upgrade erforderlich<br />";
		}
		return $string;
		
		
		}
}	
	
	function com_install() {
	
	
	$installer = new SimpleClmInstaller();
	$database = $installer->_getDB();
	$collation = $database->getCollation();
	// Prüfung on Neuinstallation oder Upgrade anhand des Vorhandensein von Tabelle clm_user
	$database->setQuery ("SHOW COLUMNS FROM #__clm_user");
	$fields = $database->loadObjectList();
	if ( !count($fields) ) {
		// Neuinstallation laufen lassen
		$dbinstall = $installer->dbinstall($collation);
		if ( $dbinstall ) {
			echo "<h3>Anlegen Tabellen:</h3><br />";
			echo $dbinstall;
			echo "<font color='green'>---> OK!</font><br />";
		} else {
			echo "<h3 style=\"color: red;\">Fehler während Anlegen Datenbank..<br/></h3>";
			return false;
		}
	}
	// Die Upgrade-Funktion läuft immer auch wenn die DB gerade angelegt wurde
	// Hiermit werden die DB-Tabellen auf die letzte Version gebracht
	$dbupgrade = $installer->dbupgrade($collation);
	if ( $dbupgrade ) {
		echo "<h3>Update DB-Tabellen:</h3><br />";
		echo $dbupgrade;
		echo "<font color='green'>----> OK!</font><br />";
	} else {
		echo "<h3 style=\"color: red;\">Fehler während Update Datenbank..<br/></h3>";
		return false;
	}

	jimport('joomla.filesystem.file');

	$path	= JPATH_ROOT.DS.'administrator'.DS.'components'.DS;
	$backup	= $path.'__backup_clm';

	// Backup Ordner suchen und ggf. Backup Dateien einspielen
	if (JFolder::exists($backup)){
	echo "<h3>Backup der userspezifischen Dateien einspielen :</h3>";

	////////////////////////////
	// Parameter zurückschreiben
	$db	=& JFactory::getDBO();
	// Backup Paramter holen
	$sql = " SELECT params FROM #__clm_params ";
	$db->setQuery( $sql);
	$param_clm = $db->loadObjectList();
	if (isset($param_clm) AND count($param_clm) == 1) {																							
		// Joomla-Version ermitteln
		$version = new JVersion();
		$joomlaVersion = $version->getShortVersion();
		if (substr_count($joomlaVersion, '1.5')) {
			// Parameter schreiben
			$sql = " UPDATE #__components SET `params` = '".$param_clm[0]->params."'"
				." WHERE `option` = 'com_clm'"
				;
		} else {
			// Parameter schreiben
			$sql = " UPDATE #__extensions SET `params` = '".$param_clm[0]->params."'"
				." WHERE `element` = 'com_clm'"
				;
		}
	}
	$db->setQuery( $sql);
	$db->query();
	
	// Parameter löschen
	$sql = " TRUNCATE TABLE #__clm_params ";
	$db->setQuery( $sql);
	$db->query();
	
	// Ende Parameter
	/////////////////
 
	// Sprachdatei Frontend kopieren
	$path_fe	= JPATH_ROOT.DS.'language'.DS;
	$src_fe		= $path_fe.'de-DE'.DS.'.com_clm.ini';
	$dest_fe	= $backup.DS.'de-DE__fe__com_clm.ini';

	if(JFile::exists($dest_fe)){
	JFile::copy($dest_fe, $src_fe);
	echo "<br><font color='green'>Backup der Frontend Sprachdatei (de-DE) erfolgreich kopiert !</font>";
	} else {
	echo "<br><font color='red'>Backup der Frontend Sprachdatei (de-DE) existiert nicht !</font>";
	}

	$src_fe		= $path_fe.'en-GB'.DS.'.com_clm.ini';
	$dest_fe	= $backup.DS.'en-GB__fe__com_clm.ini';

	if(JFile::exists($dest_fe)){
	JFile::copy($dest_fe, $src_fe);
	echo "<br><font color='green'>Backup der Frontend Sprachdatei (en-GB) erfolgreich kopiert !</font>";
	} else {
	echo "<br><font color='red'>Backup der Frontend Sprachdatei (en-GB) existiert nicht !</font>";
	}

	// CSS Dateien Frontend kopieren
	$path_fe	= JPATH_ROOT.DS.'components'.DS.'com_clm'.DS.'includes'.DS;
	if(JFile::exists($backup.DS.'style.css')){
	JFile::copy($backup.DS.'style.css', $path_fe.'style.css');
	JFile::copy($backup.DS.'clm_content.css', $path_fe.'clm_content.css');
	echo "<br><font color='green'>Backup der Frontend Stylesheetdateien erfolgreich kopiert !</font>";
	} else {
	echo "<br><font color='red'>Backup der Frontend Stylesheetdateien existiert nicht !</font>";
	}

	// PDF Header und Footer Dateien kopieren
	if(JFile::exists($backup.DS.'pdf_header.php')){
	JFile::copy($backup.DS.'pdf_header.php', $path_fe.'pdf_header.php');
	JFile::copy($backup.DS.'pdf_footer.php', $path_fe.'pdf_footer.php');
	echo "<br><font color='green'>Backup der Frontend PDF Styles erfolgreich kopiert !</font>";
	} else {
	echo "<br><font color='red'>Backup der Frontend PDF Styles existiert nicht !</font>";
	}

	// Copyright Hinweis kopieren
	if(JFile::exists($backup.DS.'copy.php')){
	JFile::copy($backup.DS.'copy.php', $path_fe.'copy.php');
	echo "<br><font color='green'>Backup des Frontend Copyright Hinweises erfolgreich kopiert !</font>";
	} else {
	echo "<br><font color='red'>Backup des Frontend Copyright Hinweises existiert nicht !</font>";
	}

	// Dateien Backend kopieren
	$path_be	= JPATH_ROOT.DS.'administrator'.DS.'language'.DS;
	$src_be		= $path_be.'de-DE'.DS.'com_clm.ini';
	$dest_be	= $backup.DS.'de-DE__be__com_clm.ini';

	if(JFile::exists($dest_be)){
	JFile::copy($dest_be, $src_be);
	echo "<br><font color='green'>Backup der Backend Sprachdatei (de-DE) erfolgreich kopiert !</font>";
	} else {
	echo "<br><font color='red'>Backup der Backend Sprachdatei (de-DE) existiert nicht !</font>";
	}

	$src_be		= $path_be.'en-GB'.DS.'com_clm.ini';
	$dest_be	= $backup.DS.'en-GB__be__com_clm.ini';

	if(JFile::exists($dest_be)){
	JFile::copy($dest_be, $src_be);
	echo "<br><font color='green'>Backup der Backend Sprachdatei (en-GB) erfolgreich kopiert !</font>";
	} else {
	echo "<br><font color='red'>Backup der Backend Sprachdatei (en-GB) existiert nicht !</font>";
	}
		} else { echo "<br><font color='red'>Es existiert kein Backup Ordner, daher wurden keine Backups installiert !</font>"; }

	echo "<h3>Installation erfolgreich beendet!</h3><br />";
	
	echo "<br /><br />
			Achtung: Es wurde die Version 1.1.17 oder höher der CLM-Hauptkomponente installiert. Diese enthält die Funktion Mannschaftsturniere. <br/>
			Es ist die Version 1.1.0 oder höher des Darstellungsmoduls nötig. Bitte ggf. anschl. installieren und Parameter setzen. <br/>
			Soll die Funktion Mannschaftsturniere genutzt werden, ist hierfür eine Kopie des neuen Darstellungsmoduls nötig.
			";
			
	echo "<br /><br />
			Achtung: Bei (Einzel-)Turnieren, die mit Versionen <= 1.0.3 erstellt wurden, muß die Tabelle neu berechnet werden!<br/>
			Eine Änderung an der Speicherung der Feinwertungen macht dies unumgänglich.
			Bitte dies manuell über die Teilnehmerliste, dort 'Tabelle erneuern' durchführen.
			";

	echo "<br /><br />
			<b><font color='red'>Achtung</font></b>: Es wurde die Hauptkomponente des ChessLeagueManagers installiert. <br>
			Auf unserer Projekt-Seite www.chessleaguemanager.de unter Schnellstart finden Sie erste Hinweise zum Setup. <br/><br>
			Auch möchten wir auf die nötigen Module zur Darstellung im Frontend aufmerksam machen: <br/>
			- Darstellungsmodul mod_clm zur Darstellung von Ligen und/oder Mannschaftsturniere <br/>
			  (falls beides: den Eintrag in der Modultabelle kopieren) <br/>
			- Login-Modul mod_clm_log, wenn die Ergebnisse durch die Mannschaftsleiter über das Frontend eingegeben werden. <br/>
			  (ein sehr häufiger Ansatz) <br/>
			- Einzelturnier-Modul mod_clm_turmultiple zur Darstellung von Einzelturnieren <br/>
			- Termin-Modul mod_clm_termine zur Darstellung der Spiel- und Veranstaltungstermine im Kalender <br/>
			- Archiv-Modul mod_clm_archiv zur Darstellung der Ligen und Mannschaftsturniere der Vorjahre <br/>
			  (also erst ab zweiter Saison sinnvoll) <br/>
			";
			
	return true;
}
?>