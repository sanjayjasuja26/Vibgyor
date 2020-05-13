<?php  
 

return [
	"rewards" => [
		"add" => [
			"Reward[title]" => \Faker\Factory::create()->text(10),
			"Reward[description]" => \Faker\Factory::create()->text(10),
			"Reward[points]" => \Faker\Factory::create()->text(10),
			"Reward[start_date]" => "2018-04-17 17:58:46",
			"Reward[end_date]" => "2018-04-17 17:58:46",
			"Reward[state_id]" => 0,
			"Reward[type_id]" => 0,
			"Reward[created_on]" => "2018-04-17 17:58:46",
			"Reward[updated_on]" => "2018-04-17 17:58:46",
			"Reward[created_by_id]" => 1,
			],
		"update?id={id}"=>  [
			"Reward[title]" => \Faker\Factory::create()->text(10),
			"Reward[description]" => \Faker\Factory::create()->text(10),
			"Reward[points]" => \Faker\Factory::create()->text(10),
			"Reward[start_date]" => "2018-04-17 17:58:46",
			"Reward[end_date]" => "2018-04-17 17:58:46",
			"Reward[state_id]" => 0,
			"Reward[type_id]" => 0,
			"Reward[created_on]" => "2018-04-17 17:58:46",
			"Reward[updated_on]" => "2018-04-17 17:58:46",
			"Reward[created_by_id]" => 1,
			],
		"index" => [],
		"get?id={}" => [],
		"delete?id={}" => []
	]
];
?>
