(function( $ ) {// jscs:ignore validateLineBreaks

  'use strict';

  var api = wp.customize;

  /*
   * wp.customize( callback ) with a lone argument is an id lookup, not a ready
   * handler, so this callback never ran and the Builder page's widget section
   * was never opened for you.
   */
  api.bind( 'ready', function() {
    var currentURL = api.settings.url.preview,
        urlBase,
        urlParts,
        pageSidebarID;
    if ( currentURL !== ShapelyBuilder.siteURL ) {
      urlParts = currentURL.split( '/' );
      urlParts.pop();
      urlBase = urlParts[ urlParts.length - 1 ];
      if ( undefined !== ShapelyBuilder.pages[ urlBase ] ) {
        pageSidebarID = 'sidebar-widgets-shapely-' + urlBase;
        /*
         * Defer focus until:
         * 1. The section exist.
         * 2. The instance is embedded in the document (and so is focusable).
         * 3. The preview has finished loading so that the active states have been set.
         */
        api.section( pageSidebarID, function( instance ) {
          instance.deferred.embedded.done( function() {
            api.previewer.deferred.active.done( function() {
              // A section is not a jQuery object; it has no trigger().
              instance.focus();
            } );
          } );
        } );
      }
    }
  } );

})( jQuery );
