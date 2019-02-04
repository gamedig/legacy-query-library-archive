<?PHP

/* 
 * GameQ - Hexen II protocol (http://gameq.sf.net)
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


/* parse variables */
$d = $data[0];

for ($i=5   ; $d{$i} != chr(0); $i++);
for ($j=++$i; $d{$j} != chr(0); $j++);
$output['hostname'] = substr($d, $i, $j-$i);
for ($i=++$j; $d{$i} != chr(0); $i++);
$output['map']         = substr($d, $j, $i-$j);
$output['num_players'] = ord($d{++$i});
$output['max_players'] = ord($d{++$i});
$output['something']   = ord($d{++$i});			// don't know what this is...
?>