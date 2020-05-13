<table class="table table-striped">
	<thead>
		<tr>
			<th scope="col">Url</th>
			<th scope="col">Result</th>
		</tr>
	</thead>
	<tbody>
  <?php
foreach ($urls as $url) {
    $link = yii\helpers\Url::to($url['loc'], true);
    $response = @get_headers($link);
    list ($http, $code, $status) = explode(' ', $response[0]);
    $class = 'btn btn-danger';
    
    if ($code == '200')
        $class = 'btn btn-success';
    if ($code == '302')
        $class = 'btn btn-warning';
    ?>
    <tr>
			<td scope="row"><?= $link?></td>
			<?php ?>
			
			<td><span class="<?=$class?>"> <?= $code?></span></td>
		</tr>
    <?php }?>
  </tbody>
</table>