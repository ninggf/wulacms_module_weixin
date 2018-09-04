<?php

namespace weixin;

use weixin\classes\WxSetting;
use wula\cms\CmfModule;
use wulaphp\app\App;
use wulaphp\auth\AclResourceManager;

/**
 * weixin
 *
 * @package weixin
 */
class WeixinModule extends CmfModule {
	public function getName() {
		return '微信';
	}

	public function getDescription() {
		return '提供微信公众号、订阅号、企业号、第三方平台接入功能。并提供微信公众号开发的基本功能';
	}

	public function getHomePageURL() {
		return 'https://www.wulacms.com/module/weixin';
	}

	public function getAuthor() {
		return 'Leo Ning';
	}

	public function getVersionList() {
		$v['1.0.0'] = '开发了';

		return $v;
	}

	/**
	 * @param array $setts
	 *
	 * @filter backend/settings
	 * @return array
	 */
	public static function sets($setts) {
		$setts['wx'] = new WxSetting();

		return $setts;
	}

	/**
	 * @param \backend\classes\DashboardUI $ui
	 *
	 * @bind dashboard\initUI
	 */
	public static function initMenu($ui) {
		$passport = whoami('admin');
		if ($passport->cando('m:wx')) {
			$wx            = $ui->getMenu('wx', '微信', 2);
			$wx->icon      = '&#xe63e;';
			$wx->iconStyle = 'color:green';
			if ($passport->cando('m:wx/account')) {
				$acc              = $wx->getMenu('acc', '公众号', 1);
				$acc->icon        = '&#xe672;';
				$acc->data['url'] = App::url('weixin/account');
			}
		}
	}

	/**
	 * @param \wulaphp\auth\AclResourceManager $mgr
	 *
	 * @bind rbac\initAdminManager
	 */
	public static function initAcl(AclResourceManager $mgr) {
		$mgr->getResource('wx', '微信', 'm');
		$cur = $mgr->getResource('wx/account', '公众号', 'm');
		$cur->addOperate('e', '编辑');
		$cur->addOperate('d', '删除');
	}
}

App::register(new WeixinModule());
// end of bootstrap.php