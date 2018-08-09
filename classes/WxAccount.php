<?php
/*
 * This file is part of wulacms.
 *
 * (c) Leo Ning <windywany@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace weixin\classes;

use EasyWeChat\Factory;
use EasyWeChat\OfficialAccount\Application;
use Psr\SimpleCache\InvalidArgumentException;
use wulaphp\app\App;

/**
 * Class WxAccount
 * @package weixin\classes
 * @property-read Application                          $app      App
 * @property-read \EasyWeChat\MiniProgram\Application  $miniApp
 * @property-read \EasyWeChat\OpenPlatform\Application $openApp
 * @property-read \EasyWeChat\Work\Application         $workApp
 * @property-read string                               $baseUrl  基本URL
 * @property-read string                               $appid    APPID
 * @property-read string                               $wxid     微信号
 * @property-read string                               $originId 原始ID
 * @property-read string                               $token
 * @property-read string                               $name
 * @property-read string                               $type
 * @property-read bool                                 $authed
 * @property-read bool                                 $debug
 * @property-read array                                $info
 */
class WxAccount {
	public const TYPES = [
		'DY' => '订阅号',
		'FW' => '服务号',
		'QY' => '企业号',
		'XC' => '小程序',
		'XY' => '小游戏',
		'OP' => '开放平台',
		'Sf' => '三方平台'
	];
	/**
	 * @var \weixin\classes\WxAccount[]
	 */
	private static $accounts = [];
	private static $infos    = [];
	/**
	 * @var \EasyWeChat\OfficialAccount\Application
	 */
	private $officialAapp;
	/**
	 * @var array
	 */
	private $info;
	private $baseUrl;

	private function __construct(array $info) {
		$this->info = $info;
		if ($info['base_url']) {
			$this->baseUrl = untrailingslashit($info['base_url']);
		} else {
			$this->baseUrl = untrailingslashit(App::cfg('base_url@wx'));
		}
		$this->info['base_url'] = $this->baseUrl;
	}

	/**
	 * @param string $accid
	 *
	 * @return \weixin\classes\WxAccount
	 */
	public static function getWechat(string $accid): ?WxAccount {
		try {
			if (!array_key_exists($accid, self::$accounts)) {
				$config = self::cfg($accid, $acc);
				if (!$config) {
					self::$accounts[ $accid ] = null;

					return null;
				}
				switch ($acc['type']) {
					case 'XC':
					case 'XY':
						$app = Factory::miniProgram($config);
						break;
					case 'OP':
					case 'SF':
						$app = Factory::openPlatform($config);
						break;
					case 'QY':
						$app = Factory::work($config);
						break;
					default:
						$app = Factory::officialAccount($config);
				}

				$account                  = new self($acc);
				$account->officialAapp    = $app;
				self::$accounts[ $accid ] = $account;
			}

			return self::$accounts[ $accid ];
		} catch (\Exception $e) {
			self::$accounts[ $accid ] = null;

			return null;
		}
	}

	/**
	 * 获取系统默认的微信公众号.
	 *
	 * @return null|\weixin\classes\WxAccount
	 */
	public static function getDefaultWechat(): ?WxAccount {
		try {
			$wxid = App::cfg('wxid@wx');
			if ($wxid) return self::getWechat($wxid);
		} catch (\Exception $e) {
		}

		return null;
	}

	/**
	 * 公众号信息.
	 *
	 * @param string $accid id或wxid
	 *
	 * @return array|null 公众号信息.
	 */
	public static function info(string $accid): ?array {
		try {
			if (!array_key_exists($accid, self::$infos)) {
				//TODO: 将来这个地要加缓存，不能每次都从数据库读
				$db = App::db();
				if (preg_match('/^[1-9]\d*$/', $accid)) {
					$acc = $db->queryOne('SELECT * FROM {wx_account} WHERE id=%d LIMIT 0,1', $accid);
				} else {
					$acc = $db->queryOne('SELECT * FROM {wx_account} WHERE wxid=%s LIMIT 0,1', $accid);
				}
				self::$infos[ $accid ] = $acc;
			}

			return self::$infos[ $accid ];
		} catch (\Exception $e) {
			self::$infos[ $accid ] = null;
		}

		return null;
	}

	/**
	 * @param string $accid
	 * @param        $acc
	 *
	 * @return array|null
	 */
	public static function cfg(string $accid, &$acc = null): ?array {
		$acc = self::info($accid);
		if (!$acc) return null;
		$config = [
			'app_id'        => $acc['app_id'],
			'secret'        => $acc['app_secret'],
			'token'         => $acc['token'],
			'response_type' => 'array',
			'log'           => [
				'level' => $acc['debug'] ? 'debug' : 'error',
				'file'  => LOGS_PATH . 'wechat.log',
			],
			'http'          => [
				'retries'     => 3,
				'retry_delay' => 1000,
				'timeout'     => 5.0
			],
		];
		switch ($acc['type']) {
			case 'XC':
			case 'XY':
				unset($config['token']);
				$config['log']['file'] = LOGS_PATH . 'wx_' . strtoupper($acc['type']) . '.log';

				break;
			case 'OP':
			case 'SF':
				$config['aes_key']     = $acc['aeskey'];
				$config['log']['file'] = LOGS_PATH . 'wx_' . strtoupper($acc['type']) . '.log';

				break;
			case 'QY':
				unset($config['app_id'], $config['token']);
				$config['agent_id']    = $acc['token'];
				$config['corp_id']     = $acc['app_id'];
				$config['log']['file'] = LOGS_PATH . 'wx_' . strtoupper($acc['type']) . '.log';

				break;
			default:
				if ($acc['mode'] != 'T') {
					$config['aes_key'] = $acc['aeskey'];
				}
		}

		return $config;
	}

	/**
	 * jssdk签名.
	 *
	 * @param array  $apis
	 * @param string $url
	 *
	 * @return string
	 */
	public function wxSign(array $apis, string $url = ''): string {
		try {
			$app = $this->app;
			$app->jssdk->setUrl($url);

			return $app->jssdk->buildConfig($apis, $this->debug, false);
		} catch (\Exception $e) {
			log_warn($e->getMessage(), 'wx_sign');
		} catch (InvalidArgumentException $e) {
			log_warn($e->getMessage(), 'wx_sign');
		}

		return '';
	}

	/**
	 * 生成URL.
	 *
	 * @param string $url
	 *
	 * @return string
	 */
	public function url(string $url): string {
		return $this->baseUrl . App::url($url);
	}

	public function __get(string $name) {
		switch ($name) {
			case 'app':
			case 'officialAapp':
			case 'miniApp':
			case 'workApp':
			case 'openApp':
				return $this->officialAapp;
			case 'appid':
				return $this->info['app_id'];
			case 'appsecret':
				return $this->info['app_secret'];
			case 'wxid':
				return $this->info['wxid'];
			case 'originId':
				return $this->info['origin_id'];
			case 'baseUrl':
				return $this->info['base_url'];
			case 'authed':
				return $this->info['authed'] == 1;
			case 'debug':
				return $this->info['debug'] == 1;
			default:
				return $this->info[ $name ] ?? null;
		}
	}
}