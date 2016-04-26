<?php

namespace Agna\Yii2\Web\Response;

use Yii;
use yii\base\Component;
use yii\web\ResponseFormatterInterface;

/**
 * HtmlRenderingFormatter renders and formats the given data into an HTML response content.
 *
 * It is used by [[Response]] to format response data.
 */
class HtmlRenderingFormatter extends Component implements ResponseFormatterInterface
{
    use \Agna\Yii2\Web\Response\RenderingFormatterTrait;

    /**
     * @var string the Content-Type header for the response
     */
    public $contentType = 'text/html';

    /**
     * View name suffix
     *
     * @var string
     */
    public $suffix = '-html';

    /**
     * Formats the specified response.
     * @param \yii\Web\Response $response the response to be formatted.
     */
    public function format($response)
    {
        if (stripos($this->contentType, 'charset') === false) {
            $this->contentType .= '; charset=' . $response->charset;
        }
        $response->getHeaders()->set('Content-Type', $this->contentType);

        $content = false;

        // If successful response, trying to auto render view
        if ($response->getIsSuccessful()) {

            // Rendering data
            // @todo Have to swallow exception because of compatibilty with non auto view rendering actions! Figure out something!
            // if ($content = $this->render(Yii::$app->requestedAction->id, $response->data), true) {
            if ($content = $this->render(Yii::$app->requestedAction->id, $response->data)) {
                $response->content = $content;
            }
        }

        // No content means, no view file, thus, simply returning data
        if (!$content) {
            $response->content = $response->data;
        }
    }
}
