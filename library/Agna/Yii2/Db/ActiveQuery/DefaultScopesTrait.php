<?php

namespace Agna\Yii2\Db\ActiveQuery;

trait DefaultScopesTrait
{
    /**
     * Default scope configurations
     *
     * @example
     * [
     *     'noParameters',
     *     'singleParameter' => 'single',
     *     'multiParameters' => ['first', 'second']
     * ]
     *
     * the above example will lead to the following scope calls:
     *
     * $this->noParameters();
     * $this->singleParameter('single');
     * $this->multiParameters('first','second');
     *
     * @var array
     */
    public $defaultScopes = [];

    /**
     * Initializes the default scopes
     */
    protected function initDefaultScopes()
    {
        // Calling default scopes
        foreach ($this->defaultScopes as $key => $value) {

            // Integer key or empty value means a scope without parameters
            if (is_int($key) || empty($value)) {
                $scope = is_int($key) ? $key : $value;
                $this->$scope();
            } elseif (!is_array($value)) { // If $value is not an array, means single parameter
                $this->$key($value);
            } else { // Otherwise, it's a multiparameter scope
                call_user_func_array([$this, $key], $value);
            }
        }
    }
}
