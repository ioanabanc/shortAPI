# shortAPI
URL shortener API

ShortAPI v1 documentation

http://dev1.realbiz360.com/index.php/shortme
  Method: POST
  Parameters: url (required), phoneUrl, tabletUrl
  Description: Shortens an URL

http://dev1.realbiz360.com/index.php/{shortUrl}
  Method: PUT
  Parameters: url, phoneUrl, tabletUrl - at elast one is required
  Description: Updated url(s) for an existing short url
  
http://dev1.realbiz360.com/index.php/info/{shortUrl}
  Method: GET
  Description: Gets the info of a short url
  
http://dev1.realbiz360.com/index.php/shortme
  Method: GET
  Description: Retuns the info of al short urls
  
http://dev1.realbiz360.com/index.php/{shortUrl}
  Method: GET
  Description: Redirects to corresponding url (desktop - default, phone, tablet)
  
  
