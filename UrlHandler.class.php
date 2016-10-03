<?php

require_once 'Url.class.php';
require_once 'UrlHitsHandler.class.php';


class UrlHandler {
    //table names
    static $urlTable = "urls";
     
    //object class names
    static $urlClass = "Url";
    
    private $db;
	private $urlFields;
  
    public function __construct(Database $db){
        $this->db = $db;
		$this->urlFields = implode(',', array_keys(get_class_vars(self::$urlClass)));
    }
    
    /*
     * returns last inserted id on success or an array with errors
     */
    public function addUrl(Url $urlObj)
    {        
        $insert = "INSERT INTO " .self::$urlTable."
                        (desktop_url,
						phone_url,
						tablet_url
                        )
                   VALUES
                        (
                            :desktop_url,
                            :phone_url,
                            :tablet_url
                        )";
        
        $stmt = $this->db->prepare($insert);
        
        $stmt->bindParam(':desktop_url', $urlObj->desktop_url);
        $stmt->bindParam(':phone_url', $urlObj->phone_url);
        $stmt->bindParam(':tablet_url', $urlObj->tablet_url);
        
        $stmt->execute();
        
        return $this->db->lastInsertId(); 
    } 
    
    /*
     * returns boolean true on success or false on fail
     */
    public function updateUrl(Url $urlObj)
    {   
        if(empty($urlObj->id))
        {
            return false;// 'No url id';
        }
        
        $edit = "  UPDATE " .self::$urlTable."
                   SET  desktop_url = :desktop_url,
                        phone_url = :phone_url,
                        tablet_url = :tablet_url
                    WHERE id = :url_id
                   ";
        
        $stmt = $this->db->prepare($edit);
        
        $stmt->bindParam(':desktop_url', $urlObj->desktop_url);
        $stmt->bindParam(':phone_url', $urlObj->phone_url);
        $stmt->bindParam(':tablet_url', $urlObj->tablet_url);
       
        $stmt->bindParam(':url_id', $urlObj->id);
        
        if($stmt->execute()){
            return true;
        }else{
			return false; //or an array with errors: $stmt->errorInfo(), $stmt->errorCode()
        }
    }
    
    public function getUrl($id)
    {
		$sqlStatement = $this->prepareStatement(self::$urlTable, $this->urlFields, "id = ?");
        
        $urlObj = $this->db->getRowObj($sqlStatement, array($id), self::$urlClass);
        
        return $urlObj;

    }
	/*
	If id is provided - get info for that id only
	If not, get info for all stored urls...
	*/
	public function getInfo($hashids, $url_id = null){
		$where = '1';
		$params = array();
		if($url_id){
			$where = "id = ? order by id asc";
			$params[] = $url_id;
		}
		
		$info = array();
		
		$sqlStatement = $this->prepareStatement(self::$urlTable, $this->urlFields, $where);
		$Urls = $this->db->getAllRowsObj($sqlStatement, $params, self::$urlClass);
		$urlHitsHandlerObj = new UrlHitsHandler($this->db);
		foreach($Urls as $urlObj){
			$hitsInfo = $urlHitsHandlerObj->getUrlHitInfo($urlObj->id);
			$info[$hashids->encode($urlObj->id)] = array(
				'CreatedOn' => date_format(date_create($urlObj->creation_date), 'g:ia \\o\n l jS F Y'),
				'DESKTOP' => array('URL' => $urlObj->desktop_url, 'Hits' => $hitsInfo['DESKTOP']),
				'PHONE' => array('URL' => $urlObj->phone_url, 'Hits' => $hitsInfo['PHONE']),
				'TABLET' => array('URL' => $urlObj->tablet_url, 'Hits' => $hitsInfo['TABLET'])
			);
		}
		
		return $info;
	}
    
	private function prepareStatement($table, $fields, $where, $orderBy = ''){
        $sqlStatement = "SELECT " . $fields . " FROM " . $table . " WHERE $where";
        
        if($orderBy != '') $sqlStatement .= "ORDER BY $orderBy";
        
        return $sqlStatement;
    }
}
