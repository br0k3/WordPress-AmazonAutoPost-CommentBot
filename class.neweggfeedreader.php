<?php

class NewEggFeedReader {
	
	private $keyword;
	private $category;
	
	public function __construct($keyword='', $category=''){
		$this->keyword = $keyword;
		$this->category = $category;
	}
	
	public function getKeyword($feed = ''){
		$keyword = '';
		$this->keyword = false;
		preg_match_all('/<title>(.*)<\/title>/iUs',$feed,$matches);
		$this->category = explode(' - ',$matches[1][0]);
		$this->category = end($this->category);
		if(count($matches[1]) > 2){
			$keyword = $matches[1][rand(2,count($matches[1])-1)];
			if(strstr($keyword, ' - ')){
				$k = explode(' - ',$keyword);
				$this->keyword = strtolower(str_replace(' ','-',end($k)));
			}
		}
		return $this->keyword;
	}
	
	public function getCategory(){
		return $this->category;
	}

	private function _getRandomFeedFromIndex($html = ''){
		$matches = array();
		preg_match('/<ul class="rssCat">(.*)<\/ul>/sUi',$html,$matches);
		return $matches[1];
	}
	
	public function getRandomFeedUrl($html = ''){
		$urls = '';
		$feedUrl = false;
		$urls = $this->_getRandomFeedFromIndex($html);
		preg_match_all('/<a href="(.*)"/sUi',$urls,$matches);
		$feedUrl = $matches[1][rand(0,count($matches[1]))];
		return $feedUrl;
	}
	
}

