<?PHP

/*
 * GameQ - Tribes protocol (http://gameq.sf.net)
 * Copyright (C) 2003 Tom Buskens (tombuskens@users.sourceforge.net)
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
$output['game']      = $this->aux->tribesString($d, $i);
$output['version']   = $this->aux->tribesString($d, $i);
$output['hostname']  = $this->aux->tribesString($d, $i);

/* more vars */
$vars = array('dedicated', 'needpass', 'num_players', 'max_players', 'cpu_lsb', 'cpu_msb');
$var_cnt = count($vars, COUNT_RECURSIVE)+$i;
for($i; $i<$var_cnt; $i++) $output[$vars[$var_cnt-$i]] = ord($d{$i});

/* you guessed it */
$output['mod']       = $this->aux->tribesString($d, $i);
$output['game_type'] = $this->aux->tribesString($d, $i);
$output['map']       = $this->aux->tribesString($d, $i);
$output['motd']      = $this->aux->tribesString($d, $i);
$output['game_type'] = $this->aux->tribesString($d, $i);

/* no playercount yet */
?>