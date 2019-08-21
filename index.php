<?php
set_time_limit(-1);
error_reporting();

require_once "simple_html_dom.php";

$pageFrom = 1;
if( isset($_GET['from'])){
	$pageFrom = $_GET['from'];
}
$pageTo = 10000;
if( isset($_GET['to'])){
	$pageTo = $_GET['to'];
}
$categoryId = 628;
if( isset($_GET['categoryId'])){
	$categoryId = $_GET['categoryId'];
}
$subCatId = 0;
if( isset($_GET['subCatId'])){
	$subCatId = $_GET['subCatId'];
}

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
		$curUrl = "https://www.marktplaats.nl" . $value->getAttribute("href");
		if( !in_array( $curUrl, $arrRet)){
			$arrRet[] = $curUrl;
		}
	}
	return $arrRet;
}
$list_url = "";
$url_prefix = "";
$url_last = "";
$url_endings = "";
$is_Search = false;
switch ($categoryId) {
	case 628:
		$pageTo = $pageTo > 167 ? 167 : $pageTo;
		$url_prefix = "https://www.marktplaats.nl/z/kleding-dames/blouses-en-tunieken/gedragen-ophalen-of-verzenden-zo-goed-als-nieuw.html?categoryId=";
		$url_last = "&attributes=S%2C4941+S%2C35+S%2C31&currentPage=";
		break;
	case 642:
		$pageTo = $pageTo > 167 ? 167 : $pageTo;
		$url_prefix = "https://www.marktplaats.nl/z/kleding-heren/schoenen/gedragen-ophalen-of-verzenden-zo-goed-als-nieuw.html?categoryId=";
		$url_last = "&attributes=S%2C4941+S%2C35+S%2C31&currentPage=";
		break;
	case 2784:
		$pageTo = $pageTo > 106 ? 106 : $pageTo;
		$url_prefix = "https://www.marktplaats.nl/z/kleding-dames/jassen-winter/gedragen-ophalen-of-verzenden-zo-goed-als-nieuw.html?categoryId=";
		$url_last = "&attributes=S%2C4941+S%2C35+S%2C31&currentPage=";
		break;
	case 630:
		$pageTo = $pageTo > 167 ? 167 : $pageTo;
		$url_prefix = "https://www.marktplaats.nl/z/kleding-dames/jassen-zomer/gedragen-ophalen-of-verzenden-zo-goed-als-nieuw.html?categoryId=";
		$url_last = "&attributes=S%2C4941+S%2C35+S%2C31&currentPage=";
		break;
	case 771:
		$pageTo = $pageTo > 4 ? 4 : $pageTo;
		$url_prefix = "https://www.marktplaats.nl/z/muziek-en-instrumenten/blaasinstrumenten-klarinetten/ophalen-of-verzenden.html?categoryId=";
		$url_last = "&attributes=S%2C35&currentPage=";
		break;
	case 1766: // done
		$pageTo = $pageTo > 9 ? 9 : $pageTo;
		$url_prefix = "https://www.marktplaats.nl/z/muziek-en-instrumenten/blaasinstrumenten-saxofoons/ophalen-of-verzenden.html?categoryId=";
		$url_last = "&attributes=S%2C35&currentPage=";
		break;
	case 247:
		$pageTo = $pageTo > 95 ? 95 : $pageTo;
		$url_prefix = "https://www.marktplaats.nl/z/doe-het-zelf-en-verbouw/gereedschap-handgereedschap/ophalen-of-verzenden-gebruikt-zo-goed-als-nieuw.html?categoryId=";
		$url_last = "&attributes=S%2C35+S%2C32+S%2C31&currentPage=";
		break;
	case 1882:
		$pageTo = $pageTo > 167 ? 167 : $pageTo;
		$url_prefix = "https://www.marktplaats.nl/z/boeken/geschiedenis-wereld/ophalen-of-verzenden.html?categoryId=";
		$url_last = "&attributes=S%2C35&currentPage=";
		break;
	case 1953:
		$pageTo = $pageTo > 85 ? 85 : $pageTo;
		$url_prefix = "https://www.marktplaats.nl/z/telecommunicatie/mobiele-telefoons-apple-iphone/zonder-abonnement-zonder-simlock-ophalen-of-verzenden-gebruikt-zo-goed-als-nieuw.html?categoryId=";
		$url_last = "&attributes=S%2C6864+S%2C6862+S%2C35+S%2C32+S%2C31&currentPage=";
		break;
	case 2:	// done
		$pageTo = $pageTo > 167 ? 167 : $pageTo;
		$url_prefix = "https://www.marktplaats.nl/z/antiek-en-kunst/antiek-bestek/ophalen-of-verzenden.html?categoryId=";
		$url_last = "&attributes=S%2C35&currentPage=";
		break;
	case 487:	//done
		$pageTo = $pageTo > 160 ? 160 : $pageTo;
		$url_prefix = "https://www.marktplaats.nl/z/audio-tv-en-foto/fotografie-camera-s-digitaal/ophalen-of-verzenden-gebruikt-zo-goed-als-nieuw.html?categoryId=";
		$url_last = "&attributes=S%2C35+S%2C32+S%2C31&currentPage=";
		break;
	case 31:
		$is_Search = true;
		$pageTo = $pageTo > 157 ? 157 : $pageTo;
		$url_prefix = "https://www.marktplaats.nl/l/audio-tv-en-foto/fotografie-camera-s-digitaal/f/zo-goed-als-nieuw/";
		$url_endings = "/#f:35,32|searchInTitleAndDescription:false";
		break;
	case 32:
		$is_Search = true;
		$url_prefix = "https://www.marktplaats.nl/l/telecommunicatie/mobiele-telefoons-apple-iphone/f/gebruikt/";
		switch ($subCatId) {
			case 0:
				$pageTo = $pageTo > 7 ? 7 : $pageTo;
				$url_endings = "/#f:11345,35|searchInTitleAndDescription:false";
				break;
			case 1: 
				$url_endings = "/#f:35,11604|searchInTitleAndDescription:false";
				$pageTo = $pageTo > 12 ? 12 : $pageTo;
				break;
		}
		break;
	
	default:
		# code...
		break;
}

$fileName = $categoryId . "_" . $subCatId . "_" . $pageFrom . "_" . $pageTo . ".csv";
file_put_contents($fileName, "Name,Given Name,Additional Name,Family Name,Yomi Name,Given Name Yomi,Additional Name Yomi,Family Name Yomi,Name Prefix,Name Suffix,Initials,Nickname,Short Name,Maiden Name,Birthday,Gender,Location,Billing Information,Directory Server,Mileage,Occupation,Hobby,Sensitivity,Priority,Subject,Notes,Language,Photo,Group Membership,Phone 1 - Type,Phone 1 - Value");

for( $i = $pageFrom; $i <= $pageTo; $i++){
	echo "<br>" . $i . "<br>";
	file_put_contents("working_" . $categoryId . "_" . $subCatId . ".txt", $i);
	$list_url = $url_prefix . $categoryId . $url_last . $i . $url_endings;
	
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