<?PHP
/* 
 * GameQ - Savage protocol (http://gameq.sf.net)
 * Copyright (C) 2004 Tom Buskens (tombuskens@users.sourceforge.net)
 * savageString function in class.aux.php by Dave Mednick
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


/* paste all strings together */
$datastring = '';
for($i=0; $i != count($data); $i++)
{
	$datastring .= $data[$i];
}

/* process data */
$output = $this->aux->savageString($datastring);

/* sort players */
if (isset($output['players']))
{
	/* get lines, remove last (empty) line */
	$lines = preg_split("/\n/", $output['players']);
	$cnt = count($lines)-1;
	unset($lines[$cnt]);

	/* go trough lines */	
	$team_name = 'unknown';
	$team_id = -1;
	$player_cnt = 0;
	
	for ($i=0; $i!=$cnt; $i++)
	{
		/* get team name & number */
		if (preg_match("/^Team (\d) \((.+)\):$/", $lines[$i], $match))
		{
			$team_name = $match[1];
			$team_id = $match[2];
		}
		/* set player */
		elseif ($lines[$i] != '--empty team--')
		{
			$players[] = array(
				'name'    => $lines[$i],
				'team'    => $team_name,
				'team_id' => $team_id
			);
		}
	}
	
	/* set player array & player count */
	if (isset($players))
	{
		$output['players'] = $players;
		$output['num_players'] = count($players);
	}
	/* no players, remove player array & set player count to 0 */
	else
	{
		$output['num_players'] = 0;
		unset($output['players']);
	}
}
?>