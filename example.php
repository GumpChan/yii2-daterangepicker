<?php 

use namespace\DatePicker

echo DatePicker::widget([
	'type' => DatePicker::TYPE_RANGE,
	'pluginOptions' => [
		'language' => 'zh-CN',
		'init' => [
		'startDate' => date("Y-m-d"),//set output format through the property 'format'
		'endDate' => date("Y-m-d"),
		'separator'=> ' - ',
		],
	//'minDate'=> "01/01/2012",
	'maxDate'=> date("Y-m-d"),
	'dateLimit'=> [
		'days'=>60
	],
	'showDropdowns'=> true,
	'showWeekNumbers'=> false,
	'timePicker'=> false,
	'timePickerIncrement'=>1,
	'timePicker12Hour'=> true,
	'ranges'=> [
		'今天'=> [date("Y-m-d")],
		'昨天'=> [date("Y-m-d",strtotime("-1 day")),date("Y-m-d",strtotime("-1 day"))],
		'过去七天'=> [date("Y-m-d",strtotime("-7 day")), date("Y-m-d")],
		'过去30天'=> [date("Y-m-d",strtotime("-29 day")), date("Y-m-d")],
		'当月'=> [date("Y-m-01"), date("Y-m-d")],
		'上月'=> [date("Y-m-01",strtotime("last month")),date("Y-m-t",strtotime("last month"))]
	],
	'opens'=> 'left',
	'buttonClasses'=> ['btn btn-default'],
	'applyClass'=> 'btn-small btn-primary',
	'cancelClass'=> 'btn-small',
	'format'=> "YYYY-MM-DD",
	'separator'=> ' 到 ',
	'spanLayout'=>'{start}-{end}'
	
	],  
	'pluginEvents' => [
		'apply' => "function(ev, picker){
			picker.startDate.format('MMMM D, YYYY'); 
			picker.endDate.format('MMMM D, YYYY');
		}", 
	],
]);
?>
