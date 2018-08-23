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
 * Time: 2018/8/11 下午2:48
 */

namespace weixin\api\v1;

use passport\classes\model\OauthSessionTable;
use rest\classes\API;
use weixin\classes\WxappUtil;
use wulaphp\app\App;
use wulaphp\util\RedisLock;

/**
 * Class WxappApi
 * @package weixin\api\v1
 * @name 小程序
 */
class WxappApi extends API {
	/**
	 * 小程序
	 * @apiName fromId存储用于推送(用户没登录不需要请求)
	 *
	 * @param string $token  (required) 用户标识
	 * @param string $fromId (required)fromId通过button form获取
	 *
	 * @error   404=>参数缺少
	 * @error   405=>用户不存在
	 *
	 * @paramo  int status 0成功
	 *
	 * @throws
	 * @return array {
	 * "status": 0
	 * }
	 */
	public function saveFromid(string $token, string $fromId) {
		if (!$token) {
			$this->error(404, '请先授权');
		}
		$info = (new OauthSessionTable())->getInfo($token);
		if (!$info) {
			$this->error(406, '用户不存在');
		}
		if (!$fromId) {
			$this->error(407, 'fromID参数缺失');
		}
		$mid      = $info['uid'];
		$lock_key = 'user_fromid_save_lock_key_' . $mid;
		$lock     = RedisLock::ulock($lock_key);
		if ($lock) {
			try {
				$res = WxappUtil::saveFromId($mid, $fromId);
				if ($res) {
					return ['status' => 0];
				} else {
					$this->error(410, 'fromId保存失败');
				}
			} finally {
				RedisLock::uunlock($lock_key);
			}
		}
		$this->error(405, '请求太快了,亲');
	}

	/**
	 * 小程序
	 * @apiName 创建带场景值的小程序码
	 *
	 * @param string $token      (required) 用户标识
	 * @param string $type       (required)类型
	 * @param string $scene      (required)场景值 不要超过32个字符
	 * @param string $page       必须是已经发布的小程序存在的页面（否则报错）
	 * @param int    $width      二维码的宽度
	 * @param int    $auto_color 1 是 0否 自动配置线条颜色
	 * @param int    $is_hyaline 1 是 0否 是否需要透明底色
	 *
	 * @error   404=>参数缺少
	 * @error   405=>用户不存在
	 *
	 * @paramo  string url 二维码地址
	 *
	 * @throws
	 * @return array {
	 * "url": 'ssddd.png'
	 * }
	 */
	public function createScenePost($token, $type, $scene, $page = '', $width = 0, $auto_color = 0, $is_hyaline = 0) {
		if (!$token || !$type || !$scene) {
			$this->error(404, '参数缺失');
		}
		$info = (new OauthSessionTable())->getInfo($token);
		if (!$info) {
			$this->error(406, '用户不存在');
		}
		$mid      = $info['uid'];
		$lock_key = 'scene_qr_lock_' . $mid;
		$lock     = RedisLock::ulock($lock_key);
		if ($lock) {
			try {
				$options = [];
				if ($page) {
					$options['page'] = $page;
				}
				if ($width) {
					$options['width'] = $width;
				}
				if ($auto_color) {
					$options['auto_color'] = true;
				}
				if ($is_hyaline) {
					$options['is_hyaline'] = true;
				}
				$file_name = WxappUtil::createQrScene($mid, $type, $scene, $options);
				if (!$file_name) {
					$this->error(407, '生成失败,请联系管理员(David Wang)');
				}

				return ['url' => $file_name];

			} finally {
				RedisLock::uunlock($lock_key);
			}
		}
		$this->error(405, '请求太快了,亲');
	}

	/**
	 * 小程序
	 * @apiName 渠道统计
	 *
	 * @param string $channel (required) 渠道名
	 *
	 * @error   404=>参数缺少
	 * @error   405=>用户不存在
	 *
	 * @paramo  int status 0成功
	 *
	 * @throws
	 * @return array {
	 * "status": 0
	 * }
	 */
	public function channel($channel) {
		if (!$channel) {
			$this->error(404, '参数缺失');
		}
		try {
			$db  = App::db();
			$rst = $db->cud('UPDATE {wx_channel} SET click_num=click_num+1 WHERE channel=%s ', $channel);
			if ($rst) {
				return ['status' => 0];
			} else {
				$this->error(405, '无效渠道');
			}
		} catch (\Exception $e) {
			$this->error(500, '请求失败了');
		}
	}

}