<?php

namespace Agna\Yii2\I18n;

use Agna\Yii2\Base\InvalidParamException;

use Yii;
use yii\i18n\MessageSource;
use yii\i18n\DbMessageSource;
use Agna\Yii2\Helpers\ArrayHelper;
use yii\base\InvalidConfigException;

/**
 * Extended I18N class, providing features related with internationalization (I18N) and localization (L10N).
 *
 * Changes:
 * + Added Agna\Yii2 library specific message sources
 * + Multi message sources, meaning, you can provide 0-N message source for every category
 * + Message references inside a message
 *
 * @author Agoston Nagy
 */
class I18N extends \yii\i18n\I18N
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        if (!isset($this->translations['Agna\Yii2']) && !isset($this->translations['Agna\Yii2*'])) {
            $this->translations['Agna\Yii2'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'sourceLanguage' => 'en-US',
                'basePath' => '@Agna/Yii2/Messages'
            ];
        }
    }

    /**
     * Translates a message to the specified language.
     *
     * There is also a possiblity to use message references inside a message. In that case
     * the original message gets translated first, then the reference.
     *
     * Usage:
     * %%message%% - referencing another message in the same category
     * %%category:message%% - referencing another message in another category
     *
     * Example:
     * This is a very valuable %%animal%%
     * This is a very valuable %%jewel:necklace%%
     *
     * @see \yii\i18n\I18N
     *
     * @param string $category the message category.
     * @param string $message the message to be translated.
     * @param array $params the parameters that will be used to replace the corresponding placeholders in the message.
     * @param string $language the language code (e.g. `en-US`, `en`).
     * @return string the translated and formatted message.
     */
    public function translate($category, $message, $params, $language)
    {
        $messageSource = $this->getMessageSource($category);

        if (!is_array($messageSource)) {
            $messageSource = [$messageSource];
        }

        // Processing source queue
        foreach ($messageSource as $source) {

            // If translated, breaking queue
            if ($translation = $source->translate($category, $message, $language)) {
                $message = $translation;
                break;
            }
        }

        $references = array();

        // Replacing references
        if (preg_match_all('/%{2}[^\s]*%{2}/i', $message, $references, PREG_PATTERN_ORDER) > 0) {
            $references = $references[0]; // We need full matches only
            foreach ($references as $reference) {

                $referenceArray = explode(':', str_replace('%%', '', $reference), 2);

                // Reference within same category
                if (count($referenceArray) == 1) {
                    array_unshift($referenceArray, $category);
                }

                $message = str_replace(
                    $reference,
                    $this->translate($referenceArray[0], $referenceArray[1], $params, $source, $language),
                    $message
                );
            }
        }

        if ($translation === false) {
            return $this->format($message, $params, $source->sourceLanguage);
        }

        return $this->format($translation, $params, $language);
    }

    public function getMessageSource($category)
    {
        if (isset($this->translations[$category])) {
            $source = $this->translations[$category];
            if ($source instanceof MessageSource ||
                (isset($source[0]) && $source[0] instanceof MessageSource)
            ) {
                return $source;
            } elseif (isset($source[0]) && $source[0] === 'multiSource') {
                unset($source[0]);
                foreach ($source as $key => $config) {
                    $source[$key] = Yii::createObject($config);
                }

                return $this->translations[$category] = $source;
            } else {
                return $this->translations[$category] = Yii::createObject($source);
            }
        } else {
            // try wildcard matching
            foreach ($this->translations as $pattern => $source) {
                if (strpos($pattern, '*') > 0 && strpos($category, rtrim($pattern, '*')) === 0) {
                    if ($source instanceof MessageSource ||
                        (isset($source[0]) && $source[0] instanceof MessageSource)
                    ) {
                        return $source;
                    } elseif (isset($source[0]) && $source[0] === 'multiSource') {
                        unset($source[0]);
                        foreach ($source as $key => $config) {
                            $source[$key] = Yii::createObject($config);
                        }
                        return $this->translations[$category] = $this->translations[$pattern] = $source;
                    } else {
                        return $this->translations[$category] = $this->translations[$pattern] = Yii::createObject($source);
                    }
                }
            }
            // match '*' in the last
            if (isset($this->translations['*'])) {
                $source = $this->translations['*'];
                if ($source instanceof MessageSource ||
                    (isset($source[0]) && $source[0] instanceof MessageSource)
                ) {
                    return $source;
                } elseif (isset($source[0]) && $source[0] === 'multiSource') {
                    unset($source[0]);
                    foreach ($source as $key => $config) {
                        $source[$key] = Yii::createObject($config);
                    }

                    return $this->translations[$category] = $this->translations['*'] = $source;
                } else {
                    return $this->translations[$category] = $this->translations['*'] = Yii::createObject($source);
                }
            }
        }

        throw new InvalidConfigException("Unable to locate message source for category '$category'.");
    }
}
