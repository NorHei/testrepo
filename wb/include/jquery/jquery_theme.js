var JQUERY_THEME = WB_URL+'/include/jquery';

$(document).ready(function() {
//        $.insert(JQUERY_THEME+'/jquery-ui.css' );
        $.include( JQUERY_THEME+'/jquery-ui-min.js');
        LoadOnFly( 'head', JQUERY_THEME+'/jquery-ui.css' );
//        LoadOnFly( 'body', JQUERY_THEME+'/jquery-ui-min.js' );
});
