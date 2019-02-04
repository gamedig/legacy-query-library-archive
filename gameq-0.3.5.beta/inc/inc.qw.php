<?PHP
/* 
 * GameQ - QuakeWorld protocol (http://gameq.sf.net)
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


$data = $data[0];

/* parse variables */
$strlen = strlen($data);

for ($i=0; $data{$i} != '\\'; $i++);
for ($j=$i; $j<$strlen && $data{$j} != chr(10); $j++);

$output = $this->aux->spyString(substr($data, $i, $j-$i));


/* parse players */
$num_players = 0;

for ($i=$j+1; $i<$strlen && $data{$i} != chr(0); $i++)
{
	$x=$i;
	for($i; $data{$i} != chr(10); $i++);
	
	/* get name, score and ping */
	preg_match("/^(-?\d+) (-?\d+) (-?\d+) (-?\d+) \"(.*)\" \"(.*)\" (-?\d+) (-?\d+)$/", substr($data, $x, $i-$x), $match);
	
	$player['id'] = $match[1];
	$player['score'] = $match[2];
	$player['time'] = $match[3];	
	$player['ping'] = $match[4];
	$player['name'] = $match[5];
	$player['skin'] = $match[6];
	$player['color_top'] = $match[7];
	$player['color_bottom'] = $match[8];
	
	/* put player into main array */
	$output['players'][$num_players++] = $player;
	
}

$output['num_players'] = $num_players;

/* sort players */
$this->aux->sortPlayers($output, 'quake');
?>