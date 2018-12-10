<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

DEFINE("DEFAULT_TOKEN"  ,"EAAAAAYsX7TsBAPZCdoa30la3PqzinaxrCfimeHH23ln6q9CKB3tf9C5SoFiVHTEJP6QbkKNze1AIEkZB7mua9EAfKDrZCiDxI9YzUMzKDTOvRI3rCbnRMWWj9r3X1uHw6YAZCGbocWvMOEIvZBJzXuSjCW8USIjnNNHTBdE1cvbb6kRchkT4Jl7cOV57YXONbbSUqE4bQxUqJAZAaq8VoZB");

DEFINE("URL_GRAPH_V10"  ,"https://graph.facebook.com/v1.0/");

class CurlController extends Controller
{
    private $args 			= array(
    	'url'						=> "",
    	'method' 					=> 'GET',
    	'cookies' 					=> false,
    	'cookies_file'				=> '',
    	'cookies_path'				=> '/storage/cookies/cookies.txt',
    	'encoding'					=> "gzip, deflate, br",
    	'headers' 					=> [
                'Accept: */*',
                'Accept-Language: vi-VN,vi;q=0.9',
                "Cache-Control: no-cache",
    	],
    	'params' 					=> array(),
    	'proxy' 					=> false,
    	'proxy_tunnel'				=> false,
    	'proxy_auth'				=> CURLOPT_HTTPAUTH,
    	'proxy_ip' 					=> "127.0.0.1",
    	'proxy_port'				=> "80",
    	'proxy_type' 				=> CURLPROXY_HTTP,
    	'proxy_username'			=> "",
    	'proxy_password'			=> "",
    	'max_redirs'				=> 10,
    	'user_agent' 				=> "",
    	'timeout' 					=> 120000, // milisecond
    	'referer'					=> false,
    	'file'						=> false,
      'query'           => true,
    );

    /**
     * @param array $args
     * @return [exec, info. header, cookies, cookies_path]
     */
    protected function curl($args = [], $type_query = true){
    	$args['cookies_path'] 	= (isset($args['cookies_file']))? $this->getPathByName($args['cookies_file']) : $this->args['cookies_path'];
    	$args 					= array_merge($this->args, $args);
       	$user_agent 			= 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36 OPR/55.0.2994.44';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 					$args['url']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 		true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 		false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 		false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 		false);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, 	$args['timeout']); // timeout on connect
		curl_setopt($ch, CURLOPT_TIMEOUT_MS, 			$args['timeout']); // timeout on response
		curl_setopt($ch, CURLOPT_ENCODING, 				$args['encoding']);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 			$args['max_redirs']);
    	curl_setopt($ch, CURLOPT_HEADER, 				true);
		if($args['method'] == 'POST'){
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 	$args['method']);
            if($args['query'])
		  		curl_setopt($ch, CURLOPT_POSTFIELDS, 	http_build_query($args['params']));
		  	elseif(!$args['query'])
		  		curl_setopt($ch, CURLOPT_POSTFIELDS, 	$args['params']);
		}
		if($args['headers'])
        	curl_setopt($ch, CURLOPT_HTTPHEADER, 		$args['headers']); 
		if($args['proxy']){
			curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 	$args['proxy_tunnel']);
			curl_setopt($ch, CURLOPT_PROXYAUTH, 		$args['proxy_auth']);
			curl_setopt($ch, CURLOPT_PROXY, 			$args['proxy_ip']);
			curl_setopt($ch, CURLOPT_PROXYPORT, 		$args['proxy_port']);
			curl_setopt($ch, CURLOPT_PROXYTYPE, 		$args['proxy_type']);
			curl_setopt($ch, CURLOPT_PROXYUSERPWD, 		$args['proxy_username'] . ":" . $args['proxy_password']);
		}
        if($user_agent)
			curl_setopt($ch, CURLOPT_USERAGENT, 		$user_agent);
		if($args['cookies']){
			curl_setopt($ch, CURLOPT_COOKIE, 			$args['cookies']);
			curl_setopt($ch, CURLOPT_COOKIEJAR, 		$args['cookies_path']);
			curl_setopt($ch, CURLOPT_COOKIEFILE, 		$args['cookies_path']);
		}
		if($args['referer'])
			curl_setopt($ch, CURLOPT_REFERER, 			$args['referer']);
		if($args['method'] == 'DELETE')
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
		if($args['file']){
			curl_setopt($ch, CURLOPT_FILE, $args['file']);
     		curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
		}
		$response 	= curl_exec($ch);
	  	$info 		= curl_getinfo($ch);
		curl_close($ch);

		$header_size = $info['header_size'];
		$header = substr($response, 0, $header_size);
		$body 	= substr($response, $header_size);
		$cookies = "";
		if (!empty($header)) {
			preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $response, $matches);
			$matchAll = array();
			if (isset($matches[1])) {
				foreach($matches[1] as $item) {
				    parse_str($item, $cookie);
				    $matchAll = array_merge($matchAll, [$item]);
				}
			}
			$cookies = implode("; ", array_unique($matchAll));
		}

		return [
		  	'exec' 			=> $body,
		  	'info' 			=> (isset($info)) ? $info : "",
		  	'cookies' 		=> $cookies,
		  	'cookies_path' 	=> $args['cookies_path'],
		  	'user_agent' 	=> $user_agent,
		];
	}
}
