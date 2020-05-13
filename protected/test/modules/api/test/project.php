<?php  
 

return [
	"project" => [
		"add" => [
			"Project[title]" => \Faker\Factory::create()->text(10),
			"Project[url]" => \Faker\Factory::create()->text(10),
			"Project[description]" => \Faker\Factory::create()->text,
			"Project[conclusion]" => \Faker\Factory::create()->text,
			"Project[target_count]" => \Faker\Factory::create()->text(10),
			"Project[target_time]" => \Faker\Factory::create()->text(10),
			"Project[task_time]" => \Faker\Factory::create()->text(10),
			"Project[reward]" => \Faker\Factory::create()->text(10),
			"Project[location]" => \Faker\Factory::create()->text(10),
			"Project[state_id]" => 0,
			"Project[type_id]" => 0,
			"Project[is_archived]" => \Faker\Factory::create()->boolean,
			"Project[start_date]" => "2018-04-17 17:25:43",
			"Project[end_date]" => "2018-04-17 17:25:43",
			"Project[created_on]" => "2018-04-17 17:25:43",
			"Project[created_by_id]" => 1,
			],
		"update?id={id}"=>  [
			"Project[title]" => \Faker\Factory::create()->text(10),
			"Project[url]" => \Faker\Factory::create()->text(10),
			"Project[description]" => \Faker\Factory::create()->text,
			"Project[conclusion]" => \Faker\Factory::create()->text,
			"Project[target_count]" => \Faker\Factory::create()->text(10),
			"Project[target_time]" => \Faker\Factory::create()->text(10),
			"Project[task_time]" => \Faker\Factory::create()->text(10),
			"Project[reward]" => \Faker\Factory::create()->text(10),
			"Project[location]" => \Faker\Factory::create()->text(10),
			"Project[state_id]" => 0,
			"Project[type_id]" => 0,
			"Project[is_archived]" => \Faker\Factory::create()->boolean,
			"Project[start_date]" => "2018-04-17 17:25:43",
			"Project[end_date]" => "2018-04-17 17:25:43",
			"Project[created_on]" => "2018-04-17 17:25:43",
			"Project[created_by_id]" => 1,
			],
		"index" => [],
		"get?id={}" => [],
		"delete?id={}" => []
	]
];
?>
