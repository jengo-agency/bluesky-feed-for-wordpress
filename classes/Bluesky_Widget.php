<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Class Bluesky_Widget
 *
 * A widget to display recent Bluesky posts.
 *
 * @package Bluesky_Feed_For_WordPress
 * @since   1.0.0
 */
class Bluesky_Widget extends WP_Widget {
    /**
     * Constructor.
     *
     * Initializes the widget with ID, name, and description.
     *
     * @since  1.0.0
     * @return void
     */
    public function __construct() {
        parent::__construct(
            'bluesky_widget',
            esc_html__( 'Bluesky Feed Widget', 'bluesky-feed' ),
            [
                'description' => esc_html__( 'Display your recent Bluesky posts.', 'bluesky-feed' ),
            ]
        );
    }

    /**
     * Outputs the widget content on the frontend.
     *
     * @param array $args     Display arguments including 'before_widget' and 'after_widget'.
     * @param array $instance Settings for the current widget instance.
     *
     * @since  1.0.0
     * @return void
     */
    public function widget( $args, $instance ) {
        // Get title from widget instance or use default
        $title = ! empty( $instance['title'] ) ? $instance['title'] : '';
        $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
        
        // Get settings from plugin options.
        $settings = [
            'username'        => esc_attr( get_option( 'bluesky_username', '' ) ),
            'postCount'       => absint( get_option( 'bluesky_post_count', 5 ) ),
            'includePins'     => absint( get_option( 'bluesky_include_pins', 1 ) ),
            'includeLink'     => absint( get_option( 'bluesky_include_link', 1 ) ),
            'theme'           => esc_attr( get_option( 'bluesky_theme', 'light' ) ),
            'scrollableWidget' => absint( get_option( 'bluesky_scrollable_widget', 0 ) ),
            'scrollableHeight' => absint( get_option( 'bluesky_scrollable_height', 400 ) ),
        ];

        // Determine if widget should be scrollable
        $scrollable_class = $settings['scrollableWidget'] ? ' scrollable' : '';
        $scrollable_style = $settings['scrollableWidget'] ? sprintf( ' style="height: %dpx; overflow-y: auto;"', $settings['scrollableHeight'] ) : '';

        // Output widget content.
        echo $args['before_widget'];
        
        // Display the title if it's set
        if ( ! empty( $title ) ) {
            echo $args['before_title'] . esc_html( $title ) . $args['after_title'];
        }
        
        echo sprintf(
            '<div class="bluesky-feed-widget%s"%s data-settings="%s"></div>',
            esc_attr( $scrollable_class ),
            $scrollable_style,
            esc_attr( wp_json_encode( $settings ) )
        );
        echo $args['after_widget'];
    }

    /**
     * Outputs the widget settings form in the admin area.
     *
     * @param array $instance Current settings for the widget instance.
     *
     * @since  1.0.0
     * @return void
     */
    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : '';
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'bluesky-feed' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p><?php esc_html_e( 'Other settings can be configured in the plugin options page.', 'bluesky-feed' ); ?></p>
        <?php
    }

    /**
     * Handles updating settings for the current widget instance.
     *
     * @param array $new_instance New settings for this instance as input by the user via form().
     * @param array $old_instance Old settings for this instance.
     *
     * @since  1.0.0
     * @return array Updated settings.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = [];
        $instance['title'] = ! empty( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '';
        
        return $instance;
    }
}
