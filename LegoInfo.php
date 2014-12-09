<?php
class LegoInfo {
	private $dbh = null;
	private $p1 = null;
	function __construct($dbh)
	{
		$this->dbh = $dbh;
//		$this->dbh = new PDO($DB['DSN'],$DB['DB_USER'], $DB['DB_PWD'],
//				array( PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
//					PDO::ATTR_PERSISTENT => false));
		# 錯誤的話, 就不做了
		$this->dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		$this->p1 = $this->dbh->prepare("insert into lego_info (`days`,`id`,`badges`,`price`,`condition`,
			`rating`,`name`,`url`) values (:days,:id,:badges,:price,:condition,:rating,:name,:url) 
			on duplicate key update `badges`=:badges,`price`=:price,`condition`=:condition,`rating`=:rating,
			`name`=:name,`url`=:url");
	}

	function __destruct()
	{
		
	}

	function InsertItem($DataArray)
	{
		try {
			$this->p1->bindParam(':days',$DataArray['days'],PDO::PARAM_STR);
			$this->p1->bindParam(':id',$DataArray['id'],PDO::PARAM_STR);
			$this->p1->bindParam(':badges',$DataArray['badges'],PDO::PARAM_STR);
			$this->p1->bindParam(':price',$DataArray['price'],PDO::PARAM_STR);
			$this->p1->bindParam(':condition',$DataArray['condition'],PDO::PARAM_STR);
			$this->p1->bindParam(':rating',$DataArray['rating'],PDO::PARAM_STR);
			$this->p1->bindParam(':name',$DataArray['name'],PDO::PARAM_STR);
			$this->p1->bindParam(':url',$DataArray['url'],PDO::PARAM_STR);
			$this->p1->execute();
			if($this->p1->rowCount() == 0)
                                return false;
			return true;
		} catch (PDOException $e) {
			error_log('['.date('Y-m-d H:i:s').'] '.__METHOD__.' Error: ('.$e->getLine().') ' . $e->getMessage()."\n",3,"./log/LegoInfo.txt");
			return false;
		}
		#error_log('['.date('Y-m-d H:i:s').'] '.__METHOD__.' Finish'."\n",3,"./log/LegoInfo.txt");
	}
}
?>
