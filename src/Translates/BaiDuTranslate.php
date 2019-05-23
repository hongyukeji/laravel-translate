<?php
/**
 * +----------------------------------------------------------------------
 * | 百度翻译 [ http://api.fanyi.baidu.com/api/trans/product/apidoc ]
 * +----------------------------------------------------------------------
 * | Copyright (c) 2015~2019 http://www.wmt.ltd All rights reserved.
 * +----------------------------------------------------------------------
 * | 版权所有：贵州鸿宇叁柒柒科技有限公司
 * +----------------------------------------------------------------------
 * | Author: shadow <admin@hongyuvip.com>  QQ: 1527200768
 * +----------------------------------------------------------------------
 * | Version: v1.0.0  Date:2019-05-23 Time:08:21
 * +----------------------------------------------------------------------
 */

namespace Hongyukeji\LaravelTranslate\Translates;

use BadMethodCallException;
use ErrorException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Hongyukeji\LaravelTranslate\Tokens\BaiDuTokenGenerator;
use Hongyukeji\LaravelTranslate\Tokens\TokenProviderInterface;
use UnexpectedValueException;

class BaiDuTranslate
{
    /**
     * @var string appId
     */
    protected $appId;

    /**
     * @var string key
     */
    protected $key;

    /**
     * @var string salt
     */
    protected $salt;

    /**
     * @var \GuzzleHttp\Client HTTP Client
     */
    protected $client;

    /**
     * @var string|null Source language - from where the string should be translated
     */
    protected $source;

    /**
     * @var string Target language - to which language string should be translated
     */
    protected $target;

    /**
     * @var string|null Last detected source language
     */
    protected $lastDetectedSource;

    /**
     * @var string Google Translate URL base
     */
    protected $url = 'http://api.fanyi.baidu.com/api/trans/vip/translate';

    /**
     * @var array Dynamic GuzzleHttp client options
     */
    protected $options = [];

    /**
     * @var array URL Parameters
     */
    protected $urlParams = [];

    /**
     * @var array Regex key-value patterns to replace on response data
     */
    protected $resultRegexes = [
        '/,+/'  => ',',
        '/\[,/' => '[',
    ];

    /**
     * @var TokenProviderInterface Token provider
     */
    protected $tokenProvider;

    /**
     * Class constructor.
     *
     * For more information about HTTP client configuration options, see "Request Options" in
     * GuzzleHttp docs: http://docs.guzzlephp.org/en/stable/request-options.html
     *
     * @param string $target Target language
     * @param string|null $source Source language
     * @param array|null $options Associative array of http client configuration options
     * @param TokenProviderInterface|null $tokenProvider
     * @param string|null $appId
     * @param string|null $key
     * @param string|null $salt
     */
    public function __construct(string $target = 'en', string $source = null, array $options = null, TokenProviderInterface $tokenProvider = null, string $appId = null, string $key = null, string $salt = null)
    {
        $this->client = new Client();
        $this->setTokenProvider($tokenProvider ?? new BaiDuTokenGenerator)
            ->setOptions($options)// Options are already set in client constructor tho.
            ->setSource($source)
            ->setTarget($target)
            ->setSalt($salt ?? rand(10000, 99999));
    }

    public function setAppId(string $appId): self
    {
        $this->appId = $appId;
        return $this;
    }

    public function setKey(string $key): self
    {
        $this->key = $key;
        return $this;
    }

    public function setSalt(string $salt): self
    {
        $this->salt = $salt;
        return $this;
    }

    /**
     * Set target language for translation.
     *
     * @param string $target Language code
     * @return BaiDuTranslate
     */
    public function setTarget(string $target): self
    {
        $this->target = $target;
        return $this;
    }

    /**
     * Set source language for translation.
     *
     * @param string|null $source Language code
     * @return BaiDuTranslate
     */
    public function setSource(string $source = null): self
    {
        $this->source = $source ?? 'auto';
        return $this;
    }

    /**
     * Set Google Translate URL base
     *
     * @param string $url Google Translate URL base
     * @return BaiDuTranslate
     */
    public function setUrl(string $url): self
    {
        $this->url = $url;
        return $this;
    }

    /**
     * Set GuzzleHttp client options.
     *
     * @param array $options guzzleHttp client options.
     * @return BaiDuTranslate
     */
    public function setOptions(array $options = null): self
    {
        $this->options = $options ?? [];
        return $this;
    }

    /**
     * Set token provider.
     *
     * @param TokenProviderInterface $tokenProvider
     * @return BaiDuTranslate
     */
    public function setTokenProvider(TokenProviderInterface $tokenProvider): self
    {
        $this->tokenProvider = $tokenProvider;
        return $this;
    }

    /**
     * Get last detected source language
     *
     * @return string|null Last detected source language
     */
    public function getLastDetectedSource()
    {
        return $this->lastDetectedSource;
    }

