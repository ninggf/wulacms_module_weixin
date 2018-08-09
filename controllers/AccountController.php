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
use wulaphp\io\Ajax;
use wulaphp\validator\ValidateException;

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

	/**
	 * 新增、编辑
	 *
	 * @param string $id
	 *
	 * @return \wulaphp\mvc\view\SmartyView
	 * @acl e:wx/account
	 */
	public function edit($id = '') {
		$form = new WxAccountForm(true);
		if ($id) {
			$form->inflateFromDB(['id' => $id]);
		}
		$data['form']  = BootstrapFormRender::v($form);
		$data['rules'] = $form->encodeValidatorRule($this);

		return view($data);
	}

	/**
	 * 保存
	 *
	 * @param string $id
	 *
	 * @acl e:wx/account
	 * @return \wulaphp\mvc\view\View
	 */
	public function save($id = '') {
		$form = new WxAccountForm(true);
		$id   = intval($id);
		$data = $form->inflate();
		try {
			$form->validate();
			$data['update_time'] = time();
			$data['update_uid']  = $this->passport->uid;
			//未指定token时，自动生成
			if (empty($data['token'])) {
				$data['token'] = rand_str(24, 'a-z,0-9');
			}
			//非明文模式且未指定aeskey时，自动生成
			if ($data['mode'] != 'T' && empty($data['aeskey'])) {
				$data['aeskey'] = rand_str(43, 'a-z,0-9,A-Z');
			}

			if ($id) {
				$rst = $form->updateAccount($data, $id);
			} else {
				unset($data['id']);
				$data['create_time'] = $data['update_time'];
				$data['create_uid']  = $data['update_uid'];
				$rst                 = $form->newAccount($data);
			}
			if (!$rst) {
				return Ajax::error($form->lastError());
			}
		} catch (ValidateException $e) {
			return Ajax::validate('Form', $e->getErrors());
		}

		return Ajax::reload('#table', '公众号已保存');
	}

	public function data($type = '', $wxid = '', $authed = '', $uauthed = '', $count = '') {
		$where = [];
		$query = App::db()->select('*')->from('{wx_account}')->page()->sort();
		if ($type) {
			$where['type'] = $type;
		}
		if ($wxid) {
			$where['wxid'] = $wxid;
		}
		if ($authed && !$uauthed) {
			$where['authed'] = 1;
		} else if (!$authed && $uauthed) {
			$where['authed'] = 0;
		}

		$query->where($where);
		$rows  = $query->toArray();
		$total = '';
		if ($count) {
			$total = $query->total('id');
		}

		$data['total']    = $total;
		$data['rows']     = $rows;
		$data['types']    = WxAccount::TYPES;
		$data['base_url'] = App::cfg('base_url@wx');
		$data['modes']    = ['T' => '明文模式', 'C' => '兼容模式', 'S' => '安全模式'];
		$data['canEdit']  = $this->passport->cando('e:wx/account');

		return view($data);
	}
}