<?php
/**
 * +----------------------------------------------------------------------
 * | laravel-translate [ File Description ]
 * +----------------------------------------------------------------------
 * | Copyright (c) 2015~2019 http://www.wmt.ltd All rights reserved.
 * +----------------------------------------------------------------------
 * | 版权所有：贵州鸿宇叁柒柒科技有限公司
 * +----------------------------------------------------------------------
 * | Author: shadow <admin@hongyuvip.com>  QQ: 1527200768
 * +----------------------------------------------------------------------
 * | Version: v1.0.0  Date:2019-05-22 Time:20:49
 * +----------------------------------------------------------------------
 */

namespace Hongyukeji\LaravelTranslate\Services;

use Hongyukeji\LaravelTranslate\Contracts\TranslationService;

class YouDao implements TranslationService
{
    const CURL_TIMEOUT = 2000;
    const URL = 'http://openapi.youdao.com/api';
    public $appid;
    public $key;

    public function __construct()
    {
        $appid = config('translate.services.youdao.appid');
        $key = config('translate.services.youdao.key');
        $this->appid = $appid;
        $this->key = $key;
    }

    /**
     * Translate a string using the Google Cloud Translate API.
     *
     * @see https://ai.youdao.com/docs/doc-trans-api.s#p02
     *
     * @param string $text
     * @param string $target
     * @return string|null
     */
    public function translate(string $text, string $target): ?string
    {
        $ret = $this->do_request($text, $target);
        $ret = json_decode($ret, true);
        return isset($ret['translation']) ?: $text;
    }

    function do_request($q, $target)
    {
        $salt = $this->create_guid();
        $args = array(
            'q'      => $q,
            'appKey' => $this->appid,
            'salt'   => $salt,
        );
        $args['from'] = 'auto';
        $args['to'] = $target;
        $args['signType'] = 'v3';
        $curtime = strtotime("now");
        $args['curtime'] = $curtime;
        $signStr = $this->appid . $this->truncate($q) . $salt . $curtime . $this->key;
        $args['sign'] = hash("sha256", $signStr);
        $ret = $this->call(self::URL, $args);
        return $ret;
    }

    // 发起网络请求
    function call($url, $args = null, $method = "post", $testflag = 0, $timeout = self::CURL_TIMEOUT, $headers = array())
    {
        $ret = false;
        $i = 0;
        while ($ret === false) {
            if ($i > 1)
                break;
            if ($i > 0) {
                sleep(1);
            }
            $ret = $this->callOnce($url, $args, $method, false, $timeout, $headers);
            $i++;
        }
        return $ret;
    }

    function callOnce($url, $args = null, $method = "post", $withCookie = false, $timeout = self::CURL_TIMEOUT, $headers = array())
    {
        $ch = curl_init();
        if ($method == "post") {
            $data = $this->convert($args);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_POST, 1);
        } else {
            $data = $this->convert($args);
            if ($data) {
                if (stripos($url, "?") > 0) {
                    $url .= "&$data";
                } else {
                    $url .= "?$data";
                }
            }
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        if ($withCookie) {
            curl_setopt($ch, CURLOPT_COOKIEJAR, $_COOKIE);
        }
        $r = curl_exec($ch);
        curl_close($ch);
        return $r;
    }

    function convert(&$args)
    {
        $data = '';
        if (is_array($args)) {
            foreach ($args as $key => $val) {
                if (is_array($val)) {
                    foreach ($val as $k => $v) {
                        $data .= $key . '[' . $k . ']=' . rawurlencode($v) . '&';
                    }
                } else {
                    $data .= "$key=" . rawurlencode($val) . "&";
                }
            }
            return trim($data, "&");
        }
        return $args;
    }

    // uuid generator
    function create_guid()
    {
        $microTime = microtime();
        list($a_dec, $a_sec) = explode(" ", $microTime);
        $dec_hex = dechex($a_dec * 1000000);
        $sec_hex = dechex($a_sec);
        $this->ensure_length($dec_hex, 5);
        $this->ensure_length($sec_hex, 6);
        $guid = "";
        $guid .= $dec_hex;
        $guid .= $this->create_guid_section(3);
        $guid .= '-';
        $guid .= $this->create_guid_section(4);
        $guid .= '-';
        $guid .= $this->create_guid_section(4);
        $guid .= '-';
        $guid .= $this->create_guid_section(4);
        $guid .= '-';
        $guid .= $sec_hex;
        $guid .= $this->create_guid_section(6);
        return $guid;
    }

    function create_guid_section($characters)
    {
        $return = "";
        for ($i = 0; $i < $characters; $i++) {
            $return .= dechex(mt_rand(0, 15));
        }
        return $return;
    }

    function truncate($q)
    {
        $len = strlen($q);
        return $len <= 20 ? $q : (substr($q, 0, 10) . $len . substr($q, $len - 10, $len));
    }

    function ensure_length(&$string, $length)
    {
        $strlen = strlen($string);
        if ($strlen < $length) {
            $string = str_pad($string, $length, "0");
        } else if ($strlen > $length) {
            $string = substr($string, 0, $length);
        }
    }
}