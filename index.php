<?php
set_time_limit(-1);
error_reporting();

require_once "simple_html_dom.php";

$pageFrom = 2;
if( isset($_GET['from'])){
	$pageFrom = $_GET['from'];
}
$pageTo = 2;
if( isset($_GET['to'])){
	$pageTo = $_GET['to'];
}
$categoryId = 628;
if( isset($_GET['categoryId'])){
	$categoryId = $_GET['categoryId'];
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
$fileName = $categoryId . "_" . $pageFrom . "_" . $pageTo . ".csv";
file_put_contents($fileName, "Name,Given Name,Additional Name,Family Name,Yomi Name,Given Name Yomi,Additional Name Yomi,Family Name Yomi,Name Prefix,Name Suffix,Initials,Nickname,Short Name,Maiden Name,Birthday,Gender,Location,Billing Information,Directory Server,Mileage,Occupation,Hobby,Sensitivity,Priority,Subject,Notes,Language,Photo,Group Membership,Phone 1 - Type,Phone 1 - Value");
$list_url = "";
$url_prefix = "";
$url_last = "";
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
	
	default:
		# code...
		break;
}
for( $i = $pageFrom; $i <= $pageTo; $i++){
	echo "<br>" . $i . "<br>";
	file_put_contents("working_" . $categoryId . ".txt", $i);
	$list_url = $url_prefix . $categoryId . $url_last . $i;
	
	$contents = file_get_contents($list_url);
	if( !$contents)
		continue;
	$pageUrls4Scrape = getAllUrlsFromList($contents);
	foreach ($pageUrls4Scrape as $pageUrl) {
		$pageContents = @file_get_contents($pageUrl);
		if( $pageContents == "")
			continue;
		$pageHtml = str_get_html($pageContents);
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
// Sample url : http://localhost/phone_check/?categoryId=630&from=1&to=167
?>

<a id="donwload" href="<?=$fileName?>" donwload></a>