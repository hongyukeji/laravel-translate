<?php

namespace Hongyukeji\LaravelTranslate\Translators;

class BaiDuTranslator implements TranslatorInterface
{
    const CURL_TIMEOUT = 10;
    const URL = 'http://api.fanyi.baidu.com/api/trans/vip/translate';
    public $appid;
    public $key;
    protected $translator;
    protected $source;
    protected $target;

    public function __construct()
    {
        $this->appid = config('translate.services.baidu.appid');
        $this->key = config('translate.services.baidu.key');
        $this->source = config('translate.source_language');
        $this->target = config('translate.target_language');
    }

    public function setSource(string $source)
    {
        $this->source = strtoupper($source);

        return $this;
    }

    public function setTarget(string $target)
    {
        $this->target = strtoupper($target);

        return $this;
    }

    /**
     * 百度翻译
     *
     * @see http://api.fanyi.baidu.com/api/trans/product/apidoc
     *
     * @param string $string
     * @return string
     */
    public function translate(string $string): string
    {
        $result = $this->do_request($string, 'auto', $this->target);
        return isset($result['trans_result'][0]['dst']) ? $result['trans_result'][0]['dst'] : '';
    }

    function do_request($query, $from, $to)
    {
        $args = array(
            'q'     => $query,
            'appid' => $this->appid,
            'salt'  => rand(10000, 99999),
            'from'  => $from,
            'to'    => $to,
        );
        $args['sign'] = $this->buildSign($query, $this->appid, $args['salt'], $this->key);
        $ret = $this->call(self::URL, $args);
        $ret = json_decode($ret, true);
        return $ret;
    }

    //加密
    function buildSign($query, $appID, $salt, $secKey)
    {/*{{{*/
        $str = $appID . $query . $salt . $secKey;
        $ret = md5($str);
        return $ret;
    }/*}}}*/

    //发起网络请求
    function call($url, $args = null, $method = "post", $testflag = 0, $timeout = self::CURL_TIMEOUT, $headers = array())
    {/*{{{*/
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
    }/*}}}*/

    function callOnce($url, $args = null, $method = "post", $withCookie = false, $timeout = self::CURL_TIMEOUT, $headers = array())
    {/*{{{*/
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
    }/*}}}*/

    function convert(&$args)
    {/*{{{*/
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
    }/*}}}*/
}
