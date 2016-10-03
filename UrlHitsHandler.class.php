<?php

require_once 'Url.class.php';

class UrlHitsHandler {
    //table names
    static $urlHitsTable = "url_hits";
     
    //object class names
    static $urlHitClass = "UrlHit";
    
    private $db;
	private $urlHitFields;
  
    public function __construct(Database $db){
        $this->db = $db;
		$this->urlHitFields = implode(',', array_keys(get_class_vars(self::$urlHitClass)));
    }
    
    /*
     * returns last inserted id on success or an array with errors
     */
    public function addUrlHit(UrlHit $urlHitObj)
    {        
        $insert = "INSERT INTO " .self::$urlHitsTable." (target,url_id ) VALUES ( :target, :url_id )";
        
        $stmt = $this->db->prepare($insert);
        
        $stmt->bindParam(':target', $urlHitObj->target);
		$stmt->bindParam(':url_id', $urlHitObj->url_id);
    
        $stmt->execute();
        
        return $this->db->lastInsertId(); 
    }
	/*
	* Returns an array with number of hits from each device type
	*/
	public function getUrlHitInfo($url_id){
		$deviceTypes = array('TABLET', 'PHONE', 'DESKTOP');
		$hitsInfo = array();
		
		foreach($deviceTypes as $type){
			$sqlStatement = "SELECT COUNT(id) FROM " . self::$urlHitsTable . ' where url_id = ? and target = ?';
			$result = $this->db->prepare($sqlStatement);
			$result->execute(array($url_id, $type) );
			$total = $result->fetchColumn();
			if($total){
				$hitsInfo[$type] = $total;
			}
			else{
				$hitsInfo[$type] = 0;
			}
		}
		
		return $hitsInfo;
	}
}
