<?PHP

/*
 * GameQ - Tribes 2 protocol (http://gameq.sf.net)
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

$i=0;
/* first string */
if (isset($data[0]) && $data[0]{$i} == chr(16))
{
	$d = $data[0];
	$i=6;

	/* vars */
	$output['query_version'] = $this->aux->tribesString($d, $i, FALSE);
	$output['protocol']      = ord($d{$i}); $i+=4;
	$output['min_protocol']  = ord($d{$i}); $i+=4;
	$output['build']         = ord($d{$i}) + ord($d{$i+1})*256; $i+=4;
	$output['hostname']      = $this->aux->tribesString($d, $i, FALSE);
}

/* second string */
if (isset($data[1]) && $data[1]{$i} == chr(20))
{

	$d = $data[1];
	$strlen = strlen($d);
	$i=6;

	/* some vars */
	$output['mod']       = $this->aux->tribesString($d, $i, FALSE);
	$output['game_type'] = $this->aux->tribesString($d, $i, FALSE);
	$output['map']       = $this->aux->tribesString($d, $i, FALSE);

	/* parse the bitflag */
	$bitflag = ord($d{$i++});
	$flags = array('dedicated', 'password', 'linux', 'tournament', 'no_alias');
	$output += $this->aux->parseBitFlag($bitflag, $flags);

	/* more vars */
	$output['num_players'] = ord($d{$i++});
	$output['max_players'] = ord($d{$i++});
	$output['num_bots']    = ord($d{$i++});
	$output['cpu']         = ord($d{$i++}) + ord($d{$i++})*256;
	$output['info']        = $this->aux->tribesString($d, $i, FALSE);
	$i+=2;

	/* teams */
	$output['num_teams']  = $d{$i++};
	for ($j=0; $j<$output['num_teams']; $j++)
	{
		$i++;
		$begin = $i;
		for ($i; $d{$i} != chr(10); $i++);

		preg_match("/^(.+)\x09(\d+)$/", substr($d, $begin, $i-$begin), $match);
		$output['team'][$match[1]]['score'] = $match[2];

	}


	/* players */
	if ($output['num_players'] > 0)
	{

		for ($i++; $d{$i} != chr(10); $i++);  /* second playercount, skip it */
		$i++;

		/* go through all players */
		for ($j=0; $j<$output['num_players']; $j++){

			$begin = $i;
			while($i<$strlen && $d{$i} != chr(10)) $i++;

			/* two name formats:
			 * #11:tag:#8:name:#17:#9:team:#9:score
			 * #8:name:#11:tag or nothing:#17:#9:team:#9:score
			 */

			preg_match("/^(.)((\x08(.+)\x0B(.*))|(\x0B(.+)\x08(.+)))\x11\x09(.+)\x09(.+)$/", substr($d, $begin, $i-$begin), $match);
			if (!empty($match[4]))
			{
				$player['name'] = $match[4];
				$player['full_name'] = $player['name'];
				if (!empty($match[5])){
					$player['tag'] = $match[5];
					$player['full_name'] .= $player['tag'];
				}
			}
			else
			{
				$player['name'] = $match[8];
				$player['tag']  = $match[7];
				$player['full_name'] = $player['tag'].$player['name'];
			}
			$player['team'] = $match[9];
			$player['score'] = $match[10];

			$output['players'][$j] = $player;
			unset($player);
			$i++;

		}
	}
	/* sort players */
	$this->aux->sortPlayers($output, 'quake');
}