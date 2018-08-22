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
 * Time: 2018/8/17 上午10:36
 */

namespace weixin\classes\template;

use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use weixin\classes\WxAccount;
use wulaphp\app\App;
use wulaphp\conf\ConfigurationLoader;

class WxappTemplate {
	public         $error;
	private        $wx_account;
	public         $data   = [];
	private static $CONFIG = false;

	private function __construct($wxid, array $data = []) {
		$this->wx_account = WxAccount::getWechat($wxid);
		$this->data       = $data;
	}

	/**
	 * 获取模板
	 * @param string      $id
	 * @param null|string $wxid
	 *
	 * @return \weixin\classes\template\WxAppTemplate
	 */
	public static function getTemplate(string $id, ?string $wxid = null): WxAppTemplate {
		if (self::$CONFIG === false) {
			self::$CONFIG = ConfigurationLoader::loadFromFile('wxapp');
		}
		if (!$wxid) {
			//默认的wx_id
			$wxid = App::cfg('wxapp_id@wx');
		}

		return new self($wxid, self::$CONFIG[ $id ]);
	}

	/**
	 * 发送小程序模板
	 *
	 * @param array $data
	 *
	 * @return bool
	 */
	public function send(array $data): bool {
		if (!$data && !$this->validate($data)) {
			return false;
		}
		$this->data = array_merge($this->data, $data);
		try {
			$this->wx_account->miniApp->template_message->send($this->data);
		} catch (InvalidArgumentException $e) {
			$this->error = $e->getMessage();
			log_error($this->error, 'wxapp_template');

			return false;

		}

		return true;
	}

	protected function validate(array $data): bool {
		if (!$data['touser'] || !$data['form_id'] || !$data['page']) {
			$this->error = '请检查(touser,from_id,page)参数是否传递';

			return false;
		}

		return true;
	}
}