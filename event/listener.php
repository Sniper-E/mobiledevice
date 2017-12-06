<?php
/**
* @package phpBB Extension - Mobile Device
* @copyright (c) 2015 Sniper_E - http://www.sniper-e.com
* @copyright (c) 2015 dmzx - http://www.dmzx-web.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*/

namespace sniper\mobiledevice\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface
{
	/** @var \phpbb\request\request */
	protected $request;
	
	/** @var \phpbb\user */
	protected $user;
	
	/** @var \phpbb\template\template */
	protected $template;
	
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;
	
	/** @var \phpbb\config\config */
	protected $config;
	
	/** @var \phpbb\auth\auth */
	protected $auth;
	
	/** @var \phpbb\controller\helper */
	protected $helper;
	
	/** @var string database tables */
	protected $mobilelogs_table;
	
	/** @var \phpbb\files_factory */
	protected $files_factory;

	/**
	* Constructor
	* @param \phpbb\request\request            $request
	* @param \phpbb\user                       $user
	* @param \phpbb\template\template          $template
	* @param \phpbb\db\driver\driver_interface $db
	* @param \phpbb\config\config              $config
	* @param \phpbb\auth\auth                  $auth
	* @param \phpbb\controller\helper          $helper
	* @param \phpbb\user                       $user
	* @param string                            $mobilelogs_table
	* @param \phpbb\files_factory              $files_factory
	*/
	public function __construct(
		\phpbb\request\request $request, 
		\phpbb\user $user, 
		\phpbb\template\template $template, 
		\phpbb\db\driver\driver_interface $db, 
		\phpbb\config\config $config, 
		\phpbb\auth\auth $auth, 
		\phpbb\controller\helper $helper,
		$mobilelogs_table, 
		\phpbb\files\factory $files_factory = null
	)
	{
		$this->request          = $request;
		$this->user             = $user;
		$this->template         = $template;
		$this->db               = $db;
		$this->config           = $config;
		$this->auth             = $auth;
		$this->helper           = $helper;
		$this->mobilelogs_table = $mobilelogs_table;
		$this->files_factory    = $files_factory;
	}

	static public function getSubscribedEvents()
	{
		return array(
			'core.permissions'                 => 'permissions',
			'core.user_setup'                  => 'load_language_on_setup',
			'core.ucp_prefs_view_data'         => 'ucp_prefs_get_data',
			'core.ucp_prefs_view_update_data'  => 'ucp_prefs_set_data',
			'core.memberlist_view_profile'     => 'memberlist_view_profile',
			'core.viewtopic_post_rowset_data'  => 'viewtopic_post_rowset_data',
			'core.viewtopic_modify_post_row'   => 'viewtopic_modify_post_row',
			'core.viewtopic_modify_post_row'   => 'viewtopic_modify_post_row',
			'core.submit_post_modify_sql_data' => 'submit_post_modify_sql_data',
			'core.user_setup_after'            => 'user_setup_after',
			'core.session_kill_after'          => 'session_kill_after',
			'core.page_header'                 => 'page_header',
		);
	}

	public function permissions($event)
	{
		$permissions = $event['permissions'];
		$permissions['u_mobile_logs_view'] = array('lang' => 'ACL_U_MOBILE_LOGS_VIEW', 'cat' => 'misc');
		$permissions['u_mobile_logs_clear'] = array('lang' => 'ACL_U_MOBILE_LOGS_CLEAR', 'cat' => 'misc');
		$event['permissions'] = $permissions;
	}

