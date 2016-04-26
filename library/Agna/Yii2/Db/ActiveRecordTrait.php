<?php

namespace Agna\Yii2\Db;

use Yii;

/**
 * Active record extensions
 *
 * Changes:
 * + Configurable active query class, no need to override find().
 * + Configurable database connection.
 *
 * @author Agoston Nagy
 */
trait ActiveRecordTrait
{
    /**
     * Database connection to use (default: '')
     *
     * @var string
     */
    //public static $database = '';

    /**
     * Returns the database connection used by this AR class.
     * By default, the "db" application component is used as the database connection.
     * You may override this method if you want to use a different database connection.
     * @return Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get(static::$database);
    }

    /**
     * Creates an [[ActiveQueryInterface|ActiveQuery]] instance for query purpose.
     *
     * The returned [[ActiveQueryInterface|ActiveQuery]] instance can be further customized by calling
     * methods defined in [[ActiveQueryInterface]] before `one()` or `all()` is called to return
     * populated ActiveRecord instances.
     *
     * @see \yii\db\ActiveRecordInterface::find()
     *
     * @return ActiveQuery the newly created [[ActiveQuery]] instance.
     */
    public static function find()
    {
        return Yii::createObject(static::$activeQueryClass, [get_called_class()]);
    }

    /**
     * Returns with an ActiveDataProvider object set up for searching.
     *
     * You can provide ActiveDataProvider options in options parameter.
     * To use different ActiveDataProvider class use the options['class'] parameter.
     *
     * @param array $parameters
     * @param array $options (default: [])
     * @return ActiveDataProvider
     */
    public function search($parameters, $options = [])
    {
        if (isset($options['reset']) && $options['reset'] === true) {
            // Resetting old attributes
            $this->setOldAttributes(null);
        }
        unset($options['reset']);

        // Setting query
        $query = static::find();

        // Load the request parameters
        $this->load($parameters);

        if (!empty($this->getDirtyAttributes())) {

            $filterConditions = $this->attributeFilterConditions();

            // Getting dirty attributes and applying them as a 'WHERE =' filter
            foreach ($this->getDirtyAttributes() as $attribute => $value) {

                // Having a pre-set condition
                if (isset($filterConditions[$attribute])) {
                    $query->filterWhere([$filterConditions[$attribute], $attribute, $value]);
                } else {
                    $query->filterWhere([$attribute => $value]);
                }
            }
        }

        $options['query'] = $query;

        // Default dataprovider class
        $dataProvider = 'yii\data\ActiveDataProvider';

        // Using custom ActiveDataProvider
        if (isset($options['class']) && is_string($options['class'])) {
            $dataProvider = $options['class'];
        }
        unset($options['class']);

        // Creating dataprovider
        return new $dataProvider($options);
    }

    /**
     * Returns with the attribute filter conditions. Used by search().
     *
     * @return array
     */
    public function attributeFilterConditions()
    {
        return [];
    }
}
