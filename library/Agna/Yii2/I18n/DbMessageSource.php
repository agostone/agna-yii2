<?php

namespace Agna\Yii2\I18n;

/**
 * Database message source.
 *
 * Changes:
 * + Possibility to turn off categories, meaning, every message will be loaded using/saved under the same category name: noCategory.
 *
 * @author Agoston Nagy
 */
class DbMessageSource extends \yii\i18n\DbMessageSource
{
    /**
     * The name of the source message table. (default: i18n_default_message)
     *
     * @var string
     */
    public $sourceMessageTable = '{{%i18n_source_message}}';

    /**
     * The name of the translated message table. (default: i18n_message)
     *
     * @var string
     */
    public $translatedMessageTable = '{{%i18n_message}';

    /**
     * If set to true categories are turned off. (default: false)
     *
     * @var boolean
     */
    public $noCategories = false;

    /**
     * {@inheritdoc}
     */
    protected function loadMessagesFromDb($category, $language)
    {
        if ($this->noCategories) {
            $category = 'noCategories';
        }

        return parent::loadMessagesFromDb($category, $language);
    }
}
