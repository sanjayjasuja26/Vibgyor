<?php  
 

return [
	"task" => [
		"add" => [
			"Task[title]" => \Faker\Factory::create()->text(10),
			"Task[description]" => \Faker\Factory::create()->text,
			"Task[work_done]" => \Faker\Factory::create()->text,
			"Task[cancel_reason]" => \Faker\Factory::create()->text,
			"Task[task_time]" => \Faker\Factory::create()->text(10),
			"Task[project_id]" => 1,
			"Task[start_date]" => "2018-04-17 17:58:09",
			"Task[end_date]" => "2018-04-17 17:58:09",
			"Task[state_id]" => 0,
			"Task[type_id]" => 0,
			"Task[created_on]" => "2018-04-17 17:58:09",
			"Task[updated_on]" => "2018-04-17 17:58:09",
			"Task[created_by_id]" => 1,
			],
		"update?id={id}"=>  [
			"Task[title]" => \Faker\Factory::create()->text(10),
			"Task[description]" => \Faker\Factory::create()->text,
			"Task[work_done]" => \Faker\Factory::create()->text,
			"Task[cancel_reason]" => \Faker\Factory::create()->text,
			"Task[task_time]" => \Faker\Factory::create()->text(10),
			"Task[project_id]" => 1,
			"Task[start_date]" => "2018-04-17 17:58:09",
			"Task[end_date]" => "2018-04-17 17:58:09",
			"Task[state_id]" => 0,
			"Task[type_id]" => 0,
			"Task[created_on]" => "2018-04-17 17:58:09",
			"Task[updated_on]" => "2018-04-17 17:58:09",
			"Task[created_by_id]" => 1,
			],
	    "comment?id={id}"=>  [
	        "Comment[file]" => '',
	        "Task[work_done]" => \Faker\Factory::create()->text,
	    ],
		"index" => [],
		"get?id={}" => [],
		"delete?id={}" => []
	]
];
?>
