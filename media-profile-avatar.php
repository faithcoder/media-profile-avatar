<?php
/**
 * Plugin Name: Media Profile Avatar
 * Plugin URI:  https://example.com/media-profile-avatar
 * Description: Allows users to set custom profile pictures using the WordPress Media Library.
 * Version:     1.0.0
 * Author:      Your Name
 * Author URI:  https://example.com
 * License:     GPL-2.0+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: media-profile-avatar
 * Domain Path: /languages
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Add custom profile picture field to user profiles.
 *
 * @param WP_User $user The current WP_User object.
 */
function mpa_add_profile_picture_field( $user ) {
    ?>
    <h3><?php esc_html_e( 'Custom Profile Picture', 'media-profile-avatar' ); ?></h3>
    <table class="form-table">
        <tr>
            <th><label for="mpa_custom_profile_picture"><?php esc_html_e( 'Profile Picture', 'media-profile-avatar' ); ?></label></th>
            <td>
                <?php
                $custom_picture = get_user_meta( $user->ID, 'mpa_custom_profile_picture', true );
                if ( $custom_picture ) {
                    echo '<img src="' . esc_url( $custom_picture ) . '" style="max-width:100px; margin-bottom:10px; display:block;">';
                }
                ?>
                <input type="hidden" name="mpa_custom_profile_picture" id="mpa_custom_profile_picture" value="<?php echo esc_url( $custom_picture ); ?>">
                <button type="button" class="button" id="mpa_upload_profile_picture_button"><?php esc_html_e( 'Select Profile Picture', 'media-profile-avatar' ); ?></button>
                <button type="button" class="button" id="mpa_remove_profile_picture_button" style="display: <?php echo $custom_picture ? 'inline-block' : 'none'; ?>;"><?php esc_html_e( 'Remove Picture', 'media-profile-avatar' ); ?></button>
                <br>
                <span class="description"><?php esc_html_e( 'Select an image from the Media Library.', 'media-profile-avatar' ); ?></span>
            </td>
        </tr>
    </table>
    <?php
    wp_enqueue_media();
    wp_enqueue_script( 'mpa-profile-picture-script', plugin_dir_url( __FILE__ ) . 'assets/js/mpa-profile-picture.js', [ 'jquery' ], '1.0.0', true );
}
add_action( 'show_user_profile', 'mpa_add_profile_picture_field' );
add_action( 'edit_user_profile', 'mpa_add_profile_picture_field' );

/**
 * Save custom profile picture field.
 *
 * @param int $user_id The ID of the user being saved.
 */
function mpa_save_profile_picture_field( $user_id ) {
    if ( ! current_user_can( 'edit_user', $user_id ) ) {
        return false;
    }

    if ( isset( $_POST['mpa_custom_profile_picture'] ) ) {
        update_user_meta( $user_id, 'mpa_custom_profile_picture', esc_url_raw( $_POST['mpa_custom_profile_picture'] ) );
    }
}
add_action( 'personal_options_update', 'mpa_save_profile_picture_field' );
add_action( 'edit_user_profile_update', 'mpa_save_profile_picture_field' );

/**
 * Replace Gravatar with custom profile picture.
 *
 * @param string $avatar      Avatar image HTML.
 * @param mixed  $id_or_email ID, email address, or WP_User object.
 * @param int    $size        Size of the avatar.
 * @param string $default     Default avatar URL.
 * @param string $alt         Alt text for the avatar.
 *
 * @return string Modified avatar HTML.
 */
function mpa_replace_gravatar( $avatar, $id_or_email, $size, $default, $alt ) {
    $user = false;

    if ( is_numeric( $id_or_email ) ) {
        $user = get_user_by( 'id', $id_or_email );
    } elseif ( is_object( $id_or_email ) && ! empty( $id_or_email->user_id ) ) {
        $user = get_user_by( 'id', $id_or_email->user_id );
    } elseif ( is_string( $id_or_email ) ) {
        $user = get_user_by( 'email', $id_or_email );
    }

    if ( $user ) {
        $custom_picture = get_user_meta( $user->ID, 'mpa_custom_profile_picture', true );
        if ( $custom_picture ) {
            $avatar = '<img src="' . esc_url( $custom_picture ) . '" alt="' . esc_attr( $alt ) . '" class="avatar avatar-' . esc_attr( $size ) . ' photo" width="' . esc_attr( $size ) . '" height="' . esc_attr( $size ) . '">';
        }
    }

    return $avatar;
}
add_filter( 'get_avatar', 'mpa_replace_gravatar', 10, 5 );

/**
 * Register JavaScript for the plugin.
 */
function mpa_register_scripts() {
    wp_register_script( 'mpa-profile-picture-script', plugin_dir_url( __FILE__ ) . 'assets/js/mpa-profile-picture.js', [ 'jquery' ], '1.0.0', true );
}
add_action( 'admin_enqueue_scripts', 'mpa_register_scripts' );
