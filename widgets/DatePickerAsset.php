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
class DatePickerAsset extends AssetBundle
{
    public $depends = [
        'datepicker\widgets\momentAsset',
    ];
    public function init()
    {
        $this->setSourcePath(__DIR__ . '/../lib');
        $this->setupAssets('css', ['css/daterangepicker-bs2', 'css/daterangepicker-bs3']);
        $this->setupAssets('js', ['js/daterangepicker']);
        parent::init();
    }
}
