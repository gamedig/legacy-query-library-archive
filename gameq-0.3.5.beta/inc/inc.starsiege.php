<?PHP

/*
 * GameQ - Starsiege protocol (http://gameq.sf.net)
 * Copyright (C) 2004 RA Butterbean (http://redarmageddon.net)
 * Modified by Tom Buskens (tombuskens@users.sourceforge.net)
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

/* some vars */
$i=4;
$output['g_name']      	= $this->aux->tribesString($d, $i, FALSE);
$output['g_version']   	= $this->aux->tribesString($d, $i, FALSE);
$output['server_name'] 	= $this->aux->tribesString($d, $i, FALSE);
$output['password']  	= ord($d{$i++});
$output['dedicated']    = ord($d{$i++});
$output['drop_in_prog'] = ord($d{$i++});
$output['game_in_prog'] = ord($d{$i++});
$output['num_players'] 	= ord($d{$i}); $i+=4;
$output['max_players']  = ord($d{$i}); $i+=4;
$output['team_play']    = ord($d{$i++});
$output['mission']      = $this->aux->tribesString($d, $i, FALSE);
$output['cpu_speed']	= ord($d{$i++}) + ord($d{$i++})*256;
$output['factory_veh']  = ord($d{$i++});
$output['allow_tecmix'] = ord($d{$i++});
$output['spawn_limit']  = ord($d{$i}); $i+=4;
$output['frag_limit'] 	= ord($d{$i}); $i+=4;
$output['time_limit']   = ord($d{$i}); $i+=4;
$output['tech_limit'] 	= ord($d{$i}); $i+=4;
$output['combat_limit'] = ord($d{$i}) + ord($d{$i+1})*256; $i+=4;
$output['mass_limit']   = ord($d{$i}) + ord($d{$i+1})*256; $i+=4;
$output['players_sent'] = ord($d{$i}); $i+=4;

/* get players */
$strlen = strlen($d);
$j=0;
$team = array('01' => 'yellow', '02' => 'blue', '04' => 'red', '08' => 'purple');

while ($i < $strlen)
{
	$output['players'][$j]['name'] = $this->aux->tribesString($d, $i, FALSE);
	$output['players'][$j++]['team'] = $team[bin2hex($d{$i++})];
}
?>
