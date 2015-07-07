<?php
/**
*
* @package phpBB Extension - Browsers icons in miniprofile
* @copyright (c) 2015 Sheer
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace sheer\browsers_icons\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class listener implements EventSubscriberInterface
{
/**
* Assign functions defined in this class to event listeners in the core
*
* @return array
* @static
* @access public
*/
	static public function getSubscribedEvents()
	{
		return array(
			'core.viewtopic_cache_user_data'	=> 'cache_user_data',
			'core.viewtopic_modify_post_row'	=> 'viewtopic_modify_post_row',
		);
	}

	/** @var \phpbb	emplate	emplate */
	protected $template;

	//** @var string phpbb_root_path */
	protected $phpbb_root_path;

	/** @var \phpbb\db\driver\driver_interface $db */
	protected $db;

	/** @var \phpbb\config\config $config */
	protected $config;

	/**
	* Constructor
	*/
	public function __construct(
		$phpbb_root_path,
		\phpbb\template\template $template,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\config\config $config
	)
	{
		$this->phpbb_root_path = $phpbb_root_path;
		$this->template = $template;
		$this->db = $db;
		$this->config = $config;
	}

	public function cache_user_data($event)
	{
		$cache_user_data = $event['user_cache_data'];
		$sql = 'SELECT session_browser
			FROM ' . SESSIONS_TABLE . '
			WHERE session_user_id = '. $event['poster_id']. '
			AND session_time >= '. (time() - ($this->config['load_online_time'] * 60)) .'';
		$result = $this->db->sql_query($sql);
		if ($cache_user_data['session_browser'] = $this->db->sql_fetchfield('session_browser'))
		{
			$event['user_cache_data'] = $cache_user_data;
		}
	}

	public function viewtopic_modify_post_row($event)
	{
		if(isset($event['user_poster_data']['session_browser']))
		{
			$session_browser = $event['user_poster_data']['session_browser'];

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

			$this->template->assign_vars(array(
				'BROWSER'	=> '<img src="' . $this->phpbb_root_path . 'ext/sheer/browsers_icons/styles/all/theme/images/browsers/' . strtolower($user_browser) . '.png" alt="' . $user_browser . '" title="' . $user_browser . '"/>',
				'SYSTEM'	=> '<img src="' . $this->phpbb_root_path . 'ext/sheer/browsers_icons/styles/all/theme/images/os/' . $sys . '.png" alt="' . $system . '" title="' . $system . '"/>',
			));
		}
	}
}
