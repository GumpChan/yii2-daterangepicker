<?php

/**
 * @copyright Copyright &copy; gump 2014
 * @package yii2-widgets
 * @version 0.0.1
 */

namespace datepicker\widgets;

/**
 * Asset bundle for DatePicker Widget
 *
 * @author gump <gump80069@gmail.com>
 * @since 0.1
 */
class momentAsset extends AssetBundle
{

    public function init()
    {
        $this->setSourcePath(__DIR__ . '/../lib');
        $this->setupAssets('js', ['js/moment']);
        parent::init();
    }
}
