<?php

// Takes care of various aspects of the 'answer' post type
class QA_Answers {

    /**
     * Holds the parent question object
     *
     * @var object
     */
    var $question_slug;

    function __construct() {
        add_action('init', array( &$this, 'init' ));
        add_action('request', array( &$this, 'request' ));
        add_filter('redirect_canonical', array( &$this, 'redirect_canonical' ), 10, 2);
        add_filter('post_type_link', array( &$this, 'answer_permalink' ), 10, 3);

        add_action('admin_init', array( &$this, 'admin_init' ));
        add_action('admin_enqueue_scripts', array( &$this, 'admin_enqueue_scripts' ));
    }

    function init() {
        $args = array(
            'show_ui' => true,
            'show_in_menu' => 'edit.php?post_type=question',
            'rewrite' => false,
            'capability_type' => 'answer',
            'capabilities' => array(
                'read' => 'read_answers',
                'edit_posts' => 'edit_published_answers',
                'delete_posts' => 'delete_published_answers',
            ),
            'map_meta_cap' => true,
            'supports' => array( 'title', 'editor', 'author', 'revisions' ),
            'labels' => array(
                'name' => __('Antworten', QA_TEXTDOMAIN),
                'singular_name' => __('Antwort', QA_TEXTDOMAIN),
                'add_new' => __('Neue Antwort', QA_TEXTDOMAIN),
                'add_new_item' => __('Neue Antwort hinzufügen', QA_TEXTDOMAIN),
                'edit_item' => __('Antwort bearbeiten', QA_TEXTDOMAIN),
                'new_item' => __('Neue Antwort', QA_TEXTDOMAIN),
                'view_item' => __('Antwort anzeigen', QA_TEXTDOMAIN),
                'search_items' => __('Antworten suchen', QA_TEXTDOMAIN),
                'not_found' => __('Keine Antworten gefunden', QA_TEXTDOMAIN),
                'not_found_in_trash' => __('Keine Antworten im Papierkorb gefunden', QA_TEXTDOMAIN),
            )
        );
        $args = apply_filters('qa_answer_register_post_type_args', $args);
        register_post_type('answer', $args);
    }

    function admin_init() {
        wp_register_style('qa-answers-remove-add', QA_PLUGIN_URL . 'css/qa-answers-remove-add.css');
    }

    function admin_enqueue_scripts( $hook ) {
        global $post;
        if ( 'edit.php' != $hook || (isset($post) && $post->post_type != 'answer') )
            return;
        wp_enqueue_style('qa-answers-remove-add');
    }

    // Handle answer feed
    function request( $args ) {
        if ( isset($args['question']) && isset($args['feed']) ) {
            $question = get_posts(array(
                'post_type' => 'question',
                'question' => $args['question'],
            ));

            if ( !$question )
                return array( 'error' => 404 );

            $this->question = reset($question);

            $args['post_type'] = 'answer';
            $args['post_parent'] = $this->question->ID;

            unset($args['question'], $args['name']);

            add_filter('wp_title_rss', array( &$this, 'wp_title_rss' ), 10, 2);
            add_filter('the_title_rss', array( &$this, 'answer_title_rss' ));
        }

        return $args;
    }

    // Prevent redirect on paginated answers
    function redirect_canonical( $redirect_url, $requested_url ) {
        if ( is_singular('question') && is_paged() )
            return false;

        return $redirect_url;
    }

    function wp_title_rss() {
        $sep = '&#187;'; // http://core.trac.wordpress.org/ticket/16983
        return " $sep " . sprintf(__('Antworten auf "%s"', QA_TEXTDOMAIN), $this->question->post_title);
    }

    function answer_title_rss() {
        return sprintf(__('Von: %s', QA_TEXTDOMAIN), get_the_author());
    }

    function answer_permalink( $permalink, $post, $leavename ) {

        if ( 'answer' == $post->post_type ) {
            $question_id = $post->post_parent;
            $question = get_post($question_id);
            if ( $question->post_type == 'question' ) {
                $url = get_permalink($question_id);
                $post_link = $url . '#answer-' . $post->ID;
                return $post_link;
            } else {
                return $permalink;
            }
        } else {
            return $permalink;
        }
    }

}

$_qa_answers = new QA_Answers();

