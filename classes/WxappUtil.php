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
 * DEC : 小程序相关操作
 * User: David Wang
 * Time: 2018/8/11 下午2:51
 */

namespace weixin\classes;

use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use wulaphp\app\App;
use wulaphp\util\RedisClient;

class WxappUtil {
	static $FromIdKey = 'wxapp_user_fromId_key_';

	/**
	 * 存储FromId
	 *
	 * @param $mid
	 * @param $fromId
	 *
	 * @return bool|int
	 * @throws \Exception
	 */
	public static function saveFromId(int $mid, string $fromId) {
		$redis = RedisClient::getRedis();
		$key   = self::$FromIdKey . $mid;
		$res   = $redis->lPush($key, $fromId);

		return $res;
	}

	/**
	 * 获取用户FROMID
	 *
	 * @param $mid
	 *
	 * @return string
	 * @throws \Exception
	 */
	public static function getFromId(int $mid) {
		$redis = RedisClient::getRedis();
		$key   = self::$FromIdKey . $mid;
		$res   = $redis->rPop($key);

		return $res;
	}

	/**
	 *
	 * 创建带场景值的小程序码
	 *
	 * @param int    $mid
	 * @param string $type
	 * @param string $scene
	 * @param array  $options
	 *
	 * @return bool|string
	 */
	public static function createQrScene(int $mid, string $type, string $scene, array $options) {
		$root  = WEB_ROOT;
		$group = $type . ($mid % 256) . '/' . ($mid % 256) . '/' . ($mid % 1000);
		if (!is_dir($root . App::cfg('upload_dir@media', 'files') . '/' . $group)) {
			@mkdir($root . App::cfg('upload_dir@media', 'files') . '/' . $group, 0777, true);
		}
		//保存的文件名
		$save_name = $type . '_' . $mid . '.png';
		//保存在目录
		$url_path = $root . App::cfg('upload_dir@media', 'files') . '/' . $group . '/';
		//完整路径
		$save_path = $url_path . $save_name;
		//返回值
		$rtn = App::cfg('upload_dir@media', 'files') . '/' . $group . '/' . $save_name;
		if (!is_file($save_path)) {
			$wx_account = WxAccount::getWechat(App::cfg('wxapp_id@wx'));
			if (!$wx_account) {
				log_warn('getWechat失败', 'createQrScen.log');

				return false;
			}
			$reponse = $wx_account->miniApp->app_code->getUnlimit($scene, $options);
			log_warn($reponse, 'createQrScen.log');
			try {
				$reponse->saveAs($url_path, $save_name);
			} catch (InvalidArgumentException $e) {
				log_warn($e->getMessage(), 'createQrScen.log');

				return false;
			}

		}

		return the_media_src($rtn);
	}
}