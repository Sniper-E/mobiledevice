<?php
/**
* @package phpBB Extension - Mobile Device
* @copyright (c) 2015 Sniper_E - http://www.sniper-e.com
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*/

namespace sniper\mobiledevice\acp;

class mobiledevice_info
{
	function module()
	{
		return array(
			'filename'	=> '\sniper\mobiledevice\acp\mobiledevice_module',
			'title'		=> 'ACP_MOBILEDEVICE',
			'modes'		=> array(
			'config'	=> array('title' => 'ACP_MOBILEDEVICE_CONFIG_SETTINGS', 'auth' => 'ext_sniper/mobiledevice && acl_a_board', 'cat' => array('ACP_MOBILEDEVICE')),
			),
		);
	}
}
