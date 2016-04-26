<?php

namespace Agna\Yii2\Web\Rest;

/**
 * Controller is the base class for RESTful API controller classes.
 *
 * Controller implements the following steps in a RESTful API request handling cycle:
 *
 * 1. Resolving response format (see [[ContentNegotiator]]);
 * 2. Formatting response data (see [[serializeData()]]).
 *
 * @author stoned
 */
class Controller extends \Agna\Yii2\Web\Controller
{
    /**
     * @var string|array the configuration for creating the serializer that formats the response data.
     */
    public $serializer = 'yii\rest\Serializer';

    /**
     * @inheritdoc
     */
    public $enableCsrfValidation = false;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'contentNegotiator' => [
                'class' => \Agna\Yii2\Filters\ContentNegotiator::className(),
                'formats' => [
                    'application/json' => \Agna\Yii2\Web\Response::FORMAT_JSON,
                    'application/xml' => \Agna\Yii2\Web\Response::FORMAT_XML,
                ],
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function afterAction($action, $result)
    {
        $result = parent::afterAction($action, $result);
        return $this->serializeData($result);
    }

    /**
     * Serializes the specified data.
     * The default implementation will create a serializer based on the configuration given by [[serializer]].
     * It then uses the serializer to serialize the given data.
     * @param mixed $data the data to be serialized
     * @return mixed the serialized data.
     */
    protected function serializeData($data)
    {
        return \Yii::createObject($this->serializer)->serialize($data);
    }
}
