<?php

namespace Agna\Yii2\Mail\Zend;

use yii\mail\MailerInterface;
use Agna\Yii2\Base\InvalidParamException;
use yii\base\UnknownMethodException;
use yii\base\InvalidCallException;
use yii\base\UnknownPropertyException;

/**
 * Message class for Zend\Mail integration.
 *
 * @todo Implement the attach, embed methods!
 *
 * @author Agoston Nagy
 */
class Message extends \Zend\Mail\Message implements \yii\mail\MessageInterface
{

    /**
     * @var MailerInterface the mailer instance that created this message.
     * For independently created messages this is `null`.
     */
    public $mailer;

    /**
     * Sends this email message.
     * @param MailerInterface $mailer the mailer that should be used to send this message.
     * If no mailer is given it will first check if [[mailer]] is set and if not,
     * the "mail" application component will be used instead.
     * @return boolean whether this message is sent successfully.
     */
    public function send(MailerInterface $mailer = null)
    {
        if ($mailer === null && $this->mailer === null) {
            $mailer = Yii::$app->getMailer();
        } elseif ($mailer === null) {
            $mailer = $this->mailer;
        }
        return $mailer->send($this);
    }

    /**
     * {@inheritdoc}
     */
    public function setCharset($charset)
    {
        return $this->setEncoding($charset);
    }

    /**
     * {@inheritdoc}
     */
    public function getCharset()
    {
        return $this->getEncoding();
    }

    /**
     * {@inheritdoc}
     */
    public function setHtmlBody($html)
    {
        $htmlPart = new \Zend\Mime\Part($html);
        $htmlPart->encoding = \Zend\Mime\Mime::ENCODING_QUOTEDPRINTABLE;
        $htmlPart->type = \Zend\Mime\Mime::TYPE_HTML;
        $htmlPart->charset = $this->getEncoding();

        return $this->setBodyPart(0, $htmlPart);
    }

    /**
     * {@inheritdoc}
     */
    public function setTextBody($text)
    {
        $textPart = new \Zend\Mime\Part($text);
        $textPart->encoding = \Zend\Mime\Mime::ENCODING_QUOTEDPRINTABLE;
        $textPart->type = \Zend\Mime\Mime::TYPE_TEXT;
        $textPart->charset = $this->getEncoding();

        return $this->setBodyPart(1, $textPart);
    }

    /**
     * {@inheritdoc}
     */
    public function setBody($body)
    {
        throw new \RuntimeException('Calling this function is not allowed, call setHtmlBody or setTextBody instead!');

        if (!$body instanceof \Zend\Mime\Message) {
            throw new InvalidParamException('$body', $body, '\Zend\Mime\Message');
        }

        return parent::setBody($body);
    }

    /**
     * Sets a body part to a desired value
     *
     * @param integer $partId
     * @param \Zend\Mime\Part $partValue
     * @return \Agna\Yii2\Mail\Zend\Message
     */
    protected function setBodyPart($partId, \Zend\Mime\Part $partValue)
    {
        $body = $this->body;

        if (!$body instanceof \Zend\Mime\Message) {
            $body = new \Zend\Mime\Message();
        }

        $parts = $body->getParts();
        $parts[$partId] = $partValue;
        $body->setParts($parts);

        parent::setBody($body);

        if ($this->body->isMultiPart()) {
            $header = $this->getHeaders()->get('Content-Type');
            $header->setType('multipart/alternative');
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function attach($fileName, array $options = [])
    {
        throw new \RuntimeException('Not supported!');
    }

    /**
     * {@inheritdoc}
     */
    public function attachContent($content, array $options = [])
    {
        throw new \RuntimeException('Not supported!');
    }

    /**
     * {@inheritdoc}
     */
    public function embed($fileName, array $options = [])
    {
        throw new \RuntimeException('Not supported!');
    }

    /**
     * {@inheritdoc}
     */
    public function embedContent($content, array $options = [])
    {
        throw new \RuntimeException('Not supported!');
    }

    /**
     * Returns the value of an object property.
     *
     * Do not call this method directly as it is a PHP magic method that
     * will be implicitly called when executing `$value = $object->property;`.
     * @param string $name the property name
     * @return mixed the property value
     * @throws UnknownPropertyException if the property is not defined
     * @throws InvalidCallException if the property is write-only
     * @see __set()
     */
    public function __get($name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->$getter();
        } elseif (method_exists($this, 'set' . $name)) {
            throw new InvalidCallException('Getting write-only property: ' . get_class($this) . '::' . $name);
        } else {
            throw new UnknownPropertyException('Getting unknown property: ' . get_class($this) . '::' . $name);
        }
    }

    /**
     * Sets value of an object property.
     *
     * Do not call this method directly as it is a PHP magic method that
     * will be implicitly called when executing `$object->property = $value;`.
     * @param string $name the property name or the event name
     * @param mixed $value the property value
     * @throws UnknownPropertyException if the property is not defined
     * @throws InvalidCallException if the property is read-only
     * @see __get()
     */
    public function __set($name, $value)
    {
        $setter = 'set' . $name;
        if (method_exists($this, $setter)) {
            $this->$setter($value);
        } elseif (method_exists($this, 'get' . $name)) {
            throw new InvalidCallException('Setting read-only property: ' . get_class($this) . '::' . $name);
        } else {
            throw new UnknownPropertyException('Setting unknown property: ' . get_class($this) . '::' . $name);
        }
    }

    /**
     * Checks if the named property is set (not null).
     *
     * Do not call this method directly as it is a PHP magic method that
     * will be implicitly called when executing `isset($object->property)`.
     *
     * Note that if the property is not defined, false will be returned.
     * @param string $name the property name or the event name
     * @return boolean whether the named property is set (not null).
     */
    public function __isset($name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->$getter() !== null;
        } else {
            return false;
        }
    }

    /**
     * Sets an object property to null.
     *
     * Do not call this method directly as it is a PHP magic method that
     * will be implicitly called when executing `unset($object->property)`.
     *
     * Note that if the property is not defined, this method will do nothing.
     * If the property is read-only, it will throw an exception.
     * @param string $name the property name
     * @throws InvalidCallException if the property is read only.
     */
    public function __unset($name)
    {
        $setter = 'set' . $name;
        if (method_exists($this, $setter)) {
            $this->$setter(null);
        } elseif (method_exists($this, 'get' . $name)) {
            throw new InvalidCallException('Unsetting read-only property: ' . get_class($this) . '::' . $name);
        }
    }

    /**
     * Calls the named method which is not a class method.
     *
     * Do not call this method directly as it is a PHP magic method that
     * will be implicitly called when an unknown method is being invoked.
     * @param string $name the method name
     * @param array $params method parameters
     * @throws UnknownMethodException when calling unknown method
     * @return mixed the method return value
     */
    public function __call($name, $params)
    {
        throw new UnknownMethodException('Calling unknown method: ' . get_class($this) . "::$name()");
    }

    public function setHeader($header)
    {
        if (is_array($header)) {
            $headers = $this->getHeaders();
            foreach ($header as $name => $value) {
                $headers->removeHeader($name);
            }
            $this->getHeaders()->addHeaders($header);
        } else {
            parent::setHeaders($header);
        }

        return $this;
    }
}