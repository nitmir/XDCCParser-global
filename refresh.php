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
 * @version 1.2.0
 * @author Alex 'xshadowfire' Yu <ayu@xshadowfire.net>
 * @author DrX
 * @copyright 2008-2009 Alex Yu and DrX
 */

ignore_user_abort( TRUE );
set_time_limit(0);
require_once 'core.php';

$bots = array();
$access = xp_get("botconfig");
$config = xp_get("config");
$sizes = array('K' => 1.0/1024, 'M' => 1, 'G' => 1024, 'T' => 1048576);
foreach($access as $file) {
	if(function_exists("curl_init") && (stristr($file,"http://") || stristr($file,"ftp://") || stristr($file,"https://"))) {
		$ch = curl_init();
		curl_setopt_array($ch, array( CURLOPT_URL => $file, CURLOPT_RETURNTRANSFER => TRUE, CURLOPT_TIMEOUT => 30, CURLOPT_SSL_VERIFYPEER => FALSE ));
		if(!($xdccList = curl_exec($ch))) {
			print("ERROR: Unable to fetch remote file {$file}<br />\n");
			print(curl_error($ch)."<br />\n");
			continue;
		}
	} else {
		if(!($xdccList = file_get_contents($file))) {
			print("ERROR: Unable to fetch file {$file}<br />\n");
			continue;
		}
	}
	$xdccList = str_replace( array( chr(2), chr(3), chr(16), chr(31), chr(13) ), "", $xdccList ); //remove irc formatting (or something <_<)
	if(preg_match("/\s+\*\*\s+To\s+request\s+a\s+file,\s+type\s+\"\/msg\s+(.*?)\s+xdcc\s+send|get\s+#x\"\s+\*\*\s+\W/mi",$xdccList,$data['nick'])) {
		$bot = array();
		$match = $config['group'] ? ".*".str_replace( array("^",".","*","\\","+","?","\$"), array("\^","\.","\*","\\\\","\+","\?","\\\$"), $config['group'] ).".*" : ".*";
		eval("preg_match_all(\"/#(\\d+)\\s+\\d+x\\s+\\[.*?(\\d+\\.?\\d+?)(\\D)\\]\\s+(\\d+\\.\\d+\\.\\d+\\s+\\d+:\\d+\\s+)?(".$match.")\\W/mi\",\$xdccList,\$bot['packs']);");
		$bot['nick'] = $data['nick'][1];
		for($i=0;$i<count($bot['packs'][0]);$i++) {
			$bot['packs'][2][$i] = round($bot['packs']['2'][$i]*doubleval($sizes[$bot['packs'][3][$i]]));
			$bot['packs'][4][$i] = preg_replace("/(.+)(\[|\()[a-f0-9]{8}(\]|\))/i","$1",$bot['packs'][5][$i]);
			// 5 = file with crc, 4 = file without crc
			// time stamp support will be added in 2.0. let's just overwrite it for now...
		}
		//clean up excess variables
		for($i=6;$i<=count($bot['packs'])-1;$i++)
			unset($bot['packs'][$i]);
		unset($bot['packs'][0],$bot['packs'][3]);
		$bots[] = $bot;
	} else {
		print("ERROR: Unable to parse {$file}<br />\n");
	}
}

$time = time();
xp_set("bots",$bots);
xp_set("time",$time);

?>
