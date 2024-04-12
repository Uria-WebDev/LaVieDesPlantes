<?php

function skt_blocks_add_google_fonts() {
    global $post;
    if ( is_object( $post ) && property_exists( $post, 'post_content' ) ) {
        $blocks = parse_blocks( $post->post_content );
        $google_fonts = gather_google_fonts($blocks);

        if ( count( $google_fonts) ) {
            foreach ( $google_fonts as &$font ) {
                $font = str_replace( ' ', '+', $font ) . ':100,100italic,200,200italic,300,300italic,400,400italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic';
            }

            $fonts_url = sprintf( 'https://fonts.bunny.net/css?family=%s', implode( rawurlencode( '|' ), $google_fonts ) .'&display=swap');
            wp_enqueue_style( 'skt-blocks-bunny-fonts', $fonts_url );
        }
    }
}
add_action( 'wp_enqueue_scripts', 'skt_blocks_add_google_fonts' );

function gather_google_fonts( $blocks ) {
    $google_fonts = array();
    foreach ( $blocks as $block ) {

        // Gather all "fontFamily" attribute values
        foreach ( $block['attrs'] as $attr_name => $font_name ) {
            if ( preg_match( '/fontFamily$/i', $attr_name ) ) {

                if ( ! in_array( $font_name, $google_fonts ) ) {
                    $google_fonts[] = $font_name;
                }
            }
        }

        // Look for fonts in inner blocks.
        if ( ! empty( $block['innerBlocks'] ) ) {
            $google_fonts = array_unique( array_merge( $google_fonts, gather_google_fonts( $block['innerBlocks'] ) ) );
        }
    }
    return $google_fonts;
}
