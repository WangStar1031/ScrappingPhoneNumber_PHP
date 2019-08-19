<?php
set_time_limit(-1);
error_reporting();

$url = "https://www.marktplaats.nl/l/audio-tv-en-foto/fotografie-camera-s-digitaal/f/zo-goed-als-nieuw/31/p/2/#f:35,32|searchInTitleAndDescription:false";

require_once "simple_html_dom.php";
function getAllUrlsFromList4Search($_listPageContents){
	$html = str_get_html($_listPageContents);
	$records = $html->find("li.mp-Listing--list-item a");
	$arrRet = [];
	if( !$records)return $arrRet;
	foreach ($records as $value) {
		$arrRet[] = "https://www.marktplaats.nl" . $value->getAttribute("href");
	}
	return $arrRet;
}

$contents = file_get_contents($url);
// print_r($contents);
$pageUrls4Scrape = getAllUrlsFromList($contents);
print_r($pageUrls4Scrape);
?>