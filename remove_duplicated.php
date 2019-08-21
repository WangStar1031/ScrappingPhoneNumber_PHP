<?php
$srcFileName = "31_0_1_157.csv";
$fileContents = file_get_contents($srcFileName);
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
file_put_contents($srcFileName, implode(PHP_EOL, $arrRealContents));
?>