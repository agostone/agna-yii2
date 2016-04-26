<?php

namespace Agna\Yii2\Web;

/**
 * Extended UrlRule class.
 *
 * Changes:
 * + Adding support for patterns ending with '.*' directive to convert remainder of the path info to parameter key/value pairs.
 *
 * @example
 * Url: http://domain.ln/home/this/is/cool?oh=yeah
 * Pattern: '<controller:(.[^/]*)>/<action:(.[^/]*)>.*'
 * Result of \Yii::$app->getRequest()->get() is HomeController::actionThis(): ['oh' => 'yeah', 'is' => 'cool']
 *
 * @todo CreateUrl testing and implementing
 *
 * @author Agoston Nagy
 */
class UrlRule extends \yii\web\UrlRule
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        // If the end of the pattern is .* it means everything after the matched part should be parsed as key/value parameter pairs
        if ($this->pattern !== null && substr($this->pattern, strlen($this->pattern) - 2) == '.*') {
            $this->pattern =
                substr($this->pattern, 0, strlen($this->pattern) - 2) . '<__restAsParameter__:(.*)>';
        }

        parent::init();
    }

    /**
     * {@inheritdoc}
     */
    public function parseRequest($manager, $request)
    {
        $routeParams = parent::parseRequest($manager, $request);

        // If request was parsed and we have .* directive, parsing remaining path info as key => value parameter pairs
        if ($routeParams && isset($routeParams[1]['__restAsParameter__'])) {

            $parameters = explode('/', trim($routeParams[1]['__restAsParameter__'], '/'));
            $parameters['pairs'] = [];
            for ($index = 0; isset($parameters[$index]); $index += 2) {
                $parameters['pairs'][$parameters[$index]] =
                    isset($parameters[$index + 1]) ? $parameters[$index + 1] : '';
            }
            $parameters = $parameters['pairs'];
            unset($routeParams[1]['__restAsParameter__']);

            $routeParams[1] = array_merge($routeParams[1], $parameters);
        }

        return $routeParams;
    }
}
