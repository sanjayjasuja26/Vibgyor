


<div class="tugii-complete">Complete</div>
<div style="padding-top: 20px; text-align: center;">
	Operation is completed. <br />
  <?php
		foreach ( $list as $type => $files ) {
			echo $type . "<br/>\n";

			foreach ( $files as $file )

			{
				echo '\t => ' . $file->path . "<br/>\n";
			}
		}
		?>
</div>