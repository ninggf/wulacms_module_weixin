<?php

namespace weixin\controllers;

use EasyWeChat\Kernel\Exceptions\BadRequestException;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Exceptions\InvalidConfigException;
use weixin\classes\WxAccount;
use wulaphp\io\Response;
use wulaphp\mvc\controller\Controller;

/**
 *
 */
class IndexController extends Controller {

	/**
	 * 接入认证接口.
	 *
	 * @param string $accid 公众号ID
	 *
	 * @return string
	 */
	public function index(string $accid) {
		$wechat = WxAccount::getWechat($accid);
		if ($wechat) {
			try {
				fire('weixin\regMsgHandler', $wechat->server, $accid);
				$resp = $wechat->server->serve();
				$resp->send();
				Response::getInstance()->close();
			} catch (BadRequestException $e) {
				log_warn($e->getMessage(), 'wexin');
			} catch (InvalidArgumentException $e) {
				log_warn($e->getMessage(), 'wexin');
			} catch (InvalidConfigException $e) {
				log_warn($e->getMessage(), 'wexin');
			}
		}
		Response::respond(403);

		return null;
	}
}