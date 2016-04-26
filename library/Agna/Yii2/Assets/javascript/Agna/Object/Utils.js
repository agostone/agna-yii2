'use strict';

(function (Utils) {

    /**
     * Returns with an object property value
     *
     * @public
     * @param {object} object
     * @param {string} property
     * @return {mixed}
     */
    Utils.getPropertyRecursive = function (object, property)
    {
        if (!Array.isArray(property) && typeof(property) !== 'string') {
            return false;
        }

        if (!Array.isArray(property)) {
            property = property.split('.');
        }

        var value = property.shift();

        if (!(value in object)) {
            return false;
        }

        value = object[value];

        if (property.length > 0) {
            value = Utils.getPropertyRecursive(value, property);
        }

        return value;
    };

    /**
     * Sets an object property recursively
     *
     * @public
     * @param {object} object
     * @param {string} property
     * @param {mixed} value
     * @return {mixed}
     */
    Utils.setPropertyRecursive = function (object, property, value)
    {
        if (!Array.isArray(property) && typeof(property) !== 'string') {
            throw 'Invalid type of "object" and/or "property" parameter!';
        }

        if (!Array.isArray(property)) {
            property = property.split('.');
        }

        var targetObject = object;
        var targetProperty = property.shift();

        if (property.length > 0) {
            if (targetObject[targetProperty] === undefined) {
                targetObject[targetProperty] = {};
            }
            targetObject = Utils.setPropertyRecursive(targetObject[targetProperty], property.join('.'), value);
        } else {
            targetObject[targetProperty] = value;
        }

        return targetObject;
    }

})(Agna.Namespace.get('Agna.Object.Utils'));

