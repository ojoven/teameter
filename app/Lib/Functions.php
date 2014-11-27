<?php

class Functions {

	public static function setCache($key,$value) {
		// Opening the file
		$h = fopen(self::get_file_name($key),'w');
		if (!$h) throw new Exception('Could not write to cache');
		$value = serialize($value);
		if (fwrite($h,$value)===false) {
			throw new Exception('Could not write to cache');
		}
		fclose($h);
	}

	public static function getCache($key) {
		$filename = self::get_file_name($key);
		if (!file_exists($filename) || !is_readable($filename)) return false;
		$data = file_get_contents($filename);
		$data = @unserialize($data);
		return $data;
	}

	/** AUXILIAR **/
	public static function get_file_name($key) {

		$filename = '../cache/' . $key;
		return $filename;

	}

	public static function serialize_optimized( &$data, $buf = "" ) {
		if( is_array( $data )) {
			$buf .= "a:" . count( $data ) . ":{";
			foreach( $data as $key => $value ) {
				$buf .= serialize( $key ) . self::serialize_optimized( $value );
			}
			$buf .= "}";
			return $buf;
		} else {
			return $buf . serialize( $data );
		}
	}

	/** DATE **/
	public static function ago($createddate) {

		$str = $createddate;
		$today = strtotime(date('Y-m-d H:i:s'));

		// It returns the time difference in Seconds...
		$time_differnce = $today - $str;

		// To Calculate the time difference in Years...
		$years = 60 * 60 * 24 * 365;

		// To Calculate the time difference in Months...
		$months = 60 * 60 * 24 * 30;

		// To Calculate the time difference in Days...
		$days = 60 * 60 * 24;

		// To Calculate the time difference in Hours...
		$hours = 60 * 60;

		// To Calculate the time difference in Minutes...
		$minutes = 60;

		if (intval($time_differnce / $years) > 1) {
			$datediff = intval($time_differnce / $years) . ' years ago';
		} else if (intval($time_differnce / $years) > 0) {
			$datediff = intval($time_differnce / $years) . ' year ago';
		} else if (intval($time_differnce / $months) > 1) {
			$datediff = intval($time_differnce / $months) . ' months ago';
		} else if (intval(($time_differnce / $months)) > 0) {
			$datediff = intval(($time_differnce / $months)) . ' month ago';
		} else if (intval(($time_differnce / $days)) > 1) {
			$datediff = intval(($time_differnce / $days)) . ' days ago';
		} else if (intval(($time_differnce / $days)) > 0) {
			$datediff = intval(($time_differnce / $days)) . ' day ago';
		} else if (intval(($time_differnce / $hours)) > 1) {
			$datediff = intval(($time_differnce / $hours)) . ' hours ago';
		} else if (intval(($time_differnce / $hours)) > 0) {
			$datediff = intval(($time_differnce / $hours)) . ' hour ago';
		} else if (intval(($time_differnce / $minutes)) > 1) {
			$datediff = intval(($time_differnce / $minutes)) . ' minutes ago';
		} else if (intval(($time_differnce / $minutes)) > 0) {
			$datediff = intval(($time_differnce / $minutes)) . ' minute ago';
		} else if (intval(($time_differnce)) > 5) {
			$datediff = intval(($time_differnce)) . ' seconds ago';
		} else {
			$datediff = ' few seconds ago';
		}

		return $datediff;
	}

}