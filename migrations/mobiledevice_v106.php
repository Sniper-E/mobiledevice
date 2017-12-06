<?php
/**
* @package phpBB Extension - Mobile Device
* @copyright (c) 2015 Sniper_E - http://www.sniper-e.com
* @copyright (c) 2015 dmzx - http://www.dmzx-web.net
* @copyright (c) 2015 martin - http://www.martins-phpbb.com
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*/

namespace sniper\mobiledevice\migrations;

class mobiledevice_v106 extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array(
			'\sniper\mobiledevice\migrations\mobiledevice_data',
		);
	}

	public function update_data()
	{
		return array(
			// Update config
			array('config.update', array('mobiledevice_version', '1.0.6')),
			// Add config
			array('config.add', array('mobile_test_enable', '0')),
		);
	}
}
