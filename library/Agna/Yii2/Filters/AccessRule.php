<?php

namespace Agna\Yii2\Filters;

use yii\web\Request;

/**
 * Extended AccessRule class.
 *
 * Changes:
 * + Possiblity to restrict access using request header filtering
 *
 * @author Agoston Nagy
 */
class AccessRule extends \yii\filters\AccessRule
{
    /**
     * List of header key/values this rule applies to. The comparison is case-isensitive. (default: null)
     *
     * If not set or empty, it means this rule applies to all actions.
     *
     * @var array
     */
    public $headers;

    /**
     * {@inheritdoc}
     */
    public function allows($action, $user, $request)
    {
        if (parent::allows($action, $user, $request) &&
            $this->matchHeaders($request)
        ) {
            return $this->allow ? true : false;
        }

        return null;
    }

    /**
     * Returns true if the header rule apply to the user
     *
     * @param \yii\web\Request $request
     * @return boolean
     */
    protected function matchHeaders($request)
    {
        $headers = $request->getHeaders();

        foreach ($this->headers as $key => $value) {
            $key = strtolower($key);
            $header = $headers->has($key) ? $headers->get($key) : null;

            if ($header === $value) {
                return true;
            }
        }

        return false;
    }
}

