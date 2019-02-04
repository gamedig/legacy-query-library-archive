<?PHP

/* 
 * GameQ - Freelancer protocol (http://gameq.sf.net)
 * Copyright (C) 2004 Tom Buskens (tombuskens@users.sourceforge.net)
 * Based on script by Sam Evans (sam@neuroflux.com)
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

$d = $data[0];

$start = 91;
$playing = ord(substr($d, 24, 24))-1;
$maximum = ord(substr($d, 20, 20))-1;
$server_name_length = ord($d{3}) - 92;
$end = ($server_name_length + $start + 2);



$server_name = substr($d, $start, ($server_name_length + 2));
$server_name = preg_replace('/\0/', '', trim($server_name));

$output['servername'] = $server_name;
$output['num_players'] = $playing;
$output['max_players'] = $maximum;
?>