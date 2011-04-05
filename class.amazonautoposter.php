<?php

class AmazonAutoPoster {

	public $_curl;
	private $affid;
	private $pubTime;
	private $targetUrl;
	private $text;
	private $postTitle;
	private $content;
	private $keyword;
	private $category;
	private $description;
	private $thumb;
	private $dom;
	private $fileData;
	private $fileName;
	private $fileType;
		
	public function __construct(CurlHelper $_curl, $affid = 'affid', $pubTime = 'now'){
		$this->_curl = $_curl;
		$this->dom = new DOMDocument();
		$this->affid = $affid;
		$this->pubTime = $pubTime;
		$this->targetUrl = '';
		$this->text = '';
		$this->postTitle = '';
		$this->content = '';
		$this->thumb = '';
		$this->description = '';
		$this->category = '';
		$this->keyword = '';
		$this->fileName = '';
		$this->fileData = '';
		$this->fileType = '';
	}
	
	private function buildTargetUrl($html = ''){
		if($html != ''){
			@$this->dom->loadHTML($html);
			$xpath = new DOMXPath($this->dom);
			$paras = $xpath->query("//div[@class='title']/a");
			$para = $paras->item(0);
			if($para != '' || $para != null){
				$url = '';
				$url = $para->getAttribute('href');
				$url .= "?ie=UTF8&tag=".$this->affid;
				$this->targetUrl = $url;
			} else { 
				throw new Exception('bulding target url failed');
			}
			$this->text = $para->textContent;
		}
	}
	
	private function buildPostTitle($html = ''){
		if($html != ''){
			@$this->dom->loadHTML($html);
			$xpath = new DOMXPath($this->dom);
			$paras = $xpath->query("//h1[@class='parseasinTitle']/span");
			$para = $paras->item(0);
			$this->postTitle = $para->textContent;
		}
	}
	
	private function buildThumbnail($html = ''){
		if($html != ''){
			@$this->dom->loadHTML($html);
			$xpath = new DOMXPath($this->dom);
			$imgs = $xpath->query("//table//td//*[@id='prodImageCell']//img");
			$img = $imgs->item(0);
			$src = $img->getAttribute('src');
			$alt = $img->getAttribute('alt');
			$this->thumb = '<a href="' . $this->getTargetUrl() . '"><img style="float:left;width: 150px;height:150px;margin-right: 10px;" src="' . $src . '" alt="' . $alt . '" /></a>';
			$ext = substr($src,-3);
			switch($ext){
				case 'jpg':
					$this->fileType = 'image/jpeg';	
				break;
				case 'gif':
					$this->fileType = 'image/gif';
				break;
				case 'png':
					$this->fileType = 'image/png';
				break;
				default:
					throw new Exception('unknown image type');				
				break;
			}
			$this->fileName = md5(rand(0,1024)).".".$ext;
			$fd = file_get_contents($src);
			xmlrpc_set_type($fd,'base64');
			$this->fileData = $fd;
		}
	}
	
	private function buildDescription($html = ''){
		if($html != ''){
			@$this->dom->loadHTML($html);
			$xpath = new DOMXPath($this->dom);
			$paras = $xpath->query("//div[@id='productDescription']/div[@class='content']");
			$para = $paras->item(0);
			$para2 = $para->textContent;
			$patterns = array(
				'/Product Description/sU',
				'/Amazon.com Product Description/sU',
				'/Manufacturer\'s Description/sU',
				'/Amazon.com/sU',
				'/From the Manufacturer/sU'
			);
			$para2 = preg_replace($patterns, '', $para2);
			$this->description = $para2;
		}
	}
	
	private function buildContent($response = ''){
		$this->buildPostTitle($response);
		$this->buildThumbnail($response);
		$this->buildDescription($response);
		$this->content = $this->thumb . $this->description;
	}
	
	public function createPost($html = '', $keyword = '', $category = ''){
		$this->keyword = $keyword;
		$this->category = $category;
		$this->buildTargetUrl($html);
		$this->_curl->run($this->targetUrl);
		$response = '';
		$response = $this->_curl->getResponse();
		$this->buildContent($response);
	}
	
	public function getFileType(){
		return $this->fileType;
	}
	
	public function getFileData(){
		return $this->fileData;
	}
	
	public function getFileName(){
		return $this->fileName;
	}
	
	public function getContent(){
		return $this->content;
	}
	
	public function getTitle(){
		return $this->postTitle;
	}
	
	public function getThumbnail(){
		return $this->thumb;
	}
	
	public function getDescription(){
		return $this->description;
	}
	
	public function getTargetUrl(){
		return $this->targetUrl;
	}
	
	public function getKeyword(){
		return $this->keyword;
	}
	
	public function getCategory(){
		return $this->category;
	}

}

