<?php

class CurlHelper {

	private $ch;
	private $response;
	private $url;
	private $options;
	
	public function __construct($url = '', $options = array()){
		$this->url = $url;
		$this->options = $options;
		$this->response = '';
		$this->ch = null;
	}
	
	public function run($url = ''){
		$this->init();
		$this->setUrl($url);
		$this->setOptions();
		$this->execute();
		$this->close();
	}
	
	public function init(){
		$this->ch = curl_init();
	}
	
	public function setUrl($url = ''){
		$this->url = $url;
		curl_setopt($this->ch, CURLOPT_URL, $this->url);
	}
	
	public function setOptions($options = array(CURLOPT_USERAGENT => 'Firefox (WindowsXP) - Mozilla/5.0 (Windows; U; Windows NT 5.1; en-GB; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6', CURLOPT_FAILONERROR => true, CURLOPT_AUTOREFERER => true, CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 30)){
		$this->options = $options;
		foreach($this->options as $k => $v){
			curl_setopt($this->ch, $k, $v);
		}
	}
	
	public function execute(){
		if($this->options[CURLOPT_RETURNTRANSFER]){
			$this->response = curl_exec($this->ch);
		} else {
			curl_exec($this->ch);
		}
	}
	
	public function getResponse(){
		return $this->response;
	}
	
	public function close(){
		curl_close($this->ch);
	}
	
}

