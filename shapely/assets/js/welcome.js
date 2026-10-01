/**
 * Shapely welcome screen.
 *
 * Replaces the Epsilon welcome screen's JS. Two behaviours matter:
 *
 *  - The demo import posts `shapely_companion_import_content` with a
 *    `welcome_nonce`. That endpoint lives in the Shapely Companion plugin, so
 *    the action name, the `import` value and the nonce field are a fixed
 *    contract and must not be renamed here.
 *  - Plugin installation is left to core's wp.updates rather than reimplemented.
 */
( function ( $ ) {
	'use strict';

	if ( typeof window.shapelyWelcome === 'undefined' ) {
		return;
	}

	var cfg = window.shapelyWelcome;

	/**
	 * Collect the checked import options inside a container.
	 *
	 * The old screen sent 'import-all' when more than one option was ticked,
	 * and the single option's own value when exactly one was. The companion
	 * plugin still branches on those values, so keep the same mapping.
	 */
	function importSelection( $container ) {
		var options = [];

		$container.find( 'input[type="checkbox"][name="options"]:checked' ).each( function () {
			options.push( $( this ).val() );
		} );

		if ( ! options.length ) {
			return null;
		}

		return options.length === 1 ? options[ 0 ] : 'import-all';
	}

	function runImport( $button ) {
		var $container = $button.closest( '.shapely-action' );
		var selection = importSelection( $container );

		if ( null === selection ) {
			// Nothing ticked: there is nothing to import.
			return;
		}

		// The button is a link, which ignores `disabled`. A second click used
		// to send a second import, and 'import-all' then duplicated the demo posts.
		if ( $button.hasClass( 'updating-message' ) ) {
			return;
		}

		$button.data( 'label', $button.text() );
		$button.addClass( 'updating-message' ).prop( 'disabled', true ).text( cfg.strings.importing );

		$.ajax( {
			url: cfg.ajaxurl,
			type: 'POST',
			dataType: 'json',
			data: {
				action: 'shapely_companion_import_content',
				import: selection,
				nonce: cfg.importNonce
			}
		} )
			.done( function ( response ) {
				var ok = response && ( response.success === true || ( response.data && response.data.status === true ) );

				if ( ok ) {
					$container.html( '<h3>' + cfg.strings.imported + '</h3>' );
					window.setTimeout( function () {
						window.location.reload();
					}, 1500 );
					return;
				}

				importFailed( $container, $button );
			} )
			.fail( function () {
				importFailed( $container, $button );
			} );
	}

	function importFailed( $container, $button ) {
		$container.find( '.shapely-action__error' ).remove();
		$container.append( $( '<p class="shapely-action__error"></p>' ).text( cfg.strings.failed ) );
		$button.removeClass( 'updating-message' ).prop( 'disabled', false ).text( $button.data( 'label' ) );
	}

	function dismissAction( $button ) {
		var $item = $button.closest( '.shapely-action' );
		var id = $item.data( 'action-id' );

		if ( ! id ) {
			return;
		}

		$.ajax( {
			url: cfg.ajaxurl,
			type: 'POST',
			dataType: 'json',
			data: {
				action: 'shapely_dismiss_action',
				id: id,
				nonce: cfg.dismissNonce
			}
		} ).done( function () {
			$item.slideUp( 200, function () {
				$item.remove();
			} );
		} );
	}

	$( function () {
		$( document ).on( 'click', '[data-action="import_demo"]', function ( e ) {
			e.preventDefault();
			runImport( $( this ) );
		} );

		$( document ).on( 'click', '.shapely-action__dismiss', function ( e ) {
			e.preventDefault();
			dismissAction( $( this ) );
		} );

		// Toggle the advanced import options.
		$( document ).on( 'click', '.epsilon-hidden-content-toggler', function ( e ) {
			e.preventDefault();
			$( $( this ).attr( 'href' ) ).slideToggle( 150 );
		} );

		// The notice's dismiss button is added by core's common.js, so hook its click.
		$( document ).on( 'click', '.shapely-welcome-notice .notice-dismiss', function () {
			$.post( cfg.ajaxurl, {
				action: 'shapely_dismiss_notice',
				nonce: cfg.dismissNonce
			} );
		} );
	} );
} )( jQuery );
