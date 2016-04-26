<?php

namespace Agna\Yii2\Db;

/**
 * Extended active record class
 *
 * Changes:
 * + Uses \Agna\Yii2\Base\ModelTrait.
 * + Uses \Agna\Yii2\Db\ActiveRecordTrait.
 * + Uses \Agna\Yii2\Db\ActiveQuery.
 *
 * @author Agoston Nagy
 */
class ActiveRecord extends \yii\db\ActiveRecord
{
    use \Agna\Yii2\Base\ModelTrait;
    use \Agna\Yii2\Db\ActiveRecordTrait;

    /**
     * Database connection to use (default: db)
     *
     * @var string
     */
    public static $database = 'db';

    /**
     * Active query class used by find() (default: \Agna\Yii2\Db\ActiveQuery)
     *
     * @var string
     */
    public static $activeQueryClass = '\Agna\Yii2\Db\ActiveQuery';
}
