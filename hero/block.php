<?php
defined( 'ABSPATH' ) || die();

/**
 * Hero Block Template.
 *
 * @param  array       $block       The block settings and attributes.
 * @param  string      $content     The block inner HTML (empty).
 * @param  bool        $is_preview  True during AJAX preview.
 * @param  int|string  $post_id     The post ID this block is saved to.
 */ 

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
