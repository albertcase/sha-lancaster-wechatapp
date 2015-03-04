<?php 
/**
 * 数据库操作类文件
 * @author Demon.zhang@samesamechina.com
 *
 */
class Pdb extends PDO{   
	private static $dbInstance =  null;   
    private $pdo = null;   
    private $tableKeys = array();   
    private $cacheTable = "trio_caches";   
	public  $lastInsertId = '';
    
    public function __construct() {   
		$dbDsn = database::dbType . ':host=' . database::dbHost . ';port='.database::dbport.';dbname=' . database::dbName; 
        @$this->pdo = new PDO($dbDsn, database::dbUser, database::dbPass); 
		$this->pdo->exec("set names " . database::dbCharset); 
        $this->tableKeys = $this->getTableKeys();   
     }   
    public static function getDb() {   
       if (null == self::$dbInstance)   
           self::$dbInstance = new Pdb();   
        return self::$dbInstance;   
    }   
        
    public function quote($str,$paramtype = null) {
         return $this->pdo->quote($str);   
    }   
        
     public function execute($query) {   
     	$tableKey = $this->getExecTableKey($query);   
        $rs = $this->pdo->exec($query);   
        $this->lastInsertId = $this->pdo->lastInsertId();
        $this->clearQueryCache($tableKey);   
		if($rs)
			 return $rs;
		else
			 return false;
		
       
     }   
   
     public function query($query, $fetchType = 'all', $fetchAssoc=false,$cache = database::dbCache) {   
        if($cache) {   
            $tableKey = $this->getQueryTableKey($query);   
            $queryKey = md5($query);   
            $queryCache = $this->getQueryCache($tableKey, $queryKey, $fetchType);   
             if (false != $queryCache) {   
                return $queryCache;   
            }    
         }   
        $pdoStmt = $this->pdo->query($query);
        if ($fetchAssoc){  
        	$pdoStmt->setFetchMode(PDO::FETCH_ASSOC);
        }
        if (false != $pdoStmt) {   
            switch($fetchType){   
                 case 'all': $result = $pdoStmt->fetchAll(); break;   
                 case 'row': $result = $pdoStmt->fetch(); break;   
                 case 'one': $result = $pdoStmt->fetchColumn(0); break;   
            }   
             if($cache) $this->setQueryCache($tableKey, $queryKey, $fetchType, $query, $result);    
        }   
         return $result;   
     }   
        
     private function setQueryCache($tableKey, $queryKey, $fetchType, $queryText, $queryValue) {   
        $query = "REPLACE INTO " . $this->cacheTable . " VALUES ($tableKey, '$queryKey', '$fetchType',  
                  " . $this->pdo->quote($queryText) . "," . $this->pdo->quote(serialize($queryValue)) . ")";   
        $this->pdo->exec($query);   
   }   
       
    public function getQueryCache($tableKey, $queryKey, $fetchType) {   
        $query = "SELECT query_value FROM " . $this->cacheTable ."   
                  WHERE tables_key=$tableKey AND query_key='$queryKey' AND fetch_type='$fetchType'";   
        $pdoStmt = $this->pdo->query($query);   
        if (false != $pdoStmt)    
             return unserialize($pdoStmt->fetchColumn(0));   
        return false;   
     }   
       
    public function clearQueryCache($tableKey) {   
         $strQuery = "DELETE FROM " . $this->cacheTable . " WHERE tables_key & $tableKey > 0";   
         $this->pdo->exec($strQuery);   
    }   
        
    public function getAll($query,$fetchAssoc=false) {   
         return $this->query($query, 'all',$fetchAssoc);   
     }   
       
    public function getRow($query,$fetchAssoc=false) {   
        return $this->query($query, 'row',$fetchAssoc);   
    }   
       
     public function getOne($query) {   
         return $this->query($query, 'one');   
    }   
       
    public function getExecTableKey($query) {   
        if (preg_match('/^delete\s*from\s*(\w+)/i', $query, $match))    
             return $this->tableKeys[$match[1]];   
       	if (preg_match('/^update\s*(\w+)/i', $query, $match))   
            return $this->tableKeys[$match[1]];   
        if (preg_match('/^insert\s*into\s*(\w+)/i', $query, $match))   
            return $this->tableKeys[$match[1]];   
        if (preg_match('/^replace\s*into\s*(\w+)/i', $query, $match))   
            return $this->tableKeys[$match[1]];    
     }   
        
    private function getQueryTableKey($query) {   
        $queryTables = $this->getQueryTables($query);   
        $tableKey = 0;   
        foreach($queryTables as $tblName) {   
            $tableKey = $tableKey | $this->tableKeys[$tblName];   
        }   
        return $tableKey;   
    }   
       
     private function getQueryTables($query){   
        $query = 'explain ' . $query;   
        $pdoStmt = $this->pdo->query($query);   
        $tables = array();   
        if (false != $pdoStmt) {   
            while($tblName = $pdoStmt->fetchColumn(2)) {   
                $pattern = '/(\w+)\s+(?:as\s+)?\b' . $tblName . '\b/i';   
                if(preg_match($pattern, $query, $match)) {   
                     if (false == in_array(strtolower($match[1]), array('from', 'join'))) {   
                        $tables[] = $match[1];   
                     } else {   
                       $tables[] =$tblName;   
                    }   
                } else{   
                    $tables[] = $tblName;   
                }   
            }   
         }   
           
        return array_unique($tables);   
    }   
  
     private function getTableKeys() {   
         $strQuery = 'show tables';   
         $pdoStmt = $this->pdo->query($strQuery);   
         $tblKeys = array();   
        if (false != $pdoStmt) {   
            $i = 0;   
            while($tblName = strtolower($pdoStmt->fetchColumn())) {   
                $tblKeys[$tblName] = pow(2, $i);   
                $i++;   
            }   
        }   
            
         return $tblKeys;   
   }   
};   
  
?>