<?php
class ItemInfo {
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
		$this->p1 = $this->dbh->prepare("select * from item_info where sub_name=:sub_name 
			and item_type in ('Sets','Gears')");
		$this->p2 = $this->dbh->prepare("update item_info set badges=:badges where id=:id");
		$this->p3 = $this->dbh->prepare("update item_info set badges=0");
	}

	function __destruct()
	{
		
	}

	function getLibrickID($sub_name)
	{
		try {
			$this->p1->bindParam(':sub_name',$sub_name,PDO::PARAM_STR);
			$this->p1->execute();
			if($this->p1->rowCount() == 0)
                                return null;
			$resData = $this->p1->fetch(PDO::FETCH_OBJ);
			return $resData->id;
		} catch (PDOException $e) {
			error_log('['.date('Y-m-d H:i:s').'] '.__METHOD__.' Error: ('.$e->getLine().') ' . $e->getMessage()."\n",3,"./log/ItemInfo.txt");
			return null;
		}
		#error_log('['.date('Y-m-d H:i:s').'] '.__METHOD__.' Finish'."\n",3,"./log/ItemInfo.txt");
	}

	function updateBadges($librick_id,$badges)
	{
		try {
			$this->p2->bindParam(':id',$librick_id,PDO::PARAM_STR);
			$this->p2->bindParam(':badges',$badges,PDO::PARAM_STR);
			$this->p2->execute();
			return true;
		} catch (PDOException $e) {
			error_log('['.date('Y-m-d H:i:s').'] '.__METHOD__.' Error: ('.$e->getLine().') ' . $e->getMessage()."\n",3,"./log/ItemInfo.txt");
			return false;
		}
		#error_log('['.date('Y-m-d H:i:s').'] '.__METHOD__.' Finish'."\n",3,"./log/ItemInfo.txt");
	}

	function cleanBadges()
	{
		try {
			$this->p3->execute();
			return true;
		} catch (PDOException $e) {
			error_log('['.date('Y-m-d H:i:s').'] '.__METHOD__.' Error: ('.$e->getLine().') ' . $e->getMessage()."\n",3,"./log/ItemInfo.txt");
			return false;
		}
		#error_log('['.date('Y-m-d H:i:s').'] '.__METHOD__.' Finish'."\n",3,"./log/ItemInfo.txt");
	}
}
?>
