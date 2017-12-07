<?php
/**
* @package phpBB Extension - Mobile Device
* @copyright (c) 2015 Sniper_E - http://www.sniper-e.com
* @copyright (c) 2015 dmzx - http://www.dmzx-web.net
* @copyright (c) 2015 martin - http://www.martins-phpbb.com
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*/

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
//
// Some characters you may want to copy&paste:
// ’ » “ ” …
//

$lang = array_merge($lang, array(
// Mobile main:
	'ACP_MOBILEDEVICE_TITLE'                  => 'Mobile Device',
	'ACP_MOBILEDEVICE_VERSION'                => 'Version',
	'ACP_MOBILEDEVICE_DESC'                   => 'Manage which mobile device features will be displayed on your board.',
	'ACP_MOBILEDEVICE_CONFIG_SAVED'           => 'Mobile device settings saved',
	'ACP_MOBILEDEVICE_SETTINGS'               => 'Mobile device settings',
// Mobile discription:
	'ACP_MOBILEDEVICE_EXT_DESC_TITLE'         => 'Mobile device description',
	'ACP_MOBILEDEVICE_EXT_DESC'               => '<div style="margin: 0 10px">Adds [ Post made via Mobile Device ] when posting from a mobile device. Welcome mobile user message on index page showing user&#39;s mobile device. Displays logged in user&#39;s mobile image in header navbar on all other pages. Displays user is on their mobile in topic mini-profile and profile page. Mobile Logs popup to view mobile device visitors. Selections in UCP -> Board preferences -> Edit display for user options. Selections in ACP -> Extensions -> Mobile device -> Settings for enabling features. Permissions -> Misc for selecting who can view logs and who can clear logs.</div>',
// General option:
	'ACP_MOBILEDEVICE_CONFIG'                 => 'General option',
	'ACP_MOBILE_ENABLE'                       => 'Enable mobile device',
	'ACP_MOBILE_ENABLE_EXPLAIN'               => 'Enable mobile device detection to run on your board.',
	'ACP_MOBILE_TEST_ENABLE'                  => 'Enable test mobile device',
	'ACP_MOBILE_TEST_ENABLE_EXPLAIN'          => 'Enable test mobile device for testing with desktop.',
// Display options:
	'ACP_MOBILEDEVICE_CONFIG_SET'             => 'Display options',
	'ACP_MOBILE_WELCOME_ENABLE'               => 'Enable mobile welcome',
	'ACP_MOBILE_WELCOME_ENABLE_EXPLAIN'       => 'Display mobile welcome message on index to all registered users.',
	'ACP_MOBILE_WELCOME_GUEST_ENABLE'         => 'Enable guest mobile welcome',
	'ACP_MOBILE_WELCOME_GUEST_ENABLE_EXPLAIN' => 'Display mobile welcome message on index to all guests.',
	'ACP_MOBILE_HEADER_ENABLE'                => 'Enable mobile header',
	'ACP_MOBILE_HEADER_ENABLE_EXPLAIN'        => 'Display mobile user&#39;s device image in header navbar.',
	'ACP_MOBILE_PROFILE_ENABLE'               => 'Enable mobile profile',
	'ACP_MOBILE_PROFILE_ENABLE_EXPLAIN'       => 'Display mobile message in posts and profiles to all users.',
// Mobile logs option:
	'ACP_MOBILE_LOGS_SETTINGS'                => 'Mobile logs option',
	'ACP_MOBILE_LOGS_ENABLE'                  => 'Enable mobile logs',
	'ACP_MOBILE_LOGS_ENABLE_EXPLAIN'          => 'Browser user agents of visiting mobile users will be detected and logged.',
	'ACP_MOBILE_LOGS_REFRESH'                 => 'Mobile logs refresh',
	'ACP_MOBILE_LOGS_REFRESH_EXPLAIN'         => 'Set time interval from 10 to 60 seconds for mobile logs refresh rate.',
));
