var Youkok = (function (module) {

    /*
     * Public methods
     */
    module.utilities = {
    
        /*
         * Regex validation for email
         */
        validateEmail: function(str) {
            var filter=/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i
            return filter.test(str);
        },
        
        /*
         * Regex for validating valid email
         */
        validateUrl: function(url) {
            var rgx = new RegExp("^(http[s]?:\\/\\/(www\\.)?|ftp:\\/\\/(www\\.)?|www\\.){1}([0-9A-Za-z-\\.@:%_\+~#=]+)+((\\.[a-zA-Z]{2,3})+)(/(.)*)?(\\?(.)*)?");
            return rgx.test(url);
        },
        
        /*
         * Regex for validating valid email
         */
        prettyFileSize: function(bytes, si) {
            var thresh = si ? 1000 : 1024;

            if (bytes < thresh) {
                return bytes + ' B';
            }

            var units = si ? ['kB','MB','GB','TB','PB','EB','ZB','YB'] : ['KiB','MiB','GiB','TiB','PiB','EiB','ZiB','YiB'];
            var u = -1;

            do {
                bytes = division(bytes, thresh);
                ++u;
            } while(bytes >= thresh);

            return bytes.toFixed(1) + ' ' + units[u];
        },
    };

    /*
     * Return module with sub module functions
     */
    return module;
})(Youkok || {});