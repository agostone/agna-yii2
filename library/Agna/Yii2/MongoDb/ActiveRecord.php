<?php

namespace Agna\Yii2\MongoDb;

use Yii;

/**
 * Extended active record class
 *
 * Changes:
 * + Uses \Agna\Yii2\Base\ModelTrait.
 * + Uses \Agna\Yii2\Db\ActiveRecordTrait.
 * + Uses \Agna\Yii2\MongoDb\ActiveQuery.
 *
 * @author Agoston Nagy
 */
class ActiveRecord extends \yii\mongodb\ActiveRecord
{
    use \Agna\Yii2\Base\ModelTrait;
    use \Agna\Yii2\Db\ActiveRecordTrait;

    /**
     * Database connection to use (default: mongodb)
     *
     * @var string
     */
    public static $database = 'mongodb';

    /**
     * Active query class used by find() (default: \Agna\Yii2\MongoDb\ActiveQuery)
     *
     * @var string
     */
    public static $activeQueryClass = '\Agna\Yii2\MongoDb\ActiveQuery';
}
