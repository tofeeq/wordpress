<?php
namespace Ocenture;

class View {

	public static function render( $name, array $args = array() ) {

		$args = apply_filters( 'view_arguments', $args, $name );
		
		foreach ( $args as $key => $val ) {
			$$key = $val;
		}

		$file = OCENTURE_VIEWS_DIR . $name . '.php';

		if (file_exists($file)) {
			require( $file );
		} else {
			echo "view $file not found";
		}
	}
}

//Example: View::render( 'notice', array( 'type' => 'spam-check', 'link_text' => $link_text ) );