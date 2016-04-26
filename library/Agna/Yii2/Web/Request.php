<?php

namespace Agna\Yii2\Web;

use yii\base\Event;

/**
 * Extended http request class
 *
 * Changes:
 * + getParams method to get all parammeters
 * + setParam method to add extra parameters
 * + deleteParam to remove parameters
 * + Automatic (configurable) removal of empty parameters (GET, POST)
 * = getParam precedence changed, POST variables are stronger than GET
 * = getUserIP now checks proxies
 *
 * @todo Perhaps should encapsulate the $_REQUEST object as well.
 * @todo Case insensitive parameter handling, a'la routing
 *
 * @author Agoston Nagy
 */
class Request extends \yii\web\Request implements \yii\base\BootstrapInterface
{
    /**
     * Determines whether to delete empty parameters (0 = false, 1 = true, 2 = recursive) (default: 2)
     *
     * @var boolean
     */
    public $deleteEmtpyParameters = 2;

    /**
     * {@inheritdoc}
     */
    public function bootstrap($application)
    {
        $application->on(Application::EVENT_BEFORE_REQUEST, array($this, 'beforeRequest'));
    }

    /**
     * Before request event handler
     *
     * @param Event $event
     */
    public function beforeRequest(Event $event)
    {
        // Deleting empty parameters
        if ($this->deleteEmtpyParameters) {
            $this->deleteEmptyParameters();
        }
    }

    /**
     * Recursively removes empty elements from a parameter array
     *
     * @param array $parameters (default: null)
     * @return array
     */
    protected function deleteEmptyParameters(array $parameters = null)
    {
        // No parameters given, means we should start somewhere
        if ($parameters === null) {

            // Not using get or getQueryParams, because it sets the internal _queryParams array.
            // As this runs way before the routing, it would break the UrlRule class's .* pattern ending.
            // Meaning $_GET array would be filled with uri specific key/value pairs, however, _queryParams not,
            // therefore, result of get, getQueryParams or manual evaluation of $_GET would be different.
            $_GET = $this->deleteEmptyParameters($_GET);

            // Same problem like with $_GET above.
            $_POST = $this->deleteEmptyParameters($_POST);
        } else {
            // Looping through the parameters array
            foreach ($parameters as $key => $value) {

                // Empty value, removing from the array
                if ($value === '' || $value === null) {
                    unset($parameters[$key]);
                } elseif (is_array($parameters[$key])) { // $value is an array, should process it as well (recursion)
                    $parameters[$key] = $this->deleteEmptyParameters($parameters[$key]);
                }
            }
        }

        return $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function getParam($name, $defaultValue = null)
    {
        $parameter = $defaultValue;

        // If $parameter is in GET
        if ($this->get($name)) {
            $parameter = $this->get($name);
        }

        // If $parameter is in POST
        if ($post = $this->post($name)) {

            // Merging if both are arrays
            if (is_array($post) && is_array($parameter)) {
                $parameter = array_merge($parameter, $post);
            } else { // Post takes precendence
                $parameter = $post;
            }
        }

        return $parameter;
    }

    /**
     * Returns with all the specified request parameters.
     *
     * @param string $type Request type to get the params for (get, post, both) (default: both)
     * @return array|false
     */
    public function getParams($type = 'both')
    {
        $returnValue = false;

        switch ($type) {
            case 'get':
                $returnValue = $this->get();
                break;
            case 'post':
                $returnValue = $this->post();
                break;
            case 'both':
                $returnValue = array_merge($this->get(), $this->post());
                break;
        }

        return $returnValue;
    }

    /**
     * Sets a request parameter
     *
     * @param string $name Parameter name
     * @param string $value Parameter value
     * @param string $type Type of the parameter (get, post, autoGet, autoPost) (default: autoGet)
     * @return WwwHttpRequest
     */
    public function setParam($name, $value, $type = 'autoGet')
    {
        switch (strtolower($type)) {
            case 'post':
                $post = $this->getBodyParams();
                $post[$name] = $value;
                $this->setBodyParams($post);
                break;
            case 'get':
                $get = $this->getQueryParams();
                $get[$name] = $value;
                $this->setQueryParams($get);
                break;
            case 'autoget':
            case 'autopost':
                if ($this->get($name) || (!$this->post($name) && $type == 'autoGet')) {
                    $autoType = 'get';
                } elseif ($this->post($name) || (!$this->get($name) && $type == 'autoPost')) {
                    $autoType = 'post';
                } else {
                    throw new \RuntimeException('Can\'t determine parameter type automatically!');
                }

                $this->setParam($name, $value, $autoType);
                break;
        }

        return $this;
    }

    /**
     * Deletes a request parameter
     *
     * @param string $name Parameter name
     * @param string $type Type of the parameter (get, post, both) (default: both)
     * @return WwwHttpRequest
     */
    public function deleteParam($name, $type = 'both')
    {
        switch (strtolower($type)) {
            case 'get':
                if ($this->get($name)) {
                    $get = $this->get();
                    unset($get[$name]);
                    $this->setQueryParams($get);
                }
                break;
            case 'post':
                if ($this->post($name)) {
                    $post = $this->post();
                    unset($post[$name]);
                    $this->setBodyParams($post);
                }
                break;
            case 'both':
            default:
                $this->deleteParam($name, 'get');
                $this->deleteParam($name, 'post');
                break;
        }

        return $this;
    }

    /**
	 * Returns the user IP address. Even if using a proxy.
     *
     * @param string $default (default: '0.0.0.0')
     * @param bool $excludeReserved Determines whether to exclude private and reserved IPs (default: false)
     * @return string
     */
    public function getUserIP($default = '0.0.0.0', $excludeReserved = false)
    {
        $serverKeys = array('HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'REMOTE_ADDR');

        foreach ($serverKeys as $key) {

            if (!isset($_SERVER[$key])) {
                continue;
            }

            $ip = explode(',', $_SERVER[$key]);

            // First element is the client ip, then the proxy list if multiply proxy is involved
            $ip = trim(array_shift($ip));

            if ($ip =
                    filter_var(
                        $ip,
                        FILTER_VALIDATE_IP,
                        $excludeReserved ? FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE : null
                    )
            ) {
                return $ip;
            }
        }

        return $default;
    }
}
