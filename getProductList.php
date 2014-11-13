#!/usr/bin/php -q
<?php
require_once('vendor/autoload.php');
require_once(__DIR__.'/WebService.php');
error_reporting(E_ALL);
ini_set("display_errors",true);
ini_set("html_errors",false);
date_default_timezone_set("Asia/Taipei");
require_once("./PHPExcel/Classes/PHPExcel.php");
function GetLegoProductList($num)
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
			'code'=> $code,
			'price'=> str_replace(array(" ","\t","\n"),"",$item->find(".test-navigation-show-price-".$code)->text()),
			'status'=> $status,
			'rating'=> $item->find('div')->attr('rating'),
		);
		if($item['status'] == '') $count++;
		$arrayList[] = $item;
	}
	return $arrayList;
}

function GetRawDataFromArray($DataArray,&$objPHPExcel)
{
	$rows = 2;
	foreach($DataArray as $item)
	{
		$objPHPExcel->getActiveSheet()->setCellValueExplicit("A$rows",$item['badges'],PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()->setCellValueExplicit("B$rows",$item['url'],PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()->setCellValueExplicit("C$rows",$item['name'],PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()->setCellValueExplicit("D$rows",$item['code'],PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()->setCellValueExplicit("E$rows",$item['price'],PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()->setCellValueExplicit("F$rows",$item['status'],PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()->setCellValueExplicit("G$rows",$item['rating'],PHPExcel_Cell_DataType::TYPE_STRING);
		$rows++;
	}
}

function GenerateExcel($filename,$num,$useTemplate=false)
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
		
		$DataArray = GetLegoProductList($num);
		GetRawDataFromArray($DataArray,$objPHPExcel);
		// Save File	
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
		$objWriter->save($filename);
	}catch (Exception $e) {
		echo "PHPExcel Error : ".$e->getMessage()."<BR>";
		return;
	}
	return ;
}

$filename = "Lego.xlsx";
GenerateExcel($filename,2000,true);
