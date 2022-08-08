<?php
require_once get_stylesheet_directory() . '/inc/class.Menu_With_Icons.php';

// leave parent styles and add new
add_action('wp_enqueue_scripts', 'enqueue_ev_scripts');

function enqueue_ev_scripts() {
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
}

add_action('after_setup_theme', 'twentytwentych_theme_setup');
function twentytwentych_theme_setup(){
    load_child_theme_textdomain( 'twentytwenty-child', get_stylesheet_directory() . '/languages' );
}

// Register ADS custom type
add_action('init', 'register_post_type_ads');
function register_post_type_ads() {
    $labels = array(
        'name' => __('Advert', 'twentytwenty-child'),
        'singular_name' => __('Advert', 'twentytwenty-child'),
        'add_new' => __('Add advert', 'twentytwenty-child'),
        'add_new_item' => __('Add new advert', 'twentytwenty-child'),
        'edit_item' => __('Edit advert', 'twentytwenty-child'),
        'new_item' => __('New advert', 'twentytwenty-child'),
        'all_items' => __('All advert', 'twentytwenty-child'),
        'view_item' => __('View advert', 'twentytwenty-child'),
        'search_items' => __('Find advert', 'twentytwenty-child'),
        'not_found' => __('Adverts not found', 'twentytwenty-child'),
        'not_found_in_trash' => __('No adverts in trash', 'twentytwenty-child'),
        'menu_name' => __('Advert', 'twentytwenty-child'),
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
    add_meta_box('ads-image-div', __('Advert images', 'twentytwenty-child'), 'ads_metabox', 'ads', 'normal', 'low');
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
            <td><?php _e('Select advert images', 'twentytwenty-child'); ?></td>
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
                            title: '<?php _e('Insert image', 'twentytwenty-child'); ?>',
                            button: {text: '<?php _e('Use this image', 'twentytwenty-child'); ?>'},
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
                $(this).hide().prev().val('').prev().addClass('button').html('<?php _e('Add Media', 'twentytwenty-child'); ?>');
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
    $image = '">' . __('Add Media', 'twentytwenty-child');
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

    if (!current_user_can('edit_posts')) {
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
    wp_register_script('sunset-admin-script', get_template_directory_uri() . '/js/news_admin.js', array('jquery'), '1.0.0', true);
    wp_enqueue_script('sunset-admin-script');
}

add_filter('wp_nav_menu_args', 'my_add_menu_descriptions');
function my_add_menu_descriptions($args) {
    $args['walker'] = new Menu_With_Icons;
    return $args;
}

add_action('edit_attachment', 'custom_media_save_attachment');
//save your custom media field
function custom_media_save_attachment($attachment_id) {
    if (isset($_REQUEST['attachments'][$attachment_id]['custom_media_style'])) {
        $custom_media_style = $_REQUEST['attachments'][$attachment_id]['custom_media_style'];
        update_post_meta($attachment_id, 'custom_media_style', $custom_media_style);
    }
}

add_action("wp_ajax_send_email", "send_email");
add_action("wp_ajax_nopriv_send_email", "send_email");

function send_email() {
    // Timeout sending email to user in minutes
    $timeout = 5;
    
    $uploadOk = 1;

    $client_title = $_POST['client_title'];
    $client_mail = sanitize_email($_POST['client_mail']);
    
    if (!filter_var($client_mail, FILTER_VALIDATE_EMAIL) || empty($client_title))
        $uploadOk = 0;
    
    $upload_dir = wp_upload_dir();
    if ( wp_mkdir_p( $upload_dir['path'] ) ) {
      $file = $upload_dir['path'] . '/' . $_FILES['attach_file']['name'];
    }
    else {
      $file = $upload_dir['basedir'] . '/' . $_FILES['attach_file']['name'];
    }    
    
    $wp_filetype = wp_check_filetype( $_FILES['attach_file']['name'], null );

    // Allow certain file formats
    if ($wp_filetype['type'] != "image/jpeg" && $wp_filetype['type'] != "image/png") {
        //echo "Sorry, only  doc, docx or pdf files are allowed.";
        $uploadOk = 0;
    }
    
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        //echo "Sorry, your file was not uploaded.";
        // if everything is ok, try to upload file
        echo json_encode(array('success' => 'false', 'sendmail' => 'false', 'message' => 'error_fields_or_type'));
    } else {
        if (move_uploaded_file($_FILES['attach_file']['tmp_name'], $file)) {

            $attachment = array(
                'post_mime_type' => $wp_filetype['type'],
                'post_title' => sanitize_file_name( $_FILES['attach_file']['name'] ),
                'post_content' => '',
                'post_status' => 'inherit'
            );

            $attach_id = wp_insert_attachment( $attachment, $file );
            require_once( ABSPATH . 'wp-admin/includes/image.php' );
            $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
            wp_update_attachment_metadata( $attach_id, $attach_data );
            
            // insert the post and set the category
            $post_id = wp_insert_post(array (
                'post_type' => 'ads',
                'post_title' => $client_title,
                'post_status' => 'draft',
                'comment_status' => 'closed',
                'ping_status' => 'closed',
            ));

            if ($post_id) {
                // insert post meta
                $ads_imgs = get_post_meta(get_the_ID(), 'post_ads_img', true);
                add_post_meta($post_id, 'post_ads_img', $attach_id);
            }            
            $contact_to = get_option('admin_email');
            $headers = array ('MIME-Version: 1.0',
            'Content-Type: text/html; charset=UTF-8',
            'From: "Apply form" <'.$contact_to.'.com>',
            'Reply-To: "User" <' . $client_mail . '>');
            $subject = __('User add new ads', 'twentytwenty-child');
            $message = __('User add new ads', 'twentytwenty-child').', <a href="'.get_permalink($post_id).'">'.__('check it', 'twentytwenty-child').'</a>. ';

            wp_schedule_single_event(time() + 60*$timeout, 'user_sending_mail_action', array(array('email' => $client_mail, 'time'=>time(), 'title' => $client_title, 'action' => 'user_sending_mail_action')) );

            if (empty($contact_to) || wp_mail($contact_to, $subject, $message, $headers) === false) {
                echo json_encode(array('success' => 'true', 'sendmail' => 'false', 'message' => 'send_faild'));
            } else {
              
                echo json_encode(array('success' => 'true', 'sendmail' => 'true', 'message' => 'send_success'));
            }
        } else {
            echo json_encode(array('success' => 'false', 'upload' => 'false', 'message' => 'move_uploaded_file_error'));
        }
    }

    die();
}

add_action('user_sending_mail_action', 'user_sending_mail', 1, 1);
function user_sending_mail($args) {
    $contact_to = $args['email'];
    $admin = get_option('admin_email');
    $headers = array ('MIME-Version: 1.0',
    'Content-Type: text/html; charset=UTF-8',
    'From: "Apply form" <'.$admin.'>',
    'Reply-To: "Admin " <' . $admin . '>');
    $subject = __('Hi! Thanks for the posted ad! ', 'twentytwenty-child');
    $message = __('Thanks for the posted ad! If this ad passes moderation, it will definitely appear on the site.', 'twentytwenty-child');

    wp_mail($contact_to, $subject, $message, $headers);
}    