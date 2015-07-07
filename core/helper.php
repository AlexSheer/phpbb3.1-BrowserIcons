<?php
/**
*
* @package phpBB Extension - Browsers icons in who is online
* @copyright (c) 2015 Sheer
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace sheer\browsers_icons\core;

class helper
{
	//** @var string phpbb_root_path */
	protected $phpbb_root_path;

	/**
	* Constructor
	*/
	public function __construct($phpbb_root_path)
	{
		$this->phpbb_root_path = $phpbb_root_path;
	}

	public function get_system($session_browser)
	{
		$systems = array('Amiga', 'BeOS', 'FreeBSD', 'HP-UX', 'Linux', 'NetBSD', 'OS/2', 'SunOS', 'Symbian', 'Unix', 'Windows', 'Sun', 'Macintosh', 'Mac');
		$system = '';
		foreach ($systems as $item)
		{
			if(strpos($session_browser, $item))
			{
				$system = $item;
				break;
			}
		}
		if ($system == 'Linux')
		{
			$systems = array('Android', 'CentOS', 'Debian', 'Fedora', 'Freespire', 'Gentoo', 'Katonix', 'KateOS', 'Knoppix', 'Kubuntu', 'Linspire', 'Mandriva', 'Mandrake', 'RedHat', 'Slackware', 'Slax', 'Suse', 'Xubuntu', 'Ubuntu', 'Xandros', 'Arch', 'Ark');

			foreach ($systems as $item)
			{
				if(strpos($session_browser, $item))
				{
					$system = $item;
					break;
				}
			}
			if ($system == '')
			{
				$system = 'Linux';
			}

			if ($system == 'Mandrake')
			{
				$system = 'Mandriva';
			}
		}
		elseif ($system == 'Windows')
		{
			$version = substr($session_browser, strpos(strtolower($session_browser), 'windows nt ') + 11);
			if (substr($version, 0, 3) == 5.1)
				$system = 'Windows XP';
			elseif (substr($version, 0, 1) == 6)
			{
				if (substr($version, 0, 3) == 6.0)
				{
					$system = 'Windows Vista';
				}
				elseif (substr($version, 0, 3) == 6.1)
				{
					$system = 'Windows 7';
				}
				elseif (substr($version, 0, 3) == 6.2)
				{
					$system = 'Windows 8';
				}
				elseif (substr($version, 0, 3) == 6.3)
				{
					$system = 'Windows 8.1';
				}
			}
			elseif (substr($version, 0, 3) == 10)
			{
				if (substr($version, 0, 3) == 10.0)
				{
					$system = 'Windows 10';
				}
			}
		}
		elseif ($system == 'Mac')
		{
			$system = 'Macintosh';
		}
		if (!$system)
		{
			$system = 'unknown';
		}

		if (substr($system, 0, 11) == 'unknown')
		{
			$sys = 'unknown';
		}

		$sys = str_replace(' ', '', strtolower($system)); // remove spaces
		$sys = preg_replace('/[^a-z0-9_]/', '', $sys); // remove special characters
		$sys = '<img src="' . $this->phpbb_root_path . 'ext/sheer/browsers_icons/styles/all/theme/images/os/' . $sys . '.png" alt="' . $system . '" title="' . $system . '"/>';
		//print "Sys $sys<br />";
		return $sys;
	}

	public function get_user_browser($session_browser)
	{
		if (stristr($session_browser, 'Firefox')) $user_browser = 'Firefox';
		elseif (stristr($session_browser, 'Chrome')) $user_browser = 'Chrome';
		elseif (stristr($session_browser, 'Safari')) $user_browser = 'Safari';
		elseif (stristr($session_browser, 'Opera Mini')) $user_browser = 'Operamini';
		elseif (stristr($session_browser, 'Opera')) $user_browser = 'Opera';
		elseif (stristr($session_browser, 'MSIE 6.0')) $user_browser = 'IE6';
		elseif (stristr($session_browser, 'MSIE 7.0')) $user_browser = 'IE7';
		elseif (stristr($session_browser, 'MSIE 8.0')) $user_browser = 'IE8';
		elseif (stristr($session_browser, 'MSIE 9.0')) $user_browser = 'IE9';
		elseif (stristr($session_browser, 'MSIE 10.0')) $user_browser = 'IE10';
		elseif (stristr($session_browser, 'Trident/7.0; rv:11.0')) $user_browser = 'IE11';
		elseif (stristr($session_browser, 'UCBrowser')) $user_browser = 'Usbrowser';
		else $user_browser = 'Unknown';
		//print "$user_browser<br />";
		return '<img src="' . $this->phpbb_root_path . 'ext/sheer/browsers_icons/styles/all/theme/images/browsers/' . strtolower($user_browser) . '.png" alt="' . $user_browser . '" title="' . $user_browser . '"/>';
	}
}
