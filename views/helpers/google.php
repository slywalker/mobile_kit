<?php
class GoogleHelper extends AppHelper {
	var $helpers = array('Html', 'Form');
	
	function analytics($key)
	{
		if (!$key) {
			return;
		}
		
		// http://it.kndb.jp/entry/show/id/2376
		echo '<!-- Google Analytics for mobile START -->';
		$var_utmac = $key; // Google AnalyticsのID
		$var_utmhn = env('HTTP_HOST'); // 解析するサイトのドメイン
		$var_utmn = rand(1000000000,9999999999);
		$var_cookie = ''; //$session; //cookie number
		$var_random = rand(1000000000,2147483647);
		$var_today = time(); //today
		$var_referer = env('HTTP_REFERER'); //referer url
		$var_uservar = ''; //$storeinfo['storeid'];
		$var_utmp = env('REQUEST_URI'); // request uri

		$urchinUrl =
'http://www.google-analytics.com/__utm.gif?utmwv=1&utmn='.$var_utmn.
'&utmsr=-&utmsc=-&utmul=-&utmje=0&utmfl=-&utmdt=-&utmhn='.$var_utmhn.
'&utmr='.$var_referer.'&utmp='.$var_utmp.'&utmac='.$var_utmac.
'&utmcc=__utma%3D'.
$var_cookie.'.'.
$var_random.'.'.
$var_today.'.'.
$var_today.'.'.
$var_today.
'.2%3B%2B__utmb%3D'.$var_cookie.
'%3B%2B__utmc%3D'.$var_cookie.
'%3B%2B__utmz%3D'.$var_cookie.'.'.$var_today.
'.2.2.utmccn%3D(direct)%7Cutmcsr%3D(direct)%7Cutmcmd%3D(none)%3B%2B__utmv%3D'.
$var_cookie.'.'.
$var_uservar.'%3B';

		$header = '';

		if (env('HTTP_ACCEPT_LANGUAGE')) {
			$header =
				'Accept-language: '.env('HTTP_ACCEPT_LANGUAGE').'\r\n';
		}

		if (env('HTTP_USER_AGENT')) {
			$header = 'User-Agent: '.env('HTTP_USER_AGENT').'\r\n';
		}
		$opts = array(
			'http'=>array('method'=>'GET', 'header'=>$header )
		);
		$handle = fopen($urchinUrl, 'r', false,
			stream_context_create($opts));
		$test = fgets($handle);
		fclose($handle);
		echo '<!-- Google Analytics for mobile END -->';
		return;
	}
}
?>