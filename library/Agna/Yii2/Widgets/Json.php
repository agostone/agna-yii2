<?php

namespace Agna\Yii2\Widgets;

use Yii;
use yii\base\Widget;

use Agna\Yii2\Base\InvalidParamException;

class Json extends Widget
{
    /**
     * Json type flag to indicate an array
     *
     * @var binary
     */
    const TYPE_ARRAY = 0b0001;

    /**
     * Json type bitmask (default: 0 - an object)
     *
     * @var integer
     */
    public $type = 0;

    protected $hasItems = false;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        if ($this->type & static::TYPE_ARRAY) {
            echo('[');
        } else {
            echo('{');
        }
    }

    public function item($value, $key = null, $encode = true)
    {
        if (!is_string($key) && $this->type !== static::TYPE_ARRAY) {
            throw new InvalidParamException('$key', $key, 'string as the json will be an object');
        }

        if ($this->hasItems) {
            $this->separator();
        }

        if (is_string($key)) {
            $this->key($key);
        }

        $this->value($value, $encode);

        $this->hasItems = true;

        return $this;
    }

    public function subItem($key, $config = [])
    {
        if ($this->hasItems) {
            $this->separator();
        }

        $this->key($key);

        return static::begin($config);
    }

    protected function key($key)
    {
        echo("\"{$key}\":");
        return $this;
    }

    protected function value($value, $encode = true)
    {
        if ($encode) {
            $value = \yii\helpers\Json::encode($value);
        }

        echo($value);

        return $this;
    }

    protected function separator()
    {
        echo(',');
        return $this;
    }

    public function run()
    {
        parent::run();

        if ($this->type & static::TYPE_ARRAY) {
            echo(']');
        } else {
            echo('}');
        }
    }

    public function hasItems()
    {
        return $this->hasItems;
    }
}