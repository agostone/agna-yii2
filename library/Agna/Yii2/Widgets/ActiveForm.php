<?php

namespace Agna\Yii2\Widgets;

use yii\helpers\Json;
use yii\widgets\ActiveFormAsset;

/**
 * Extended ActiveForm class
 *
 * Changes:
 * = $fieldClass defaults to Agna\Yii2\Widgets\ActiveField
 *
 * @author Agoston Nagy
 */
class ActiveForm extends \yii\widgets\ActiveForm
{
    public $fieldClass = 'Agna\Yii2\Widgets\ActiveField';
}
