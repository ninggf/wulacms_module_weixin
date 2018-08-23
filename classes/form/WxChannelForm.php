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
 * Time: 2018/8/22 下午5:44
 */

namespace weixin\classes\form;

use wulaphp\form\FormTable;
use wulaphp\validator\JQueryValidator;

class WxChannelForm extends FormTable {
	use JQueryValidator;
	protected $primaryKeys   = ['channel'];//住建不是自增 设置一个主键
	protected $autoIncrement = false;

	/**
	 * 渠道名称
	 * @var \backend\form\TextField
	 * @type string
	 * @required
	 * @layout 1,col-xs-6
	 */
	public $channel_name;

	/**
	 * 渠道标识
	 * @var \backend\form\TextField
	 * @type string
	 * @required
	 * @note   标识唯一 不可修改
	 * @layout 1,col-xs-6
	 */
	public $channel;

	public function addData(array $data = []) {
		return $this->insert($data);
	}

	public function updateRecord(array $where = [], array $data = []) {
		return $this->update($data, $where);
	}
}