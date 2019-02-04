<?PHP

/* 
 * GameQ - Ghost Recon protocol (http://gameq.sf.net)
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


/*
 * based on the qstat ghost recon protocol by Bob Marriott
 * this is pretty undocumented, so a lot of unknown vars are simply skipped
 * compatibility may break if the game is updated
*/



$d = $data[0];

/* some stuff at the beginning, skip it */
$str_header   = "\xc0\xde\xf1\x11";
$str_pktstart = "\x42";
$str_tag      = "\xf4\x03\x14\x04\x00\x41\x04\x00\x41\x00\x00\x78\x30\x63";
$junk = $str_header.$str_pktstart.$str_tag;

$i = strlen($junk) + 6;


/* variables */
$output['name']        = $this->aux->ghostReconString($d, $i);
$output['map']         = $this->aux->ghostReconString($d, $i);
$output['mission']     = $this->aux->ghostReconString($d, $i);
$output['game_type']   = $this->aux->ghostReconString($d, $i);
$output['password']    = ord($d{$i}); $i+=2;
$output['max_players'] = $this->aux->unsignedLong($d, $i);
$output['num_players'] = $this->aux->unsignedLong($d, $i);


/* player names */
for ($j=0; $j != $output['num_players']; $j++)
{
	$output['players'][$j]['name'] = $this->aux->ghostReconString($d, $i);
}
$i+=17;
$x = $i;


/* more vars */
$output['version']     = $this->aux->ghostReconString($d, $i);
$output['mods']        = $this->aux->ghostReconString($d, $i);
$output['dedicated']   = ord($d{$i++});
$output['time_played'] = $this->aux->unsignedLong($d, $i);

switch (ord($d{$i}))
{
	case 3:  $type = 'joining'; break;
	case 4:  $type = 'playing'; break;
	case 5:  $type = 'debriefing'; break;
	default: $type = 'undefined'; break;
}
$output['status'] = $type; $i+=4;

switch (ord($d{$i}))
{
	case 2:  $type = 'coop'; break;
	case 3:  $type = 'solo'; break;
	case 4:  $type = 'team'; break;
	default: $type = 'unknown'; break;
}
$output['gamemode'] = $type; $i+=4;
	$output['motd'] = $this->aux->ghostReconString($d, $i);

switch (ord($d{$i}))
{
	case 0:  $type = 'none'; break;
	case 1:  $type = 'individual'; break;
	case 2:  $type = 'team'; break;
	case 3:  $type = 'infinite'; break;
	default: $type = 'unknown'; break;
}
$output['spawn_type'] = $type; $i+=4;

$output['total_time']   = $this->aux->unsignedLong($d, $i);
$output['num_players2'] = $this->aux->unsignedLong($d, $i);


/* player data */
$min_players = min($output['num_players'], $output['num_players2']); // in case of mismatch
for ($j=0; $j != $min_players; $j++)
{
	$i++;
	$output['players'][$j]['death'] = ord($d{$i++});
	switch (ord($d{$i})){
		case 1:  $type = 'red'; break;
		case 2:  $type = 'blue'; break;
		case 3:  $type = 'yellow'; break;
		case 4:  $type = 'green'; break;
		default: $type = 'unknown'; break;
	}
	$output['players'][$j]['team'] = $type;
	$i+=3;	
}

	
/* team data, don't know what's in here */
$i+=(5*8); $i++;

	
/* yet more vars */
$output['usestarttimer'] = ord($d{$i++});
$output['starttimeset']  = $this->aux->unsignedLong($d, $i);
$output['debrieftime']   = $this->aux->unsignedLong($d, $i);
$output['respawnmin']    = $this->aux->unsignedLong($d, $i);
$output['respawnmax']    = $this->aux->unsignedLong($d, $i);
$output['respawnsafe']   = $this->aux->unsignedLong($d, $i);
$i+=4;
$output['num_spawns'] = ord($d{$i++});
$i+=4;
$output['observers'] = ord($d{$i++});
$i+=3;
$output['startwait'] = $this->aux->unsignedLong($d, $i);

switch(ord($d{$i++}))
{
	case 0:  $type = 'none'; break;
	case 1:  $type = 'reticule'; break;
	case 2:  $type = 'names'; break;
	default: $type = 'unknown'; break;
}
$output['iff'] = $type;

$output['threat_indicator'] = ord($d{$i++});
$i+=5;
$output['restrictions'] = $this->aux->ghostReconString($d, $i);
?>