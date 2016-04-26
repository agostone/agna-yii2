<?php

namespace Agna\Yii2\Moip;

use Agna\Yii2\Base\InvalidParamException;
use Agna\Moip\Api\Subscription\EntryFactory;

/**
 * Moip api component for yii
 *
 * To configure the component check the example below:
 * @example
 * [
 *     'moip' => [
 *         'class' => 'Agna\Yii2\Moip\Component',
 *         'subscription' => [
 *             'key' => '<moip secret key>',
 *             'token' => '<moip token>',
 *             'production' => true | false // Indicates production mode,
 *             'options' => [
 *                 'modules' => [
 *                     '<name>' => '<handling class>',
 *                     ...
 *                 ],
 *                 'http' => [
 *                     'sslverifypeer' => false
 *                     ...
 *                 ]
 *             ]
 *         ],
 *         'transcation' => [
 *         // @todo Implement the class
 *         ],
 *         'webhooks' => [
 *             'authorizationCode' => '<authorization code>',
 *             'requestClass' => '<request class>' // Optional,
 *             'notifications' => [
 *                 '<route>' => [
 *                     'class' => '<handling class, if it's just a node, use NotificationContainer>',
 *                     '<key>' => '<value>',
 *                     'notifications' => [
 *                         '<route>' => [
 *                             ...
 *                         ]
 *                     ]
 *                 ],
 *                 ...
 *             ]
 *         ]
 *     ]
 * ]
 *
 * @author Agoston Nagy
 */
class Component extends \Agna\Yii2\Base\Component
{
    /**
     * Subscription api instance
     *
     * @var \Agna\Moip\Api\Subscription
     */
    protected $subscription;

    /**
     * Transaction api instance
     *
     * @var \Agna\Moip\Api\Transaction
     */
    protected $transaction;

    /**
     * Sets the subscription api options
     *
     * @param array $options
     * @return \Agna\Yii2\Moip\Component
     */
    public function setSubscription(array $options)
    {
        $this->subscription = $this->getApiInstance('Agna\Moip\Api\Subscription', $options);
        return $this;
    }

    /**
     * Sets the transaction api options
     *
     * @param array $options
     * @return \Agna\Yii2\Moip\Component
     */
    public function setTransaction(array $options)
    {
        $this->transaction = $this->getApiInstance('Agna\Moip\Api\Transaction', $options);
        return $this;
    }

    /**
     * Sets the Moip EntryFactor classname resolver.
     *
     * @param mixed $resolver
     * @return \Agna\Yii2\Moip\Component
     */
    public function setClassnameResolver($resolver)
    {
        EntryFactory::$classnameResolver = $resolver;
        return $this;
    }

    /**
     * Sets the webhooks api options
     *
     * @param mixed $options
     * @return \Agna\Yii2\Moip\Component
     */
    public function setWebhooks($options)
    {
        $this->webhooks = \Agna\Moip\Webhooks\Manager::getInstance()->initialize($options);
        return $this;
    }

    /**
     * Creates a moip api instance
     *
     * @param string $class
     * @param array $options
     * @throws InvalidParamException
     * @return \Agna\Moip\Api\Subscription\Entry
     */
    protected function getApiInstance($class, array $options)
    {
        if (!isset($options['key']) || !is_string($options['key'])) {
            throw new InvalidParamException(
                "options['key']",
                isset($options['key']) ? $options['key'] : null,
                'string'
            );
        }

        if (!isset($options['token'])) {
            throw new InvalidParamException(
                "options['token']",
                isset($options['token']) ? $options['token'] : null,
                'string'
            );
        }

        return new $class(
            $options['key'],
            $options['token'],
            isset($options['production']) ? $options['production'] : false,
            isset($options['options']) ? $options['options'] : []
        );
    }

    /**
     * Returns the subscription api instance
     *
     * @return \Agna\Moip\Api\Subscription
     */
    public function getSubscription()
    {
        return $this->subscription;
    }

    /**
     * Returns with the transaction api instance
     *
     * @return \Agna\Moip\Api\Transaction
     */
    public function getTransaction()
    {
        return $this->transaction;
    }

    /**
     * Returns with the webooks api manager
     *
     * @return
     */
    public function getWebhooks()
    {
        return $this->webhooks;
    }

}
