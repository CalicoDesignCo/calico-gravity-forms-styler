<?php
/**
 * Plugin Name: Calico Gravity Forms Styler for Elementor
 * Description: Elementor styling controls for native Gravity Forms, including individual field overrides.
 * Version: 0.1.1
 * Author: Calico
 * Requires at least: 6.5
 * Requires PHP: 7.4
 * Requires Plugins: elementor
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Text Domain: calico-gravity-forms-styler
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'CGFS_VERSION', '0.1.1' );
define( 'CGFS_PATH', plugin_dir_path( __FILE__ ) );
define( 'CGFS_URL', plugin_dir_url( __FILE__ ) );

final class CGFS_Plugin {
    private static $visited = [];
    private static $field_classes = [];

    public static function boot() {
        if ( ! did_action( 'elementor/loaded' ) || ! class_exists( 'GFAPI' ) || ! function_exists( 'gravity_form' ) ) {
            add_action( 'admin_notices', [ __CLASS__, 'notice' ] );
            return;
        }
        add_action( 'elementor/widgets/register', [ __CLASS__, 'register_widget' ] );
        add_action( 'wp_enqueue_scripts', [ __CLASS__, 'assets' ], 20 );
        add_action( 'elementor/preview/enqueue_styles', [ __CLASS__, 'assets' ] );
        add_filter( 'gform_field_css_class', [ __CLASS__, 'field_classes' ], 10, 3 );
    }

    public static function notice() {
        if ( current_user_can( 'activate_plugins' ) ) {
            echo '<div class="notice notice-warning"><p>' . esc_html__( 'Calico Gravity Forms Styler requires active Elementor and Gravity Forms plugins. Elementor Pro is not required.', 'calico-gravity-forms-styler' ) . '</p></div>';
        }
    }

    public static function register_widget( $manager ) {
        require_once CGFS_PATH . 'includes/widget.php';
        $manager->register( new CGFS_Widget() );
    }

    public static function assets() {
        wp_register_style( 'cgfs', CGFS_URL . 'assets/styler.css', [], CGFS_VERSION );
        // Discover forms before wp_head, including forms in referenced saved templates.
        self::enqueue_document( get_queried_object_id() );
    }

    private static function enqueue_document( $id ) {
        $id = absint( $id );
        if ( ! $id || isset( self::$visited[ $id ] ) ) { return; }
        self::$visited[ $id ] = true;
        $data = get_post_meta( $id, '_elementor_data', true );
        if ( is_string( $data ) ) { $data = json_decode( $data, true ); }
        if ( is_array( $data ) ) { self::enqueue_elements( $data ); }
    }

    private static function enqueue_elements( array $elements ) {
        foreach ( $elements as $element ) {
            $settings = $element['settings'] ?? [];
            if ( 'cgfs-gravity-form' === ( $element['widgetType'] ?? '' ) && ! empty( $settings['form_id'] ) ) {
                self::register_field_overrides( absint( $settings['form_id'] ), $settings );
                self::enqueue_form( absint( $settings['form_id'] ), 'yes' === ( $settings['ajax'] ?? 'yes' ) );
            }
            if ( 'template' === ( $element['widgetType'] ?? '' ) && ! empty( $settings['template_id'] ) ) {
                self::enqueue_document( $settings['template_id'] );
            }
            if ( ! empty( $element['elements'] ) ) { self::enqueue_elements( $element['elements'] ); }
        }
    }

    public static function enqueue_form( $id, $ajax ) {
        if ( $id && function_exists( 'gravity_form_enqueue_scripts' ) ) {
            gravity_form_enqueue_scripts( $id, $ajax );
        }
    }

    public static function register_field_overrides( $form_id, array $settings ) {
        foreach ( (array) ( $settings['field_overrides'] ?? [] ) as $row ) {
            $field_id = absint( $row['field_id'] ?? 0 );
            $row_id = sanitize_html_class( $row['_id'] ?? '' );
            if ( $field_id && $row_id ) {
                self::$field_classes[ $form_id ][ $field_id ][ $row_id ] = true;
            }
        }
    }

    public static function field_classes( $classes, $field, $form ) {
        foreach ( self::$field_classes[ (int) $form['id'] ][ (int) $field->id ] ?? [] as $row_id => $enabled ) {
            $classes .= ' elementor-repeater-item-' . $row_id;
        }
        return $classes;
    }
}
add_action( 'plugins_loaded', [ 'CGFS_Plugin', 'boot' ], 20 );
