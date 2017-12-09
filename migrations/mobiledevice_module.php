<?php
/**
* @package phpBB Extension - Mobile Device
* @copyright (c) 2015 Sniper_E - http://www.sniper-e.com
* @copyright (c) 2015 dmzx - http://www.dmzx-web.net
* @copyright (c) 2015 martin - http://www.martins-phpbb.com
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*/

namespace sniper\mobiledevice\migrations;

class mobiledevice_module extends \phpbb\db\migration\migration
{
	public function update_data()
	{
		return array(
			array('module.add', array(
				'acp',
				'ACP_CAT_DOT_MODS',
				'ACP_MOBILEDEVICE'
			)),
			array('module.add', array(
				'acp',
				'ACP_MOBILEDEVICE',
				array(
					'module_basename' => '\sniper\mobiledevice\acp\mobiledevice_module',
					'modes'           => array('config'),
				),
			)),
		);
	}
}
