<?php

namespace Hongyukeji\LaravelTranslate\Translators;

use GuzzleHttp\Client;
use Hongyukeji\LaravelTranslate\Exceptions\LanguageCodeNotExist;

class BaiDuTranslator implements TranslatorInterface
{
    protected $translator;
    protected $source;
    protected $target;

    public function __construct()
    {
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
        $text = $string;
        // 实例化 HTTP 客户端
        $http = new Client;
        // 初始化配置信息
        $api = 'http://api.fanyi.baidu.com/api/trans/vip/translate?';
        $appid = config('translate.services.baidu.appid');
        $key = config('translate.services.baidu.key');
        $salt = time();

        // 根据文档，生成 sign
        // http://api.fanyi.baidu.com/api/trans/product/apidoc
        // appid+q+salt+密钥 的MD5值
        $sign = md5($appid . $text . $salt . $key);

        // 构建请求参数
        $query = http_build_query([
            "q"     => $text,
            "from"  => "auto",
            "to"    => $this->target,
            "appid" => $appid,
            "salt"  => $salt,
            "sign"  => $sign,
        ]);

        // 发送 HTTP Get 请求
        $response = $http->get($api . $query);

        $result = json_decode($response->getBody(), true);

        /**
         * 获取结果，如果请求成功，dd($result) 结果如下：
         *
         * array:3 [▼
         * "from" => "zh"
         * "to" => "en"
         * "trans_result" => array:1 [▼
         * 0 => array:2 [▼
         * "src" => "XSS 安全漏洞"
         * "dst" => "XSS security vulnerability"
         * ]
         * ]
         * ]
         **/

        // 尝试获取获取翻译结果
        if (isset($result['trans_result'][0]['dst'])) {
            return $result['trans_result'][0]['dst'];
        } else {
            return '';
        }
    }
}
