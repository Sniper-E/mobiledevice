<?php
/**
* @package phpBB Extension - Mobile Device
* @copyright (c) 2015 Sniper_E - http://www.sniper-e.com
* @copyright (c) 2015 dmzx - http://www.dmzx-web.net
* @copyright (c) 2015 martin - http://www.martins-phpbb.com
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*/

namespace sniper\mobiledevice\controller;

class admin_controller
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\log\log_interface */
	protected $log;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var string Custom form action */
	protected $u_action;

	/**
	 * Constructor
	 *
	 * @param \phpbb\config\config				$config
	 * @param \phpbb\template\template			$template
	 * @param \\phpbb\log\log_interface			$log
	 * @param \phpbb\user						$user
	 * @param \phpbb\request\request			$request
	 */
	public function __construct(
		\phpbb\config\config $config,
		\phpbb\template\template $template,
		\phpbb\log\log_interface $log,
		\phpbb\user $user,
		\phpbb\request\request $request
	)
	{
		$this->config 		= $config;
		$this->template 	= $template;
		$this->log 			= $log;
		$this->user 		= $user;
		$this->request 		= $request;
	}

	/**
	* Display the options a user can configure for this extension
	*
	* @return null
	* @access public
	*/
	public function display_options()
	{
		add_form_key('acp_mobiledevice_config');

		// Is the form being submitted to us?
		if ($this->request->is_set_post('submit'))
		{
			if (!check_form_key('acp_mobiledevice_config'))
			{
				trigger_error('FORM_INVALID');
			}

			// Set the options the user configured
			$this->set_options();

			// Add option settings change action to the admin log
			$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_ACP_MOBILEDEVICE_SETTINGS_SAVED');

			trigger_error($this->user->lang('ACP_MOBILEDEVICE_CONFIG_SAVED') . adm_back_link($this->u_action));
		}

		$this->template->assign_vars(array(
			'U_ACTION'							=> $this->u_action,
			'ACP_MOBILE_ENABLE'					=> $this->config['mobile_enable'],
			'ACP_MOBILE_TEST_ENABLE'			=> $this->config['mobile_test_enable'],
			'ACP_MOBILE_LOGS_REFRESH'		 	=> $this->config['mobile_logs_refresh'],
			'ACP_MOBILE_WELCOME_ENABLE'			=> $this->config['mobile_welcome_enable'],
			'ACP_MOBILE_WELCOME_GUEST_ENABLE' 	=> $this->config['mobile_welcome_guest_enable'],
			'ACP_MOBILE_HEADER_ENABLE'			=> $this->config['mobile_header_enable'],
			'ACP_MOBILE_PROFILE_ENABLE'			=> $this->config['mobile_profile_enable'],
			'ACP_MOBILE_LOGS_ENABLE'			=> $this->config['mobile_logs_enable'],
			'ACP_MOBILEDEVICE_VERSION'			=> $this->config['mobiledevice_version'],
		));
	}

	/**
	* Set the options a user can configure
	*
	* @return null
	* @access protected
	*/
	protected function set_options()
	{
		$this->config->set('mobile_enable', $this->request->variable('mobile_enable', 1));
		$this->config->set('mobile_test_enable', $this->request->variable('mobile_test_enable', 1));
		$this->config->set('mobile_logs_refresh', $this->request->variable('mobile_logs_refresh', ''));
		$this->config->set('mobile_welcome_enable', $this->request->variable('mobile_welcome_enable', 1));
		$this->config->set('mobile_welcome_guest_enable', $this->request->variable('mobile_welcome_guest_enable', 1));
		$this->config->set('mobile_header_enable', $this->request->variable('mobile_header_enable', 1));
		$this->config->set('mobile_profile_enable', $this->request->variable('mobile_profile_enable', 1));
		$this->config->set('mobile_logs_enable', $this->request->variable('mobile_logs_enable', 1));
	}

	/**
	* Set page url
	*
	* @param string $u_action Custom form action
	* @return null
	* @access public
	*/
	public function set_page_url($u_action)
	{
		$this->u_action = $u_action;
	}
}
