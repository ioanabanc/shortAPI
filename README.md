# shortAPI
URL shortener API

ShortAPI v1 documentation

http://www.mydomain.com/index.php/shortme
  Method: POST
  Parameters: url (required), phoneUrl, tabletUrl
  Description: Shortens an URL

http://www.mydomain.com/index.php/{shortUrl}
  Method: PUT
  Parameters: url, phoneUrl, tabletUrl - at elast one is required
  Description: Updated url(s) for an existing short url
  
http://www.mydomain.com/index.php/info/{shortUrl}
  Method: GET
  Description: Gets the info of a short url
  
http://www.mydomain.com/index.php/shortme
  Method: GET
  Description: Retuns the info of al short urls
  
http://www.mydomain.com/index.php/{shortUrl}
  Method: GET
  Description: Redirects to corresponding url (desktop - default, phone, tablet)
  

Used frameworks:
  Slim
  MobileDetect
  Hashids
  
The API uses a MySql DB with these settings: 
		'username' => 'usr_shorten',
		'password' => 'pass_shorten',
		'hostspec' => '127.0.0.1',
		'database' => 'shorten_url'
		
The exported DB SQL is inside shorten_url.sql file.

The files index.php, config_db.inc.php, Database.class.php, tests.php, Url.class.php, UrlHandler.class.php, UrlHitsHandler.class.php should be places together on Web server.

The web server can be configured so that the API url calls don't have to contain "index.php".

# Here is the url to the simple test file: http://www.mydomain.com/tests.php
