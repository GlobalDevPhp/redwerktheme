<?php
// leave parent styles
add_action('wp_enqueue_scripts', 'enqueue_parent_styles');

function enqueue_parent_styles() {
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
}

// Register ADS custom type
add_action('init', 'register_post_type_ads');

function register_post_type_ads() {
    $labels = array(
        'name' => __('Advert'),
        'singular_name' => __('Advert'),
        'add_new' => __('Add advert'),
        'add_new_item' => __('Add new advert'),
        'edit_item' => __('Edit advert'),
        'new_item' => __('New advert'),
        'all_items' => __('All advert'),
        'view_item' => __('View advert'),
        'search_items' => __('Find advert'),
        'not_found' => __('Adverts not found'),
        'not_found_in_trash' => __('No adverts in trash'),
        'menu_name' => __('Advert'),
    );
    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => true,
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_icon' => 'dashicons-businessperson',
        'menu_position' => 20, // порядок в меню
        'rewrite' => array(
            'slug' => 'ads', // use this slug instead of post type name
            'with_front' => FALSE, // if you have a permalink base such as /blog/ then setting this to false ensures your custom post type permalink structure will be /products/ instead of /blog/products
            'pages' => true,
            'feeds' => true,
        ),
        'supports' => array('title'),
        'taxonomies' => array('ads'),
    );

    register_post_type('ads', $args);

    //flush_rewrite_rules();
}

// Add metabox for our custom post
add_action('add_meta_boxes', 'ads_meta_box_add');

function ads_meta_box_add() {
    add_meta_box('ads-image-div', __('Advert images'), 'ads_metabox', 'ads', 'normal', 'low');
}

function ads_metabox($post) {
    wp_nonce_field(basename(__FILE__), 'ads-image');
    $ids = get_post_meta($post->ID, 'vdw_ads_id', true);

    $ads_img = get_post_meta($post->ID, 'post_ads_img', true);
    ?>
    <style type="text/css">
        .multi-upload-medias ul li .delete-img {
            position: absolute;
            right: 3px;
            top: 2px;
            background: aliceblue;
            border-radius: 50%;
            cursor: pointer;
            font-size: 14px;
            line-height: 20px;
            color: red;
        }
        .multi-upload-medias ul li {
            width: 120px;
            display: inline-block;
            vertical-align: middle;
            margin: 5px;
            position: relative;
        }
        .multi-upload-medias ul li img {
            width: 100%;
        }
    </style>

    <table cellspacing="10" cellpadding="10">
        <tr>
            <td><?php _e('Select advert images'); ?></td>
            <td>
                <?php echo multi_media_uploader_field('post_ads_img', $ads_img); ?>
            </td>
        </tr>
    </table>

    <script type="text/javascript">
        jQuery(function ($) {

            $('body').on('click', '.wc_multi_upload_image_button', function (e) {
                e.preventDefault();

                var button = $(this),
                        custom_uploader = wp.media({
                            title: '<?php _e('Insert image'); ?>',
                            button: {text: '<?php _e('Use this image'); ?>'},
                            multiple: true
                        }).on('select', function () {
                    var attech_ids = '';
                    attachments
                    var attachments = custom_uploader.state().get('selection'),
                            attachment_ids = new Array(),
                            i = 0;
                    attachments.each(function (attachment) {
                        attachment_ids[i] = attachment['id'];
                        attech_ids += ',' + attachment['id'];
                        if (attachment.attributes.type == 'image') {
                            $(button).siblings('ul').append('<li data-attechment-id="' + attachment['id'] + '"><a href="' + attachment.attributes.url + '" target="_blank"><img class="true_pre_image" src="' + attachment.attributes.url + '" /></a><i class=" dashicons dashicons-no delete-img"></i></li>');
                        } else {
                            $(button).siblings('ul').append('<li data-attechment-id="' + attachment['id'] + '"><a href="' + attachment.attributes.url + '" target="_blank"><img class="true_pre_image" src="' + attachment.attributes.icon + '" /></a><i class=" dashicons dashicons-no delete-img"></i></li>');
                        }

                        i++;
                    });

                    var ids = $(button).siblings('.attechments-ids').attr('value');
                    if (ids) {
                        var ids = ids + attech_ids;
                        $(button).siblings('.attechments-ids').attr('value', ids);
                    } else {
                        $(button).siblings('.attechments-ids').attr('value', attachment_ids);
                    }
                    $(button).siblings('.wc_multi_remove_image_button').show();
                })
                        .open();
            });

            $('body').on('click', '.wc_multi_remove_image_button', function () {
                $(this).hide().prev().val('').prev().addClass('button').html('<?php _e('Add Media'); ?>');
                $(this).parent().find('ul').empty();
                return false;
            });

        });

        jQuery(document).ready(function () {
            jQuery(document).on('click', '.multi-upload-medias ul li i.delete-img', function () {
                var ids = [];
                var this_c = jQuery(this);
                jQuery(this).parent().remove();
                jQuery('.multi-upload-medias ul li').each(function () {
                    ids.push(jQuery(this).attr('data-attechment-id'));
                });
                jQuery('.multi-upload-medias').find('input[type="hidden"]').attr('value', ids);
            });
        })
    </script>

    <?php
}

