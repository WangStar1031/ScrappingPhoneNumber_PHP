<?php
set_time_limit(-1);
error_reporting();

require_once "simple_html_dom.php";
$arrUrls = [
	"https://www.marktplaats.nl/l/boeken/gezondheid-dieet-en-voeding/#f:35",
	"https://www.marktplaats.nl/l/boeken/kookboeken/#f:35",
	"https://www.marktplaats.nl/l/boeken/kinderboeken-baby-s-en-peuters/#f:35",
	"https://www.marktplaats.nl/l/boeken/kinderboeken-jeugd-13-jaar-en-ouder/#f:35",
	"https://www.marktplaats.nl/l/boeken/film-tv-en-media/#f:35",
	"https://www.marktplaats.nl/l/boeken/kunst-en-cultuur-architectuur/f/zo-goed-als-nieuw/31/#f:35,1602",
	"https://www.marktplaats.nl/l/boeken/muziek/f/gelezen/1602/#f:35,31",
	"https://www.marktplaats.nl/l/boeken/kunst-en-cultuur-fotografie-en-design/f/zo-goed-als-nieuw/31/#f:35,1602",
	"https://www.marktplaats.nl/l/boeken/kunst-en-cultuur-beeldend/f/zo-goed-als-nieuw/31/#f:35,1602",
	"https://www.marktplaats.nl/l/boeken/encyclopedieen/f/zo-goed-als-nieuw/31/#f:35,1602",
	"https://www.marktplaats.nl/l/boeken/economie-management-en-marketing/f/zo-goed-als-nieuw/31/#f:35,1602"
];

function getPhoneNumber($_strPageContents){
	$arrContents = explode("sellerPhone", $_strPageContents);
	if( count($arrContents) < 2) return "";
	$strBuf = trim(explode(",", $arrContents[1])[0]);
	$strRet = trim( str_replace("'", "", $strBuf));
	$strRet = trim(str_replace(':', "", $strRet));
	$strRet = str_replace('\n', "", $strRet);
	$strRet = str_replace(PHP_EOL, "", $strRet);
	return $strRet;
}
function getAllUrlsFromList($_listPageContents){
	$html = str_get_html($_listPageContents);
	$records = $html->find("article.search-result");
	$arrRet = [];
	if( !$records)return $arrRet;
	foreach ($records as $value) {
		$arrRet[] = $value->getAttribute("data-url");
	}
	return $arrRet;
}
function getAllUrlsFromList4Search($_listPageContents){
	$html = str_get_html($_listPageContents);
	$records = $html->find("li.mp-Listing--list-item a");
	$arrRet = [];
	if( !$records)return $arrRet;
	foreach ($records as $value) {
		$_curUrl = "https://www.marktplaats.nl" . $value->getAttribute("href");
		if( !in_array( $_curUrl, $arrRet)){
			$arrRet[] = $_curUrl;
		}
	}
	return $arrRet;
}
$id = 0;
if( isset($_GET['id'])){
	$id = $_GET['id'];
}
$from = 1;
$to = 1;
switch ($id) {
	case 0:
		$from = 1; $to = 437;
		break;
	case 1:
		$from = 1; $to = 669;
		break;
	case 2:
		$from = 1; $to = 156;
		break;
	case 3:
		$from = 1; $to = 404;
		break;
	case 4:
		$from = 1; $to = 45;
		break;
	case 5:
		$from = 1; $to = 83;
		break;
	case 6:
		$from = 1; $to = 116;
		break;
	case 7:
		$from = 1; $to = 104;
		break;
	case 8:
		$from = 1; $to = 341;
		break;
	case 9:
		$from = 1; $to = 31;
		break;
	case 10:
		$from = 1; $to = 468;
		break;
}

if( !file_exists(__DIR__ . "/results/")){
	mkdir(__DIR__ . "/results/");
}

$fileName = __DIR__ . "/results/id_" . $id . ".csv";
file_put_contents($fileName, "Name,Given Name,Additional Name,Family Name,Yomi Name,Given Name Yomi,Additional Name Yomi,Family Name Yomi,Name Prefix,Name Suffix,Initials,Nickname,Short Name,Maiden Name,Birthday,Gender,Location,Billing Information,Directory Server,Mileage,Occupation,Hobby,Sensitivity,Priority,Subject,Notes,Language,Photo,Group Membership,Phone 1 - Type,Phone 1 - Value");
$curUrl = $arrUrls[$id];
$arrTemp = explode("#", $curUrl);
$prefix = $arrTemp[0];
$endfix = "#" . $arrTemp[1];
// $to = 1;
$is_Search = true;
for( $i = $from; $i <= $to; $i++){
	echo "<br>" . $i . "<br>";
	file_put_contents(__DIR__ . "/results/working_id_" . $id . ".txt", $i);
	$list_url = $prefix . "p/" . $i . "/" . $endfix;
	
	$contents = file_get_contents($list_url);
	if( !$contents)
		continue;
	$pageUrls4Scrape = [];
	if( $is_Search == false){
		$pageUrls4Scrape = getAllUrlsFromList($contents);
	} else{
		$pageUrls4Scrape = getAllUrlsFromList4Search($contents);
	}
	// print_r( $pageUrls4Scrape);
	foreach ($pageUrls4Scrape as $pageUrl) {
		$pageContents = @file_get_contents($pageUrl);
		if( $pageContents == "")
			continue;
		$pageHtml = str_get_html($pageContents);
		if( count($pageHtml->find("#title") ) == 0 || count($pageHtml->find("span.price") ) == 0)
			continue;
		$mainName = $pageHtml->find("#title")[0]->text();
		$price = $pageHtml->find("span.price")[0]->text();
		$phoneNumber = getPhoneNumber($pageContents);
		if( $phoneNumber == ""){
			// echo "SKIPPED-- " . $pageUrl . " : " . " : " . $mainName . " : " . $price . " : " . $phoneNumber;
		}else{
			$strAppend = PHP_EOL . ',"' . $mainName . " + " . $price . '",,,,,,,,,,,,,,,,,,,,,,,,,,,,,' . $phoneNumber;
			echo $pageUrl . " : " . " : " . $mainName . " : " . $price . " : " . $phoneNumber;
			file_put_contents($fileName, $strAppend, FILE_APPEND);
			echo "<br>";
		}
	}

}
// $srcFileName = "31_0_1_157.csv";
$fileContents = file_get_contents($fileName);
$arrContents = explode(PHP_EOL, $fileContents);
$arrRealContents = [];
$arrPhonenumbers = [];
foreach ($arrContents as $value) {
	if( $value == "")
		continue;
	$arrBuff = explode(",", $value);
	$phoneNumber = $arrBuff[count($arrBuff) - 1];
	if( !in_array( $phoneNumber, $arrPhonenumbers)){
		$arrPhonenumbers[] = $phoneNumber;
		$arrRealContents[] = $value;
	}
}
file_put_contents($fileName, implode(PHP_EOL, $arrRealContents));
// Sample url : http://localhost/phone_check/?categoryId=630&from=1&to=167
?>

<a id="donwload" href="<?=$fileName?>" donwload></a>