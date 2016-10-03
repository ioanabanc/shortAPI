<?php

require './libs/Slim/Slim.php';
require './libs/Hashids/Hashids.php';
 
\Slim\Slim::registerAutoloader();
use Hashids\Hashids;

DEFINE(SHORTHASH, 'q1w2e3r4t5');
 
$app = new \Slim\Slim();

include_once("Database.class.php");
include_once("Url.class.php");
include_once("UrlHandler.class.php");
include_once("UrlHitsHandler.class.php");
require_once './libs/MobileDetect/Mobile_Detect.php';

/**
 * Verifying required params posted or not
 */
function verifyRequiredParams($required_fields) {
    $error = false;
    $error_fields = "";
    $request_params = array();
    $request_params = $_REQUEST;
    // Handling PUT request params
    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        $app = \Slim\Slim::getInstance();
        parse_str($app->request()->getBody(), $request_params);
    }
    foreach ($required_fields as $field) {
        if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
            $error = true;
            $error_fields .= $field . ', ';
        }
    }
 
    if ($error) {
        // Required field(s) are missing or empty
        // echo error json and stop the app
        $response = array();
        $app = \Slim\Slim::getInstance();
        $response["error"] = true;
        $response["message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
        echoRespnse(400, $response);
        $app->stop();
    }
	else{
		//we are good to go - all needed params are provided
		return $request_params;
	}
    
}

/**
 * Verifying required params posted or not
 */
function verifyAtLeastOneRequiredParams($required_fields) {
	$error = true;
	$error_fields = implode(',', $required_fields);
	$request_params = array();
	$request_params = $_REQUEST;
	// Handling PUT request params
	if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
		$app = \Slim\Slim::getInstance();
		parse_str($app->request()->getBody(), $request_params);
	}
	foreach ($required_fields as $field) {
		if (isset($request_params[$field]) && strlen(trim($request_params[$field])) >= 0) {
			$error = false;
			break;
		}
	}
	if ($error) {
		// Required field(s) are missing or empty
		// echo error json and stop the app
		$response = array();
		$app = \Slim\Slim::getInstance();
		$response["error"] = true;
		$response["message"] = 'At least one of field(s) ' . $error_fields . ' is reqeuired ';
		echoRespnse(400, $response);
		$app->stop();
	}
	else{
		return $request_params;
	}
}

/**
 * Echoing json response to client
 * @param String $status_code Http response code
 * @param Int $response Json response
 */
function echoRespnse($status_code, $response) {
    $app = \Slim\Slim::getInstance();
    // Http response code
    $app->status($status_code);
 
    // setting response content type to json
    $app->contentType('application/json');
 
    echo json_encode($response);
}
/*
* Returns a list with all urls info
*/
$app->get('/info', function() use ($app) {
	$hashids = new Hashids(SHORTHASH);
	$oDb = new Database('shorten_url_db');
	$urlHandlerObj = new UrlHandler($oDb);
	$info = $urlHandlerObj->getInfo($hashids);
	echoRespnse(200, $info);
});

/*
* Retuns info on single url - not requested...
*/
$app->get('/info/:shortUrl', function($shortUrl) use ($app) {
	$hashids = new Hashids(SHORTHASH);
	$decodeId = $hashids->decode($shortUrl);
	if( !empty($decodeId[0]) ){
		$url_id = $decodeId[0];
		$oDb = new Database('shorten_url_db');
		$urlHandlerObj = new UrlHandler($oDb);

		$info = $urlHandlerObj->getInfo($hashids, $url_id);
		
		if(count($info) > 0){
			echoRespnse(200, $info);
		}
		else{
			$response["error"] = true;
			$response["message"] = "Oops! This short url is no longer in the system!";
			echoRespnse(200, $response);
		}
	}
	else{
		$response["error"] = true;
		$response["message"] = "Oops! The short url is not valid";
		echoRespnse(200, $response);
	}
	
});

