<?php

class XmlRpc {

	private $_ap;
	private $url;
	private $user;
	private $pass;
	private $request;
	private $response;
	
	public function __construct($user = '', $pass = '', $url = '', AmazonAutoPoster $_ap = null){
		$this->_ap = $_ap;
		$this->url = $url;
		$this->pass = $pass;
		$this->user = $user;
		$this->request = '';
		$this->response = '';
	}
	
	public function sendPostRequest(){
		$xml = "<title>".$this->_ap->getTitle()."</title><category>".$this->_ap->getCategory()."</category>".$this->_ap->getContent();
		$params = array('','', $this->user, $this->pass, $xml, 1);
		$request = xmlrpc_encode_request('blogger.newPost', $params);
		$this->_ap->_curl->init();
		$this->_ap->_curl->setUrl($this->url);
		$this->_ap->_curl->setOptions(array(CURLOPT_POSTFIELDS => $request, CURLOPT_USERAGENT => 'Firefox (WindowsXP) - Mozilla/5.0 (Windows; U; Windows NT 5.1; en-GB; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6', CURLOPT_FAILONERROR => true, CURLOPT_AUTOREFERER => true, CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 30));
		$this->_ap->_curl->execute();
		$this->_ap->_curl->close();
		$this->response = $this->_ap->_curl->getResponse();
	}
	
	public function sendUploadFileRequest($blogId = ''){
		$data = array(
			'name' => $blogId."-".$this->_ap->getFileName(),
			'type' => $this->_ap->getFileType(),
			'bits' => $this->_ap->getFileData(),
			'overwrite' => true
		);
		$params = array('', $this->user, $this->pass, $data, 1);
		$request = xmlrpc_encode_request('wp.uploadFile', $params);
		$this->_ap->_curl->init();
		$this->_ap->_curl->setUrl($this->url);
		$this->_ap->_curl->setOptions(array(CURLOPT_POSTFIELDS => $request, CURLOPT_USERAGENT => 'Firefox (WindowsXP) - Mozilla/5.0 (Windows; U; Windows NT 5.1; en-GB; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6', CURLOPT_FAILONERROR => true, CURLOPT_AUTOREFERER => true, CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 30));
		$this->_ap->_curl->execute();
		$this->_ap->_curl->close();
		$this->response = $this->_ap->_curl->getResponse();
	}
	
	public function getResponse(){
		return $this->response;		
	}
	
}

