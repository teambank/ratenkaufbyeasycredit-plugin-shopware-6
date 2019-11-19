const path = require('path');

module.exports = function(config) {
    return {
        resolve: {
            modules: [ path.dirname(config.plugin.entryFile)+'/node_modules' ]
        }
    }
}