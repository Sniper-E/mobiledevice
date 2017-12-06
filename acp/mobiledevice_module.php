<?php
/**
* @package phpBB Extension - Mobile Device
* @copyright (c) 2015 Sniper_E - http://www.sniper-e.com
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*/

namespace sniper\mobiledevice\acp;

class mobiledevice_module
{
	var $u_action;

	function main($id, $mode)
	{
		global $config, $user, $template, $request;

		// Add the ACP lang file
		$user->add_lang_ext('sniper/mobiledevice', 'acp_mobiledevice');

		$this->tpl_name = 'acp_mobiledevice_config';
		$this->page_title = $user->lang['ACP_MOBILEDEVICE_SETTINGS'];
		add_form_key('acp_mobiledevice_config');

		$submit = $request->is_set_post('submit');

		if ($submit)
		{
			if (!check_form_key('acp_mobiledevice_config'))
			{
				trigger_error('FORM_INVALID');
			}

			$config->set('mobile_enable', $request->variable('mobile_enable', 1));
			$config->set('mobile_welcome_enable', $request->variable('mobile_welcome_enable', 1));
			$config->set('mobile_welcome_guest_enable', $request->variable('mobile_welcome_guest_enable', 1));
			$config->set('mobile_header_enable', $request->variable('mobile_header_enable', 1));
			$config->set('mobile_profile_enable', $request->variable('mobile_profile_enable', 1));
			$config->set('mobile_logs_enable', $request->variable('mobile_logs_enable', 1));

			trigger_error($user->lang['ACP_MOBILEDEVICE_CONFIG_SAVED'] . adm_back_link($this->u_action));
		}

		$template->assign_vars(array(
			'ACP_MOBILE_ENABLE'               => (!empty($config['mobile_enable'])) ? true : false,
			'ACP_MOBILE_WELCOME_ENABLE'       => (!empty($config['mobile_welcome_enable'])) ? true : false,
			'ACP_MOBILE_WELCOME_GUEST_ENABLE' => (!empty($config['mobile_welcome_guest_enable'])) ? true : false,
			'ACP_MOBILE_HEADER_ENABLE'        => (!empty($config['mobile_header_enable'])) ? true : false,
			'ACP_MOBILE_PROFILE_ENABLE'       => (!empty($config['mobile_profile_enable'])) ? true : false,
			'ACP_MOBILE_LOGS_ENABLE'          => (!empty($config['mobile_logs_enable'])) ? true : false,
			'ACP_MOBILEDEVICE_VERSION'        => (isset($config['mobiledevice_version'])) ? $config['mobiledevice_version'] : '',
			'U_ACTION'                        => $this->u_action,
		));
	}
}
