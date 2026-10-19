<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

use Elementor\Controls_Manager as C;
use Elementor\Group_Control_Typography as Typography;
use Elementor\Group_Control_Border as Border;
use Elementor\Group_Control_Box_Shadow as Shadow;

final class CGFS_Widget extends \Elementor\Widget_Base {
    public function get_name() { return 'cgfs-gravity-form'; }
    public function get_title() { return esc_html__( 'Calico Gravity Forms Styler', 'calico-gravity-forms-styler' ); }
    public function get_icon() { return 'eicon-form-horizontal'; }
    public function get_categories() { return [ 'general' ]; }
    public function get_keywords() { return [ 'gravity', 'form', 'calico', 'fields', 'styler' ]; }
    public function get_style_depends() { return [ 'cgfs' ]; }

    private function scope( $selector ) {
        return implode( ', ', array_map( static function ( $part ) {
            return '{{WRAPPER}} .cgfs ' . trim( $part );
        }, explode( ',', $selector ) ) );
    }

    private function section( $id, $label ) {
        $this->start_controls_section( $id, [ 'label' => $label, 'tab' => C::TAB_STYLE ] );
    }

    private function color( $id, $label, $selector, $property = 'color' ) {
        $this->add_control( $id, [ 'label' => $label, 'type' => C::COLOR,
            'selectors' => [ $selector => $property . ': {{VALUE}};' ] ] );
    }

    private function size( $id, $label, $selector, $property, $units = [ 'px', 'em', 'rem' ], $min = 0, $max = 150 ) {
        $this->add_responsive_control( $id, [ 'label' => $label, 'type' => C::SLIDER, 'size_units' => $units,
            'range' => [ 'px' => [ 'min' => $min, 'max' => $max ], '%' => [ 'min' => 0, 'max' => 100 ] ],
            'selectors' => [ $selector => $property . ': {{SIZE}}{{UNIT}};' ] ] );
    }

