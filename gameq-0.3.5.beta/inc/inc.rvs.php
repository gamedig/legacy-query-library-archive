<?php

/* 
 * GameQ - RavenShield protocol (http://gameq.sf.net)
 * Copyright (C) 2003 chicago (Chicago@thescreamingeagle.com/http://thescreamingeagle.com)
 * Adapted by Pyroman[FO] (http://www.gamerswithjobs.com/)
 * Improved by Ian Cazabat (PHPDev@IanCaz.com)
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA.
 *
 */


$d = $data[0];

/* set vars to defaults */
$vars = array(
  'rv' => 'serverbeaconport',
  'P1' => 'beaconport',
  'E1' => 'cmap',
  'I1' => 'name',
  'F1' => 'ctype',
  'A1' => 'maxp',
  'G1' => 'locked',
  'H1' => 'ded',
  'L1' => 'plist',
  'M1' => 'ptime',
  'N1' => 'pping',
  'O1' => 'pkills',
  'B1' => 'numplay',
  'Q1' => 'rounds',
  'R1' => 'rtime',
  'S1' => 'btime',
  'T1' => 'bomb',
  'W1' => 'snames',
  'X1' => 'iserver',
  'Y1' => 'ffire',
  'Z1' => 'balteam',
  'A2' => 'tk',
  'D2' => 'ver',
  'B2' => 'radar',
  'E2' => 'lid',
  'F2' => 'gid',
  'G2' => 'bport',
  'H2' => 'numter',
  'I2' => 'aiback',
  'J2' => 'rmap',
  'K2' => 'fpw',
  'K1' => 'maps',
  'J1' => 'gtype'  
);
$cnt = count($vars);
foreach($vars AS $var => $val) ${$val} = ' ';

/* split string */
$string = split('¶', $d);
$size = sizeof($string);

if ($size != 0)
{
	for ($i=0; $i != sizeof($string); $i++)
	{
		$substr = substr($string[$i], 0, 2);
		if (isset($vars[$substr])) ${$vars[$substr]} = substr($string[$i], 2, strlen($string[$i])-2);
	}
}

/* move vars to output */
$patterns = array ('/14/', '/13/', '/8/', '/15/', '/16/', '/3/', '/5/', '/7/');
$replace = array ('Team Survival', 'Survival', 'Hostage', 'Bomb', 'Pilot', 'Mission', 'Terrorist Hunt', 'Hostage Rescue');

$output['hostname']           = $name;
$output['map']                = $cmap;
$output['max_players']        = $maxp;
$output['num_players']        = $numplay;
$output['version']            = $ver;
$output['num_rounds']         = $rounds;
$output['radar']              = $radar;
$output['tk_penalty']         = $tk;
$output['between_round_time'] = $btime;
$output['team_autobalance']   = $balteam;
$output['friendly_fire']      = $ffire;
$output['num_terrorists']     = $numter;
$output['bomb_time']          = $bomb;
$output['locked']             = $locked;
$output['force_fpw']          = $fpw;
$output['dedicated']          = $ded;
$output['map_time']           = date('i:s',  mktime(0,0,trim($rtime)));
$output['game_type']          = str_replace($patterns, $replace, $ctype);

/* split some stuff */
$player_array = explode('/', substr($plist, 2, strlen($plist)-2));
$time_array =   explode('/', substr($ptime, 2, strlen($ptime)-2));
$ping_array =   explode('/', substr($pping, 2, strlen($pping)-2));
$kill_array =   explode('/', substr($pkills, 2, strlen($pkills)-2));

/* get players */
$size = sizeof($player_array);
if ($size == 1) $size = 0; /* remove empty substring */
for ($i=0; $i!=$size; $i++)
{
	$player['name'] = $player_array[$i];

	if ($i<=sizeof($time_array))  $player['time'] = $time_array[$i];
	else $player['time'] = '';
	
	if ($i<=sizeof($ping_array)) $player['ping'] = $ping_array[$i];
	else $player['ping'] = '';
	
	if ($i<=sizeof($kill_array)) $player['score'] = $kill_array[$i];
	else $player['score'] = '';
	
	$output['players'][$i] = $player;
}

/* sort players */
$this->aux->sortPlayers($output, 'quake');

/* maps */
$maplist = split('/', $maps);
$gametype = split('/', $gtype);

$gametype = preg_replace($patterns, $replace, $gametype);
array_shift($gametype);
array_shift($maplist);
$count = count($maplist);

for ($i=0; $i!=$count; $i++)
{
	$map['name'] = $maplist[$i];
	if ($i<count($gametype)) $map['game_type'] = $gametype[$i];
	else $map['game_type'] = 'Not Available';

	$output['map_list'][$i] = $map;	
}
?>

