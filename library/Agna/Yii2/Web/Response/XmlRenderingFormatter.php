<?php

namespace Agna\Yii2\Web\Response;

use DOMDocument;
use DOMElement;

/**
 * XmlRenderingFormatter formats the given data into an XML response content.
 *
 * It is used by [[Response]] to format response data.
 */
class XmlRenderingFormatter extends \yii\web\XmlResponseFormatter
{
    use \Agna\Yii2\Web\Response\RenderingFormatterTrait;

    /**
     * View name suffix
     *
     * @var string
     */
    public $suffix = '-xml';

    /**
     * Formats the specified response.
     * @param Response $response the response to be formatted.
     */
    public function format($response)
    {
        $charset = $this->encoding === null ? $response->charset : $this->encoding;
        if (stripos($this->contentType, 'charset') === false) {
            $this->contentType .= '; charset=' . $charset;
        }
        $response->getHeaders()->set('Content-Type', $this->contentType);

        $content = false;

        // If successful response, trying to auto render view
        if ($response->getIsSuccessful()) {

            // Turning off layout for xml request
            //\Yii::$app->requestedAction->controller->layout = false;

            // Rendering data
            if ($content = $this->render(\Yii::$app->requestedAction->id, $response->data)) {
                $response->content = $content;
            }

        }

        // No content means, no view file, thus, simple xml encoding
        if (!$content) {
            $dom = new DOMDocument($this->version, $charset);
            $root = new DOMElement($this->rootTag);
            $dom->appendChild($root);
            $this->buildXml($root, $response->data);
            $response->content = $dom->saveXML();
        }
    }
}
