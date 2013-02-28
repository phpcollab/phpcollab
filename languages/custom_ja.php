<?php
$topicNote = array(1 => "電話での会話", 2 => "会議録", 3 => "メモ");

$phaseArraySets = array(
	#Define the names of your phase sets
	#プロジェクトのフェーズをフェーズ・セットとしてプリセットする
	"sets" => array(1 => "ウェブ・サイト", 2 => "CD"),
	#List the indervitual items within each phase set.
	#各フェーズ・セットの要素工程をリストアップする。
	#Website Set
	"1" => array(0 => "計画", 1 => "設計", 2 => "テスト", 3 => "納品", 4 => "コーディング"),
	#CD Set
	"2" => array(0 => "計画", 1 => "設計", 2 => "テスト", 3 => "コーディング")
);

?>