<?php

namespace app\components;

class SDashBox extends SBaseWidget {

    public $items = [];

    public function init() {
        parent::init();
        foreach ($this->items as &$item) {
            if (!isset($item['color'])) {
                $item['color'] = 'green';
            }
            if (!isset($item['visible'])) {
                $item['visible'] = true;
            }
        }
    }

    public function renderHtml() {
        ?>

        <!--state overview start-->

        <div class="row state-overview">
        <?php
        foreach ($this->items as $item) {

            if (!$item['visible'])
                continue;
            ?>

                <a href="<?php echo $item['url'] ?>">
                    <div class="col-md-2">
                        <section class=<?php echo $item['color'] ?>>
                            <div class="symbol">
                                <i class="fa fa-users"></i>
                            </div>
                            <div class="value white">
                                <h3 data-speed="1000" data-to="320" data-from="0" class="timer"><?php echo $item['data'] ?></h3>
                                <p><?php echo $item['header'] ?></p>
                            </div>
                        </section>
                    </div>

                </a>
        <?php } ?>
        </div>

            <?php
        }

    }
    