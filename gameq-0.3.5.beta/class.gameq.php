<?PHP
/*
 * GameQ - game server query class (http://gameq.sf.net)
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



define('CFG_FILE',    'class.gameq.cfg.php'); /* path to config file */
define('INC_PATH',    'inc/');                /* path to inc. files */
define('INC_PREFIX',  'inc.');                /* prefix for .inc files */
define('INC_POSTFIX', '.php');                /* postfix for .inc files */
define('SOCK_TIMEOUT', '10');                 /* socket timeout in ms */

require('class.aux.php');




class GameQ
{

	var $cfg_types;      /* contains $cfg_type array from class.gameq.cfg.php */
	var $cfg_strings;    /* containts $cfg_string array from class.gameq.cfg.php */

	var $type_id;        /* server type id (q3, ut etc.) */
	var $svr_id;         /* current server id */
	var $svr_name;       /* server type name (Quake 3 Arena, Unreal Tournament etc.) */
	var $svr_address;    /* server address */
	var $svr_port;       /* server QUERY port (NOT the gameport) */
	var $svr_type;       /* server QUERY type name */
	var $svr_strings;    /* string(s) to send to the server */
	var $svr_timeout;    /* time (ms) to listen to incoming data, the "ping" for the server */
	var $time;           /* average communication time with server */

	var $err_msg;        /* error messages */

	var $aux;            /* class with additional functions */



	/* Load the config file. */
	function GameQ()
	{
		require(CFG_FILE);
		$this->cfg_types   = $cfg_type;
		$this->cfg_strings = $cfg_string;
		$this->aux = new Aux;
	}



	/* Main function. */
	function getInfo($servers, $timeout = 200, $output_type = 'parsed')
	{
		$this->svr_timeout = $timeout;

		if (!is_array($servers)) $this->error('input data is not an array', 0);

		/* process servers */
		while (list($this->svr_id, $server) = each($servers))
		{
			/* get config */
			if (!$this->getConfig($server)) continue;

			/* communicate with server */
			if (($strings = $this->communicate()) !== FALSE)
			{
				/* check what to do with the returned strings */
				switch ($output_type)
				{
					case 'parsed':
						$svr_output = $this->parseData($strings);
						break;

					case 'raw':
						$svr_output['strings'] = $strings;
						break;

					default:
						$this->error('wrong output type specified', 0);
				}
			}
			else
			{
				$svr_output = '';
			}

			/* add some additional info */
			$svr_output = $this->customData($svr_output);

			/* put data into output array */
			$output[$this->svr_id] = $svr_output;
		}
		return $output;
	}




	/* Get configuration data for current server. */
	function getConfig($server)
	{
		/* clear data from previous servers */
		unset($this->svr_port);
		unset($this->svr_strings);

		/* read server data */
		if (!isset($server[0])) $this->error('server type not set', 0);
		if (!isset($server[1])) $this->error('server address not set', 0);

		$this->type_id     = $server[0];
		$this->svr_address = $server[1];

		/* check if type exists */
		if (!isset($this->cfg_types[$this->type_id]))
		{
			$this->error('server type '.$this->type_id.' does not exist in the config file.');
			return FALSE;
		}

		/* get data from config */
		$cfg_data = explode('/', $this->cfg_types[$this->type_id]);
		$this->svr_name = $cfg_data[0];
		$this->svr_type = $cfg_data[2];

		/* set port */
		if (!isset($server[2]))
		{
			$this->svr_port = $cfg_data[1];
		}
		else
		{
			$this->svr_port = $server[2];
		}

		/* get strings to query server */
		if (!isset($cfg_data[3]))
		{
			$this->svr_strings = explode('/', $this->cfg_strings[$this->svr_type]);
		}
		else
		{
			$this->svr_strings = explode('/', $this->cfg_strings[$cfg_data[3]]);
		}

		return TRUE;
	}



	/* Communicates with server, gets query information. */
	function communicate()
	{
		/* open connection to the server */
		if (!($sock = @fsockopen('udp://'.$this->svr_address, $this->svr_port)))
		{
			$this->error('could not connect to server');
			return FALSE;
		}
		socket_set_timeout($sock, 0, 1000*SOCK_TIMEOUT);

		/* send strings to server, receive data */
		$string_cnt = count($this->svr_strings);

		for($i=0; $i!=$string_cnt; $i++)
		{
			/* send string */
			fwrite($sock, $this->svr_strings[$i]);
			
			/* wait for answer */
			$wait = 0;
			while ($wait < $this->svr_timeout)
			{
				$string = fread($sock, 4096);
				if (!empty($string))
				{
					$data[] = $string;
					if (strlen($string) < 4096) break;
				}
				$wait += SOCK_TIMEOUT;
			}
		}
		@fclose($sock);
		
		/* rough ping time */
		$this->ping = $wait;
		
		/* check if any data was returned */
		if (empty($data[0]))
		{
			$this->error('the server didn\'t return any data');
			return FALSE;
		}

		return $data;
	}



	/* Parses data according to gametype. */
	function parseData($data)
	{
		/* include the parse file */
		$parse_file = INC_PATH.INC_PREFIX.$this->svr_type.INC_POSTFIX;
		if (!is_readable($parse_file))
		{
			$this->error('could not read file "'.$parse_file.'".', 0);
		}

		require($parse_file);
		return $output;
	}



	/* Adds some general server info to the output. */
	function customData(&$data)
	{
		$custom['address']    = $this->svr_address;
		$custom['query_port'] = $this->svr_port;
		$custom['id']         = $this->type_id;
		$custom['type']       = $this->svr_type;
		$custom['name']       = $this->svr_name;
		$custom['ping']       = $this->ping;

		$data['custom'] = $custom;
		return $data;
	}



	/* Error function. */
	function error($msg, $type = 1)
	{
		/* set message */
		$this->err_msg[$this->svr_id] = $msg;

		if ($type == 0)
		{
			/* fatal error */
			die('[fatal error]['.$this->svr_id.'] '.$msg);
		}
	}

}
?>