	public function load_language_on_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
			'ext_name' => 'sniper/mobiledevice',
			'lang_set' => 'common',
		);
		$event['lang_set_ext'] = $lang_set_ext;
	}

	public function ucp_prefs_get_data($event)
	{
		$event['data'] = array_merge($event['data'], array(
			'mobilewelcome' => $this->request->variable('mobilewelcome', (int) $this->user->data['user_mobile_welcome']),
			'mobileheader'  => $this->request->variable('mobileheader', (int) $this->user->data['user_mobile_header']),
			'mobileself'    => $this->request->variable('mobileself', (int) $this->user->data['user_mobile_self']),
		));

		if (!$event['submit'])
		{
			$this->template->assign_vars(array(
				'S_UCP_MOBILEWELCOME' => $event['data']['mobilewelcome'],
				'S_UCP_MOBILEHEADER'  => $event['data']['mobileheader'],
				'S_UCP_MOBILESELF'    => $event['data']['mobileself'],
			));
		}
	}

	public function ucp_prefs_set_data($event)
	{
		$event['sql_ary'] = array_merge($event['sql_ary'], array(
			'user_mobile_welcome' => $event['data']['mobilewelcome'],
			'user_mobile_header'  => $event['data']['mobileheader'],
			'user_mobile_self'    => $event['data']['mobileself'],
		));
	}

	public function memberlist_view_profile($event)
	{
		$member = $event['member'];

		$this->template->assign_vars(array(
			'S_IS_MOBILE_PROFILE'   => ($member['mobile_browser']),
			'S_DEVICE_NAME_PROFILE' => ($member['device_name']),
		));
	}

	public function viewtopic_post_rowset_data($event)
	{
		$rowset_data = $event['rowset_data'];
		$row = $event['row'];

		$rowset_data = array_merge($rowset_data, array(
			'device_name'       => $row['device_name'],
			'post_device_title' => $row['post_device_title'],
		));
		$event['rowset_data'] = $rowset_data;
	}

	public function viewtopic_modify_post_row($event)
	{
		$row = $event['row'];
		$post_row = $event['post_row'];
		$post_row = array_merge($post_row, array(
			'DEVICE_TITLE'      => $row['device_name'],
			'POST_DEVICE_TITLE' => $row['post_device_title'],
		));
		$event['post_row'] = $post_row;
	}

	public function submit_post_modify_sql_data($event)
	{
		$this->user->data['is_mobile'] = $this->mobile_device_detect();
		$status = $this->mobile_device_detect();

		if ($this->user->data['is_mobile'] && ($event['post_mode'] == 'post' || $event['post_mode'] == 'reply'))
		{
			$sql_data = $event['sql_data'];
			$sql_data[POSTS_TABLE]['sql']['post_device_title'] = $status[1];
			$event['sql_data'] = $sql_data;
		}
	}

	public function user_setup_after($event)
	{
		$this->user->data['is_mobile'] = $this->mobile_device_detect();
		$status = $this->mobile_device_detect();
		$this->cookie_data['mobile_name'] = $status[1];
		$cookie_mobile_name = $this->request->variable($this->config['cookie_name'] . '_mobile_name', '', true, \phpbb\request\request_interface::COOKIE);

		if (!$cookie_mobile_name)
		{
			$this->user->set_cookie('mobile_name', $status[1], time() + 5 * 24 * 60 * 60, '/', false, false);
		}

		if ($this->user->data['is_mobile'])
		{
			$mobile_browser = $this->request->variable('mobile_browser', 1);
			$device_name = $this->request->variable('device_name', $status[1]);

			$sql = 'UPDATE ' . USERS_TABLE . '
				SET mobile_browser = "' . $this->db->sql_escape($mobile_browser) . '"
				WHERE user_id = ' . (int) $this->user->data['user_id'];
			$this->db->sql_query($sql);
	
			$sql = 'UPDATE ' . USERS_TABLE . '
				SET device_name = "' . $this->db->sql_escape($device_name) . '"
				WHERE user_id = ' . (int) $this->user->data['user_id'];
			$this->db->sql_query($sql);
		}
		else
		{
			$mobile_browser	= $this->request->variable('mobile_browser', 0);
			$device_name = $this->request->variable('device_name', '');

			$sql = 'UPDATE ' . USERS_TABLE . '
				SET mobile_browser = "' . $this->db->sql_escape($mobile_browser) . '"
				WHERE user_id = ' . (int) $this->user->data['user_id'];
			$this->db->sql_query($sql);
	
			$sql = 'UPDATE ' . USERS_TABLE . '
				SET device_name = "' . $this->db->sql_escape($device_name) . '"
				WHERE user_id = ' . (int) $this->user->data['user_id'];
			$this->db->sql_query($sql);
		}
	}

	public function session_kill_after($event)
	{
		$mobile_browser	= $this->request->variable('mobile_browser', 0);
		$device_name = $this->request->variable('device_name', '');

		$sql = 'UPDATE ' . USERS_TABLE . '
			SET mobile_browser = "' . $mobile_browser . '"
			WHERE user_id = ' . (int) $this->user->data['user_id'];
		$this->db->sql_query($sql);
	
		$sql = 'UPDATE ' . USERS_TABLE . '
			SET device_name = "' . $device_name . '"
			WHERE user_id = ' . (int) $this->user->data['user_id'];
		$this->db->sql_query($sql);
	}

	public function page_header($event)
	{
		if ($this->config['mobile_logs_enable'])
		{
			$sql = 'SELECT COUNT(log_ip) AS mobilelogs_counter
				FROM ' . $this->mobilelogs_table . '
				WHERE ' . $this->db->sql_in_set('log_ip', $this->user->ip);
			$result = $this->db->sql_query($sql);
			$mobilelogs_counter = (int) $this->db->sql_fetchfield('mobilelogs_counter');
			$this->db->sql_freeresult($result);
			
			$status = $this->mobile_device_detect();
			$user_agent = $this->request->server('HTTP_USER_AGENT');
			$user_name = $this->user->data['username'];
			$device_name = $this->request->variable('device_name', $status[1]);
		
			if ($mobilelogs_counter == 0 && $device_name != '')
			{
				$sql_ary = array(
					'user_agent'  => (string) $user_agent,
					'log_ip'      => $this->user->ip,
					'user_name'   => (string) $user_name,
					'device_name' => (string) $device_name,
					'log_time'    => (string) time(),
				);
			
				$this->db->sql_query('INSERT INTO ' . $this->mobilelogs_table . ' ' . $this->db->sql_build_array('INSERT', $sql_ary));
			}
			elseif ($this->user->data['is_mobile'])
			{
				$sql_ary = array(
					'user_agent'  => (string) $user_agent,
					'user_name'   => (string) $user_name,
					'device_name' => (string) $device_name,
					'log_time'    => (string) time(),
				);
				$sql = 'UPDATE ' . $this->mobilelogs_table . ' SET ' . $this->db->sql_build_array('UPDATE', $sql_ary) . '
					WHERE ' . $this->db->sql_in_set('log_ip', $this->user->ip);
				$this->db->sql_query($sql);
			}
		}
		
		$this->template->assign_vars(array(
			'S_IS_MOBILE'                 => $this->user->data['mobile_browser'],
			'S_DEVICE_NAME'               => $this->user->data['device_name'],
			'S_MOBILE_NAME'               => $this->cookie_data['mobile_name'],
			'S_MOBILE_WELCOME'            => $this->user->data['user_mobile_welcome'],
			'S_MOBILE_HEADER'             => $this->user->data['user_mobile_header'],
			'S_MOBILE_SELF'               => $this->user->data['user_mobile_self'],
			'U_MOBILE_LOGS'               => $this->helper->route('sniper_mobiledevice_controller', array('mode' => 'logs')),
			'U_MOBILE_VIEW_LOGS'          => $this->auth->acl_get('u_mobile_logs_view') ? true : false,
			'U_MOBILE_CLEAR_LOGS'         => $this->auth->acl_get('u_mobile_logs_clear') ? true : false,
			'MOBILE_ENABLE'               => $this->config['mobile_enable'],
			'MOBILE_WELCOME_ENABLE'       => $this->config['mobile_welcome_enable'],
			'MOBILE_WELCOME_GUEST_ENABLE' => $this->config['mobile_welcome_guest_enable'],
			'MOBILE_HEADER_ENABLE'        => $this->config['mobile_header_enable'],
			'MOBILE_PROFILE_ENABLE'       => $this->config['mobile_profile_enable'],
			'MOBILE_LOGS_ENABLE'          => $this->config['mobile_logs_enable'],
			'MOBILEDEVICE_VERSION'        => $this->config['mobiledevice_version'],
			'PHPBB_IS_32'                 => ($this->files_factory !== null) ? true : false,
		));
	}

	private function mobile_device_detect($iphone = true, $ipod = true, $ipad = true, $android = true, $opera = true, $blackberry = true, $palm = true, $windows = true, $lg = true)
	{
		$mobile_browser	= false;
		$user_agent	 = $this->request->server('HTTP_USER_AGENT');

		switch (true)
		{
			/** Make desktop is-mobile for testing 
			case (preg_match('/x86_64|WOW64|Win64|Iceweasel/i',$user_agent));
				$status = $this->user->lang['DESKTOP'];
				$mobile_browser = true;
			break;*/
			/** Remove bots from the logs */
			case (preg_match('/Bot|Google|Crawler|ICjobs|NewsBlogs|Media|Picsearch|Sitesearch|Linkcheck|BingPreview|Java|Jigsaw|httpget/i',$user_agent));
				$mobile_browser = false;
			break;
			/** Start detect mobile list */
			case (preg_match('/ipad/i',$user_agent));
				$status = $this->user->lang['IPAD'];
				$mobile_browser = $ipad;
			break;
			case (preg_match('/ipod/i',$user_agent));
				$status = $this->user->lang['IPOD'];
				$mobile_browser = $ipod;
			break;
			case (preg_match('/iphone/i',$user_agent));
				$status = $this->user->lang['IPHONE'];
				$mobile_browser = $iphone;
			break;
			case (preg_match('/android/i',$user_agent));
				if (preg_match('/SM-G870A/i',$user_agent))
				{
					$status = $this->user->lang['SGS5A'];
				}
				else if (preg_match('/SM-G900A|SM-G900F|SM-G900H|SM-G900M|SM-G900P|SM-G900R4|SM-G900T|SM-G900V|SM-G900W8|SM-G800F/i',$user_agent))
				{
					$status = $this->user->lang['SGS5'];
				}
				else if (preg_match('/SM-G920F/i',$user_agent))
				{
					$status = $this->user->lang['SGS6'];
				}
				else if (preg_match('/SGH-I497/i',$user_agent))
				{
					$status = $this->user->lang['SG2T'];
				}
				else if (preg_match('/GT-P5210|SM-T110|SM-T310/i',$user_agent))
				{
					$status = $this->user->lang['SGT3'];
				}
				else if (preg_match('/SM-T210/i',$user_agent))
				{
					$status = $this->user->lang['SGT3W'];
				}
				else if (preg_match('/SM-T335|SM-T530/i',$user_agent))
				{
					$status = $this->user->lang['SGT4'];
				}
				else if (preg_match('/SM-T520/i',$user_agent))
				{
					$status = $this->user->lang['SGTP'];
				}
				else if (preg_match('/SGH-I537/i',$user_agent))
				{
					$status = $this->user->lang['SGS4A'];
				}
				else if (preg_match('/GT-I9505|GT-I9500|SPH-L720T/i',$user_agent))
				{
					$status = $this->user->lang['SGS4'];
				}
				else if (preg_match('/GT-I9100P/i',$user_agent))
				{
					$status = $this->user->lang['SGS2'];
				}
				else if (preg_match('/SM-N9005|SM-P600/i',$user_agent))
				{
					$status = $this->user->lang['SGN3'];
				}
				else if (preg_match('/SM-N7505/i',$user_agent))
				{
					$status = $this->user->lang['SGN3N'];
				}
				else if (preg_match('/SM-N910C|SM-N910F/i',$user_agent))
				{
					$status = $this->user->lang['SGN4'];
				}
				else if (preg_match('/SM-N920P/i',$user_agent))
				{
					$status = $this->user->lang['SGN5'];
				}
				else if (preg_match('/SM-G357FZ/i',$user_agent))
				{
					$status = $this->user->lang['SGA4'];
				}
				else if (preg_match('/SM-G925P/i',$user_agent))
				{
					$status = $this->user->lang['SGS6E'];
				}
				else if (preg_match('/SM-G935F/i',$user_agent))
				{
					$status = $this->user->lang['SGS7E'];
				}
				else if (preg_match('/GT-S7582/i',$user_agent))
				{
					$status = $this->user->lang['SGSD2'];
				}
				else if (preg_match('/GT-I9100P/i',$user_agent))
				{
					$status = $this->user->lang['SGS2'];
				}
				else if (preg_match('/HONORPLK-L01/i',$user_agent))
				{
					$status = $this->user->lang['HPL01'];
				}
				else if (preg_match('/EVA-L09/i',$user_agent))
				{
					$status = $this->user->lang['HPL09'];
				}
				else if (preg_match('/IMM76B/i',$user_agent))
				{
					$status = $this->user->lang['SGN'];
				}
				else if (preg_match('/TF101/i',$user_agent))
				{
					$status = $this->user->lang['ATT'];
				}
				else if (preg_match('/Archos 40b/i',$user_agent))
				{
					$status = $this->user->lang['A4TS'];
				}
				else if (preg_match('/A0001/i',$user_agent))
				{
					$status = $this->user->lang['OPO'];
				}
				else if (preg_match('/Orange Nura/i',$user_agent))
				{
					$status = $this->user->lang['ON'];
				}
				else if (preg_match('/XT1030/i',$user_agent))
				{
					$status = $this->user->lang['MDM'];
				}
				else if (preg_match('/TIANYU-KTOUCH/i',$user_agent))
				{
					$status = $this->user->lang['TKT'];
				}
				else if (preg_match('/D2005|D2105/i',$user_agent))
				{
					$status = $this->user->lang['SXED'];
				}
				else if (preg_match('/C2005|D2303/i',$user_agent))
				{
					$status = $this->user->lang['SXM2'];
				}
				else if (preg_match('/C6906/i',$user_agent))
				{
					$status = $this->user->lang['SXZ1'];
				}
				else if (preg_match('/D5803/i',$user_agent))
				{
					$status = $this->user->lang['SXZ3'];
				}
				else if (preg_match('/P710/i',$user_agent))
				{
					$status = $this->user->lang['LGOL7IT'];
				}
				else if (preg_match('/LG-H850/i',$user_agent))
				{
					$status = $this->user->lang['LGH850'];
				}
				else if (preg_match('/LG-V500/i',$user_agent))
				{
					$status = $this->user->lang['LGV500'];
				}
				else if (preg_match('/lg/i',$user_agent))
				{
					$status = $this->user->lang['LG'];
				}
				else if (preg_match('/ASUS_T00J/i',$user_agent))
				{
					$status = $this->user->lang['ATOOJ'];
				}
				else if (preg_match('/Aquaris E5/i',$user_agent))
				{
					$status = $this->user->lang['AE5HD'];
				}
				else if (preg_match('/HTC Desire|626s/i',$user_agent))
				{
					$status = $this->user->lang['HTCD'];
				}
				else if (preg_match('/Nexus One/i',$user_agent))
				{
					$status = $this->user->lang['N1'];
				}
				else if (preg_match('/Nexus 4|LRX22C|LVY48F/i',$user_agent))
				{
					$status = $this->user->lang['N4'];
				}
				else if (preg_match('/Nexus 5|LMY48S/i',$user_agent))
				{
					$status = $this->user->lang['N5'];
				}
				else if (preg_match('/Nexus 7|KTU84P/i',$user_agent))
				{
					$status = $this->user->lang['N7'];
				}
				else if (preg_match('/Nexus 9|LMY47X/i',$user_agent))
				{
					$status = $this->user->lang['N9'];
				}
				else if (preg_match('/Lenovo_K50_T5/i',$user_agent))
				{
					$status = $this->user->lang['LK50T5'];
				}
				else
				{
					$status = $this->user->lang['ANDROID'];
				}
				$mobile_browser = $android;
			break;
			case (preg_match('/opera mini/i',$user_agent));
				$status = $this->user->lang['MOBILE_DEVICE'];
				$mobile_browser = $opera;
			break;
			case (preg_match('/blackberry/i',$user_agent));
				if (preg_match('/BlackBerry9900|BlackBerry9930|BlackBerry9790|BlackBerry9780|BlackBerry9700|BlackBerry9650|BlackBerry9000|/i',$user_agent))
				{
					$status = 'BlackBerry Bold';
				}
				else if (preg_match('/BlackBerry9380|BlackBerry9370|BlackBerry9360|BlackBerry9350|BlackBerry9330|BlackBerry9320|BlackBerry9300|BlackBerry9220|BlackBerry8980|BlackBerry8900|BlackBerry8530|BlackBerry8520|BlackBerry8330|BlackBerry8320|BlackBerry8310|BlackBerry8300/i',$user_agent))
				{
					$status = $this->user->lang['BBCURVE'];
				}
				else if (preg_match('/BlackBerry9860|BlackBerry9850|BlackBerry9810|BlackBerry9800/i',$user_agent))
				{
					$status = $this->user->lang['BBTORCH'];
				}
				else if (preg_match('/BlackBerry9900/i',$user_agent))
				{
					$status = $this->user->lang['BBTOUCH'];
				}
				else if (preg_match('/BlackBerry9105/i',$user_agent))
				{
					$status = $this->user->lang['BBPEARL'];
				}
				else if (preg_match('/BlackBerry8220/i',$user_agent))
				{
					$status = $this->user->lang['BBPEARLF'];
				}
				else if (preg_match('/BlackBerry Storm|BlackBerry Storm2/i',$user_agent))
				{
					$status = $this->user->lang['BBSTORM'];
				}
				else if (preg_match('/BlackBerry Passport/i',$user_agent))
				{
					$status = $this->user->lang['BBPP'];
				}
				else if (preg_match('/BlackBerry Porsche/i',$user_agent))
				{
					$status = $this->user->lang['BBP'];
				}
				else if (preg_match('/BlackBerry PlayBook/i',$user_agent))
				{
					$status = $this->user->lang['BBPB'];
				}
				else
				{
				$status = $this->user->lang['BLACKBERRY'];
				}
				$mobile_browser = $blackberry;
			break;
			case (preg_match('/(pre\/|palm os|palm|hiptop|avantgo|plucker|xiino|blazer|elaine)/i',$user_agent));
				$status = $this->user->lang['PALM'];
				$mobile_browser = $palm;
			break;
			case (preg_match('/(iris|3g_t|windows ce|windows Phone|opera mobi|windows ce; smartphone;|windows ce; iemobile)/i',$user_agent));
				if (preg_match('/Lumia 640 XL/i',$user_agent))
				{
					$status = $this->user->lang['L640XL'];
				}
				else
				{
				$status = $this->user->lang['WSP'];
				}
				$mobile_browser = $windows;
			break;
			case (preg_match('/lge vx10000/i',$user_agent));
				$status = $this->user->lang['VOYAGER'];
				$mobile_browser = $windows;
			break;
			case (preg_match('/(mini 9.5|vx1000|lge |m800|e860|u940|ux840|compal|wireless| mobi|ahong|lg380|lgku|lgu900|lg210|lg47|lg920|lg840|lg370|sam-r|mg50|s55|g83|t66|vx400|mk99|d615|d763|el370|sl900|mp500|samu3|samu4|vx10|xda_|samu5|samu6|samu7|samu9|a615|b832|m881|s920|n210|s700|c-810|_h797|mob-x|sk16d|848b|mowser|s580|r800|471x|v120|rim8|c500foma:|160x|x160|480x|x640|t503|w839|i250|sprint|w398samr810|m5252|c7100|mt126|x225|s5330|s820|htil-g1|fly v71|s302|-x113|novarra|k610i|-three|8325rc|8352rc|sanyo|vx54|c888|nx250|n120|mtk |c5588|s710|t880|c5005|i;458x|p404i|s210|c5100|teleca|s940|c500|s590|foma|samsu|vx8|vx9|a1000|_mms|myx|a700|gu1100|bc831|e300|ems100|me701|me702m-three|sd588|s800|8325rc|ac831|mw200|brew |d88|htc\/|htc_touch|355x|m50|km100|d736|p-9521|telco|sl74|ktouch|m4u\/|me702|8325rc|kddi|phone|lg |sonyericsson|samsung|240x|x320|vx10|nokia|sony cmd|motorola|up.browser|up.link|mmp|symbian|smartphone|midp|wap|vodafone|o2|pocket|kindle|mobile|psp|treo)/i',$user_agent));
				$status = $this->user->lang['MOBILE_DEVICE'];
				$mobile_browser = true;
			break;
			case (isset($post['HTTP_X_WAP_PROFILE'])||isset($post['HTTP_PROFILE']));
				$status = $this->user->lang['MOBILE_DEVICE'];
				$mobile_browser = true;
			break;
			case (in_array(strtolower(substr($user_agent,0,4)),array('1207'=>'1207','3gso'=>'3gso','4thp'=>'4thp','501i'=>'501i','502i'=>'502i','503i'=>'503i','504i'=>'504i','505i'=>'505i','506i'=>'506i','6310'=>'6310','6590'=>'6590','770s'=>'770s','802s'=>'802s','a wa'=>'a wa','acer'=>'acer','acs-'=>'acs-','airn'=>'airn','alav'=>'alav','asus'=>'asus','attw'=>'attw','au-m'=>'au-m','aur '=>'aur ','aus '=>'aus ','abac'=>'abac','acoo'=>'acoo','aiko'=>'aiko','alco'=>'alco','alca'=>'alca','amoi'=>'amoi','anex'=>'anex','anny'=>'anny','anyw'=>'anyw','aptu'=>'aptu','arch'=>'arch','argo'=>'argo','bell'=>'bell','bird'=>'bird','bw-n'=>'bw-n','bw-u'=>'bw-u','beck'=>'beck','benq'=>'benq','bilb'=>'bilb','blac'=>'blac','c55/'=>'c55/','cdm-'=>'cdm-','chtm'=>'chtm','capi'=>'capi','cond'=>'cond','craw'=>'craw','dall'=>'dall','dbte'=>'dbte','dc-s'=>'dc-s','dica'=>'dica','ds-d'=>'ds-d','ds12'=>'ds12','dait'=>'dait','devi'=>'devi','dmob'=>'dmob','doco'=>'doco','dopo'=>'dopo','el49'=>'el49','erk0'=>'erk0','esl8'=>'esl8','ez40'=>'ez40','ez60'=>'ez60','ez70'=>'ez70','ezos'=>'ezos','ezze'=>'ezze','elai'=>'elai','emul'=>'emul','eric'=>'eric','ezwa'=>'ezwa','fake'=>'fake','fly-'=>'fly-','fly_'=>'fly_','g-mo'=>'g-mo','g1 u'=>'g1 u','g560'=>'g560','gf-5'=>'gf-5','grun'=>'grun','gene'=>'gene','go.w'=>'go.w','good'=>'good','grad'=>'grad','hcit'=>'hcit','hd-m'=>'hd-m','hd-p'=>'hd-p','hd-t'=>'hd-t','hei-'=>'hei-','hp i'=>'hp i','hpip'=>'hpip','hs-c'=>'hs-c','htc '=>'htc ','htc-'=>'htc-','htca'=>'htca','htcg'=>'htcg','htcp'=>'htcp','htcs'=>'htcs','htct'=>'htct','htc_'=>'htc_','haie'=>'haie','hita'=>'hita','huaw'=>'huaw','hutc'=>'hutc','i-20'=>'i-20','i-go'=>'i-go','i-ma'=>'i-ma','i230'=>'i230','iac'=>'iac','iac-'=>'iac-','iac/'=>'iac/','ig01'=>'ig01','im1k'=>'im1k','inno'=>'inno','iris'=>'iris','jata'=>'jata','java'=>'java','kddi'=>'kddi','kgt'=>'kgt','kgt/'=>'kgt/','kpt '=>'kpt ','kwc-'=>'kwc-','klon'=>'klon','lexi'=>'lexi','lg g'=>'lg g','lg-a'=>'lg-a','lg-b'=>'lg-b','lg-c'=>'lg-c','lg-d'=>'lg-d','lg-f'=>'lg-f','lg-g'=>'lg-g','lg-k'=>'lg-k','lg-l'=>'lg-l','lg-m'=>'lg-m','lg-o'=>'lg-o','lg-p'=>'lg-p','lg-s'=>'lg-s','lg-t'=>'lg-t','lg-u'=>'lg-u','lg-w'=>'lg-w','lg/k'=>'lg/k','lg/l'=>'lg/l','lg/u'=>'lg/u','lg50'=>'lg50','lg54'=>'lg54','lge-'=>'lge-','lge/'=>'lge/','lynx'=>'lynx','leno'=>'leno','m1-w'=>'m1-w','m3ga'=>'m3ga','m50/'=>'m50/','maui'=>'maui','mc01'=>'mc01','mc21'=>'mc21','mcca'=>'mcca','medi'=>'medi','meri'=>'meri','mio8'=>'mio8','mioa'=>'mioa','mo01'=>'mo01','mo02'=>'mo02','mode'=>'mode','modo'=>'modo','mot '=>'mot ','mot-'=>'mot-','mt50'=>'mt50','mtp1'=>'mtp1','mtv '=>'mtv ','mate'=>'mate','maxo'=>'maxo','merc'=>'merc','mits'=>'mits','mobi'=>'mobi','motv'=>'motv','mozz'=>'mozz','n100'=>'n100','n101'=>'n101','n102'=>'n102','n202'=>'n202','n203'=>'n203','n300'=>'n300','n302'=>'n302','n500'=>'n500','n502'=>'n502','n505'=>'n505','n700'=>'n700','n701'=>'n701','n710'=>'n710','nec-'=>'nec-','nem-'=>'nem-','newg'=>'newg','neon'=>'neon','netf'=>'netf','noki'=>'noki','nzph'=>'nzph','o2 x'=>'o2 x','o2-x'=>'o2-x','opwv'=>'opwv','owg1'=>'owg1','opti'=>'opti','oran'=>'oran','p800'=>'p800','pand'=>'pand','pg-1'=>'pg-1','pg-2'=>'pg-2','pg-3'=>'pg-3','pg-6'=>'pg-6','pg-8'=>'pg-8','pg-c'=>'pg-c','pg13'=>'pg13','phil'=>'phil','pn-2'=>'pn-2','pt-g'=>'pt-g','palm'=>'palm','pana'=>'pana','pire'=>'pire','pock'=>'pock','pose'=>'pose','psio'=>'psio','qa-a'=>'qa-a','qc-2'=>'qc-2','qc-3'=>'qc-3','qc-5'=>'qc-5','qc-7'=>'qc-7','qc07'=>'qc07','qc12'=>'qc12','qc21'=>'qc21','qc32'=>'qc32','qc60'=>'qc60','qci-'=>'qci-','qwap'=>'qwap','qtek'=>'qtek','r380'=>'r380','r600'=>'r600','raks'=>'raks','rim9'=>'rim9','rove'=>'rove','s55/'=>'s55/','sage'=>'sage','sams'=>'sams','sc01'=>'sc01','sch-'=>'sch-','scp-'=>'scp-','sdk/'=>'sdk/','se47'=>'se47','sec-'=>'sec-','sec0'=>'sec0','sec1'=>'sec1','semc'=>'semc','sgh-'=>'sgh-','shar'=>'shar','sie-'=>'sie-','sk-0'=>'sk-0','sl45'=>'sl45','slid'=>'slid','smb3'=>'smb3','smt5'=>'smt5','sp01'=>'sp01','sph-'=>'sph-','spv '=>'spv ','spv-'=>'spv-','sy01'=>'sy01','samm'=>'samm','sany'=>'sany','sava'=>'sava','scoo'=>'scoo','send'=>'send','siem'=>'siem','smar'=>'smar','smit'=>'smit','soft'=>'soft','sony'=>'sony','t-mo'=>'t-mo','t218'=>'t218','t250'=>'t250','t600'=>'t600','t610'=>'t610','t618'=>'t618','tcl-'=>'tcl-','tdg-'=>'tdg-','telm'=>'telm','tim-'=>'tim-','ts70'=>'ts70','tsm-'=>'tsm-','tsm3'=>'tsm3','tsm5'=>'tsm5','tx-9'=>'tx-9','tagt'=>'tagt','talk'=>'talk','teli'=>'teli','topl'=>'topl','hiba'=>'hiba','up.b'=>'up.b','upg1'=>'upg1','utst'=>'utst','v400'=>'v400','v750'=>'v750','veri'=>'veri','vk-v'=>'vk-v','vk40'=>'vk40','vk50'=>'vk50','vk52'=>'vk52','vk53'=>'vk53','vm40'=>'vm40','vx98'=>'vx98','virg'=>'virg','vite'=>'vite','voda'=>'voda','vulc'=>'vulc','w3c '=>'w3c ','w3c-'=>'w3c-','wapj'=>'wapj','wapp'=>'wapp','wapu'=>'wapu','wapm'=>'wapm','wig '=>'wig ','wapi'=>'wapi','wapr'=>'wapr','wapv'=>'wapv','wapy'=>'wapy','wapa'=>'wapa','waps'=>'waps','wapt'=>'wapt','winc'=>'winc','winw'=>'winw','wonu'=>'wonu','x700'=>'x700','xda2'=>'xda2','xdag'=>'xdag','yas-'=>'yas-','your'=>'your','zte-'=>'zte-','zeto'=>'zeto','acs-'=>'acs-','alav'=>'alav','alca'=>'alca','amoi'=>'amoi','aste'=>'aste','audi'=>'audi','avan'=>'avan','benq'=>'benq','bird'=>'bird','blac'=>'blac','blaz'=>'blaz','brew'=>'brew','brvw'=>'brvw','bumb'=>'bumb','ccwa'=>'ccwa','cell'=>'cell','cldc'=>'cldc','cmd-'=>'cmd-','dang'=>'dang','doco'=>'doco','eml2'=>'eml2','eric'=>'eric','fetc'=>'fetc','hipt'=>'hipt','http'=>'http','ibro'=>'ibro','idea'=>'idea','ikom'=>'ikom','inno'=>'inno','ipaq'=>'ipaq','jbro'=>'jbro','jemu'=>'jemu','java'=>'java','jigs'=>'jigs','kddi'=>'kddi','keji'=>'keji','kyoc'=>'kyoc','kyok'=>'kyok','leno'=>'leno','lg-c'=>'lg-c','lg-d'=>'lg-d','lg-g'=>'lg-g','lge-'=>'lge-','libw'=>'libw','m-cr'=>'m-cr','maui'=>'maui','maxo'=>'maxo','midp'=>'midp','mits'=>'mits','mmef'=>'mmef','mobi'=>'mobi','mot-'=>'mot-','moto'=>'moto','mwbp'=>'mwbp','mywa'=>'mywa','nec-'=>'nec-','newt'=>'newt','nok6'=>'nok6','noki'=>'noki','o2im'=>'o2im','opwv'=>'opwv','palm'=>'palm','pana'=>'pana','pant'=>'pant','pdxg'=>'pdxg','phil'=>'phil','play'=>'play','pluc'=>'pluc','port'=>'port','prox'=>'prox','qtek'=>'qtek','qwap'=>'qwap','rozo'=>'rozo','sage'=>'sage','sama'=>'sama','sams'=>'sams','sany'=>'sany','sch-'=>'sch-','sec-'=>'sec-','send'=>'send','seri'=>'seri','sgh-'=>'sgh-','shar'=>'shar','sie-'=>'sie-','siem'=>'siem','smal'=>'smal','smar'=>'smar','sony'=>'sony','sph-'=>'sph-','symb'=>'symb','t-mo'=>'t-mo','teli'=>'teli','tim-'=>'tim-','tosh'=>'tosh','treo'=>'treo','tsm-'=>'tsm-','upg1'=>'upg1','upsi'=>'upsi','vk-v'=>'vk-v','voda'=>'voda','vx52'=>'vx52','vx53'=>'vx53','vx60'=>'vx60','vx61'=>'vx61','vx70'=>'vx70','vx80'=>'vx80','vx81'=>'vx81','vx83'=>'vx83','vx85'=>'vx85','wap-'=>'wap-','wapa'=>'wapa','wapi'=>'wapi','wapp'=>'wapp','wapr'=>'wapr','webc'=>'webc','whit'=>'whit','winw'=>'winw','wmlb'=>'wmlb','xda-'=>'xda-',)));
				$status = $this->user->lang['MOBILE_DEVICE'];
				$mobile_browser = true;
			break;
			default;
				$status = $this->user->lang['DESKTOP'];
				$mobile_browser = false;
			break;
		}
		header('Cache-Control: no-transform');
		header('Vary: User-Agent');
		if($mobile_browser=='')
		{
			return $mobile_browser;
		}
		else
		{
			return array($mobile_browser,$status);
		}
	}
}
