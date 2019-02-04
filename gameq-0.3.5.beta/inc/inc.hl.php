<?PHP

/* 
 * GameQ - HalfLife protocol (http://gameq.sf.net)
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


/* first string */
if (!empty($data[0]))
{
	$d = $data[0];
	
	/* skip stuff at beginning */
	for ($i=0; $d{$i} != chr(0); $i++);
	$i++;
	
	/* parse variables */
	$output['hostname']     = $this->aux->HLString($d, $i);
	$output['map']          = $this->aux->HLString($d, $i);
	$output['game_dir']     = $this->aux->HLString($d, $i);
	$output['game_type']    = $this->aux->HLString($d, $i);
	$output['num_players']  = ord($d{$i++});
	$output['max_players']  = ord($d{$i++});
	$output['net_protocol'] = ord($d{$i++});
	$output['dedicated']    = $d{$i++};
	$output['server_os']    = $d{$i++};
	$output['needpass']     = ord($d{$i++});
	$output['mod']          = ord($d{$i++});
	
	/* parse mod variables */
	if ($output['mod'])
	{
		$output['http_info']     = $this->aux->HLString($d, $i);
		$output['http_download'] = $this->aux->HLString($d, $i);
		$i+=8;
		$output['mod_ssonly']    = ord($d{$i++});
		$output['mod_customdll'] = ord($d{$i++});
	}
}


/* second string (players) */
if (!empty($data[1]))
{
	$d = $data[1];
	$strlen = strlen($d);
	
	/* skip stuff at beginning */
	for($i=0; $i<$strlen && $d{$i} != chr(255) && $d{$i} != chr(244); $i++);
	$i+=5;
		
	if ($i<$strlen)
	{
		$player_cnt = ord($d{$i++});

		
		/* get players */
		for ($player_no=0; $player_no<$player_cnt; $player_no++)
		{
			$i++;
			
			/* get name */
			$name = '';
			for ($i; $d{$i} != chr(0); $i++)
			{
				$name .= $d{$i};
			}
			$i++;
			
			/* get other variables */
			$score = unpack("L", substr($d, $i, $i+4)); $i+=4;
			$time  = unpack("f", substr($d, $i, $i+4)); $i+=4;
			$time  = mktime(0, 0, $time[1]);
			$time  = date("H:i:s", $time);
			
			$player['name']  = htmlentities($name);
			$player['score'] = $score[1];
			$player['time']  = $time;
			
			$output['players'][$player_no] = $player;
		}
		
		$this->aux->sortPlayers($output, 'quake');
	}
}


/* third string (rules) */
$d = '';
$pattern = "/(?s).ÿÿÿ.+ÿÿÿÿ/";
$length1 = 16;
$length2 = 9;

/* Rules string can consist of multiple packets.
 * These don't always arive in the proper order, so
 * they're sorted and pasted together.
 */
if (!empty($data[2]) && !empty($data[3]))
{
	if (preg_match($pattern, $data[2]))
	{
		$d = substr($data[2], $length1, strlen($data[2])-$length1).
		     substr($data[3], $length2, strlen($data[3])-$length2);		
	}
	elseif (preg_match($pattern, $data[3]))
	{
		$d = substr($data[3], $length1, strlen($data[3])-$length1).
		     substr($data[2], $length2, strlen($data[2])-$length2);
	}
} 
elseif (!empty($data[2]))
{
	$d = substr($data[2], $length2, strlen($data[2])-$length2);
}

/* cut the string into pieces, put it into the array */
if (!empty($d))
{
	$pieces = explode("\x00", $d);
	$cnt_pieces = count($pieces);
	
	for ($i=1; $i<$cnt_pieces; $i+=2)
	{
		$output[$pieces[$i-1]] = $pieces[$i];
	}
}
?>