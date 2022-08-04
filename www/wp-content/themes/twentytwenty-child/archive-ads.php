<?php
/**
 * The template for displaying ads tax.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WordPress
 * @subpackage Twenty_Twenty
 * @since Twenty Twenty 1.0
 */
get_header();
?>

<main>
    <?php
    $archive_title = '';
    $archive_subtitle = '';

    if (is_archive() && !have_posts()) {
        $archive_title = __('Nothing Found', 'twentytwenty');
    } elseif (!is_home()) {
        $archive_title = get_the_archive_title();
        $archive_subtitle = get_the_archive_description();
    }

    if ($archive_title || $archive_subtitle) {
        ?>

        <header class="archive-header has-text-align-center header-footer-group">

            <div class="archive-header-inner section-inner medium">

                <?php if ($archive_title) { ?>
                    <h1 class="archive-title"><?php echo wp_kses_post($archive_title); ?></h1>
                <?php } ?>

                <?php if ($archive_subtitle) { ?>
                    <div class="archive-subtitle section-inner thin max-percentage intro-text"><?php echo wp_kses_post( wpautop( $archive_subtitle ) ); ?></div>
                <?php } ?>

            </div><!-- .archive-header-inner -->

        </header><!-- .archive-header -->
        <?php
    }

    if (have_posts()) {
        ?>
    <div id="ads-archive">
        
        <?php
        while (have_posts()) {
            the_post();
            $ads_imgs = get_post_meta(get_the_ID(), 'post_ads_img', true);
            $ads_imgs = explode(',', $ads_imgs);
            $title = get_the_title();
            $imgage_url = !empty($ads_imgs[0])?wp_get_attachment_url( $ads_imgs[0] ): get_stylesheet_directory_uri() . '/images/default_pic.jpg';
    ?>

    <article class="ads-single-card ">
        <div class="aspect-ratio-box">
            <img src="<?php echo $imgage_url; ?>" alt="<?php echo esc_attr($title); ?>" title="<?php echo esc_attr($title); ?>"/>
        </div>
        <header class="entry-header">
            <h2 class="entry-title heading-size-1">
                <a href="<?php echo esc_url( get_permalink() ) ?>" title="<?php echo esc_attr($title); ?>">
                    <?php echo $title; ?>
                </a>
            </h2>
        </header>
    </article>
    <?php
        }
    }
?>
    </div>
</main><!-- #site-content -->

<?php get_template_part('template-parts/footer-menus-widgets'); ?>

<?php get_footer(); ?>
