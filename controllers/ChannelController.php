<?php
/**
 * //                            _ooOoo_
 * //                           o8888888o
 * //                           88" . "88
 * //                           (| -_- |)
 * //                            O\ = /O
 * //                        ____/`---'\____
 * //                      .   ' \\| |// `.
 * //                       / \\||| : |||// \
 * //                     / _||||| -:- |||||- \
 * //                       | | \\\ - /// | |
 * //                     | \_| ''\---/'' | |
 * //                      \ .-\__ `-` ___/-. /
 * //                   ___`. .' /--.--\ `. . __
 * //                ."" '< `.___\_<|>_/___.' >'"".
 * //               | | : `- \`.;`\ _ /`;.`/ - ` : | |
 * //                 \ \ `-. \_ __\ /__ _/ .-` / /
 * //         ======`-.____`-.___\_____/___.-`____.-'======
 * //                            `=---='
 * //
 * //         .............................................
 * //                  佛祖保佑             永无BUG
 * DEC :
 * User: David Wang
 * Time: 2018/8/22 下午5:17
 */

namespace weixin\controllers;

use backend\classes\IFramePageController;
use backend\form\BootstrapFormRender;
use passport\classes\model\PassportTable;
use weixin\classes\form\WxChannelForm;
use wulaphp\io\Ajax;

/**
 * Class ChannelController
 * @package weixin\controllers
 * @acl     m:wx/channel
 */
class ChannelController extends IFramePageController {

	public function index() {
		$data['deleted'] = ['正常', '删除'];

		return $this->render($data);
	}

	public function data($q = '', $count = '') {
		$model  = new WxChannelForm();
		$delted = rqst('deleted');
		if ($delted != '') {
			$where['deleted'] = $delted;
		}
		if ($q) {
			$where['channel Like'] = '%' . $q . '%';
		}
		$query = $model->select('*')->where($where)->page()->sort();
		$rows  = $query->toArray();
		$total = '';
		if ($count) {
			$total = $query->total('channel');
		}
		$passTable = new PassportTable();
		foreach ($rows as &$row) {
			$row['auth_num'] = $passTable->count(['channel' => $row['channel']], 'id');
		}
		$data['rows']  = $rows;
		$data['total'] = $total;

		return view($data);
	}

	public function edit($channel = '') {
		$form = new WxChannelForm(true);
		if ($channel) {
			$query = $form->get($channel);
			$info  = $query->ary();
			$form->inflateByData($info);
		}
		$data['form']  = BootstrapFormRender::v($form);
		$data['rules'] = $form->encodeValidatorRule($this);

		return view($data);
	}

	public function del($channel) {
		if (!$channel) {
			return Ajax::error('参数错误啦!哥!');
		}
		$form = new WxChannelForm();
		$res  = $form->updateRecord(['channel' => $channel], ['deleted' => 1]);

		return Ajax::reload('#table', $res ? '删除成功' : '删除失败');
	}

	public function savePost() {
		$form                = new WxChannelForm(true);
		$data                = $form->inflate();
		$data['update_time'] = time();
		$is_exist            = $form->get(['channel' => $data['channel']])->ary();
		if ($is_exist) {
			$res = $form->updateRecord(['channel' => $data['channel']], $data);
		} else {
			$data['create_time'] = time();

			$res = $form->addData($data);
		}
		if ($res) {
			return Ajax::reload('#table', $is_exist ? '修改成功' : '新类型已经成功创建');
		} else {
			return Ajax::error('操作失败了');
		}

	}
}