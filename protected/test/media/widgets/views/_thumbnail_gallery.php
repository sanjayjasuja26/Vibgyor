<style>
.btn:focus, .btn:active, button:focus, button:active {
	outline: none !important;
	box-shadow: none !important;
}

.gallery .modal-footer {
	display: block;
}

.thumb {
	margin-top: 15px;
	margin-bottom: 15px;
}

.thumb a i {
	color: #fff;
	text-aling : right;
}
</style>

<?php
if (! empty($images)) {
    foreach ($images as $image) {
        ?>
<div class="col-lg-3 col-md-4 col-xs-6 thumb">
	<a class="label label-danger pull-right" href="<?= $image['deleteUrl'] ?>"
		data-method="post"
		data-confirm="<?= \Yii::t('app', 'Are you sure you want to delete this item?') ?>"><i
		class="fa fa-trash"></i></a> <a class="thumbnail" href="#"
		data-image-id="<?= $image['id'] ?>" data-toggle="modal"
		data-title="<?= $image['title'] ?>" data-image="<?= $image['url'] ?>"
		data-target="#<?= $id ?>"> <img class="img-thumbnail"
		src="<?= $image['thumb'] ?>" alt="<?= $image['title'] ?>">
	</a>
	<div><?= $image['title'] ?></div>
</div>
<?php
    }
}
?>

<div class="modal fade gallery" id="<?= $id ?>" tabindex="-1"
	role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="<?= $id ?>-title"></h4>
				<button type="button" class="close" data-dismiss="modal">
					<span aria-hidden="true">×</span><span class="sr-only">Close</span>
				</button>
			</div>
			<div class="modal-body">
				<img id="<?= $id ?>-image" class="img-responsive col-md-12" src="">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary float-left"
					id="<?= $id ?>-previous-image">
					<i class="fa fa-arrow-left"></i>
				</button>

				<button type="button" id="<?= $id ?>-next-image"
					class="btn btn-secondary float-right">
					<i class="fa fa-arrow-right"></i>
				</button>
			</div>
		</div>
	</div>
</div>

<script>
let modalId = $('#<?= $id ?>');

$(document)
  .ready(function () {

    loadGallery(true, 'a.thumbnail');

    //This function disables buttons when needed
    function disableButtons(counter_max, counter_current) {
      $('#<?= $id ?>-previous-image, #<?= $id ?>-next-image')
        .show();
      if (counter_max === counter_current) {
        $('#<?= $id ?>-next-image')
          .hide();
      } else if (counter_current === 1) {
        $('#<?= $id ?>-previous-image')
          .hide();
      }
    }

    /**
     *
     * @param setIDs        Sets IDs when DOM is loaded. If using a PHP counter, set to false.
     * @param setClickAttr  Sets the attribute for the click handler.
     */

    function loadGallery(setIDs, setClickAttr) {
      let current_image,
        selector,
        counter = 0;

      $('#<?= $id ?>-next-image, #<?= $id ?>-previous-image')
        .click(function () {
          if ($(this)
            .attr('id') === '<?= $id ?>-previous-image') {
            current_image--;
          } else {
            current_image++;
          }

          selector = $('[data-image-id="' + current_image + '"]');
          updateGallery(selector);
        });

      function updateGallery(selector) {
        let $sel = selector;
        current_image = $sel.data('image-id');
        $('#<?= $id ?>-title')
          .text($sel.data('title'));
        $('#<?= $id ?>-image')
          .attr('src', $sel.data('image'));
        disableButtons(counter, $sel.data('image-id'));
      }

      if (setIDs == true) {
        $('[data-image-id]')
          .each(function () {
            counter++;
            $(this)
              .attr('data-image-id', counter);
          });
      }
      $(setClickAttr)
        .on('click', function () {
          updateGallery($(this));
        });
    }
  });

// build key actions
$(document)
  .keydown(function (e) {
    switch (e.which) {
      case 37: // left
        if ((modalId.data('bs.modal') || {})._isShown && $('#<?= $id ?>-previous-image').is(":visible")) {
          $('#<?= $id ?>-previous-image')
            .click();
        }
        break;

      case 39: // right
        if ((modalId.data('bs.modal') || {})._isShown && $('#<?= $id ?>-next-image').is(":visible")) {
          $('#<?= $id ?>-next-image')
            .click();
        }
        break;

      default:
        return; // exit this handler for other keys
    }
    e.preventDefault(); // prevent the default action (scroll / move caret)
  });
</script>