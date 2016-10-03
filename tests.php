<?php
//super simple test file for checking shortAPI calls


//create short url:
$baseServer = 'http://dev1.realbiz360.com/index.php/';

$target_url = $baseServer . 'shortme';
$params = array('url' => 'http://url.com', 'phoneUrl' => 'http://phoneUrl.com', 'tabletUrl' => 'http://tabletUrl.com'); 
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $target_url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$result = curl_exec ($ch);
curl_close ($ch);

$response = json_decode($result);
if(!$response->error){
	$shortUrl = $baseServer.$response->shortUrl;
	$shortUrlInfo = $baseServer.'info/'.$response->shortUrl;
	
	echo '<a href="'.$shortUrl.'" target="_blank">Short url is: '.$baseServer.$response->shortUrl.' </a><br/><br/>';
	
	//use shortUrl + PUT to update it
	$ch = curl_init($shortUrl);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
	$params = array('url' => 'http://urlUpdated.com', 'phoneUrl' => 'http://phoneUrlUpdated.com', 'tabletUrl' => 'http://tabletUrlUpdated.com'); 
	curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($params));
	$response = curl_exec($ch);
	if ($response === false) {
		$info = curl_getinfo($ch);
		curl_close($ch);
	}
	curl_close($ch);
	$decoded = json_decode($response);
	
	if(!$response->error){
		echo '<div><span>URL '.$shortUrl.' updated</span></div>';
	}
	else{
		echo '<div><span>Failed to update URL '.$shortUrl.'</span></div>';
	}
	
	//get info about the url: 
	var_dump(file_get_contents($shortUrlInfo)); //it will display a json string with info for created short url
}

$allInfo = $baseServer . 'info';
var_dump(file_get_contents($shortUrlInfo)); //it will display a json string with info for all db urls.
?>