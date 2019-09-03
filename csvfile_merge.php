<?php
$from = 0;
$to = 10;
$arrPhoneNumbers = [];
$arrRealContents = [];
$fileIndex = 1;
for( $i = $from; $i <= $to; $i++){
	$fileName = __DIR__ . "/results/id_" . $i . ".csv";
	$contents = file_get_contents($fileName);
	$arrContents = explode(PHP_EOL, $contents);
	foreach ($arrContents as $value) {
		if( $value == "")
			continue;
		$arrBuf = explode(",", $value);
		$phoneNumber = $arrBuf[count($arrBuf) - 1];
		if( !in_array($phoneNumber, $arrPhoneNumbers)){
			$arrPhoneNumbers[] = $phoneNumber;
			$arrRealContents[] = $value;
			if( count($arrRealContents) == 150){
				$OutputFileName = __DIR__ . "/results/all_id_" . $fileIndex . ".csv";
				$fileIndex ++;
				file_put_contents($OutputFileName, "Name,Given Name,Additional Name,Family Name,Yomi Name,Given Name Yomi,Additional Name Yomi,Family Name Yomi,Name Prefix,Name Suffix,Initials,Nickname,Short Name,Maiden Name,Birthday,Gender,Location,Billing Information,Directory Server,Mileage,Occupation,Hobby,Sensitivity,Priority,Subject,Notes,Language,Photo,Group Membership,Phone 1 - Type,Phone 1 - Value" . PHP_EOL);
				file_put_contents($OutputFileName, implode(PHP_EOL, $arrRealContents), FILE_APPEND);
				$arrRealContents = [];
			}
		}
	}
}
if( count($arrRealContents) != 0){
	$OutputFileName = __DIR__ . "/results/all_id_" . $fileIndex . ".csv";
	$fileIndex ++;
	file_put_contents($OutputFileName, "Name,Given Name,Additional Name,Family Name,Yomi Name,Given Name Yomi,Additional Name Yomi,Family Name Yomi,Name Prefix,Name Suffix,Initials,Nickname,Short Name,Maiden Name,Birthday,Gender,Location,Billing Information,Directory Server,Mileage,Occupation,Hobby,Sensitivity,Priority,Subject,Notes,Language,Photo,Group Membership,Phone 1 - Type,Phone 1 - Value" . PHP_EOL);
	file_put_contents($OutputFileName, implode(PHP_EOL, $arrRealContents), FILE_APPEND);
	$arrRealContents = [];
}
?>