    /**
     * Override translate method for static call.
     *
     * @param string $string
     * @param string $target
     * @param string|null $source
     * @param array $options
     * @param TokenProviderInterface|null $tokenProvider
     * @param string|null $appId
     * @param string|null $key
     * @param string|null $salt
     * @return null|string
     * @throws ErrorException If the HTTP request fails
     */
    public static function trans(string $string, string $target = 'en', string $source = null, array $options = [], TokenProviderInterface $tokenProvider = null, string $appId = null, string $key = null, string $salt = null)
    {
        return (new self)
            ->setTokenProvider($tokenProvider ?? new BaiDuTokenGenerator)
            ->setOptions($options)// Options are already set in client constructor tho.
            ->setSource($source)
            ->setTarget($target)
            ->setSalt($salt)
            ->setKey($key)
            ->setAppId($appId)
            ->translate($string);
    }

    /**
     * Translate text.
     *
     * This can be called from instance method translate() using __call() magic method.
     * Use $instance->translate($string) instead.
     *
     * @param string $string String to translate
     * @return string|null
     * @throws ErrorException           If the HTTP request fails
     * @throws UnexpectedValueException If received data cannot be decoded
     */
    public function translate(string $string): string
    {
        $responseArray = $this->getResponse($string);

        if (empty($responseArray['trans_result'])) {
            dump($responseArray);
        }

        /*
         * if response in text and the content has zero the empty returns true, lets check
         * if response is string and not empty and create array for further logic
         */
        if (is_string($responseArray) && $responseArray != '') {
            $responseArray = [$responseArray];
        }

        // Check if translation exists
        if (!isset($responseArray['trans_result']) || empty($responseArray['trans_result'])) {
            return '';
        }

        // Detect languages
        $detectedLanguages = [];

        // the response contains only single translation, don't create loop that will end with
        // invalid foreach and warning
        if (!is_string($responseArray)) {
            foreach ($responseArray as $item) {
                if (is_string($item)) {
                    $detectedLanguages[] = $item;
                }
            }
        }

        // Another case of detected language
        if (isset($responseArray[count($responseArray) - 2][0][0])) {
            $detectedLanguages[] = $responseArray[count($responseArray) - 2][0][0];
        }

        // Set initial detected language to null
        $this->lastDetectedSource = null;

        // Iterate and set last detected language
        foreach ($detectedLanguages as $lang) {
            if ($this->isValidLocale($lang)) {
                $this->lastDetectedSource = $lang;
                break;
            }
        }

        // the response can be sometimes an translated string.
        if (is_string($responseArray)) {
            return $responseArray;
        } else {
            if (is_array($responseArray['trans_result'])) {
                return (string)array_reduce($responseArray['trans_result'], function ($carry, $item) {
                    $carry .= $item['dst'];
                    return $carry;
                });
            } else {
                return (string)$responseArray['trans_result'][0]['dst'];
            }
        }
    }

    /**
     * Get response array.
     *
     * @param string $string String to translate
     * @throws ErrorException           If the HTTP request fails
     * @throws UnexpectedValueException If received data cannot be decoded
     * @return array|string Response
     */
    public function getResponse(string $string): array
    {
        $formParams = [
            'q'     => $string,
            'from'  => strtolower($this->source),
            'to'    => strtolower($this->target),
            'appid' => $this->appId,
            'salt'  => $this->salt,
            'sign'  => $this->tokenProvider->generateToken(strtolower($this->source), strtolower($this->target), $string, $this->appId, $this->key, $this->salt),
        ];

        try {
            $response = $this->client->post($this->url, [
                    'form_params' => $formParams,
                ] + $this->options);
        } catch (RequestException $e) {
            throw new ErrorException($e->getMessage());
        }

        $body = $response->getBody(); // Get response body

        // Modify body to avoid json errors
        $bodyJson = preg_replace(array_keys($this->resultRegexes), array_values($this->resultRegexes), $body);

        // Decode JSON data
        if (($bodyArray = json_decode($bodyJson, true)) === null) {
            throw new UnexpectedValueException('Data cannot be decoded or it is deeper than the recursion limit');
        }

        return $bodyArray;
    }

    /**
     * Check if given locale is valid.
     *
     * @param string $lang Langauge code to verify
     * @return bool
     */
    protected function isValidLocale(string $lang): bool
    {
        return (bool)preg_match('/^([a-z]{2})(-[A-Z]{2})?$/', $lang);
    }

    function use_translate($string)
    {
        $result = $this->do_request($string, $this->source, $this->target);
        return isset($result['trans_result'][0]['dst']) ? $result['trans_result'][0]['dst'] : '';
    }

    function do_request($query, $from, $to)
    {
        $args = array(
            'q'     => $query,
            'appid' => $this->appid,
            'salt'  => rand(10000, 99999),
            'from'  => $from,
            'to'    => $to[0],
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