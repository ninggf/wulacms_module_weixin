<?php
/*
 * This file is part of wulacms.
 *
 * (c) Leo Ning <windywany@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace weixin\controllers;

use backend\classes\IFramePageController;
use backend\form\BootstrapFormRender;
use weixin\classes\form\WxAccountForm;
use weixin\classes\WxAccount;
use wulaphp\app\App;

/**
 * Class AccountController
 * @package weixin\controllers
 * @acl     m:wx/account
 */
class AccountController extends IFramePageController {

	public function index() {
		$data['types'] = WxAccount::TYPES;

		return $this->render($data);
	}

	public function edit($id = '') {

		$form = new WxAccountForm(true);
		if ($id) {
			$form->inflateFromDB(['id' => $id]);
		}
		$data['form']  = BootstrapFormRender::v($form);
		$data['rules'] = $form->encodeValidatorRule($this);

		return view($data);
	}

	public function data($type = '', $wxid = '', $count = '') {
		$where = [];
		$query = App::db()->select('*')->from('{wx_account}')->page()->sort();
		if ($type) {
			$where['type'] = $type;
		}
		if ($wxid) {
			$where['wxid'] = $wxid;
		}
		$query->where($where);
		$rows  = $query->toArray();
		$total = '';
		if ($count) {
			$total = $query->total('id');
		}

		$data['total'] = $total;
		$data['rows']  = $rows;
		$data['types'] = WxAccount::TYPES;

		return view($data);
	}
}