    private function dimensions( $id, $label, $selector, $property, $units = [ 'px', 'em', '%' ] ) {
        $this->add_responsive_control( $id, [ 'label' => $label, 'type' => C::DIMENSIONS, 'size_units' => $units,
            'selectors' => [ $selector => $property . ': {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );
    }

    private function text_controls( $id, $selector ) {
        $this->add_group_control( Typography::get_type(), [ 'name' => $id . '_type', 'selector' => $selector ] );
        $this->color( $id . '_color', 'Text color', $selector );
        $this->add_responsive_control( $id . '_align', [ 'label' => 'Text alignment', 'type' => C::SELECT,
            'options' => [ '' => 'Default', 'start' => 'Start', 'center' => 'Center', 'end' => 'End' ],
            'selectors' => [ $selector => 'text-align: {{VALUE}};' ] ] );
    }

    private function box_controls( $id, $selector ) {
        $this->color( $id . '_bg', 'Background', $selector, 'background-color' );
        $this->add_group_control( Border::get_type(), [ 'name' => $id . '_border', 'selector' => $selector ] );
        $this->dimensions( $id . '_radius', 'Border radius', $selector, 'border-radius' );
        $this->dimensions( $id . '_padding', 'Padding', $selector, 'padding' );
        $this->dimensions( $id . '_margin', 'Margin', $selector, 'margin' );
        $this->add_group_control( Shadow::get_type(), [ 'name' => $id . '_shadow', 'selector' => $selector ] );
    }

    private function part( $id, $label, $selectors, $box = false ) {
        $this->section( $id . '_section', $label );
        $selector = $this->scope( $selectors );
        $this->text_controls( $id, $selector );
        if ( $box ) { $this->box_controls( $id, $selector ); }
        else { $this->dimensions( $id . '_margin', 'Margin', $selector, 'margin' ); }
        $this->end_controls_section();
    }

    private function input_selectors() {
        return '.gfield input:not([type=checkbox]):not([type=radio]):not([type=hidden]):not([type=submit]):not([type=button]):not([type=reset]):not([type=image]):not([type=file]), .gfield select, .gfield textarea';
    }

    protected function register_controls() {
        $this->start_controls_section( 'form_section', [ 'label' => 'Gravity Form' ] );
        $forms = [ '' => 'Select a form' ];
        // Form titles are editor configuration; never expose the form list on public requests.
        if ( current_user_can( 'edit_posts' ) && class_exists( 'GFAPI' ) ) {
            foreach ( GFAPI::get_forms( true, false ) as $form ) {
                $forms[ (string) $form['id'] ] = $form['title'] . ' (ID ' . $form['id'] . ')';
            }
        }
        $this->add_control( 'form_id', [ 'label' => 'Form', 'type' => C::SELECT, 'options' => $forms ] );
        $this->add_control( 'show_title', [ 'label' => 'Show form title', 'type' => C::SWITCHER, 'default' => 'yes' ] );
        $this->add_control( 'show_description', [ 'label' => 'Show form description', 'type' => C::SWITCHER, 'default' => 'yes' ] );
        $this->add_control( 'ajax', [ 'label' => 'AJAX submission', 'type' => C::SWITCHER, 'default' => 'yes' ] );
        $this->add_control( 'form_theme', [ 'label' => 'Form theme', 'type' => C::SELECT, 'default' => '',
            'options' => [ '' => 'Use Gravity Forms default', 'orbital' => 'Orbital', 'gravity-theme' => 'Gravity Forms 2.5' ] ] );
        $this->add_control( 'help', [ 'type' => C::RAW_HTML, 'raw' => 'Build fields and conditional logic in Gravity Forms. Style the form here. Payment embeds, CAPTCHA, browser-native dropdown menus and third-party add-ons have styling limits. Avoid embedding the same form twice on one page.' ] );
        $this->end_controls_section();

        $root = '{{WRAPPER}} .cgfs';
        $wrapper = $this->scope( '.gform_wrapper' );
        $this->section( 'layout_section', 'Form container & layout' );
        $this->box_controls( 'container', $root );
        $this->size( 'form_width', 'Maximum width', $root, 'max-width', [ 'px', '%', 'vw' ], 0, 1600 );
        $this->add_responsive_control( 'form_align', [ 'label' => 'Form alignment', 'type' => C::SELECT,
            'options' => [ '' => 'Default', 'start' => 'Start', 'center' => 'Center', 'end' => 'End' ],
            'selectors_dictionary' => [ 'start' => '0; margin-inline-end: auto', 'center' => 'auto; margin-inline-end: auto', 'end' => 'auto; margin-inline-end: 0' ],
            'selectors' => [ $root => 'margin-inline-start: {{VALUE}};' ] ] );
        $this->size( 'row_gap', 'Row gap', $this->scope( '.gform_fields' ), 'row-gap' );
        $this->size( 'column_gap', 'Column gap', $this->scope( '.gform_fields' ), 'column-gap' );
        $this->size( 'inner_gap', 'Sub-input gap', $wrapper, '--gf-field-gap-x' );
        $this->size( 'footer_gap', 'Footer top spacing', $this->scope( '.gform_footer, .gform_page_footer' ), 'margin-top' );
        $this->add_control( 'layout_note', [ 'type' => C::RAW_HTML, 'raw' => 'Field columns and order follow the Gravity Forms editor. Spacing controls target modern Gravity Forms markup; legacy list layouts may differ.' ] );
        $this->end_controls_section();

        $this->part( 'title', 'Form title', '.gform_title' );
        $this->part( 'description', 'Form description', '.gform_description' );
        $this->part( 'labels', 'Field labels', '.gfield_label, .gform-field-label--type-inline:not(.gform-field-label--type-sub)' );
        $this->part( 'sublabels', 'Sub-labels', '.ginput_complex label, .gform-field-label--type-sub' );
        $this->part( 'required', 'Required indicators & legend', '.gfield_required, .gform_required_legend' );
        $this->part( 'field_description', 'Field descriptions & counters', '.gfield_description:not(.validation_message), .ginput_counter' );

        $inputs = $this->input_selectors();
        $this->section( 'inputs_section', 'Inputs, selects & textareas' );
        $this->text_controls( 'inputs', $this->scope( $inputs ) );
        $this->box_controls( 'inputs', $this->scope( $inputs ) );
        $this->size( 'input_height', 'Minimum height', $this->scope( $inputs ), 'min-height' );
        $this->size( 'textarea_height', 'Textarea minimum height', $this->scope( '.gfield textarea' ), 'min-height', [ 'px', 'vh' ], 0, 800 );
        $placeholders = implode( ', ', array_map( static function ( $s ) { return trim( $s ) . '::placeholder'; }, explode( ',', $inputs ) ) );
        $this->color( 'placeholder_color', 'Placeholder color', $this->scope( $placeholders ) );
        $this->color( 'orbital_icon_color', 'Orbital select / date icon color', $wrapper, '--gf-ctrl-icon-color' );
        $this->start_controls_tabs( 'input_states' );
        foreach ( [ 'hover' => ':hover', 'focus' => ':focus', 'disabled' => ':disabled', 'error' => '' ] as $state => $pseudo ) {
            $this->start_controls_tab( 'input_' . $state . '_tab', [ 'label' => ucfirst( $state ) ] );
            $targets = implode( ', ', array_map( static function ( $s ) use ( $pseudo, $state ) {
                return ( 'error' === $state ? '.gfield_error ' : '' ) . trim( $s ) . $pseudo;
            }, explode( ',', $inputs ) ) );
            $target = $this->scope( $targets );
            $this->color( 'input_' . $state . '_color', 'Text color', $target );
            $this->color( 'input_' . $state . '_bg', 'Background', $target, 'background-color' );
            $this->color( 'input_' . $state . '_border', 'Border color', $target, 'border-color' );
            if ( 'focus' === $state ) {
                $this->color( 'input_focus_ring', 'Focus outline color', $target, 'outline-color' );
                $this->size( 'input_focus_ring_width', 'Focus outline width', $target, 'outline-width', [ 'px' ], 0, 10 );
            }
            $this->end_controls_tab();
        }
        $this->end_controls_tabs();
        $this->end_controls_section();

        $this->section( 'choice_section', 'Checkboxes & radio buttons' );
        $this->text_controls( 'choice_labels', $this->scope( '.gfield_checkbox label, .gfield_radio label, .ginput_container_consent label' ) );
        $choice = $this->scope( 'input[type=checkbox], input[type=radio]' );
        $this->color( 'choice_accent', 'Checked color', $choice, 'accent-color' );
        $this->color( 'choice_orbital_accent', 'Orbital checked background', $wrapper, '--gf-ctrl-choice-check-color' );
        $this->color( 'choice_orbital_border', 'Border color', $choice, 'border-color' );
        $this->size( 'choice_size', 'Control size', $choice, 'width', [ 'px', 'em' ], 0, 60 );
        $this->size( 'choice_height', 'Control height', $choice, 'height', [ 'px', 'em' ], 0, 60 );
        $this->size( 'choice_gap', 'Choice row gap', $this->scope( '.gfield_checkbox, .gfield_radio' ), 'row-gap' );
        $this->size( 'choice_label_gap', 'Label gap', $this->scope( '.gchoice, .ginput_container_consent' ), 'column-gap' );
        $this->end_controls_section();

        $this->part( 'section', 'Section headings', '.gsection_title' );
        $this->part( 'section_description', 'Section descriptions', '.gsection_description' );
        $this->section( 'section_container_section', 'Section container & divider' );
        $this->box_controls( 'section_container', $this->scope( '.gsection' ) );
        $this->end_controls_section();
        $this->part( 'html', 'HTML fields', '.gfield_html', true );
        $this->part( 'upload', 'File upload drop area', '.gform_drop_area', true );
        $this->part( 'upload_rules', 'Upload rules & file list', '.gform_fileupload_rules, .ginput_preview, .gform_fileupload_multifile .gfield_description' );
        $this->part( 'list', 'List fields / table cells', '.gfield_list_group_item, .gfield_list th', true );
        $this->part( 'price', 'Product prices & totals', '.ginput_product_price, .ginput_total, .ginput_shipping_price' );
        $this->part( 'validation_summary', 'Validation summary', '.gform_validation_errors', true );
        $this->part( 'validation_heading', 'Validation summary heading', '.gform_validation_errors h2' );
        $this->part( 'validation', 'Field validation messages', '.gfield_validation_message, .validation_message', true );
        $this->part( 'error_field', 'Invalid field container', '.gfield_error', true );
        $this->part( 'confirmation', 'Confirmation message', '.gform_confirmation_message', true );

        foreach ( [
            'submit' => [ 'Submit button', '.gform_button:not(.gform_next_button):not(.gform_previous_button)' ],
            'next' => [ 'Next-page button', '.gform_next_button' ],
            'previous' => [ 'Previous-page button', '.gform_previous_button' ],
            'upload_button' => [ 'Upload button', '.gform_button_select_files, input[type=file]::file-selector-button' ],
            'save' => [ 'Save & continue link', '.gform_save_link' ],
        ] as $id => $part ) {
            $this->section( $id . '_section', $part[0] );
            $selector = $this->scope( $part[1] );
            $this->text_controls( $id, $selector );
            $this->box_controls( $id, $selector );
            $this->size( $id . '_width', 'Width', $selector, 'width', [ 'px', '%' ], 0, 600 );
            $this->size( $id . '_height', 'Minimum height', $selector, 'min-height', [ 'px' ], 0, 120 );
            foreach ( [ 'hover' => ':hover', 'focus' => ':focus-visible', 'disabled' => ':disabled' ] as $state => $pseudo ) {
                // Pseudo-elements must follow pseudo-classes.
                $targets = implode( ', ', array_map( static function ( $s ) use ( $pseudo ) {
                    return str_replace( '::file-selector-button', '', trim( $s ) ) . $pseudo . ( false !== strpos( $s, '::file-selector-button' ) ? '::file-selector-button' : '' );
                }, explode( ',', $part[1] ) ) );
                $this->add_control( $id . '_' . $state . '_heading', [ 'type' => C::HEADING, 'label' => ucfirst( $state ), 'separator' => 'before' ] );
                $this->color( $id . '_' . $state . '_color', 'Text color', $this->scope( $targets ) );
                $this->color( $id . '_' . $state . '_bg', 'Background', $this->scope( $targets ), 'background-color' );
                $this->color( $id . '_' . $state . '_border', 'Border color', $this->scope( $targets ), 'border-color' );
            }
            $this->end_controls_section();
        }

        $this->part( 'progress_title', 'Progress heading', '.gf_progressbar_title' );
        $this->section( 'progress_section', 'Progress bar' );
        $this->box_controls( 'progress_track', $this->scope( '.gf_progressbar' ) );
        $this->color( 'progress_fill', 'Progress fill', $this->scope( '.gf_progressbar_percentage' ), 'background-color' );
        $this->text_controls( 'progress_text', $this->scope( '.gf_progressbar_percentage span' ) );
        $this->size( 'progress_height', 'Bar height', $this->scope( '.gf_progressbar, .gf_progressbar_percentage' ), 'height', [ 'px' ], 0, 100 );
        $this->end_controls_section();
        $this->part( 'steps', 'Page steps', '.gf_step', true );
        $this->part( 'steps_active', 'Active page step', '.gf_step_active', true );
        $this->part( 'steps_complete', 'Completed page step', '.gf_step_completed', true );
        $this->part( 'step_number', 'Page step numbers', '.gf_step_number', true );

        $this->section( 'overrides_section', 'Individual field overrides' );
        $this->add_control( 'override_help', [ 'type' => C::RAW_HTML, 'raw' => 'Enter the field ID shown in the Gravity Forms editor. Overrides affect only this widget. Sub-inputs inherit the parent field override. Hidden fields and conditional visibility are preserved.' ] );
        $r = new \Elementor\Repeater();
        $r->add_control( 'field_id', [ 'label' => 'Gravity Forms field ID', 'type' => C::NUMBER, 'min' => 1, 'step' => 1 ] );
        $field = '{{WRAPPER}} .cgfs .gfield{{CURRENT_ITEM}}';
        $r->add_control( 'background', [ 'label' => 'Field background', 'type' => C::COLOR, 'selectors' => [ $field => 'background-color: {{VALUE}};' ] ] );
        $r->add_responsive_control( 'padding', [ 'label' => 'Field padding', 'type' => C::DIMENSIONS, 'size_units' => [ 'px', 'em', '%' ],
            'selectors' => [ $field => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );
        $r->add_responsive_control( 'margin', [ 'label' => 'Field margin', 'type' => C::DIMENSIONS, 'size_units' => [ 'px', 'em', '%' ],
            'selectors' => [ $field => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );
        $r->add_control( 'label_color', [ 'label' => 'Label color', 'type' => C::COLOR, 'selectors' => [ $field . ' .gfield_label' => 'color: {{VALUE}};' ] ] );
        $r->add_group_control( Typography::get_type(), [ 'name' => 'label_type', 'selector' => $field . ' .gfield_label' ] );
        $field_inputs = implode( ', ', array_map( static function ( $s ) use ( $field ) {
            return $field . ' ' . preg_replace( '/^\.gfield /', '', trim( $s ) );
        }, explode( ',', $inputs ) ) );
        $r->add_control( 'input_color', [ 'label' => 'Input text color', 'type' => C::COLOR, 'selectors' => [ $field_inputs => 'color: {{VALUE}};' ] ] );
        $r->add_control( 'input_background', [ 'label' => 'Input background', 'type' => C::COLOR, 'selectors' => [ $field_inputs => 'background-color: {{VALUE}};' ] ] );
        $r->add_group_control( Border::get_type(), [ 'name' => 'input_border', 'selector' => $field_inputs ] );
        $r->add_group_control( Typography::get_type(), [ 'name' => 'input_type', 'selector' => $field_inputs ] );
        $this->add_control( 'field_overrides', [ 'label' => 'Field overrides', 'type' => C::REPEATER, 'fields' => $r->get_controls(),
            'title_field' => 'Field {{{ field_id }}}', 'prevent_empty' => false ] );
        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $id = absint( $settings['form_id'] ?? 0 );
        $editor = \Elementor\Plugin::instance()->editor->is_edit_mode();
        if ( ! $id || ! class_exists( 'GFAPI' ) || ! function_exists( 'gravity_form' ) ) {
            if ( $editor ) { echo '<div class="cgfs-editor-notice">' . esc_html__( 'Select an active Gravity Form to preview its styling.', 'calico-gravity-forms-styler' ) . '</div>'; }
            return;
        }
        $form = GFAPI::get_form( $id );
        if ( ! $form || empty( $form['is_active'] ) || ! empty( $form['is_trash'] ) ) {
            if ( $editor ) { echo '<div class="cgfs-editor-notice">' . esc_html__( 'This form is unavailable, inactive, or in Trash.', 'calico-gravity-forms-styler' ) . '</div>'; }
            return;
        }
        $ajax = 'yes' === ( $settings['ajax'] ?? 'yes' );
        CGFS_Plugin::register_field_overrides( $id, $settings );
        CGFS_Plugin::enqueue_form( $id, $ajax );
        $theme = in_array( $settings['form_theme'] ?? '', [ 'orbital', 'gravity-theme' ], true ) ? $settings['form_theme'] : null;
        // Render first so theme-specific assets requested by Gravity Forms are also queued.
        $markup = gravity_form( $id, 'yes' === ( $settings['show_title'] ?? '' ), 'yes' === ( $settings['show_description'] ?? '' ), false, null, $ajax, 0, false, $theme );
        // Late-loaded templates/popups may be discovered after wp_head. Print queued styles once.
        if ( did_action( 'wp_head' ) && ! wp_doing_ajax() ) {
            $styles = wp_styles();
            wp_print_styles( array_diff( $styles->queue, $styles->done ) );
        }
        echo '<div class="cgfs">';
        // Keep native rendering, validation, nonce, accessibility, conditional logic and submission intact.
        echo $markup; // Native Gravity Forms HTML; escaping would break form markup and initialization.
        echo '</div>';
    }
}
