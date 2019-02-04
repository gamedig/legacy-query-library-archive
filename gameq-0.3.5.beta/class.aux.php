<?PHP
/* 
 * GameQ - misc functions (http://gameq.sf.net)
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
 */

class Aux
{
	function spyString($string, $del = '\\', $start = 2)
	{
		/* cut string into pieces according to delimiter */
		$pieces = explode($del, $string);
		$cnt = count($pieces, COUNT_RECURSIVE);
		for($i=$start; $i<$cnt; $i+=2)
		{
			$result[$pieces[$i-1]] = $pieces[$i];
		}
		return $result;
	}



	function savageString($string)
	{
		/* cut string into pieces */
		$pieces = explode('ÿ', $string);
		$cnt = count($pieces, COUNT_RECURSIVE);
		for($i=1; $i<$cnt; $i++)
		{
			$smpieces = explode('ş',$pieces[$i]);
			$result[$smpieces[0]] = $smpieces[1];
		}
		return $result;
	}



	function HLString($string, &$i)
	{
		$begin = $i;
		$strlen = strlen($string);
		for ($i; ($i < $strlen) && ($string{$i} != chr(0)); $i++);
		$result = substr($string, $begin, $i-$begin);
		$i++;
		return $result;
	}



	function tribesString($string, &$i, $count_index = TRUE)
	{
		$strlen = ord($string{$i});
		if ($count_index) $result = substr($string, ++$i, $strlen-1);
		else $result = substr($string, ++$i, $strlen);
		$i+=$strlen;
		return $result;
	}
	

	/* Unreal 2 XMP strings sometimes have color coding.
	 * See http://unreal.student.utwente.nl/UT2003-queryspec.html for more details.
	 */
	function unreal2String($string, &$i, $count_index = TRUE)
	{
		if (substr($string, $i+1, 4) == "\x5e\x00\x23\x00")
		{
			
			/* color coded string */
			$strlen = ord($string{$i})-128;
			$strlen*=2;
		}
		else
		{
			/* normal (Tribes)string */
			$strlen = ord($string{$i});
		}
		if ($count_index) $result = substr($string, ++$i, $strlen-1);
		else $result = substr($string, ++$i, $strlen);
		$i+=$strlen;
		return $result;

	}


	function ghostReconString($string, &$i)
	{
		$substr = substr($string, $i, 4);
		if (strlen($substr) < 4) return 0;
		$length = current(unpack("V", $substr));
		$i+=4;
		$j=0;
		while ($j < $length && $string{$i+$j} != chr(0)) $j++; // check for first "\x00" in the string
		$result = substr($string, $i, min($j, $length-1));
		$i+=$length;
		return $result;
	}



	function unsignedLong($string, &$i)
	{
		$substr = substr($string, $i, 4);
		if (strlen($substr) < 4) return 0;
		$result = current(unpack("V", $substr));
		$i+=4;
		return $result;
	}



	function parseBitFlag($flag, $data)
	{
		$bit = 1;
		foreach($data AS $elt)
		{
			if ($flag & $bit) $output[$elt] = 1;
			else $output[$elt] = 0;
			$bit *= 2;
		}
		return $output;
	}
	
	
	/* Sorts players by score, puts player data for ALL gametypes
	 * into $data['players'][$i], clears any other
	 * player data.
	 * This breaks compatibility with versions < 0.2.5
	 */
	function sortPlayers(&$data, $type = 'spy')
	{
		/* possible variables to sort players by */
		$sortvars = array('score', 'frags', 'kills', 'honor');
		$cnt = count($sortvars);

		switch($type){
				
			/* gamespy style players */
			case 'spy':
				
				/* put all data with key <name>_<postfix> into an array
				 * $player[<postfix>][<name>]
				 */
				foreach($data AS $key => $val)
				{
					if (preg_match("/^(.+)_(\d\d?)$/", $key, $match) && $match[1] != 'teamname') // fix for bf1942
					{
						$players_u[$match[2]][$match[1]] = $data[$key];
						unset($data[$key]);
					}
				}
				
				/* check if a sortvar can be found */
				for($i=0; $i!=$cnt; $i++)
				{
					if (isset($players_u[0][$sortvars[$i]]))
					{
						$sortvar = $sortvars[$i];
						break;
					}
				}
				
				/* if no sortvar is found, return players unsorted */
				if (!isset($sortvar))
				{
					if (isset($players_u)) $data['players'] = $players_u;
					return TRUE;
				}
	
				/* re-index players so they can be sorted more easily */
				foreach($players_u AS $key => $val)
				{
					$players[] = $players_u[$key];
				}
				break;
			
			
			/* quake style players */
			case 'quake':
				/* check if a sortvar can be found */
				for($i=0; $i!=$cnt; $i++)
				{
					if (isset($data['players'][0][$sortvars[$i]]))
					{
						$sortvar = $sortvars[$i];
						break;
					}
				}
				
				/* if no sortvar is found, return players unsorted */
				if (!isset($sortvar)) return TRUE;
				$players = $data['players'];
				break;
		}
		
		/* sort */
		$cnt = count($players);
		for($i=0; $i != $cnt; $i++){
			$a = $i;
			$b = $cnt-1;
			while ($a != $b){
				if ($players[$a][$sortvar] > $players[$b][$sortvar]) $b--;
				else $a++;
			}
			$h = $players[$i];
			$players[$i] = $players[$a];
			$players[$a] = $h;
		}
				
		/* put playerdata back into the array */
		$data['players'] = $players;
	}


}
?>