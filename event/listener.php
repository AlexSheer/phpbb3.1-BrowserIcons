<?php
/**
*
* @package phpBB Extension - Browsers icons in who is online
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
			'core.viewtopic_cache_user_data'			=> 'cache_user_data',
			'core.viewtopic_modify_post_row'			=> 'viewtopic_modify_post_row',
			'core.obtain_users_online_string_sql'		=> 'users_online_string_sql',
			'core.obtain_users_online_string_modify'	=> 'users_online_string',
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

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\auth\auth */
	protected $auth;

	/**
	* Constructor
	*/
	public function __construct(
		$phpbb_root_path,
		\phpbb\template\template $template,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\config\config $config,
		\phpbb\controller\helper $controller_helper,
		$helper,
		\phpbb\user $user,
		\phpbb\auth\auth $auth
	)
	{
		$this->phpbb_root_path = $phpbb_root_path;
		$this->template = $template;
		$this->db = $db;
		$this->config = $config;
		$this->controller_helper = $controller_helper;
		$this->helper = $helper;
		$this->user = $user;
		$this->auth = $auth;
	}

	public function cache_user_data($event)
	{
		$cache_user_data = $event['user_cache_data'];
		$sql_where = ($this->auth->acl_get('u_viewonline')) ? '' : 'AND session_viewonline = 1';
		$sql = 'SELECT session_browser
			FROM ' . SESSIONS_TABLE . '
			WHERE session_user_id = '. $event['poster_id']. '
			' . $sql_where . '
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

			$user_browser = $this->helper->get_user_browser($session_browser);
			$user_system = $this->helper->get_system($session_browser);

			$this->template->assign_vars(array(
				'BROWSER'	=> $user_browser,
				'SYSTEM'	=> $user_system,
			));
		}
	}

	public function users_online_string_sql($event)
	{
		$string_sql = $event['sql'];
		$online_users = $event['online_users']['online_users'];
		$sql = 'SELECT u.username, u.username_clean, u.user_id, u.user_type, u.user_allow_viewonline, u.user_colour, s.session_browser
			FROM ' . USERS_TABLE . ' u JOIN ' . SESSIONS_TABLE . ' s
			ON u.user_id = s.session_user_id
			WHERE ' . $this->db->sql_in_set('u.user_id', $event['online_users']['online_users']) . '
				AND s.session_time >= ' . (time() - ($this->config['load_online_time'] * 60)) . '
			GROUP BY u.user_id
			ORDER BY u.username_clean ASC';
			$event['sql'] = $sql;
	}

	public function users_online_string($event)
	{
		$rowset = $event['rowset'];
		$user_online_link = $event['user_online_link'];
		$online_userlist = $event['online_userlist'];
		$browser = $sys = array();
		foreach($rowset as $key => $value)
		{
			$browser[$value['user_id']] = $this->helper->get_user_browser($value['session_browser']);
			$sys[$value['user_id']] = $this->helper->get_system($value['session_browser']);

		}

		foreach($user_online_link as $key => $value)
		{
			$user_online_link[$key] = $user_online_link[$key] . ' '.$browser[$key]. ' ' . $sys[$key];
		}

		$event['online_userlist'] = $this->user->lang['REGISTERED_USERS'] . ' ' . implode(', ', $user_online_link);
	}
}
