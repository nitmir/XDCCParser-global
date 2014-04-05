<?php

/**
 * XDCC Parser
 * |- Refresh Module
 *
 * This software is free software and you are permitted to
 * modify and redistribute it under the terms of the GNU General
 * Public License version 3 as published by the Free Sofware
 * Foundation.
 *
 * @link http://xdccparser.is-fabulo.us/
 * @version 2.0
 * @author Alex 'xshadowfire' Yu <ayu@xshadowfire.net>
 * @author DrX
 * @copyright 2008-2009 Alex Yu and DrX
 */

//@touch( "./cache/last_refresh" ); // kind of race condition, should help on high traffic lists...
$lock = @fopen( "./cache/last_refresh", "a" ); // open the lock, but don't truncate before we even start...

if( flock( $lock, LOCK_EX ) ) {

	ftruncate( $lock, 0 ); // ok _now_ we truncate

	// prepare our environment
	require_once 'fauxdb.class.php';
	ignore_user_abort( TRUE );
	set_time_limit( 300 ); //if it's taking 5 minutes to process, something's seriously wrong.

	$masterdb = new fauxdb( "masterdb" );

	$bots = array();
	$sizes = array('K' => 1.0/1024, 'M' => 1, 'G' => 1024, 'T' => 1048576);
	$match = $masterdb->data['config']['group'] ? ".*".str_replace( array("^",".","*","\\","+","?","\$"), array("\^","\.","\*","\\\\","\+","\?","\\\$"), $masterdb->data['config']['group'] ).".*" : ".*";
	$preg_string = "return preg_match_all(\"/#(\\d+)\\s+\\d+x\\s+\\[\\s*?(\\<?\\d+\\.?\\d?\\D)\\](\\s+\\d{4}-\\d{2}-\\d{2}\\s+\\d{2}:\\d{2})?\\s+(".$match.")\\W/mi\",\$xdccList,\$bot);";

	// fetch the packlists
	foreach($masterdb->data['botconfig'] as $id => $botinfo) {
		if( CURL_ENABLED && (stristr($botinfo['uri'],"http://") || stristr($botinfo['uri'],"ftp://") || stristr($botinfo['uri'],"https://"))) {
			$ch = curl_init();
			curl_setopt_array($ch, array( CURLOPT_URL => $botinfo['uri'], CURLOPT_RETURNTRANSFER => TRUE, CURLOPT_TIMEOUT => 30, CURLOPT_SSL_VERIFYPEER => FALSE ));
			if(!($xdccList = curl_exec($ch))) {
				xp_cache::log("ERROR: Unable to fetch remote file ".$botinfo['uri'], 1);
				xp_cache::log(curl_error($ch), 1);
				continue;
			}
		} else {
			if(!($xdccList = file_get_contents($botinfo['uri']))) {
				xp_cache::log("ERROR: Unable to fetch file ".$botinfo['uri'], 1);
				continue;
			}
		}

		// try xml first
		if( $data = @simplexml_load_string( $xdccList ) ) {
			if( stristr( (string) $data->sysinfo->stats->version, 'iroffer' ) !== FALSE ) {
				$bot = array();
				$i = 0;
				foreach( $data->pack as $pack ) {
					$bot[1][$i] = (string) $pack->packnr;
					$bot[2][$i] = (string) $pack->packsize;
					$bot[3][$i] = (string) $pack->added;
					$bot[4][$i] = (string) $pack->packname;
					$i++;
				}
				$bots[$id] = $bot;
			} else {
				xp_cache::log("ERROR: ".$botinfo['uri']." is not a valid iroffer xml file", 1);
			}
		} else {
			// try parsing as txt
			$xdccList = str_replace( array( chr(2), chr(3), chr(16), chr(31), chr(13) ), "", $xdccList ); //remove irc formatting (or something <_<)
			if(eval($preg_string)) {
				$bots[$id] = $bot;
			} else {
				xp_cache::log("ERROR: Unable to parse ".$botinfo['uri'], 1);
			}
		}
	}

	// compare new list with cached list
	$botdb = xp_cache::get( "botdb" ) or array();
	// final layout:
	// 0 = packnum, 1 = size, 2 = time, 3 = crc, 4 = no crc
	// I'll slip time and gets(?) in there later
	foreach( $bots as $id => $bot ) {
		$numpacks = count( $bot[1] );
		$exists = array();
		$ckey = array_pop( array_keys( $botdb ) );
		// array search is slow as shits, maybe some array intersect/diff hacking... but I need both keys :/
		// optimize for if there are 0 changes...
		for( $i = 0; $i < $numpacks; $i++ ) {
			if( ( $key = @array_search( $bot[4][$i], $botdb[$id][3] ) ) !== FALSE ) {
				$botdb[$id][0][$key] = $bot[1][$i];
				//update gets too;
				$exists[] = $key;
			} else {
				if( $bot[2][$i] == "<1K" && strstr( $bot[4][$i], "." ) === FALSE ) // don't want those group separators
					continue;
				$botdb[$id][0][++$ckey] = $bot[1][$i];
				$botdb[$id][1][$ckey] = round( doubleval( substr( $bot[2][$i], 0, -1 ) ) * doubleval( $sizes[ substr( $bot[2][$i], -1 ) ] ) );
				if($bot[3][$i]) {
					if(!is_numeric($bot[3][$i])) { // parsed from txt
						$timestr = trim($bot[3][$i]);
						$botdb[$id][2][$ckey] = mktime( substr( $timestr, 11, 2 ), substr( $timestr, 14, 2 ), 0, substr( $timestr, 5, 2 ), substr( $timestr, 8, 2 ), substr( $timestr, 0, 4 ) );
					} else { // directly from xml
						$botdb[$id][2][$ckey] = $bot[3][$i];
					}
				} else {
					$botdb[$id][2][$ckey] = time(); // no time specified
				}
				$botdb[$id][3][$ckey] = $bot[4][$i];
				$botdb[$id][4][$ckey] = preg_replace("/(\[|\()[a-f0-9]{8}(\]|\))/i","",$bot[4][$i]);
				$exists[] = $ckey;
			}
		}

		$keys = array_keys( $botdb[$id][0] );
		$notexist = array_diff( $keys, $exists ); // retarded php implementation might cause problems later...check all scenarios
		foreach( $notexist as $pack ) {
			unset( $botdb[$id][0][$pack], $botdb[$id][1][$pack], $botdb[$id][2][$pack], $botdb[$id][3][$pack] );
		}
//		if( count( $botdb[$id][0] ) == 0 )
//			unset( $botdb[$id] );
	}

	xp_cache::set( "botdb", $botdb );

	fwrite( $lock, time() );
	flock( $lock, LOCK_UN );
} else {
	xp_cache::log( "NOTICE: refresh already in progress", 3 );
}

fclose( $lock );

?>
