<?php

// helpers
require_once('class.curlhelper.php');
require_once('class.neweggfeedreader.php');
require_once('class.xmlrpc.php');
require_once('class.amazonautoposter.php');

// app settings
$neweggFeedUri = 'http://www.newegg.com/RSS/Index.aspx';
$amazonSearchUri = 'http://www.amazon.com/s/ref=nb_ss_gw?url=search-alias%3Daps&field-keywords=';
$amazonAffiliateId = 'sample_aff_id';
$wpRpcUser = 'rpc_enabled_username';
$wpRpcPass = 'rpc_enabled_password';
$wpRpcUri = 'http://example.com/xmlrpc.php';

// if comment is being POST'd fire off the auto poster
if($_SERVER['REQUEST_METHOD'] == 'POST'){
	try{
		$curl = new CurlHelper();
		$curl->run($neweggFeedUri);
		$response = $curl->getResponse();
		$reader = new NewEggFeedReader();
		if($feedUrl = $reader->getRandomFeedUrl($response)){
			$curl->run($feedUrl);
			$response = $curl->getResponse();
			if($keyword = $reader->getKeyword($response)){
				$searchUrl = $amazonSearchUri.$keyword.'&x=0&y=0';
				$curl->run($searchUrl);
				$response = $curl->getResponse();
				$ap = new AmazonAutoPoster($curl,$amazonAffiliateId,'now');
				$ap->createPost($response, $keyword, $reader->getCategory()); // TODO: make this createPosts with passing in post count
				$xmlRpc = new XmlRpc($wpRpcUser,$wpRpcPass,$wpRpcUri, $ap);
				$xmlRpc->sendPostRequest();
				preg_match('/<int>(.*)<\/int>/Us',$xmlRpc->getResponse(),$matches);
				$xmlRpc->sendUploadFileRequest($matches[1]);			
				// include('wp-file-attacher.php'); // TODO: add this file to project.. just a SQL statement to alter the WP tables that gets cron'd
				// var_dump($xmlRpc->getResponse());
			} else {
				// echo('no keyword found in feed');
			}
		} else {
			// echo('no feed url found');
		}
	} catch(Exception $e) {
		// die('Exception caught : ' . $e->getMessage());
	}
}

