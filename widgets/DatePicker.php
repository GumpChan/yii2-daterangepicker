<?php

/**
 * @copyright Copyright &copy; gump 2014
 * @package yii2-widgets
 * @version 0.0.1
 */
namespace datepicker\widgets;

use Yii;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\base\InvalidConfigException;
use yii\web\View;
use yii\helpers\Json;
use yii\web\JsExpression;
use datepicker\widgets\DatePickerAsset;

/**
 * Asset bundle for DatePicker Widget
 *
 * @author gump <gump80069@gmail.com>
 * @since 0.1
 */
class DatePicker extends \yii\base\Widget
{

    const TYPE_DEFAULT = 1;
    const TYPE_RANGE = 2;

    /**
     * @var string the markup type of widget markup
     * must be one of the TYPE constants. Defaults
     * to [[TYPE_COMPONENT_PREPEND]]
     */
    public $type = self::TYPE_DEFAULT;
    /**
     * @var string the locale ID (e.g. 'fr', 'de') for the language to be used by the Select2 Widget.
     * If this property not set, then the current application language will be used.
    */
    public $language;
    /**
     * @var array widget plugin options
    */
    public $pluginOptions = [];
    /**
     * @var array widget JQuery events. You must define events in
     * event-name => event-function format
     * for example:
     * ~~~
     * pluginEvents = [
     *        "change" => "function() { log("change"); }",
     *        "open" => "function() { log("open"); }",
     * ];
     * ~~~
     */
    public $pluginEvents = [];
      /**
     * @var string widget callback function getting date for rendering date  
     * to html tag or others. You must define callback in
     * callback => callback-function format
     * for example:
     * ~~~
     * callback = "function(start, end, label) 
     * {
            $('#id span').html(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
        }",
     * ~~~
     */
    public $callback = "";

    /**
     * @var array the HTML attributes for the div tag.
     */
    public $options = [];
    
    /**
     * @var string the Json encoded options
     */
    protected $_encOptions = '';
    
    /**
     * @var string The addon that will be prepended/appended for a
     * [[TYPE_COMPONENT_PREPEND]] and [[TYPE_COMPONENT_APPEND]]
     */
    public $addon = '<i class="glyphicon glyphicon-calendar fa fa-calendar"></i>';
    /**
     * @var boolean whether the widget should automatically format the date from
     * the PHP DateTime format to the javascript/jquery plugin format
     * @see http://php.net/manual/en/function.date.php
     */
    public $convertFormat = false;
    /**
     * @var string the range input separator
     * if you are using [[TYPE_RANGE]] for markup.
     * Defaults to 'to'
     */
    public $separator = 'to';

    /**
     * @var string identifier for the target DatePicker element
     */
    private $_id;

    /**
     * @var array the HTML options for the DatePicker container
     */
    private $_container = [];
    /**
     * @var string the hashed variable to store the pluginOptions
     */
    protected $_hashVar;
    /**
     * Initializes the widget
     *
     * @throw InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $this->initLanguage();
        if ($this->convertFormat && isset($this->pluginOptions['format'])) {
            $this->pluginOptions['format'] = static::convertDateFormat($this->pluginOptions['format']);
        }
        $this->options['id'] = $this->getId();
        $this->_id =  '$("#' . $this->options['id'] . '")' ;
        $this->registerAssets();
        echo $this->renderCom();
    }
    /**
     * Renders the source input for the DatePicker plugin.
     * Graceful fallback to a normal HTML  text input - in
     * case JQuery is not supported by the browser
     */
    protected function renderCom()
    {

        return $this->parseMarkup($this->getDiv('textInput'));
    }
    /**
     * Generates an custom html tag
     */
    protected function getDiv($text)
    {
        
    }
    /**
     * Initialize the plugin language
     *
     * @param string $property the name of language property in [[pluginOptions]].
     * Defaults to 'language'.
     */
    protected function initLanguage($property = 'language') {
        $lang = substr($this->language, 0, 2);
        if (empty($this->pluginOptions[$property]) && $lang != 'en') {
            $this->pluginOptions[$property] = $lang;
        }
    }
    protected function hashPluginOptions($name)
    {
        $this->_encOptions = empty($this->pluginOptions) ? '{ singleDatePicker: true }' : Json::encode($this->pluginOptions);
        if ($this->type == self::TYPE_DEFAULT) {
            $this->_encOptions = "{ singleDatePicker: true ,  language : 'zh-CN'}"; //初始化时 避免为空情况 json push
        }
        $this->_hashVar = $name. '_' . hash('crc32', $this->_encOptions);
        $this->options['data-plugin-name']    = $name;
        $this->options['data-plugin-options'] = $this->_hashVar;
    }

    /**
     * Registers plugin options by storing it in a hashed javascript variable
     */
    protected function registerPluginOptions($name)
    {
        $view = $this->getView();
        $this->hashPluginOptions($name);
        $encOptions = empty($this->_encOptions) ? '{}' : $this->_encOptions;
        $view->registerJs("var {$this->_hashVar} = {$encOptions};\n", View::POS_HEAD);
    }
    
    /**
     * Registers a specific plugin and the related events
     *
     * @param string $name the name of the plugin
     * @param string $element the plugin target element
     * @param string $callback the javascript callback function to be called after plugin loads
     * @param string $callbackCon the javascript callback function to be passed to the plugin constructor
     */
    
