<?php

namespace Agna\Yii2\Web\Response;

use Yii;
use yii\base\Component;
use yii\helpers\Json;
use yii\web\ResponseFormatterInterface;

/**
 * JsonRenderingFormatter formats the given data into a JSON or JSONP response content.
 *
 * It is used by [[Response]] to format response data.
 */
class JsonRenderingFormatter extends Component implements ResponseFormatterInterface
{
    use \Agna\Yii2\Web\Response\RenderingFormatterTrait;

    /**
     * Callback GET parameter name.
     *
     * If this parameter exists amongst the GET parameters, the request is a JSNOP request.
     * However, use CORS instead of JSONP.
     *
     * @var string
     */
    public $callbackParam = 'callback';

    /**
     * View name suffix
     *
     * @var string
     */
    public $suffix = '-json';

    /**
     * Formats the specified response.
     * @param Response $response the response to be formatted.
     */
    public function format($response)
    {
        $content = false;

        // If successful response, trying to auto render view
        if ($response->getIsSuccessful()) {

            // Rendering view
            if ($content = $this->render(Yii::$app->requestedAction->id, $response->data)) {
                $response->content = $content;
            }
        }

        // No content means, no view file, thus, simple json encoding
        if (!$content) {
            $response->content = Json::encode($response->data);
        }

        // Jsonp request
        if (!empty($this->callbackParam)
            && ($callback = Yii::$app->getRequest()->get($this->callbackParam)) !== null
        ) {
            $response->getHeaders()->set('Content-Type', 'application/javascript; charset=UTF-8');
            $response->content = sprintf('%s(%s);', $callback, $response->content);
        } else { // Normal json request
            $response->getHeaders()->set('Content-Type', 'application/json; charset=UTF-8');
        }
    }
}
