<?php
/**
* @package phpBB Extension - Mobile Device
* @copyright (c) 2015 Sniper_E - http://www.sniper-e.com
* @copyright (c) 2015 dmzx - http://www.dmzx-web.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*/

/**
* DO NOT CHANGE
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
// Mobile welcome
	'POST_MADE_VIA'         => 'Post made via',
	'MOBILE_DEVICE'         => 'Mobile Device',
	'MOBILE_DEVICE_LOGIN'   => 'Logged in via',
	'MOBILE_POST'           => 'Welcome',
	'MOBILE_POST_AFT1'      => 'We have detected your',
	'MOBILE_POST_AFT2'      => 'Detected',
// Mobile footer
	'MOBILEDEVICE_VERSION'    => 'Version',
	'MOBILE_AUTHOR_HOMEPAGES' => '<a href="http://sniper-e.com">Sniper_E</a> &amp; <a href="http://www.dmzx-web.net/">dmzx</a> &amp; <a href="http://www.martins-phpbb.com/forum/">martin</a>',
// Mobile logs
	'MOBILE_LOGS'         => 'Mobile Logs',
	'MOBILE_DEVICE_LOGS'  => 'Mobile Device Logs',
	'MOBILE_USERS'        => 'Mobile users',
	'MOBILE_CONNECTED'    => 'Connected via',
	'MOBILE_CLEAR_LOGS'   => 'Clear logs',
	'MOBILE_NO_LOGS'      => 'No logs to display.',
	'MOBILE_LOG'          => 'Log',
	'MOBILE_LOG_DELETED'  => 'Log Deleted',
	'MOBILE_CLEAR_LOGS'   => 'Clear Logs',
	'MOBILE_LOGS_CLEANED' => 'All Mobile Logs Cleared',
	'MOBILE_LOGS_CLEAR'   => 'Clear all Mobile Logs?',
	'MOBILE_TOTAL'        => 'Total Messages in logs',
// UCP
	'MOBILE_DEVICE_USER'   => 'Display mobile welcome on index',
	'MOBILE_DEVICE_HEADER' => 'Display your mobile image in header',
	'MOBILE_DEVICE_SELF'   => 'Display your &#8217;on-mobile stats&#8216; in your profiles',
// Mobile device names
	'AE5HD'         => 'Aquaris E5 HD',
	'ANDROID'       => 'Android',
	'ATT'           => 'Asus Transformer Tablet',
	'A4TS'          => 'Archos 40b Titanium Surround',
	'ATOOJ'         => 'ASUS T00J',
	'BBCURVE'       => 'BlackBerry Curve',
	'BBPEARL'       => 'BlackBerry Pearl',
	'BBPEARLF'      => 'BlackBerry Pearl Flip',
	'BBPP'          => 'BlackBerry Passport',
	'BBP'           => 'BlackBerry Porsche',
	'BBPB'          => 'BlackBerry PlayBook',
	'BBSTORM'       => 'BlackBerry Storm',
	'BBTORCH'       => 'BlackBerry Torch',
	'BBTOUCH'       => 'BlackBerry Touch',
	'BLACKBERRY'    => 'BlackBerry',
	'DESKTOP'       => 'Desktop',
	'HPL01'         => 'Huawei Honor 7',
	'HPL09'         => 'Huawei P9 EVA-L09',
	'HP8'           => 'HUAWEI P8',
	'HTCD'          => 'HTC Desire',
	'IPAD'          => 'iPad',
	'IPHONE'        => 'iPhone',
	'IPOD'          => 'iPod',
	'L640XL'        => 'Lumia 640 XL',
	'LG'            => 'LG',
	'LGH850'        => 'LG G5 H850',
	'LGOL7IT'       => 'LG Optimus L7 II',
	'LGV500'        => 'LG V500',
	'LK50T5'        => 'Lenovo K50-t5',
	'MDM'           => 'Motorola Droid Mini',
	'N1'            => 'Nexus 1',
	'N4'            => 'Nexus 4',
	'N5'            => 'Nexus 5',
	'N7'            => 'Nexus 7',
	'N9'            => 'Nexus 9',
	'ON'            => 'Orange Nura',
	'OPO'           => 'OnePlus One',
	'PALM'          => 'Palm',
	'SGS5A'         => 'Samsung Galaxy S5 Active',
	'SGS5'          => 'Samsung Galaxy S5',
	'SGS6'          => 'Samsung Galaxy S6',
	'SG2T'          => 'Samsung Galaxy2 Tablet',
	'SGT3'          => 'Samsung Galaxy Tab 3',
	'SGT3W'         => 'Samsung Galaxy Tab3',
	'SGT4'          => 'Samsung Galaxy Tab 4',
	'SGTP'          => 'Samsung Galaxy TabPRO',
	'SGS4A'         => 'Samsung Galaxy S4 Active',
	'SGS4'          => 'Samsung Galaxy S4',
	'SGS2'          => 'Samsung Galaxy S2',
	'SGN3'          => 'Samsung Galaxy Note 3',
	'SGN3N'         => 'Samsung Galaxy Note 3 Neo',
	'SGN4'          => 'Samsung Galaxy Note 4',
	'SGN5'          => 'Samsung Galaxy Note 5',
	'SGA4'          => 'Samsung Galaxy Ace 4',
	'SGS6E'         => 'Samsung Galaxy S6 Edge',
	'SGS7E'         => 'Samsung Galaxy S7 Edge',
	'SGSD2'         => 'Samsung Galaxy S Duos 2',
	'SGS2'          => 'Samsung Galaxy S2',
	'SGN'           => 'Samsung Galaxy Nexus',
	'SXED'          => 'Sony Xperia E1 Dual',
	'SXM2'          => 'Sony XPERIA M2',
	'SXZ1'          => 'Sony Xperia Z1',
	'SXZ3'          => 'Sony Xperia Z3',
	'TKT'           => 'Tianyu K-Touch',
	'VOYAGER'       => 'Voyager',
	'WSP'           => 'Windows Smartphone',
));
