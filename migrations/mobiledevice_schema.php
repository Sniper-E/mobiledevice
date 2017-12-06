<?php
/**
* @package phpBB Extension - Mobile Device
* @copyright (c) 2015 Sniper_E - http://www.sniper-e.com
* @copyright (c) 2015 dmzx - http://www.dmzx-web.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*/

namespace sniper\mobiledevice\migrations;

class mobiledevice_schema extends \phpbb\db\migration\migration
{
	public function update_schema()
	{
		return 	array(
			'add_columns' => array(
				$this->table_prefix . 'users' => array(
					'mobile_browser'		=> array('UINT:1', 0),
					'device_name'			=> array('VCHAR:30', null),
					'user_mobile_welcome'	=> array('BOOL', 1),
					'user_mobile_header'	=> array('BOOL', 1),
					'user_mobile_self'		=> array('BOOL', 1),
				),
				$this->table_prefix . 'posts' => array(
					'post_device_title' => array('VCHAR:30', null),
				),
			),
			'add_tables'	=> array(
				$this->table_prefix . 'mobile_logs'	=> array(
					'COLUMNS' => array(
					'log_id'		=> array('UINT', null, 'auto_increment'),
					'log_ip'		=> array('VCHAR:15', ''),
					'log_time'		=> array('INT:11', 0),
					'device_name'	=> array('VCHAR:30', ''),
					'user_name'		=> array('VCHAR:30', ''),
					'user_agent'	=> array('VCHAR:255', ''),
					),
					'PRIMARY_KEY'	=>	'log_id',
				),
			),
		);
	}

	public function revert_schema()
	{
		return 	array(
			'drop_columns' => array(
				$this->table_prefix . 'users' => array(
					'mobile_browser',
					'device_name',
					'user_mobile_welcome',
					'user_mobile_header',
					'user_mobile_self',
				),
				$this->table_prefix . 'posts' => array(
					'post_device_title',
				),
			),
			'drop_tables' => array(
				$this->table_prefix . 'mobile_logs',
			),
		);
	}
}
