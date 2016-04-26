<?php

namespace Agna\Yii2\Base;

/**
 * Trait extending model classes (model, active record, etc).
 *
 * Changes:
 * + Configurable form name.
  *
 * @author Agoston Nagy
 */
trait ModelTrait
{
    /**
     * Form name to use. (default: null, means using Yii default form name)
     *
     * @var string
     */
    public $formName;

    /**
     * Automatically use scenario name as form name. (default: false)
     *
     * $formName takes precedence.
     * Also, if there is no form name specified and the actual scenario is the default scenario,
     * then yii default form name will be used.
     *
     * @var boolean
     */
    public $scenarioForm = false;

    /**
     * Returns the form name that this model class should use.
     *
     * The form name is mainly used by [[\yii\widgets\ActiveForm]] to determine how to name
     * the input fields for the attributes in a model. If the form name is "A" and an attribute
     * name is "b", then the corresponding input name would be "A[b]". If the form name is
     * an empty string, then the input name would be "b".
     *
     * By default, this method returns the model class name (without the namespace part)
     * as the form name. You may override it when the model is used in different forms.
     *
     * @return string the form name of this model class.
     */
    public function formName()
    {
        return $this->formName ? $this->formName : ($this->scenarioForm && $this->getScenario() !== Model::SCENARIO_DEFAULT ? $this->getScenario() : parent::formName());
    }
}