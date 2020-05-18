<?php
echo '<?php ';
?>

<?php
$api_test_array = array();
foreach ($generator->getTableSchema()->columns as $column) :

    if ($column->name != 'id') :
        $api_test_array [$generator->ModelID . '[' . $column->name . ']'] = $generator->getFieldtestdata($generator->modelClass, $column);


    endif;
endforeach;
?> 

return [
"<?= $generator->controllerID ?>" => [
"add" => [<?php
echo PHP_EOL;
foreach ($api_test_array as $key => $value) :
    echo '			"' . $key . '" => ' . $value . ',' . PHP_EOL;
endforeach;
?>			],
"update?id={id}"=>  [<?php
echo PHP_EOL;
foreach ($api_test_array as $key => $value) :
    echo '			"' . $key . '" => ' . $value . ',' . PHP_EOL;
endforeach;
?>			],
"index" => [],
"get?id={}" => [],
"delete?id={}" => []
]
];
?>
