<?php
class WebService {
	public static function PostWebService($url,$postField=false)
	{
		$cookie = __DIR__ . '/lego_cookie.txt';
		$link = curl_init();
		curl_setopt($link, CURLOPT_URL,$url);
		curl_setopt($link, CURLOPT_VERBOSE,0);
		curl_setopt($link, CURLOPT_HEADER,0);
		curl_setopt($link, CURLOPT_HTTPHEADER, 
			array('Connection: Keep-Alive','Keep-Alive: 300','Expect:'));
		curl_setopt($link, CURLOPT_COOKIEJAR , $cookie);
		curl_setopt($link, CURLOPT_COOKIEFILE, $cookie);
		curl_setopt($link, CURLOPT_RETURNTRANSFER,true);
		curl_setopt($link, CURLOPT_REFERER , "http://shop.lego.com/en-US/catalog/productListing.jsp");
		curl_setopt($link, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
		curl_setopt($link, CURLOPT_FOLLOWLOCATION, true);
		#curl_setopt($link, CURLOPT_FORBID_REUSE,true);
		if($postField != null)
        	{
                	curl_setopt($link,CURLOPT_POST,true);
                	curl_setopt($link,CURLOPT_POSTFIELDS,http_build_query($postField));
	#		curl_setopt($link,CURLOPT_POSTFIELDS,json_encode($postField));	
        	}
		$Result = curl_exec($link);
		if(!curl_errno($link))
		{ 
			$info = curl_getinfo($link); 
			//echo 'Took ' . $info['total_time'] . ' seconds to send a request to ' . $info['url'] . "<BR>"; 
		}
		else
		{ 
			echo 'Curl error: ' . curl_error($link) . "<BR>";
		} 
		curl_close($link);
		unset($link);
		
		return $Result;
	}

	public static function GetWebService($url)
	{
		$cookie = __DIR__ . '/lego_cookie.txt';
		$link = curl_init();
		curl_setopt($link,CURLOPT_URL,$url);
		curl_setopt($link,CURLOPT_VERBOSE,0);
		curl_setopt($link,CURLOPT_HEADER,0);
		curl_setopt($link,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($link, CURLOPT_COOKIEJAR , $cookie);
		curl_setopt($link, CURLOPT_COOKIEFILE, $cookie);
		curl_setopt($link, CURLOPT_RETURNTRANSFER,true);
		curl_setopt($link, CURLOPT_FOLLOWLOCATION, true);
		$Result = curl_exec($link);
		if(!curl_errno($link))
		{ 
			$info = curl_getinfo($link); 
			//echo 'Took ' . $info['total_time'] . ' seconds to send a request to ' . $info['url'] . "<BR>"; 
		}
		else
		{ 
			echo 'Curl error: ' . curl_error($link) . "<BR>";
		} 
		curl_close($link);
		unset($link);
		return $Result;
	}
}

?>
