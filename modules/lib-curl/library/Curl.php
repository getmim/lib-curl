<?php
/**
 * Curl handler
 * @package lib-curl
 * @version 0.0.1
 */

namespace LibCurl\Library;

use Mim\Library\Fs;

class Curl
{
	private static $last_result_body = '';
	private static $last_result_type = '';
	private static $last_error = '';

    private static function addLog(array $opts, string $res, array $info): void{
        $id = gmdate('Y-m-d-H-i-s-') . uniqid();

        $data = [
            'time'      => gmdate('Y-m-d H:i:s'),
            'options'   => $opts,
            'response'      => [
                'data'      => $res,
                'info'      => $info
            ]
        ];

        $log_path = BASEPATH . '/etc/log/lib-curl/' . gmdate('Y/m/d/H') . '/' . $id;

        Fs::write($log_path, json_encode($data, JSON_PRETTY_PRINT));
    }

    private static function prepareResponse(array $opts, string $res, array $info){
        self::$last_result_body = $res ? $res : '';
        $content_type = $info['content_type'] ?? 'text/html';
        $content_type = explode(';', $content_type)[0];
        self::$last_result_type = $content_type;

        return self::lastResult();
    }

    private static function validateOpts(array $opts): bool{
        if(!filter_var($opts['url'], FILTER_VALIDATE_URL))
            return !(self::$last_error = 'Invalid URL');
        if(!in_array($opts['method'], ['GET', 'POST', 'PUT', 'DELETE']))
            return !(self::$last_error = 'Not supported request method');
        return true;
    }

	static function fetch(array $opts){
        $def_opts = [
            'body'      => [],
            'headers'   => [],
            'method'    => 'GET',
            'query'     => [],
            'agent'     => null,
            'timeout'   => 10,
            'referer'   => null,
            'download'  => null
        ];

        foreach($def_opts as $key => $def)
            $opts[$key] = $opts[$key] ?? $def;

        // validate opts
        if(!self::validateOpts($opts))
            return;

        $url = $opts['url'];
        if($opts['query']){
            $sign = strstr($opts['url'], '?') ? '&' : '?';
            $url.= $sign . http_build_query($opts['query']);
        }

        $opts['url'] = $url;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $opts['method']);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_TIMEOUT, $opts['timeout']);

        // user agent
        if($opts['agent'])
            curl_setopt($ch, CURLOPT_USERAGENT, $opts['agent']);

        // referer
        if($opts['referer'])
            curl_setopt($ch, CURLOPT_REFERER, $opts['referer']);
        else
            curl_setopt($ch, CURLOPT_AUTOREFERER, true);

        // skip ssl verification
        if(strstr($opts['url'], 'https://')){
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        }

        // request body
        if($opts['body'] && in_array($opts['method'], ['POST', 'PUT'])){
            $ctype = $opts['headers']['Content-Type'] ?? '';
            $ctype = explode(';', $ctype)[0];

            $data = $opts['body'];

            if($ctype == 'application/json'){
                $data = json_encode($data);
                $opts['headers']['Content-Length'] = strlen($data);
            }

            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        // request headers
        if($opts['headers']){
            $headers = [];
            foreach($opts['headers'] as $key => $val)
                $headers[] = $key . ': ' . $val;
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        // download file
        if($opts['download']){
            $f = fopen($opts['download'], 'w+');
            curl_setopt($ch, CURLOPT_FILE, $f);
        }

        $res = curl_exec($ch);
        $ch_info = curl_getinfo($ch);

        $log = \Mim::$app->config->libCurl->log;

        if(false === $res){
            $log = true;
            self::$last_error = curl_error($ch);
        }

        curl_close($ch);

        if($log)
            self::addLog($opts, $res, $ch_info);

        if($opts['download'])
            return fclose($f);

        return self::prepareResponse($opts, $res, $ch_info);
    }

    static function get(string $url, array $headers=null){
        return self::fetch([
            'url'     => $url,
            'headers' => $headers
        ]);
    }

    static function post(string $url, $body, array $headers=null){
        return self::fetch([
            'url'     => $url,
            'headers' => $headers,
            'body'    => $body,
            'method'  => 'POST'
        ]);
    }

    static function put(string $url, $body, array $headers=null){
        return self::fetch([
            'url'     => $url,
            'headers' => $headers,
            'body'    => $body,
            'method'  => 'PUT'
        ]);
    }

    static function download(string $url, string $file, array $headers=null): bool{
        return !!self::fetch([
            'url'      => $url,
            'headers'  => $headers,
            'download' => $file
        ]);
    }

    static function lastError(): string{
        return self::$last_error;
    }

    static function lastResult(bool $parse=true){
        $result = self::$last_result_body;

        if(!$parse)
            return $result;

        switch(self::$last_result_type){
            case 'application/json':
                $result = json_decode($result);
                break;
        }

        return $result;
    }
}