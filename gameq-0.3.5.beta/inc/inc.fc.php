<?PHP

/*
 * GameQ - FarCry protocol (http://gameq.sf.net)
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



/* rules */
if (!empty($data[0]))
{
	/* remove header */
	$d = substr($data[0], 6, strlen($data[0])-6);
	$output = $this->aux->spyString($d, "\x00", 1);
}

/* status */
if (!empty($data[1]))
{
	/* remove header & some junk? */
	$strlen = strlen($data[1])-20;
	$d = substr($data[1], 20, $strlen);
	
	/* get some vars */
	if (preg_match_all("/[^\\x00]+/", $d, $match))
	{
		list(
		  $output['hostname'],
		  $output['mod_type'],
		  $output['game_type'],
		  $output['map']
		) = $match[0];
	}
	
	/* remove the vars from the string */
	$i=1;
	foreach($output AS $var => $value)
	{
		$i += strlen($value)+1;
	}
	
	/* get some more vars */
	if ($strlen - ($i + 5) > 0)
	{
		$output['num_players'] = ord($d{$i++});
		$output['max_players'] = ord($d{$i++});
		$output['password']    = ord($d{$i++});
		$output['unknown0']    = ord($d{$i++});
		$output['unknown1']    = ord($d{$i++});
		$output['punkbuster']  = ord($d{$i});
	}
	
	/* remove color coding from hostname ($<digit>)*/
	$output['hostname'] = preg_replace("/\\$\d/", '', $output['hostname']);
}


/* players */
if (!empty($data[2]))
{
	$strlen = strlen($data[2])-8;
	$d = substr($data[2], 8, $strlen);
	$i=0; $j=0;
	while ($i<$strlen)
	{
		$players[$j]['name'] = $this->aux->HLString($d,$i);
		$players[$j]['team'] = $this->aux->HLString($d,$i);
		$players[$j]['score'] = 256*ord($d{$i++})+ord($d{$i++});
		//$players[$j]['something1'] = 256*ord($d{$i++})+ord($d{$i++});
		$i+=2;
		$players[$j]['ping'] = 256*ord($d{$i++})+ord($d{$i++});
		//$players[$j]['something3'] = 256*ord($d{$i++})+ord($d{$i++});
		//$players[$j]['something4'] = 256*ord($d{$i++})+ord($d{$i++});
		//$players[$j]['something5'] = 256*ord($d{$i++})+ord($d{$i++});$i++;
		$i+=8;		
		$j++;
	}
	if (!isset($players)) $output['num_players'] = 0;
	else
	{
		$output['players'] = $players;
		$this->aux->sortPlayers($output, 'quake');
		$output['num_players'] = $j;
	}
}

?>