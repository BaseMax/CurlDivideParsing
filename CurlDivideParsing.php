<?php
/**
 *
 * @Name : CurlDivideParsing
 * @File : CurlDivideParsing.php
 * @Version : 1.0
 * @Programmer : Max
 * @Date : 2019-12-03, 2019-12-04
 * @Released under : https://github.com/BaseMax/CurlDivideParsing/blob/master/LICENSE
 * @Repository : https://github.com/BaseMax/CurlDivideParsing
 *
 **/
function get_user_agent_string() {
	return "Mozilla/5.0 (Windows NT 6.1; r…) Gecko/20100101 Firefox/60.0";
	return "Mozilla/5.0(Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36(KHTML,like Gecko) curlrome/68.0.3440.106 Mobile Safari/537.36";
}
function curl_get_file_size($url) {
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_NOBODY, true);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_HEADER, true);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
	$data = curl_exec($curl);
	curl_close($curl);
	if($data === false) {
		return -1;
	}
	if(preg_match('/Content-Length: (\d+)/', $data, $matches)) {
		return (int)$matches[1];
	}
	return -1;
}
function curl_download_file($url, $part, $from, $to) {
	global $output;
	print $from." / ". $to."\n";
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_NOBODY, false);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

	curl_setopt($curl, CURLOPT_RANGE, $from . '-' . $to);
	curl_setopt($curl, CURLOPT_BINARYTRANSFER, 1);

	$data = curl_exec($curl);
	curl_close($curl);
	if($part == 0) {
		file_put_contents($output, $data);
	}
	else {
		file_put_contents($output, $data, FILE_APPEND);
	}
}
$link='https://r1---sn-hp57kn67.googlevideo.com/videoplayback?expire=1575479302&ei=ppPnXbW4DpzX7gSQlJvQBA&ip=2001%3A470%3A78c7%3Ad4a1%3Af6d9%3Aa2c3%3Abf91%3A1f15&id=o-AOUCuRpTeaPIB0YhJihnmK_VrQJcrkESckfK9iMffSYe&itag=135&aitags=133%2C134%2C135%2C136%2C137%2C160%2C242%2C243%2C244%2C247%2C248%2C278%2C394%2C395%2C396%2C397&source=youtube&requiressl=yes&mime=video%2Fmp4&gir=yes&clen=16101953&dur=427.800&lmt=1570275726723112&fvip=1&keepalive=yes&fexp=23842630,23860862&c=WEB&txp=5535432&sparams=expire%2Cei%2Cip%2Cid%2Caitags%2Csource%2Crequiressl%2Cmime%2Cgir%2Cclen%2Cdur%2Clmt&sig=ALgxI2wwRAIgL_ZQEHCd6mq_JvtS7MQvV1FSMPeo76P7OoekAKiIVZ8CIF8MmBHyjdKDcCl4dm3oxD4VnsgqF4B_boMNRBSCM0xi&ratebypass=yes&redirect_counter=1&rm=sn-5hnell7e&req_id=c0ae6b2649c936e2&cms_redirect=yes&ipbypass=yes&mip=2605:6400:10:7b2e:b6a7:9555:8d67:b8b7&mm=31&mn=sn-hp57kn67&ms=au&mt=1575457719&mv=m&mvi=0&pl=48&lsparams=ipbypass,mip,mm,mn,ms,mv,mvi,pl&lsig=AHylml4wRgIhANg7JxNv_gbx-TT5iqYcIG8A1ne9MjDjNs5NDMRUJcORAiEAkrmYasNJs2gw64XWbwK-uoeDt6Nk0xEiGr6FvXf7pao=';
$output="movie.mpg";
$size=curl_get_file_size($link);
if($size != -1) {
	print "Total Size: $size\n";
	$split=10485760;// 1024*1024*10 =10MByte
	$partLength=ceil($size / $split);
	$byteIndex=0;
	print "Parts: \n";
	for($partIndex=0; $partIndex < $partLength; $partIndex++) {
		$nextByteIndex=$byteIndex+$split;
		if($nextByteIndex > $size) {
			$nextByteIndex=$size;
		}
		// Download current part...
		curl_download_file($link, $partIndex, $byteIndex, $nextByteIndex);
		$byteIndex+=$split;
		if($byteIndex > $size) {
			$byteIndex=$size;
		}
	}
}
else {
	print "Cannot access to link...";
}
