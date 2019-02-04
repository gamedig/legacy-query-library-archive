<?PHP
/* 
 * GameQ - Painkiller protocol (http://gameq.sf.net)
 * Copyright (C) 2004 Tom Buskens (tombuskens@users.sourceforge.net)
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


/* process data */
$output = $this->aux->spyString(substr($data[0], 5, strlen($data[0])-5), "\x00", 1);

/* remove empty line */
unset($output['']);

/* player string, remove begin of string, not interesting */
$i=6; $zeros=0;
$strlen = strlen($data[1]);
while ($zeros != 6 || $i > $strlen)
{
	if ($data[1][$i++] == "\x00") $zeros++;
}

/* get player count */
$num_players = ord($data[1]{6});
$j=0; 

/* get players */
while ($j != $num_players)
{
	$players[$j]['name']   = $this->aux->HLString($data[1], $i);
	$players[$j]['score']  = $this->aux->HLString($data[1], $i);
	$players[$j]['deaths'] = $this->aux->HLString($data[1], $i);
	$players[$j]['ping']   = $this->aux->HLString($data[1], $i);
	$players[$j]['team']   = $this->aux->HLString($data[1], $i);
	$j++;
}
$output['players'] = $players;

/* sort players by score */
$this->aux->sortPlayers($output, 'quake');
?>