$app->get('/:shortUrl', function($shortUrl) use ($app) {
	$hashids = new Hashids(SHORTHASH);
	$decodeId = $hashids->decode($shortUrl);
	if( !empty($decodeId[0]) ){
		$url_id = $decodeId[0];
		$oDb = new Database('shorten_url_db');
		$urlHandlerObj = new UrlHandler($oDb);
		$urlObj = $urlHandlerObj->getUrl($url_id);
		
		if($urlObj){
			$redirectUrl = $urlObj->desktop_url;
			$device = 'DESKTOP';
			
			$mobileDetectObj = new Mobile_Detect();
			
			if($mobileDetectObj->isMobile()){
				if($mobileDetectObj->isTablet() ){ 
					if( !empty($urlObj->tablet_url) ){ //make sure we have a url for the TABLET...if not, keep desktop url...
						$device = 'TABLET';
						$redirectUrl = $urlObj->tablet_url;
					}
				}
				else{ //phone
					if( !empty($urlObj->phone_url) ){ //make sure we have a url for the phone...if not, keep desktop url...
						$device = 'PHONE';
						$redirectUrl = $urlObj->phone_url;
					}
				}
			}
			
			//store the hit
			$urlHitObj = new UrlHit();
			$urlHitObj->url_id = $url_id; 
			$urlHitObj->target = $device;
			
			$urlHitHandlerObj = new UrlHitsHandler($oDb);
			$urlHitHandlerObj->addUrlHit($urlHitObj);
			
			//redirect
			header("Location: " . $redirectUrl);
			exit();
			
			//echoRespnse(201, array($device, $redirectUrl) );
		}
		else{
			$response["error"] = true;
			$response["message"] = "Oops! This short url is no longer in the system!";
			echoRespnse(200, $response);
		}
	}
	else{
		$response["error"] = true;
		$response["message"] = "Oops! The short url is not valid";
		echoRespnse(200, $response);
	}
});

/*
* Update an existing url...make sure at least one url type parameter is provided...
*/
$app->put('/:shortUrl', function($shortUrl) use ($app) {
	$params = verifyAtLeastOneRequiredParams(array('url', 'phoneUrl', 'tabletUrl'));
	
	$hashids = new Hashids(SHORTHASH);
	$decodeId = $hashids->decode($shortUrl);
	if( !empty($decodeId[0]) ){
		$url_id = $decodeId[0];
		$oDb = new Database('shorten_url_db');
		$urlHandlerObj = new UrlHandler($oDb);
		$urlObj = $urlHandlerObj->getUrl($url_id);
		
		if($urlObj){
			//set the new info
			if(!empty($params['url']) && filter_var($params['url'], FILTER_VALIDATE_URL) ) $urlObj->desktop_url = $params['url'];
			if(!empty($params['phoneUrl']) && filter_var($params['phoneUrl'], FILTER_VALIDATE_URL) ) $urlObj->phone_url = $params['phoneUrl'];
			if(!empty($params['tabletUrl']) && filter_var($params['tabletUrl'], FILTER_VALIDATE_URL) ) $urlObj->tablet_url = $params['tabletUrl'];
			
			if($urlHandlerObj->updateUrl($urlObj)){
				$response["error"] = false;
				$response["message"] = "Your url is successfully updated!";
				$shortUrl = $shortUrl;
				$response["shortUrl"] = $shortUrl;
				echoRespnse(201, $response);
			}
			else{
				$response["error"] = true;
				$response["message"] = "Oops! Error occurred while updating.";
				echoRespnse(200, $response);
			}
		}
		else{
			$response["error"] = true;
			$response["message"] = "Oops! This short url is no longer in the system!";
			echoRespnse(200, $response);
		}
	}
	else{
		$response["error"] = true;
		$response["message"] = "Oops! The short url is not valid";
		echoRespnse(200, $response);
	}
});

$app->post('/shortme', function() use ($app) {
	$hashids = new Hashids(SHORTHASH);
	// check for required params
	$params = verifyRequiredParams(array('url'));

	$response = array();
	
	if (filter_var($params['url'], FILTER_VALIDATE_URL)) {
		$oDb = new Database('shorten_url_db');
		$urlObj = new Url();
		$urlObj->desktop_url = $params['url'];
		if(!empty($params['phoneUrl']) && filter_var($params['phoneUrl'], FILTER_VALIDATE_URL) ) $urlObj->phone_url = $params['phoneUrl'];
		if(!empty($params['tabletUrl']) && filter_var($params['tabletUrl'], FILTER_VALIDATE_URL) ) $urlObj->tablet_url = $params['tabletUrl'];

		$urlHandlerObj = new UrlHandler($oDb);
		$urlId = $urlHandlerObj->addUrl($urlObj);
		
		if($urlId){
			$response["error"] = false;
			$response["message"] = "Your url is successfully shortened!";
			$shortUrl = $hashids->encode($urlId);
			$response["shortUrl"] = $shortUrl;
			echoRespnse(201, $response);
		}
		else{
			$response["error"] = true;
			$response["message"] = "Oops! An error occurred while shortening...";
			echoRespnse(200, $response);
		}
	}
	else{
		$response["error"] = true;
		$response["message"] = "Oops! The string provided is not a URL.";
		echoRespnse(200, $response);
	}
	
});

$app->run();
?>