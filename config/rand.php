<?php
function getRandTop(){
	$timeAry=array("all"=>100,
					"30600"=>100,
					"30601"=>100,
					"30602"=>100,
					"30616"=>5000,
	);

	$dt=date("ndH");
	if(array_key_exists($dt,$timeAry)){
		return $timeAry[$dt];
	}
	return $timeAry["all"];
}
?>