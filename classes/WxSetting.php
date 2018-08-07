<?php
/*
 * This file is part of wulacms.
 *
 * (c) Leo Ning <windywany@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace weixin\classes;

use backend\classes\Setting;
use weixin\classes\form\WxSettingForm;

class WxSetting extends Setting {
	public function getForm($group = '') {
		return new WxSettingForm(true);
	}

	public function getName() {
		return '微信设置';
	}

	public function getIcon() {
		return '&#xe63e;';
	}

	public function getIconCls() {
		return '';
	}
}