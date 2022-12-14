<?php
/**
 * Form template
 *
 * @package WordPress
 * @subpackage Twenty_Twenty
 * @since Twenty Twenty 1.0
 */

?>
<form action="<?php echo admin_url('admin-ajax.php'); ?>" method="post" class="form-send-mail form-style">

    <div class="form-view">

        <div class="form-field">
            <input type="text" id="client_title" name="client_title" placeholder="<?php _e('Enter title', 'twentytwenty-child'); ?>" class="req-field"/>
        </div>

        <div class="form-field">
            <input type="text" id="client_mail" name="client_mail" placeholder="<?php _e('Your e-mail', 'twentytwenty-child'); ?>" class="req-field rf-mail"/>
        </div>

        <div class="form-field">
            <input type="file" id="client_attach" name="client_attach" placeholder="<?php _e('Image', 'twentytwenty-child'); ?>" data-original_id="attach" autocomplete="off" readonly="" class="req-field rf-file">
        </div>
        
        <div class="form-submit">
            <button type="submit"><?php _e('Send', 'twentytwenty-child'); ?></button>
        </div>

    </div>

    <div class="form-is-sending">
        <div class="progress">
            <div class="progress-bar"></div>
        </div>        
        <span class="pa-spinner"></span>
    </div>

</form>

