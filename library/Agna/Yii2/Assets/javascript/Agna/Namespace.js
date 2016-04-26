'use strict';

(function (root) {

    root.Agna = root.Agna || {};
    root.Agna.Namespace = root.Agna.Namespace || {};

    Agna.Namespace.get = function (namespace, object) {
        if (typeof namespace !== 'string') {
            throw 'Invalid "namespace" parameter, must be string!';
        }

        var subNamespaces = namespace.split('.');
        var namespaceObject = root;

        for (var index = 0; index < subNamespaces.length; index++) {
            if (namespaceObject[subNamespaces[index]] === undefined) {
                namespaceObject[subNamespaces[index]] = {};
            }

            namespaceObject = namespaceObject[subNamespaces[index]];
        }

        return namespaceObject;
    }

})(this);
