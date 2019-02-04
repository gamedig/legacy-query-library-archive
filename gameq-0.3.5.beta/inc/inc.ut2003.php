<?PHP

/*
 * GameQ - UT2003 protocol (http://gameq.sf.net)
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

/* first string, server info */
if (isset($data[0]))
{
	$d = $data[0];
	$i=10;
	
	$output['port'] = $this->aux->unsignedLong($d, $i);
	$i+=4; // skip unknown var
	$output['hostname']    = $this->aux->tribesString($d, $i);
	$output['map']         = $this->aux->tribesString($d, $i);
	$output['game_type']   = $this->aux->tribesString($d, $i);
	$output['num_players'] = $this->aux->unsignedLong($d, $i);
	$output['max_players'] = $this->aux->unsignedLong($d, $i);
	$output['unknown']     = $this->aux->unsignedLong($d, $i);
}

/* second string, misc vars */
if (isset($data[1]))
{
	$d = $data[1];
	$strlen = strlen($d);
	
	$item = '';
	for ($i=5; $d{$i} == chr(0); $i++);
	for ($j=0; $i<$strlen; $j++)
	{
		/* put item into the array */
		if ($d{$i} != chr(0))
		{
			$item[$j] = $this->aux->tribesString($d, $i);
		}
		/* empty item */
		else
		{
			$item[$j] = '';
			for ($i; $i<$strlen && $d{$i} == chr(0); $i++);
		}
	}
	
	/* restore the var => value relation */
	$j=0;
	$cnt = count($item, COUNT_RECURSIVE);
	
	for ($k=1; $k<$cnt; $k+=2)
	{
		if ($item[$k-1] == 'Mutator')
		{
			$output['mutator'.$j++] = $item[$k];
		}
		else
		{
			$output[$item[$k-1]] = $item[$k];
		}
	}
}

/* third string, players (not bots */
if (isset($data[2]) && strlen($data[2]) > 10)
{
	$d = $data[2];
	$strlen = strlen($d);
	$i=5;
	
	/* get players */
	for ($j=0; $i<$strlen; $j++)
	{
		$player['id']      = $this->aux->unsignedLong($d, $i);
		$player['name']    = $this->aux->tribesString($d, $i);
		$player['ping']    = $this->aux->unsignedLong($d, $i);
		$player['score']   = $this->aux->unsignedLong($d, $i);
		$player['stat_id'] = $this->aux->unsignedLong($d, $i);
		$output['players'][$j] = $player;
	}
	
	/* sort players */
	$this->aux->sortPlayers($output, 'quake');
}
?>