<?php

namespace Agna\Yii2\Filters;

use Agna\Yii2\Base\ControllerEvent;
use Agna\Yii2\Helpers\ObjectHelper;

use Yii;

/**
 * Extended ContentNegotiator class, supports response format negotiation and application language negotiation.
 *
 * Changes:
 * + Possibility to configure detection order
 * + _layout param to turn off layout rendering via GET parameters
 *
 * @todo Managing language and content type shouldn't be merged, separation required one day.
 *
 * @author stoned
 */
class ContentNegotiator extends \yii\filters\ContentNegotiator
{
    const URL = 'url';
    const COOKIE = 'cookie';
    const CLIENT = 'client';

    /**
     * The name of the GET parameter to specify the layout to be used.
     *
     * Available options:
     * ?_layout=false - The layout is forcefully turned off.
     * ?_layout=true - The default layout will be used.
     * ?_layout=<layout> - The <layout> will be used.
     *
     * @var string
     */
    public $layoutParam = '_layout';

    /**
     * Lifetime of the cookie in seconds.
     * (default: 31557600, ie. 1 year)
     *
     * @var int
     */
    public $cookieLifetime = 31557600;

    /**
     * The order to use to determine the prefered language. (default: url, cookie, client)
     *
     * @var array
     */
    public $detectionOrder = array(
        self::URL,
        self::COOKIE,
        self::CLIENT
    );

    /**
     * {@inheritdoc}
     */
    public function bootstrap($app)
    {
        $app->on(ControllerEvent::EVENT_BEFORE_CONTROLLER, array($this, 'beforeController'));
    }

    /**
     * Event handler for 'beforeController' event
     *
     * @param ControllerEvent $event
     */
    public function beforeController(ControllerEvent $event)
    {
        $controller = $event->controller;
        $behaviorName = lcfirst(ObjectHelper::getShortName($this));

        // Layout control
        if ($event->controller->layout !== false
            && !empty($this->layoutParam)
            && ($layout = Yii::$app->getRequest()->get($this->layoutParam)) !== null
        ) {
            switch ($layout) {
                case 'false':
                    $controller->layout = false;
                    break;
                case 'true':
                    $controller->layout = null;
                    break;
                default:
                    $controller->layout = $layout;
                    break;
            }
        }

        // If controller has no content negotiator behavior
        if (!$event->controller->getBehavior($behaviorName)) {
            $event->controller->attachBehavior($behaviorName, $this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function negotiate()
    {
        parent::negotiate();

        $response = $this->response ? $this->response : Yii::$app->getResponse();

        // Setting cookie if cookie is used in the detection
        if (array_search(self::COOKIE, $this->detectionOrder) !== false) {
            $this->setCookie(Yii::$app->language, $response);
        }
    }

    /**
     * Stores the selected language in cookie.
     *
     * @param string $language
     * @param \yii\web\Response $response
     * @return \Agna\Yii2\Filters\ContentNegotiator
     */
    public function setCookie($language, $response)
    {
        $response->cookies[$this->languageParam] =
            new \yii\web\Cookie(
                [
                    'name' => $this->languageParam,
                    'value' => $language,
                    'expire' => time() + $this->cookieLifetime
                ]
            );

        return $this;
    }

    /**
     * Negotiates the application language.
     *
     * @param yii\web\Request $request
     * @return string the chosen language
     */
    protected function negotiateLanguage($request)
    {
        foreach ($this->detectionOrder as $method) {

            $methodName = "detectLanguageBy{$method}";

            if (method_exists($this, $methodName)) {
                $language = $this->{$methodName}($request);
            }

            if ($language !== null) {
                break;
            }
        }

        // Can't detect language, using application default
        return $language !== null ? $language : Yii::$app->language;
    }

    /**
     * Detects url based language selection.
     *
     * @param yii\web\Request $request
     * @return string|null
     */
    protected function detectLanguageByUrl($request)
    {
        if (!empty($this->languageParam) && ($language = $request->get($this->languageParam)) !== null) {
            $language = $this->getSupportedLanguage($language);
        }

        return $language;
    }

    /**
     * Detects cookie based language selection.
     *
     * @param yii\web\Request $request
     * @return string|null
     */
    protected function detectLanguageByCookie($request)
    {
        $language = $request->cookies[$this->languageParam];

        if ($language) {
            $language = $this->getSupportedLanguage($language->value);
        }

        return $language;
    }

    /**
     * Detects client supported languages.
     *
     * @param yii\web\Request $request
     * @return string|null
     */
    protected function detectLanguageByClient($request)
    {
        $language = null;

        // @todo Try to change it to prefered language using the request!
        foreach ($request->getAcceptableLanguages() as $language) {
            $language = $this->getSupportedLanguage($language);
        }

        return $language;
    }

    /**
     * Returns with the closest supported language
     *
     * @param string $requested the requested language code
     * @return string|null the supported language code or null if none found
     */
    protected function getSupportedLanguage($requested)
    {
        if (isset($this->languages[$requested])) {
            return $this->languages[$requested];
        }

        foreach ($this->languages as $key => $supported) {

            if (is_integer($key)
                && strpos(
                    str_replace('_', '-', strtolower($requested)) . '-',
                    str_replace('_', '-', strtolower($supported)) . '-'
                ) === 0
            ) {
                return $supported;
            }
        }

        return null;
    }
}
