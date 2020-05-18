<?php
/**
 * Plugin Name:       Example ACF block
 * Description:       An example of how to user ACF to build custom blocks for the WordPress editor
 * Version:           1.0
 * Plugin URI :       https://github.com/vincedubroeucq/example-acf-block
 * Author:            Vincent Dubroeucq
 * Author URI:        https://vincentdubroeucq.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       example
 * Domain Path:       languages/
 */
defined( 'ABSPATH' ) || die();

add_action( 'acf/init', 'example_register_acf_block' );
/**
 * Registers our new block
 * 
 * @return  array  $data  Array of registered block data
 */
function example_register_acf_block(){
    if( function_exists( 'acf_register_block_type' ) ) {
        $data = acf_register_block_type( array(
            'name'              => 'hero',                                         // Unique slug for the block
            'title'             => __( 'Hero block', 'example' ),                  // Diplay title for the block
            'description'       => __( 'A simple hero block to use as header for a page.', 'example' ), // Optional
            'category'          => 'layout',                                       // Inserter category
            // 'icon'              => 'carrot',                                       // Optional. Custom SVG or dashicon slug.
            'example'           => 'true',                                         // Determines whether to show an example in the inserter or not
            'keywords'          => array( __( 'hero', 'example' ), __( 'header', 'example' ) ), // Optional. Useful to find the block in the inserter
            // 'post_types'        => array( 'post', 'page' ),                        // Optional. Default posts, pages
            'mode'              => 'preview',                                      // Optional. Default value of 'preview'
            'align'             => 'full',                                         // Default alignment. Default empty string
            'render_template'   => plugin_dir_path( __FILE__ ) . 'hero/block.php', // Path to template file. Default false
            // 'render_callback'   => 'example_block_markup',                      // Callback function to display the block if you prefer.
            'enqueue_style'     => plugins_url( '/hero/block.css', __FILE__ ),     // URL to CSS file. Enqueued on both frontend and backend
            // 'enqueue_script'    => plugins_url( '/hero/block.js', __FILE__ ),      // URL to JS file. Enqueued on both frontend and backend
            // 'enqueue_assets'    => 'example_block_assets',                      // Callback to enqueue your scripts
            'supports'          => array(                                          // Optional. Array of standard editor supports
                'align'           => array( 'full', 'wide' ),                      // Toolbar alignment supports
                'anchor'          => true,                                         // Allows for a custom ID.
                // 'customClassName' => true,                                         // Allows for a custom CSS class name
                // 'mode'            => true,                                         // Allows for toggling between edit/preview modes. Default true.
                'multiple'        => false,                                        // Allows for multiple instances of the block. Default true.
            ),
        ) );
        return $data;
    }
}

/**
 * Callback function used to display our block on both frontend and backend
 * 
 * @param  array       $block       The block settings and attributes.
 * @param  string      $content     The block inner HTML (empty).
 * @param  bool        $is_preview  True during AJAX preview.
 * @param  int|string  $post_id     The post ID this block is saved to.
 */
function example_block_markup( $block, $content = '', $is_preview = false, $post_id = 0 ){

    // Build the basic block id and class 
    $block_id     = ! empty( $block['anchor'] ) ? sanitize_title( $block['anchor'] ) : 'block-hero-' . $block['id'];
    $block_class  = 'block-hero';
    $block_class .= ! empty( $block['className'] ) ? ' ' . sanitize_html_class( $block['className'] ) : '';
    $block_class .= ! empty( $block['align'] ) ? ' align' . sanitize_key( $block['align'] ) : '';

    // Get our data
    $heading      = get_field( 'heading' ) ?: __( 'Hero Heading', 'example' );
    $description  = get_field( 'cta_description' ) ?: '';
    $button_label = get_field( 'cta_button_label' ) ?: '';
    $button_url   = get_field( 'cta_button_url' ) ?: '';
    $background_image_id = get_field( 'background_image' ) ?: '';
    $background_color    = get_field( 'background_color' ) ?: '';

    if ( $background_image_id ){
        $src = array(
            'medium_large' => wp_get_attachment_image_src( $background_image_id, 'medium_large' )[0],
            'large'        => wp_get_attachment_image_src( $background_image_id, 'large' )[0],
            'full'         => wp_get_original_image_url( $background_image_id ),
        );
    }

    // Let's display our block !
    $selector = '#' . sanitize_html_class( $block_id );
    if( ! empty( $src ) ) : ?>
        <style>
            <?php echo $selector; ?> {
                    background-image: url("<?php echo esc_url( $src['medium_large'] ); ?>");
                }
                @media screen and (min-width: 768px){
                    <?php echo $selector; ?>{
                        background-image: url("<?php echo esc_url( $src['large'] ); ?>");
                    }
                }
                @media screen and (min-width: 1024px){
                    <?php echo $selector; ?>{
                        background-image: url("<?php echo esc_url( $src['full'] ); ?>");
                    }
                }
            }
        </style>
    <?php endif;

    if( ! empty( $background_color )  ) : ?>
        <style>
            <?php echo $selector; ?> {
                background-color: <?php echo sanitize_hex_color( $background_color ); ?>
            }
        </style>
    <?php endif; ?>

    <div id="<?php echo esc_attr( $block_id ); ?>" class="<?php echo esc_attr( $block_class ); ?>">
        <div class="wrapper">
            <div class="hero-content">
                <h1 class="hero-title"><?php echo esc_html( $heading ); ?></h1>
                <?php if( ! empty( $description ) ) : ?>
                    <div class="hero-description"><?php echo wp_kses_post( $description ); ?></div>
                <?php endif; ?>
                <?php if( ! empty( $button_label ) ) : ?>
                    <a class="wp-block-button__link hero-button" href="<?php echo esc_attr( esc_url( $button_url ) ); ?>"><?php echo esc_html( $button_label ); ?></a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php return;
}