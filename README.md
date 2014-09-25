# Trait-Orm-Format

* Version: 1.0

## Information

* PHP >= 5.4
* FuelPHP = 1.7/master

## Description

FuelPHPのORMに書式化出力を提供します。

モデルのフィールドに書式を設定し、プロパティアクセスで書式化出力を行う。
* 例）Model_Personのageフィールドについて書式"%d歳"が設定されている場合

		echo $person->age // "12"
		echo $person->formatted_age //"12歳"

## Install

* git clone https://github.com/goosys/Fuel-Package-TraitOrmFormat.git fuel/packages/trait-orm-format
* vi fuel/app/config.php

		always_load => 
			packages => 'trait-orm-format',
			language => 'trait-orm-format'

* cp fuel/packages/trait-orm-format/lang/ja/trait-orm-format.php fuel/app/lang/ja/trait-orm-format.php

## Example

### Model

* fuel/app/classes/model/person.php

		class Model_Person extends \Orm\Model
		{
			use Trait_Orm_Format;

* var_dump($person);

		array(
			'name' => 'taro',
			'age'  => 12,
			'birthday' => '19800910',
			'gender' => 1, //0:woman,1:man
		);

### Lang

* vi fuel/app/lang/ja/trait-orm-format.php

		'common' => array(
			'jpy'       => function($val){ return number_format($val).'円'; },
			'percent'   => '%d%%',
			'timetodate'      => function($val){ return date('Y/m/d',$val); },
			'strtodate'       => function($val){ return date('Y/m/d',strtotime($val)); },
		),
		
		//Model_Person
		'model.person' => array(
			'age' => '%d歳',
			'birthday' => 'common.timetodate',
			'gender' => 'selector.gender',
		),

* vi fuel/app/lang/ja/selector.php

		'gender' => array(
			0 => '女性',
			1 => '男性'
		),
		'country' => array(
			'jp' => '日本',
			'us' => 'アメリカ',
			'uk' => 'イギリス',
			'de' => 'ドイツ',
		),

### Output

		echo $person->formatted_age; //"12歳"
		echo $person->formatted_birthday; //"1980/09/10"
		echo $person->formatted_name; //"taro"
		echo $person->formatted_gender; //"男性"

## Usage

### String Format

* sprintfの結果を出力

		"%d%%" //"20%"
		"%.2f%%" //"20.05%
		"%s" //"hello"

### Selector Format

* 選択肢から選択した値を出力

		"selector.gender" //"女性"
		"selector.country" //"日本"

### Functional Format

* Closureの結果を出力

		function($val){ return date('Y/m/d',$val); } //$val="1411620061" //"2014/09/25"
		function($val){ return number_format($val).'円'; } //$val="200000" //"200,000円"

### Undefined Format

* 未定義なら元のプロパティ値を出力

## Customize

## License

MIT License