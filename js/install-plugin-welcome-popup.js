jQuery(document).ready(function($) {
    var pluginSlug = 'xml-sitemap-for-google';
    var installURL = 'plugin-install.php?tab=plugin-information&plugin=' + pluginSlug + '&TB_iframe=true&width=900&height=800';
    $('#open-install-welcome-popup').on('click', function(e) {
        e.preventDefault();
        tb_show('Plugin Installation', installURL);
    });
});