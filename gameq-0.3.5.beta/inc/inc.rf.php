<?PHP

/* 
 * GameQ - Red Faction protocol (http://gameq.sf.net)
 * Copyright (C) 2004 KevinPriestley.com (anything@gnhq.com)
 * visit www.ausfaction.com!
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

if (!empty($data[0]))
{
        $d = $data[0];
        
        for ($i=1; $d{$i} != chr(0); $i++);
        $i++;
        
        $output['something']    = ord($d{$i++});
        $output['server_name']  = $this->aux->HLString($d, $i);
        $output['game_type']    = bin2hex($d{$i++});
        $output['num_players'] = ord($d{$i++});
        $output['max_players']  = ord($d{$i++});
        $output['map']          = $this->aux->HLString($d, $i);
        $output['something2']   = bin2hex($d{$i++});
        $output['dedicated']    = ord($d{$i++});
        
        switch($output['game_type'])
        {
        case '00':
        $output['game_type'] = 'Deathmatch';
        break;
        case '01':
        $output['game_type'] = 'Capture The Flag';
        break;
        case '02':
        $output['game_type'] = 'Team Deathmatch';
        break;
        }

        switch($output['dedicated'])
        {
        case '7':
        $output['dedicated'] = 'Dedicated & Password';
        break;
        case '6':
        $output['dedicated'] = 'Password';
        break;
        case '3':
        $output['dedicated'] = 'Yes';
        break;
        case '2':
        $output['dedicated'] = 'No';
        break;
        }
}
?>
