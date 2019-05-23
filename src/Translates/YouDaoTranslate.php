<?php
/**
 * +----------------------------------------------------------------------
 * | 有道翻译 [ https://ai.youdao.com/docs/doc-trans-api.s#p02 ]
 * +----------------------------------------------------------------------
 * | Copyright (c) 2015~2019 http://www.wmt.ltd All rights reserved.
 * +----------------------------------------------------------------------
 * | 版权所有：贵州鸿宇叁柒柒科技有限公司
 * +----------------------------------------------------------------------
 * | Author: shadow <admin@hongyuvip.com>  QQ: 1527200768
 * +----------------------------------------------------------------------
 * | Version: v1.0.0  Date:2019-05-23 Time:08:22
 * +----------------------------------------------------------------------
 */

namespace Hongyukeji\LaravelTranslate\Translates;

use BadMethodCallException;
use ErrorException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Hongyukeji\LaravelTranslate\Tokens\GoogleTokenGenerator;
use Hongyukeji\LaravelTranslate\Tokens\TokenProviderInterface;
use UnexpectedValueException;

class YouDaoTranslate
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
    protected $url = 'http://openapi.youdao.com/api';

    /**
     * @var array Dynamic GuzzleHttp client options
     */
    protected $options = [];

    /**
     * @var array URL Parameters
     */
    protected $urlParams = [
        'client'   => 't',
        'hl'       => 'en',
        'dt'       => [
            't',   // Translate
            'bd',  // Full translate with synonym ($bodyArray[1])
            'at',// Other translate ($bodyArray[5] - in google translate page this shows when click on translated word)
            'ex',  // Example part ($bodyArray[13])
            'ld',  // I don't know ($bodyArray[8])
            'md',  // Definition part with example ($bodyArray[12])
            'qca', // I don't know ($bodyArray[8])
            'rw',  // Read also part ($bodyArray[14])
            'rm',  // I don't know
            'ss'   // Full synonym ($bodyArray[11])
        ],
        'sl'       => null, // Source language
        'tl'       => null, // Target language
        'q'        => null, // String to translate
        'ie'       => 'UTF-8', // Input encoding
        'oe'       => 'UTF-8', // Output encoding
        'multires' => 1,
        'otf'      => 0,
        'pc'       => 1,
        'trs'      => 1,
        'ssel'     => 0,
        'tsel'     => 0,
        'kc'       => 1,
        'tk'       => null,
    ];

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
     */
    public function __construct(string $target = 'en', string $source = null, array $options = null, TokenProviderInterface $tokenProvider = null)
    {
        $this->client = new Client();
        $this->setTokenProvider($tokenProvider ?? new GoogleTokenGenerator)
            ->setOptions($options)// Options are already set in client constructor tho.
            ->setSource($source)
            ->setTarget($target);
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

    /**
     * Set target language for translation.
     *
     * @param string $target Language code
     * @return GoogleTranslate
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
     * @return GoogleTranslate
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
     * @return GoogleTranslate
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
     * @return GoogleTranslate
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
     * @return GoogleTranslate
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
     * @return null|string
     * @throws ErrorException If the HTTP request fails
     * @throws UnexpectedValueException If received data cannot be decoded
     */
    public static function trans(string $string, string $target = 'en', string $source = null, array $options = [], TokenProviderInterface $tokenProvider = null)
    {
        return (new self)
            ->setTokenProvider($tokenProvider ?? new GoogleTokenGenerator)
            ->setOptions($options)// Options are already set in client constructor tho.
            ->setSource($source)
            ->setTarget($target)
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

        /*
         * if response in text and the content has zero the empty returns true, lets check
         * if response is string and not empty and create array for further logic
         */
        if (is_string($responseArray) && $responseArray != '') {
            $responseArray = [$responseArray];
        }

        // Check if translation exists
        if (!isset($responseArray[0]) || empty($responseArray[0])) {
            return null;
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
            if (is_array($responseArray[0])) {
                return (string)array_reduce($responseArray[0], function ($carry, $item) {
                    $carry .= $item[0];
                    return $carry;
                });
            } else {
                return (string)$responseArray[0];
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
        $queryArray = array_merge($this->urlParams, [
            'sl' => $this->source,
            'tl' => $this->target,
            'tk' => $this->tokenProvider->generateToken($this->source, $this->target, $string),
        ]);

        $queryUrl = preg_replace('/%5B(?:[0-9]|[1-9][0-9]+)%5D=/', '=', http_build_query($queryArray));

        $queryBodyArray = [
            'q' => $string,
        ];

        $queryBodyEncoded = preg_replace('/%5B(?:[0-9]|[1-9][0-9]+)%5D=/', '=', http_build_query($queryBodyArray));

        try {
            $response = $this->client->post($this->url, [
                    'query' => $queryUrl,
                    'body'  => $queryBodyEncoded,
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

    public function user_translate($string)
    {
        $result = $this->do_request($string, $this->target);
        $result = json_decode($result, true);
        return isset($result['translation'][0]) ? $result['translation'][0] : '';
    }

    function do_request($q, $target)
    {
        $salt = $this->create_guid();
        $args = array(
            'q'      => $q,
            'appKey' => $this->appid,
            'salt'   => $salt,
        );
        $args['from'] = $this->source;
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