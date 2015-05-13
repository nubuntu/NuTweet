<?php
/* 
	NuTweet - Simple PHP Twitter Api
	created by Noercholis
	noercholis.com.or.die
*/

class NuTweet{
    private $oauth,$postdata=array();
	const USER_TIMELINE = "https://api.twitter.com/1.1/statuses/user_timeline.json";
	const DIRECT_MESSAGE="https://api.twitter.com/1.1/direct_messages.json";
	const DIRECT_MESSAGE_NEW="https://api.twitter.com/1.1/direct_messages/new.json";
	const MENTIONS_TIMELINE="https://api.twitter.com/1.1/statuses/mentions_timeline.json";
	const HOME_TIMELINE="https://api.twitter.com/1.1/statuses/home_timeline.json";
	const UPDATE_STATUS="https://api.twitter.com/1.1/statuses/update.json";
	const FOLLOWERS_LIST="https://api.twitter.com/1.1/followers/list.json";
    var $oauth_access_token,$oauth_access_token_secret,$consumer_key,$consumer_secret,$method;
	function get($url,$data=array()){
		$this->method="GET";
		$this->setData($data);
		return $this->api($url);
	}
	function post($url,$data=array()){
		$this->method="POST";
		$this->setData($data);
		return $this->api($url);
	}
	private function setData($data){
		$this->postdata=array();
		if(count($data)>=1){
			foreach($data as $key=>$val){
				$this->postdata[$key]=$val;
			}
		}
	}
	private function api($url){
		$this->buildOauth($url);
		$header = array($this->buildAuthorizationHeader(), 'Expect:');
		$options = array( CURLOPT_HTTPHEADER => $header,
						  CURLOPT_HEADER => false,
						  CURLOPT_URL => $url,
						  CURLOPT_RETURNTRANSFER => true,
						  CURLOPT_SSL_VERIFYPEER => false);
		if($this->method=="GET"&&count($this->postdata)>=1){
			$options[CURLOPT_URL].="?".http_build_query($this->postdata);
		}	
		if($this->method=="POST"){
			$options[CURLOPT_POSTFIELDS]=$this->postdata;
		}
		$ch = curl_init();
		curl_setopt_array($ch, $options);
		$json = curl_exec($ch);
		curl_close($ch);
		return $json;
	}
	private function buildOauth($url){
		$this->oauth = array( 'oauth_consumer_key' => $this->consumer_key,
						'oauth_nonce' => time(),
						'oauth_signature_method' => 'HMAC-SHA1',
						'oauth_token' => $this->oauth_access_token,
						'oauth_timestamp' => time(),
						'oauth_version' => '1.0',
						);	
		if($this->method=="GET"&&count($this->postdata)>=1){
			foreach($this->postdata as $key=>$val){
				$this->oauth[$key]=$val;
			}
		}
		$base_info = $this->buildBaseString($url);
		$composite_key = rawurlencode($this->consumer_secret) . '&' . rawurlencode($this->oauth_access_token_secret);
		$oauth_signature = base64_encode(hash_hmac('sha1', $base_info, $composite_key, true));
		$this->oauth['oauth_signature'] = $oauth_signature;
	}
    private function buildBaseString($baseURI) {
        $r = array();
        ksort($this->oauth);
        foreach($this->oauth as $key=>$value){
            $r[] = "$key=" . rawurlencode($value);
        }
        return $this->method."&" . rawurlencode($baseURI) . '&' . rawurlencode(implode('&', $r));
    }

    private function buildAuthorizationHeader() {
        $r = 'Authorization: OAuth ';
        $values = array();
        foreach($this->oauth as $key=>$value)
            $values[] = "$key=\"" . rawurlencode($value) . "\"";
        $r .= implode(', ', $values);
        return $r;
    }	
}
?>