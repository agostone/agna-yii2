<?php

namespace Agna\Yii2\MongoDb;

/**
 * Extended active query class
 *
 * Changes:
 * + Configurable default scopes
 *
 * @author Agoston Nagy
 */
class ActiveQuery extends \yii\mongodb\ActiveQuery
{
    use \Agna\Yii2\Db\ActiveQuery\DefaultScopesTrait;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->initDefaultScopes();
        parent::init();
    }
}