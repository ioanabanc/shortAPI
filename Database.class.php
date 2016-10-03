<?php

class Database extends PDO
{
    public function __construct($dbName)
    {
        require 'config_db.inc.php'; //returns an array ($conf) with db credentials

        if(isset($conf[$dbName]))
        {
            try
            {
                parent::__construct("mysql:host=" . $conf[$dbName]['hostspec'] . ";dbname=" . $conf[$dbName]['database'], $conf[$dbName]['username'],  $conf[$dbName]['password']);
            } 
            catch (PDOException $ex) 
            {
                die($ex->getMessage());
            }
        }
        else
        {
            die('Invalid Database Name');
        }
    }

    public function getRowObj($sqlStatement, $params, $objType = "")
    {
        try{
            $stmt = parent::prepare($sqlStatement);

            if($stmt->execute($params)){

            }else{
                print_r($stmt->errorInfo());

                die($stmt->errorCode());
            }
        }
        catch(PDOException $ex){
            die($ex->getMessage());
        }
        
        return $objType == "" ? $stmt->fetchObject() : $stmt->fetchObject($objType);
    }
    
    public function getRow($sqlStatement, $params){
        try{
            $stmt = parent::prepare($sqlStatement);

            if($stmt->execute($params)){

            }else{
                print_r($stmt->errorInfo());

                die($stmt->errorCode());
            }
        }
        catch(PDOException $ex){
            die($ex->getMessage());
        }
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getRows($sqlStatement, $params){
        $stmt = parent::prepare($sqlStatement);

        if($stmt->execute($params)){

        }else{
            print_r($stmt->errorInfo());

            die($stmt->errorCode());
        }
       
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getColumn($sqlStatement, $params){
        $stmt = parent::prepare($sqlStatement);
        
        $stmt->execute($params);
        
        return $stmt->fetchColumn();
    }
    
    public function getColumns($sqlStatement, $params){
        $stmt = parent::prepare($sqlStatement);

        if($stmt->execute($params)){

        }else{
            print_r($stmt->errorInfo());

            die($stmt->errorCode());
        }
        
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);;
    }
    
    public function getAllRowsObj($sqlStatement, $params, $objType = "")
    {
        $stmt = parent::prepare($sqlStatement);
        
        if($stmt->execute($params)){
            
        }else{
            print_r($stmt->errorInfo());
            
            die($stmt->errorCode());
        }
        
        return $objType == "" ? $stmt->fetchAll(PDO::FETCH_CLASS) : $stmt->fetchAll(PDO::FETCH_CLASS, $objType);
    }
    
    public function prepareStatement($table, $fields, $where, $orderBy = ''){
        $sqlStatement = "SELECT " . $fields . " FROM " . $table . " WHERE $where";
        
        if($orderBy != '') $sqlStatement .= "ORDER BY $orderBy";
        
        return $sqlStatement;
    }
}

?>