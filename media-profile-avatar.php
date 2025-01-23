<?php
/*
Plugin Name: Media Profile Avatar
Plugin URI: https://faithcoder.com/
Description: Allows users to upload and use custom profile pictures from the WordPress Media Library.
Version: 1.0
Author: M Arif
Author URI: https://faithcoder.com/
License: GPL2
Text Domain: media-profile-avatar
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Enqueue admin CSS and JS
function mpa_enqueue_scripts($hook) {
    if ( 'profile.php' !== $hook && 'user-edit.php' !== $hook ) {
        return;
    }

    wp_enqueue_media();
    wp_enqueue_script(
        'mpa-profile-avatar',
        plugin_dir_url( __FILE__ ) . 'assets/js/mpa-profile-avatar.js',
        array( 'jquery' ),
        '1.0',
        true
    );

    wp_enqueue_style(
        'mpa-profile-avatar',
        plugin_dir_url( __FILE__ ) . 'assets/css/mpa-profile-avatar.css',
        array(),
        '1.0'
    );
}
add_action( 'admin_enqueue_scripts', 'mpa_enqueue_scripts' );

// Add the profile picture field to user profile
function mpa_add_profile_avatar_field( $user ) { ?>
    <h3><?php esc_html_e( 'Media Profile Avatar', 'media-profile-avatar' ); ?></h3>
    <table class="form-table">
        <tr>
            <th>
                <label for="mpa_profile_avatar"><?php esc_html_e( 'Profile Picture', 'media-profile-avatar' ); ?></label>
            </th>
            <td>
                <?php
                $custom_avatar = get_user_meta($user->ID, 'mpa_profile_avatar', true);
                if ($custom_avatar) {
                    echo '<img src="' . esc_url($custom_avatar) . '" id="mpa-profile-preview" style="max-width:100px; margin-bottom:10px; display:block;">';
                }
                ?>
                <input type="hidden" name="mpa_profile_avatar" id="mpa-profile-avatar" value="<?php echo esc_attr( get_user_meta( $user->ID, 'mpa_profile_avatar', true ) ); ?>" />
                <input type="button" class="button" id="mpa-upload-avatar" value="<?php esc_attr_e( 'Select Profile Picture', 'media-profile-avatar' ); ?>" />
                <input type="button" class="button" id="mpa-remove-avatar" value="<?php esc_attr_e( 'Remove Picture', 'media-profile-avatar' ); ?>" style="display: <?php echo $custom_avatar ? 'inline-block' : 'none'; ?>;" />
                <br>
                <span class="description"><?php esc_html_e( 'Select an image from the Media Library.', 'media-profile-avatar' ); ?></span>
            </td>
        </tr>
    </table>
<?php }
add_action( 'show_user_profile', 'mpa_add_profile_avatar_field' );
add_action( 'edit_user_profile', 'mpa_add_profile_avatar_field' );

// Save the profile picture
function mpa_save_profile_avatar( $user_id ) {
    if ( ! current_user_can( 'edit_user', $user_id ) ) {
        return false;
    }

    if ( isset( $_POST['mpa_profile_avatar'] ) ) {
        update_user_meta( $user_id, 'mpa_profile_avatar', esc_url_raw( $_POST['mpa_profile_avatar'] ) );
    }
}
add_action( 'personal_options_update', 'mpa_save_profile_avatar' );
add_action( 'edit_user_profile_update', 'mpa_save_profile_avatar' );

// Display profile picture on front end
function mpa_get_avatar( $avatar, $id_or_email, $size, $default, $alt ) {
    $user = false;

    if ( is_numeric( $id_or_email ) ) {
        $user = get_user_by( 'id', $id_or_email );
    } elseif ( is_object( $id_or_email ) && ! empty( $id_or_email->user_id ) ) {
        $user = get_user_by( 'id', $id_or_email->user_id );
    } elseif ( is_string( $id_or_email ) ) {
        $user = get_user_by( 'email', $id_or_email );
    }

    if ( $user ) {
        $custom_avatar = get_user_meta( $user->ID, 'mpa_profile_avatar', true );
        if ( $custom_avatar ) {
            $avatar = '<img src="' . esc_url( $custom_avatar ) . '" alt="' . esc_attr($alt) . '" class="avatar avatar-' . esc_attr($size) . ' photo" width="' . esc_attr($size) . '" height="' . esc_attr($size) . '" />';
        }
    }

    return $avatar;
}
add_filter( 'get_avatar', 'mpa_get_avatar', 10, 5 );