    protected function registerPlugin($name, $element = null, $callback = null, $callbackCon = null)
    {
        $view = $this->getView();
        $id = ($element == null) ? "jQuery('#" . $this->options['id'] . "')" : $element;
        $init = $this->pluginOptions['init'];
        if ($this->pluginOptions !== false) {
            $this->registerPluginOptions($name, View::POS_HEAD);
            if ($this->type == self::TYPE_RANGE ) {
                if ($init != null) {
                $initExp = "$('#{$this->options['id']} .startDate').html('{$init['startDate']}');"
                . "$('#{$this->options['id']} .endDate').html('{$init['endDate']}');"
                . "$('#{$this->options['id']} .separator').html('{$init['separator']}');";
                }
                else {
                    $start = date("Y-m-d");
                    $end = date("Y-m-d");
                    $init = "$('#{$this->options['id']} .startDate').html('{$start}');"
                    . "$('#{$this->options['id']} .endDate').html('{$end}');"
                    . "$('#{$this->options['id']} .separator').html(' - ');";
                }
               if ($callbackCon == null) {
                    $callbackCon = "function(start, end, label) {
                    $('#{$this->options['id']} .startDate').html(start.format('YYYY-MM-DD'));"
                    . "$('#{$this->options['id']} .endDate').html(end.format('YYYY-MM-DD'));"
                    . "$('#{$this->options['id']} .separator').html('{$init['separator']}');"
                    . "}";
                }
            } else {
                if($init != null){
                $initExp = "$('#{$this->options['id']} .date').html('{$init['startDate']}');";
                }  else {
                    $start = date("Y-m-d");
                    $init = "$('#{$this->options['id']} .date').html('{$start}');";
                }
                if ($callbackCon == null) {
                    $callbackCon = "function(start,end, label) {
                    $('#{$this->options['id']} .date').html(start.format('YYYY-MM-DD'));"
                    . "}";
                }
            }

            $initExp = new JsExpression($initExp);
            
            $callbackCon = new JsExpression($callbackCon);
            $script = $initExp."{$id}.{$name}({$this->_hashVar}, {$callbackCon});";
            if ($callback != null) {
                $script = "\$.when({$script}).done({$callback})";
            }
            $view->registerJs($script);
        }
        if (!empty($this->pluginEvents)) {
            $js = [];
            foreach ($this->pluginEvents as $event => $handler) {
                $function = new JsExpression($handler);
                $js[] = "{$id}.on('{$event}.daterangepicker', {$function});";
            }
            $js = implode("\n", $js);
            $view->registerJs($js, View::POS_READY);
        }
    }
    /**
     * Parses the input to render based on markup type
     *
     * @param string $input
     * @return string
     */
    protected function parseMarkup($input)
    {
        if ($this->type == self::TYPE_RANGE ) {
        $this->_container = $this->options;
        Html::addCssClass($this->_container, 'pull-right');
        Html::addCssStyle($this->_container, "background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc");

        return Html::tag('div', "<i class='glyphicon glyphicon-calendar fa fa-calendar'></i>"
        . "<span class='startDate'></span>"
        . "<span class='separator'></span><span class='endDate'></span><b class='caret'></b>", $this->_container);
        }
        if ($this->type == self::TYPE_DEFAULT ) {
            $this->_container = $this->options;
            Html::addCssClass($this->_container, 'pull-right');
            Html::addCssStyle($this->_container, "background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc");

            return Html::tag('div', "<i class='glyphicon glyphicon-calendar fa fa-calendar'></i>"
            . "<span class='date'></span>", $this->_container);
        }
    }
    /**
     * Automatically convert the date format from PHP DateTime to Javascript DateTime format
     *
     * @see http://php.net/manual/en/function.date.php
     * @see http://bootstrap-datetimepicker.readthedocs.org/en/release/options.html#format
     * @param string $format the PHP date format string
     * @return string
     */
    protected static function convertDateFormat($format)
    {
        return strtr($format, [
            // meridian lowercase
            'a' => 'p',
            // meridian uppercase
            'A' => 'P',
            // second (with leading zeros)
            's' => 'ss',
            // minute (with leading zeros)
            'i' => 'ii',
            // hour in 12-hour format (no leading zeros)
            'g' => 'H',
            // hour in 24-hour format (no leading zeros)
            'G' => 'h',
            // hour in 12-hour format (with leading zeros)
            'h' => 'HH',
            // hour in 24-hour format (with leading zeros)
            'H' => 'hh',
            // day of month (no leading zero)
            'j' => 'd',
            // day of month (two digit)
            'd' => 'dd',
            // day name short is always 'D'
            // day name long
            'l' => 'DD',
            // month of year (no leading zero)
            'n' => 'm',
            // month of year (two digit)
            'm' => 'mm',
            // month name short is always 'M'
            // month name long
            'F' => 'MM',
            // year (two digit)
            'y' => 'yy',
            // year (four digit)
            'Y' => 'yyyy',
        ]);
    }
    /**
     * Registers the needed client assets
     */
    public function registerAssets()
    {
        $view = $this->getView();
        if (!empty($this->pluginOptions['language'])) {
            DatePickerAsset::register($view)->js[] = 'js/locales/daterangepicker.' . $this->pluginOptions['language'] . '.js';
        } else {
            DatePickerAsset::register($view);
        }
        $id = "$('#" . $this->options['id'] . "')";
//        if ($this->type === self::TYPE_RANGE) {
            $this->registerPlugin('daterangepicker',null,null,'');
            DatePickerAsset::register($view);
//        }
    }
}
