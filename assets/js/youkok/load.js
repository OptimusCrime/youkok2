/*
 * Fire at document ready
 */
$(document).ready(function () {
    
    /*
     * Init Youkok module
     */
    Youkok.init();
    
    /*
     * Archive
     */
    if (Youkok.getData('view') == 'archive') {
        Youkok.countdown.init();
        Youkok.archive.init();
    }
    
    /*
     * Frontpage
     */
    if (Youkok.getData('view') == 'frontpage') {
        $('#home-most-popular-dropdown li').on('click', Youkok.frontpage.changeMostPopular);
        $('#home-most-popular-dropdown li').on('click', Youkok.frontpage.changeMostPopular);
        $('.star-remove').on('click', Youkok.frontpage.removeFavorite);
    }
    
    /*
     * Attach debug listeners
     */
    $('#toggle-queries').on('click', Youkok.debug.toggleQueries);
    $('#toggle-cache-loads').on('click', Youkok.debug.toggleCache);
});