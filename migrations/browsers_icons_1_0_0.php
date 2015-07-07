<?php
/**
*
* @package phpBB Extension - Browsers icons in who is online
* @copyright (c) 2015 Sheer
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace sheer\browsers_icons\migrations;

class browsers_icons_1_0_0 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return;
	}

	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v310\dev');
	}

	public function update_schema()
	{
		return array(
		);
	}

	public function revert_schema()
	{
		return array(
		);
	}

	public function update_data()
	{
		return array(
			// Current version
			array('config.add', array('browsers_icons_version', '1.0.0')),
		);
	}
}
