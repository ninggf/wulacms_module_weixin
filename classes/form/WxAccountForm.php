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

class WxAccountForm extends FormTable {
	use JQueryValidator;
	/**
	 * @var \backend\form\HiddenField
	 * @type int
	 * @layout 1,col-xs-12
	 */
	public $id;
	/**
	 * 公众号名称
	 * @var \backend\form\TextField
	 * @type string
	 * @required
	 * @layout 5,col-xs-4
	 */
	public $name;
	/**
	 * 微信账号
	 * @var \backend\form\TextField
	 * @type string
	 * @required
	 * @layout 5,col-xs-4
	 */
	public $wxid;
	/**
	 * 原始ID
	 * @var \backend\form\TextField
	 * @type string
	 * @required
	 * @layout 5,col-xs-4
	 */
	public $origin_id;
	/**
	 * 头像
	 * @var \backend\form\TextField
	 * @type string
	 * @url
	 * @layout 10,col-xs-12
	 */
	public $avatar;
	/**
	 * 公众号二维码
	 * @var \backend\form\TextField
	 * @type string
	 * @url
	 * @layout 15,col-xs-12
	 */
	public $qrcode;
	/**
	 * AppId
	 * @var \backend\form\TextField
	 * @type string
	 * @layout 20,col-xs-4
	 */
	public $app_id;
	/**
	 * AppSecret
	 * @var \backend\form\TextField
	 * @type string
	 * @layout 20,col-xs-4
	 */
	public $app_secret;
	/**
	 * 令牌(Token)
	 * @var \backend\form\TextField
	 * @type string
	 * @layout 20,col-xs-4
	 * @note   留空时系统自动生成
	 */
	public $token;
	/**
	 * 公众号类型
	 * @var \backend\form\SelectField
	 * @type string
	 * @see    param
	 * @dsCfg  DY=订阅号&FW=服务号&QY=企业号
	 * @layout 21,col-xs-4
	 */
	public $type = 'DY';
	/**
	 * 是否认证
	 * @var \backend\form\SelectField
	 * @type int
	 * @see    param
	 * @dsCfg  0=未认证&1=已认证
	 * @layout 21,col-xs-4
	 */
	public $authed = 0;
	/**
	 * 消息加解密方式
	 * @var \backend\form\SelectField
	 * @type string
	 * @see    param
	 * @dsCfg  T=明文&C=兼容&S=安全
	 * @layout 21,col-xs-4
	 */
	public $mode = 'S';
	/**
	 * 消息加解密密钥
	 * @var \backend\form\TextField
	 * @type string
	 * @layout 22,col-xs-12
	 * @note   如果留空当消息加载解密方式为非明文模式时将自动生成
	 */
	public $aeskey;
	/**
	 * 回调地址
	 * @var \backend\form\TextField
	 * @type string
	 * @layout 25,col-xs-8
	 * @note   不填写时使用系统默认
	 */
	public $base_url;
	/**
	 * 调试模式
	 * @var \backend\form\SelectField
	 * @type int
	 * @see    param
	 * @dsCfg  1=开启&0=关闭
	 * @layout 25,col-xs-4
	 */
	public $debug = 0;

	/**
	 * 更新公众号.
	 *
	 * @param array  $data
	 * @param string $id
	 *
	 * @return bool
	 */
	public function updateAccount(array $data, string $id) {
		return $this->update($data, $id);
	}

	/**
	 * 新增公众号.
	 *
	 * @param array $data
	 *
	 * @return bool
	 */
	public function newAccount(array $data) {
		return $this->insert($data);
	}
}