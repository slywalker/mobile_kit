<?php
class GeoBehavior extends ModelBehavior {
	
	function dms2deg ($dms){
		$arr = explode(".", $dms);
		$a = $arr[2].".".$arr[3];
		$deg = $arr[0] + $arr[1]/60 + $a/3600 ;
		return $deg;
	}
	
	function deg2dms ($deg){
		$aa[0] = floor($deg);
		$fl = $deg - $aa[0];
		$tt = $fl * 360000 + 0.5;
		$aa[1] = str_pad(($tt /6000 % 60),2,"0",STR_PAD_LEFT);
		$aa[2] = str_pad(($tt /100 % 60),2,"0",STR_PAD_LEFT);
		$aa[3] = str_pad(($tt % 100),2,"0",STR_PAD_LEFT);
		$dms = implode($aa,".");
		return $dms;
	}
	
	function wgs2tokyo($lat, $lon){
		$a["lat"] = $lat + 0.00010696*$lat - 0.000017467*$lon - 0.0046020;
		$a["lon"] = $lon + 0.000046047*$lat + 0.000083049*$lon - 0.010041;
		return $a;
	}
	
	function tokyo2wgs($lat, $lon){
		$a["lat"] = $lat - 0.00010695*$lat + 0.000017464*$lon + 0.0046017;
		$a["lon"] = $lon - 0.000046038*$lat - 0.000083043*$lon + 0.010040;
		return $a;
	}

	function distance($lat1, $lon1, $lat2, $lon2){
		$dp = deg2rad(abs($lat1 - $lat2));
		$dr = deg2rad(abs($lon1 - $lon2));
		$p =  deg2rad($lat1 + (($lat2 - $lat1) / 2));
		$m = 6335439 / sqrt( pow( ( 1 - 0.006694 * sin($p) * sin($p) ),3) );
		$n = 6378137 / sqrt(1 - 0.006694 * sin($p) * sin($p) );
		$d = round(sqrt( ($m*$dp)*($m*$dp)
			+ ($n*cos($p)*$dr)*($n*cos($p)*$dr)),3);
		return $d;
	}
	
	function beforeFind(&$model, $query)
	{
		$default = array(
			'lon'=>null,
			'lat'=>null,
			'dist'=>'1km',
		);
		$query = Set::merge($default, $query);
		
		$lon = (float) $query['lon'];
		$lat = (float) $query['lat'];
		$dist = $query['dist'];

		if ($lon && $lat) {
			if (strpos($dist, 'km') === false && strpos($dist, 'm')) {
				$dist = floatval($dist)/1000;
			} else {
				$dist = floatval($dist);
			}
			$lon1 = $lon-$dist/abs(cos(deg2rad($lat))*69.09*1.609344);
			$lon2 = $lon+$dist/abs(cos(deg2rad($lat))*69.09*1.609344);
			$lat1 = $lat-($dist/(69.09*1.609344));
			$lat2 = $lat+($dist/(69.09*1.609344));
			$conditions = array(
				"{$model->alias}.lon BETWEEN ? AND ?"=>array($lon1, $lon2),
				"{$model->alias}.lat BETWEEN ? AND ?"=>array($lat1, $lat2),
			);
			$query = Set::merge($query, array('conditions'=>$conditions));
		}
		unset($query['lon']);
		unset($query['lat']);
		unset($query['dist']);
		
		if ($model->findQueryType !== 'count') {
			$fields = array(
				"{$model->alias}.*",
				"3956*2*ASIN(
					SQRT(
						POWER(
							SIN(({$lat}-`{$model->alias}`.`lat`)
							*pi()/180/2),2
						)
						+COS({$lat}*pi()/180)
						*COS(`{$model->alias}`.`lat`*pi()/180)
						*POWER(
							SIN(({$lon}-`{$model->alias}`.`lon`)
							*pi()/180/2),2
						)
					)
				)*1.609344*1000 AS `distance`",
			);
			$order = array('distance ASC');
			$query = Set::merge($query,
				array('fields'=>$fields), array('order'=>$order));
		}
		return $query;
	}
}
?>