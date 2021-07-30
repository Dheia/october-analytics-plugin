<?php

namespace Synder\Analytics\FormWidgets;

use Backend\Classes\FormWidgetBase;


class Slider extends FormWidgetBase
{
    //
    // Configurable properties
    //

    /**
     * The minimum shown value.
     * 
     * @var integer
     */
    public $min = 0;

    /**
     * The maximum shown value.
     * 
     * @var integer
     */
    public $max = 100;

    /**
     * The steps between min and max values.
     * 
     * @var integer
     */
    public $step = 1;

    /**
     * The default value
     * 
     * @var integer
     */
    public $default = 0;

    /**
     * The minimum selectable value, uses min as default.
     * 
     * @var string|null
     */
    public $selectableMin = null;

    /**
     * The maximum selectable value, uses max as default.
     * 
     * @var string|null
     */
    public $selectableMax = null;

    
    //
    // Object properties
    //

    /**
     * A unique alias to identify this widget.
     * 
     * @var string
     */
    protected $defaultAlias = 'synder-slider';

    /**
     * @inheritDoc
     */
    public function init()
    {
        $this->fillFromConfig([
            'min',
            'max',
            'step',
            'default',
            'selectableMin',
            'selectableMax',
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function loadAssets()
    {
        $this->addCss('css/slider.css', 'core');
        $this->addJs('js/slider.js', 'core');
    }

    /**
     * @inheritDoc
     */
    public function render()
    {
        $this->vars['field'] = $this->formField;
        $this->vars['value'] = $this->formField->value ?? $this->default;
        $this->vars['min'] = $this->min;
        $this->vars['max'] = $this->max;
        $this->vars['step'] = $this->step;
        $this->vars['selectableMin'] = $this->selectableMin ?? $this->min;
        $this->vars['selectableMax'] = $this->selectableMax ?? $this->max;

        return $this->makePartial('field_slider');
    }
}