function multi_media_uploader_field($name, $value = '') {
    $image = '">' . __('Add Media');
    $image_str = '';
    $image_size = 'full';
    $display = 'none';
    $value = explode(',', $value);

    if (!empty($value)) {
        foreach ($value as $values) {
            if ($image_attributes = wp_get_attachment_image_src($values, $image_size)) {
                $image_str .= '<li data-attechment-id=' . $values . '><a href="' . $image_attributes[0] . '" target="_blank"><img src="' . $image_attributes[0] . '" /></a><i class="dashicons dashicons-no delete-img"></i></li>';
            }
        }
    }

    if ($image_str) {
        $display = 'inline-block';
    }

    return '<div class="multi-upload-medias"><ul>' . $image_str . '</ul><a href="#" class="wc_multi_upload_image_button button' . $image . '</a><input type="hidden" class="attechments-ids ' . $name . '" name="' . $name . '" id="' . $name . '" value="' . esc_attr(implode(',', $value)) . '" /><a href="#" class="wc_multi_remove_image_button button" style="display:inline-block;display:' . $display . '">' . __('Remove media') . '</a></div>';
}

// Save Meta Box values.
add_action('save_post', 'ads_meta_box_save');
function ads_meta_box_save($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post')) {
        return;
    }

    if (isset($_POST['post_ads_img'])) {
        update_post_meta($post_id, 'post_ads_img', $_POST['post_ads_img']);
    }
}

// enqueue scripts image loadin on wp-admin
add_action('admin_enqueue_scripts', 'load_media_files');
function load_media_files() {
    wp_enqueue_media();
}

add_filter('wp_nav_menu_args', 'my_add_menu_descriptions');
function my_add_menu_descriptions($args) {
    $args['walker'] = new Menu_With_Description;
    return $args;
}

class Menu_With_Description extends Walker_Nav_Menu {

    function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
        global $wp_query;

        $indent = ( $depth ) ? str_repeat("t", $depth) : '';

        $class_names = $value = '';

        $classes = empty($item->classes) ? array() : (array) $item->classes;

        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item));
        $class_names = ' class="' . esc_attr($class_names) . '"';

        $output .= $indent . '<li id="menu-item-' . $item->ID . '"' . $value . $class_names . '>';

        $attributes = !empty($item->attr_title) ? ' title="' . esc_attr($item->attr_title) . '"' : '';
        $attributes .= !empty($item->target) ? ' target="' . esc_attr($item->target) . '"' : '';
        $attributes .= !empty($item->xfn) ? ' rel="' . esc_attr($item->xfn) . '"' : '';
        $attributes .= !empty($item->url) ? ' href="' . esc_attr($item->url) . '"' : '';

        $item_output = $args->before;

        // menu link output
        $item_output .= '<a' . $attributes . '>';
        if ($item->classes[0] == 'add_button')
            $item_output .= '<img src="' . get_stylesheet_directory_uri() . '/images/plus.svg" alt="' . esc_attr($item->attr_title) . '" title="' . esc_attr($item->attr_title) . '">';
        $item_output .= $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after;

        // close menu link anchor
        $item_output .= '</a>';
        $item_output .= $args->after;

        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }

}

//function to add custom media field
function custom_media_add_media_custom_field($form_fields, $post) {
    $field_value = get_post_meta($post->ID, 'custom_media_style', true);

    $form_fields['custom_media_style'] = array(
        'value' => $field_value ? $field_value : '',
        'label' => __('Style'),
        'helps' => __('Enter your style'),
        'input' => 'textarea'
    );

    return $form_fields;
}

add_filter('attachment_fields_to_edit', 'custom_media_add_media_custom_field', null, 2);

//save your custom media field
function custom_media_save_attachment($attachment_id) {
    if (isset($_REQUEST['attachments'][$attachment_id]['custom_media_style'])) {
        $custom_media_style = $_REQUEST['attachments'][$attachment_id]['custom_media_style'];
        update_post_meta($attachment_id, 'custom_media_style', $custom_media_style);
    }
}

add_action('edit_attachment', 'custom_media_save_attachment');
