#!/usr/bin/php -q
<?php
require_once("./PHPExcel/Classes/PHPExcel.php");
require_once('vendor/autoload.php');
require_once('autoload.php');
error_reporting(E_ALL);
ini_set("display_errors",true);
ini_set("html_errors",false);
date_default_timezone_set("Asia/Taipei");
function GetLegoProductList($days,$num)
{
	$url = 'http://search-en.lego.com/?cc=us&count='.$num;
	#$data = WebService::GetWebService($url);
	#echo $data;
	#exit;
	$qp = htmlqp('1.txt');
	$ul = $qp->find('#product-results');
	$arrayList = array();
	$count = 0;
	foreach ($ul->find('li.product-thumbnail') as $item)
	{
		$code = $item->find('.item-code')->text();
		$status = $item->find('.availability-now')->text();
		if($status == '')
			$status = $item->find('.availability-future')->text();
		if($status == '')
			$status = $item->find('.availability-questionable')->text();
		if($code == '') continue;
		$item = array(
			'badges'=> $item->find('ul#product-badges li')->text(),
			'url'=> $item->find('a')->attr('href'),
			'name'=>$item->find('a')->attr('title'),
			'id'=> $code,
			'days'=>$days,
			'price'=> str_replace(array(" ","\t","\n"),"",$item->find(".test-navigation-show-price-".$code)->text()),
			'condition'=> $status,
			'rating'=> $item->find('div')->attr('rating'),
		);
		if($item['condition'] == '') $count++;
		$arrayList[] = $item;
	}
	return $arrayList;
}

function GetRawDataFromArray($DataArray,&$objPHPExcel,&$outputArray)
{
	$rows = 2;
	foreach($DataArray as $item)
	{
		$outputArray[] = $item['id'] . '|' . $item['url'];
		$objPHPExcel->getActiveSheet()->setCellValueExplicit("A$rows",$item['badges'],PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()->setCellValueExplicit("B$rows",$item['url'],PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()->setCellValueExplicit("C$rows",$item['name'],PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()->setCellValueExplicit("D$rows",$item['id'],PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()->setCellValueExplicit("E$rows",$item['price'],PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()->setCellValueExplicit("F$rows",$item['condition'],PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()->setCellValueExplicit("G$rows",$item['rating'],PHPExcel_Cell_DataType::TYPE_STRING);
		$rows++;
	}
}

function GenerateData($ItemInfo,$LegoInfo,$days,$filename,$num,$useTemplate=false)
{
	try {
		// Load Files
		if($useTemplate)
		{
			$objPHPExcel = PHPExcel_IOFactory::load("./Template.xlsx");
		}
		else
			$objPHPExcel = PHPExcel_IOFactory::load($filename);

		$objPHPExcel->setActiveSheetIndex(0);
		
		$DataArray = GetLegoProductList($days,$num);
		foreach($DataArray as $key=>$item)
		{
			$librick_id = $ItemInfo->getLibrickID($item['id']);
			if($librick_id != null)
			{
				$item['id'] = $librick_id;
				unset($DataArray[$key]);
				$LegoInfo->InsertItem($item);
				switch($item['badges'])
				{
					case 'New':
						$badges = 1;
						break;
					case 'New|Exclusive':
						$badges = 1;
						break;
					case 'New|Hard To Find':
						$badges = 1;
						break;
					default:
						$badges = 0;
						break;
				}
				if($item['condition'] == "Sold Out")
					$badges = 2;
				if($item['condition'] == "Retired product")
					$badges = 3;
				$ItemInfo->updateBadges($librick_id,$badges);
			}
		}
		if(count($DataArray) != 0)
		{
			$outputArray = array();
			GetRawDataFromArray($DataArray,$objPHPExcel,$outputArray);
			// Save File	
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
			$objWriter->save($filename);
			$title = '樂高公司絕版錯誤通知';
			$notify = new SendNotify();
			$notify->pushList($title,$outputArray);
			unset($notify);
		}
	}catch (Exception $e) {
		echo "PHPExcel Error : ".$e->getMessage()."<BR>";
		return;
	}
	return ;
}
$dbh = new PDO($DB['DSN'],$DB['DB_USER'], $DB['DB_PWD'],
        array( PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
        PDO::ATTR_PERSISTENT => false));
$current_time = new DateTime('now');
$days = $current_time->format('Ymd');
$ItemInfo = new ItemInfo($dbh);
$LegoInfo = new LegoInfo($dbh);
$filename = "./Lego.xlsx";
GenerateData($ItemInfo,$LegoInfo,$days,$filename,2000,true);
