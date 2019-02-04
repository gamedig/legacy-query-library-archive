<?PHP

/*
 * GameQ - Unreal 2 XMP protocol (http://gameq.sf.net)
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


/* first string, first part, server info */
if (isset($data[0]))
{
	$d = $data[0];
	$i=10;
	$output['port'] = $this->aux->unsignedLong($d, $i);
	$i+=4;
	$output['hostname']    = $this->aux->tribesString($d, $i);
	$output['map']         = $this->aux->tribesString($d, $i);
	$output['game_type']   = $this->aux->tribesString($d, $i);
	$output['num_players'] = $this->aux->unsignedLong($d, $i);
	$output['max_players'] = $this->aux->unsignedLong($d, $i);
	$output['bla']         = $this->aux->unsignedLong($d, $i);

}

/* second string, game info */
if (isset($data[1]))
{
	$d = $data[1];
	$strlen = strlen($d);

	$item = '';
	for ($i=5; $d{$i} == chr(0); $i++);
	for ($j=0; $i<$strlen; $j++){
		if ($d{$i} != chr(0)) $item[$j] = $this->aux->tribesString($d, $i);
		else {
			$item[$j] = '';
			$i++;
		}
	}

	/* get mutators */
	$j=0;
	$cnt = count($item, COUNT_RECURSIVE);
	for($k=1; $k<$cnt; $k+=2){
		if ($item[$k-1] == 'Mutator')
		{
			$output['mutator'.$j] = $item[$k];
			$j++;
		}
		else $output[$item[$k-1]] = $item[$k];
	}
}


/* third string, players */
if (isset($data[2]))
{
	$d = $data[2];
	$strlen = strlen($d);

	$i=5;
	for ($j=0; $i<$strlen; $j++)
	{
		/* some player vars */
		$player['unknown0'] = $this->aux->unsignedLong($d, $i);
		$player['unknown1'] = $this->aux->unsignedLong($d, $i);
		$player['name']     = $this->aux->unreal2String($d, $i);
		$player['ping']     = $this->aux->unsignedLong($d, $i);
		$player['score']    = $this->aux->unsignedLong($d, $i);
		$player['unknown2'] = $this->aux->unsignedLong($d, $i);
		$player['var_count'] = ord($d{$i++});


		/* some other vars */
		$item = '';
		for ($k=0; $k<6; $k++){
			if ($d{$i} == chr(0))
			{
				$item[$k] = '';
				$i++;
			}
			else
			{
				$item[$k] = $this->aux->unreal2String($d, $i);
				for($i; $i<$strlen && $d{$i} == ord(0); $i++);
			}
		}

		/* put items into the main array */
		if (isset($item[1])) $player['team'] = str_replace("\x00", "", substr($item[1], 6, strlen($item[1]))); // remove color coding for now
		if (isset($item[3])) $player['class'] = $item[3];
		if (isset($item[5])) $player['artifact'] = $item[5];

		$output['players'][$j] = $player;
	}
	/* sort players */
	$this->aux->sortPlayers($output, 'quake');
}
?>