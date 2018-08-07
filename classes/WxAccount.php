<?php
/*
 * This file is part of wulacms.
 *
 * (c) Leo Ning <windywany@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// 2b6a3cf5c82f8e3ab566e515f9a09040
namespace weixin\classes;

use EasyWeChat\Factory;
use EasyWeChat\OfficialAccount\Application;
use wulaphp\app\App;

class WxAccount {
	public const TYPES = [
		'DY' => '订阅号',
		'FW' => '服务号',
		'QY' => '企业号'
	];
	private static $accounts = [];

	/**
	 * @param string $accid
	 *
	 * @return \EasyWeChat\OfficialAccount\Application
	 */
	public static function getWechat(string $accid): ?Application {
		try {
			if (!array_key_exists($accid, self::$accounts)) {
				$db  = App::db();
				$acc = $db->queryOne('SELECT * FROM {wx_account} WHERE id=%d LIMIT 0,1', $accid);
				if (!$acc) {
					self::$accounts[ $accid ] = null;

					return null;
				}
				$config = [
					'app_id'        => $acc['app_id'],
					'secret'        => $acc['app_secret'],
					'token'         => $acc['token'],
					'response_type' => 'array',
					'log'           => [
						'level' => APP_MODE == 'pro' ? 'error' : 'debug',
						'file'  => LOGS_PATH . '/wechat.log',
					],
					'http'          => [
						'retries'     => 3,
						'retry_delay' => 1000,
						'timeout'     => 5.0
					],
				];
				if ($acc['mode'] != 'T') {
					$config['aes_key'] = $acc['aeskey'];
				}

				$app                      = Factory::officialAccount($config);
				self::$accounts[ $accid ] = $app;
			}

			return self::$accounts[ $accid ];
		} catch (\Exception $e) {
			return null;
		}
	}
}