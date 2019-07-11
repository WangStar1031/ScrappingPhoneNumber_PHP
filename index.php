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
	// print_r($_listPageContents);
	$html = str_get_html($_listPageContents);
	// print_r($html);
	$records = $html->find("article.search-result");
	$arrRet = [];
	if( !$records)return $arrRet;
	foreach ($records as $value) {
		$arrRet[] = $value->getAttribute("data-url");
	}
	return $arrRet;
}
$fileName = "contacts_" . $pageFrom . "_" . $pageTo . ".csv";
file_put_contents($fileName, "Name,Given Name,Additional Name,Family Name,Yomi Name,Given Name Yomi,Additional Name Yomi,Family Name Yomi,Name Prefix,Name Suffix,Initials,Nickname,Short Name,Maiden Name,Birthday,Gender,Location,Billing Information,Directory Server,Mileage,Occupation,Hobby,Sensitivity,Priority,Subject,Notes,Language,Photo,Group Membership,Phone 1 - Type,Phone 1 - Value");

for( $i = $pageFrom; $i <= $pageTo; $i++){
	echo "<br>" . $i . "<br>";
	file_put_contents("working.txt", $i);
	$list_url = "https://www.marktplaats.nl/z/kleding-dames/blouses-en-tunieken/gedragen-ophalen-of-verzenden-zo-goed-als-nieuw.html?categoryId=628&attributes=S%2C4941+S%2C35+S%2C31&currentPage=" . $i;
	$contents = file_get_contents($list_url);
	if( !$contents)
		continue;
	$pageUrls4Scrape = getAllUrlsFromList($contents);
	// print_r($pageUrls4Scrape);
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
// exit();
// $contents = file_get_contents("https://www.marktplaats.nl/a/kleding-dames/jassen-zomer/m1432768838-bomber-jack.html?c=efb2ef4dc323389c4f92ed10afa33e3a&amp;previousPage=lr");
// $arrContents = explode("sellerPhone:", $contents);
// if( count($arrContents) > 1){
// 	$strBuf = $arrContents[1];
// 	$strTemp = trim( explode(",", $strBuf)[0]);
// 	$strTemp = trim( str_replace("'", "", $strTemp));
// 	$strTemp = str_replace("\n", "", $strTemp);
// 	echo $strTemp;
// }
// echo getPhoneNumber($contents);
// exit();
// echo $contents;
// https://www.marktplaats.nl/z/kleding-dames/blouses-en-tunieken.html?categoryId=628&attributes=S%2C31&attributes=S%2C4941&attributes=S%2C35&startDateFrom=always

// https://www.marktplaats.nl/z/kleding-dames/blouses-en-tunieken/gedragen-ophalen-of-verzenden-zo-goed-als-nieuw.html?categoryId=628&attributes=S%2C4941+S%2C35+S%2C31&currentPage=2

// https://www.marktplaats.nl/z/kleding-dames/blouses-en-tunieken/gedragen-ophalen-of-verzenden-zo-goed-als-nieuw.html?categoryId=628&attributes=S%2C4941+S%2C35+S%2C31&currentPage=3

?>

<a id="donwload" href="<?=$fileName?>" donwload></a>