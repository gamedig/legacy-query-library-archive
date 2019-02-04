<?PHP
/*
 * GameQ - MTA:VC protocol (http://gameq.sf.net)
 * Copyright (C) 2003 Serge Y. Zhukov (syzo@users.sourceforge.net)
 * adapted by Tom Buskens (tombuskens@users.sourceforge.net)
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

/* cut string into pieces */
$d = $data[0];
$d = preg_replace("/\x01{5}\??|\x01\?/","/////",$d);
$d = explode("/////",$d);

/* get vars from first piece */
$raw = preg_replace("/^EYE1\x04?\x08?(gta3)?mta\\x05|\x01|\x02|\x03|\x04/","/////",$d[0]);
list($null,$output['hostname'],$output['version'],$null,$output['num_players'],$output['max_players']) = explode("/////",$raw);

/* get port, hostname and version */
preg_match("/^(\d{1,5}).(.+)$/", $output['hostname'], $match);
$output['port'] = $match[1];
$output['hostname'] = $match[2];
$output['version'] = ereg_replace("\x0A|\x06"," ",$output['version']);

/* get players */
$players = array_slice($d,1);
$player_cnt = count($players);

for ($i=0; $i<$player_cnt; $i++)
{
	$players[$i] = htmlentities(substr($players[$i],1));
	if (preg_match("/^(.+)\x01\x01(\x02|\x03)(-?\d{1,5}).(-?\d{1,5})/", $players[$i], $match))
	{
		$player['name'] = $match[1];
		$player['score'] = $match[3];
		$player['ping'] = $match[4];
	}
	else
	{
		$player['name'] = 'unknown';
		$player['score'] = 0;
		$player['ping'] = 0;
	}
	$output['players'][$i] = $player;
}

/* sort players */
$this->aux->sortPlayers($output, 'quake');
?>