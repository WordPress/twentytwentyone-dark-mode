function toggleDarkMode() {
	var toggler = document.getElementById( 'dark-mode-toggler' ),
		html = document.querySelector( 'html' );

	if ( 'false' === toggler.getAttribute( 'aria-pressed' ) ) {
		toggler.setAttribute( 'aria-pressed', 'true' );
		html.classList.add( 'respect-color-scheme-preference' );
		window.localStorage.setItem( 'twentytwentyoneDarkMode', 'yes' );
	} else {
		toggler.setAttribute( 'aria-pressed', 'false' );
		html.classList.remove( 'respect-color-scheme-preference' );
		window.localStorage.setItem( 'twentytwentyoneDarkMode', 'no' );
	}
}

function darkModeInitialLoad() {
	var toggler = document.getElementById( 'dark-mode-toggler' ),
		isDarkMode = window.matchMedia( '(prefers-color-scheme: dark)' ).matches,
		html;

	if ( 'yes' === window.localStorage.getItem( 'twentytwentyoneDarkMode' ) ) {
		isDarkMode = true;
	} else if ( 'no' === window.localStorage.getItem( 'twentytwentyoneDarkMode' ) ) {
		isDarkMode = false;
	}

	if ( ! toggler ) {
		return;
	}
	if ( isDarkMode ) {
		toggler.setAttribute( 'aria-pressed', 'true' );
	}

	html = document.querySelector( 'html' );
	if ( isDarkMode ) {
		html.classList.add( 'respect-color-scheme-preference' );
	} else {
		html.classList.remove( 'respect-color-scheme-preference' );
	}
}

darkModeInitialLoad();
