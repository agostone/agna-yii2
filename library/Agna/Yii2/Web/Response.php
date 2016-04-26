<?php
namespace Agna\Yii2\Web;

/**
 * The web Response class represents an HTTP response
 *
 * Changes:
 * = Uses rendering formatters
 *
 * @author Agoston Nagy
 */
class Response extends \yii\web\Response
{

    public $formatters = [
        self::FORMAT_HTML => 'Agna\Yii2\Web\Response\HtmlRenderingFormatter',
        self::FORMAT_XML => 'Agna\Yii2\Web\Response\XmlRenderingFormatter',
        self::FORMAT_JSON => 'Agna\Yii2\Web\Response\JsonRenderingFormatter'
    ];
}
