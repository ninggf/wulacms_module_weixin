<?php
/*
 * This file is part of wulacms.
 *
 * (c) Leo Ning <windywany@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace weixin\classes\form;

use wulaphp\form\FormTable;
use wulaphp\validator\JQueryValidator;

class WxSettingForm extends FormTable {
	use JQueryValidator;
	public $table = null;
	/**
	 * 默认BASE URL
	 * @var \backend\form\TextField
	 * @type string
	 * @url
	 * @layout 2,col-xs-12
	 */
	public $base_url;
	/**
	 * 默认微信号
	 * @var \backend\form\TextField
	 * @type string
	 * @layout 3,col-xs-12
	 */
	public $wxid;

	/**
	 * 默认小程序微信号
	 * @var \backend\form\TextField
	 * @type string
	 * @layout 4,col-xs-12
	 */
	public $wxapp_id;
}