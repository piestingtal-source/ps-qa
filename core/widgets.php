<?php

class QA_Widget_Helper extends WP_Widget {
	var $default_instance = array();

	function parse_instance( $instance ) {
		return wp_parse_args( $instance, $this->default_instance );
	}

	function widget( $args, $instance ) {
		$instance = $this->parse_instance( $instance );

		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );

		echo $before_widget;

		if ( $title ) {
			echo $before_title . $title . $after_title;
		}

		$this->content( $instance );

		echo $after_widget;
	}

	function title_field( $title ) {
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Widgettitel:', QA_TEXTDOMAIN ); ?></label>
			<?php
			echo _qa_html( 'input', array(
				'class' => 'widefat',
				'type'  => 'text',
				'id'    => $this->get_field_id( 'title' ),
				'name'  => $this->get_field_name( 'title' ),
				'value' => $title
			) );
			?>
		</p>
		<?php
	}
}

class QA_Widget_Questions extends QA_Widget_Helper {

	var $default_instance = array(
		'title'      => '',
		'which'      => 'recent',
		'tags'       => '',
		'categories' => '',
		'number'     => 5
	);

	function __construct() {
		$widget_ops = array( 'description' => __( 'Liste der Fragen, die aus den letzten, beliebtesten und unbeantworteten Fragen ausgewählt werden können', QA_TEXTDOMAIN ) );
		parent::__construct( 'questions', __( 'Q&A: Fragen', QA_TEXTDOMAIN ), $widget_ops );

	}

	function content( $instance ) {
		global $post, $_qa_core;

		// Check if action is there
		if ( has_action( 'pre_get_posts', array( $_qa_core, 'questions_per_page' ) ) ) {
			$had_action = true;
			// Remove posts per page setting so that widget shows the correct number of items
			remove_action( 'pre_get_posts', array( $_qa_core, 'questions_per_page' ) );
		} else {
			$had_action = false;
		}

		extract( $instance );

		if ( ! isset( $number ) ) {
			$number = 5;
		}

		switch ( $which ) {
			case 'recent':
				$args = array();
				break;
			case 'popular':
				$args = array( 'meta_key' => '_answer_count', 'orderby' => 'meta_value_num' );
				break;
			case 'unanswered':
				$args = array( 'qa_unanswered' => true );
				break;
		}

		$args = array_merge( $args, array(
			'post_type'        => 'question',
			'posts_per_page'   => $number,
			'suppress_filters' => false
		) );

		$args = apply_filters( 'qa_question_widget_args', $args, $instance );

		$tag_ids = array();
		if ( isset( $instance['tags'] ) && ! empty( $instance['tags'] ) ) {
			$args['question_tag'] = $instance['tags'];
		}

		$cat_ids = array();
		if ( isset( $instance['categories'] ) && ! empty( $instance['categories'] ) ) {
			$args['question_category'] = $instance['categories'];
		}


		echo '<ul>';
		foreach ( get_posts( $args ) as $post ) {
			setup_postdata( $post );

			echo _qa_html( 'li', _qa_html( 'a', array( 'href' => get_permalink() ), get_the_title() ) );
		}
		echo '</ul>';

		wp_reset_postdata();

		// Add the action back
		if ( $had_action ) {
			add_action( 'pre_get_posts', array( $_qa_core, 'questions_per_page' ) );
		}
	}

	function form( $instance ) {
		$instance = $this->parse_instance( $instance );
		$this->title_field( $instance['title'] );
		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'which' ); ?>"><?php _e( 'Welche:', QA_TEXTDOMAIN ); ?></label>
			<select id="<?php echo $this->get_field_id( 'which' ); ?>"
			        name="<?php echo $this->get_field_name( 'which' ); ?>">
				<?php
				$options = array(
					'recent'     => __( 'Kürzlich', QA_TEXTDOMAIN ),
					'popular'    => __( 'Beliebt', QA_TEXTDOMAIN ),
					'unanswered' => __( 'Unbeantwortet', QA_TEXTDOMAIN ),
				);

				foreach ( $options as $value => $title ) {
					$attr = compact( 'value' );
					if ( $instance['which'] == $value ) {
						$attr['selected'] = 'selected';
					}
					echo _qa_html( 'option', $attr, $title );
				}
				?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'tags' ); ?>"><?php _e( 'Tags:', QA_TEXTDOMAIN ); ?></label>
			<?php
			echo _qa_html( 'input', array(
				'type'  => 'text',
				'size'  => 30,
				'id'    => $this->get_field_id( 'tags' ),
				'name'  => $this->get_field_name( 'tags' ),
				'value' => $instance['tags']
			) );
			?>
		<div class="instructions"><?php _e( 'Komma getrennt', QA_TEXTDOMAIN ); ?></div>
		</p>
		<p>
			<label
				for="<?php echo $this->get_field_id( 'categories' ); ?>"><?php _e( 'Kategorien:', QA_TEXTDOMAIN ); ?></label>
			<?php
			echo _qa_html( 'input', array(
				'type'  => 'text',
				'size'  => 30,
				'id'    => $this->get_field_id( 'categories' ),
				'name'  => $this->get_field_name( 'categories' ),
				'value' => $instance['categories']
			) );
			?>
		<div class="instructions"><?php _e( 'Komma getrennt', QA_TEXTDOMAIN ); ?></div>
		</p>
		<p>
			<label
				for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Anzahl der zu zeigenden Fragen:', QA_TEXTDOMAIN ); ?></label>
			<?php
			echo _qa_html( 'input', array(
				'type'  => 'text',
				'size'  => 2,
				'id'    => $this->get_field_id( 'number' ),
				'name'  => $this->get_field_name( 'number' ),
				'value' => $instance['number']
			) );
			?>
		</p>
		<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance               = $old_instance;
		$new_instance           = $this->parse_instance( $new_instance );
		$instance['title']      = strip_tags( $new_instance['title'] );
		$instance['which']      = $new_instance['which'];
		$instance['tags']       = $new_instance['tags'];
		$instance['categories'] = $new_instance['categories'];
		$instance['number']     = (int) $new_instance['number'];

		return $instance;
	}
}


class QA_Widget_Tags extends QA_Widget_Helper {

	var $default_instance = array(
		'title' => '',
	);

	function __construct() {
		$widget_ops = array( 'description' => __( 'Die beliebtesten Frage-Tags im Cloud-Format', QA_TEXTDOMAIN ) );
		parent::__construct( 'question_tags', __( 'Q&A: Frage-Tags', QA_TEXTDOMAIN ), $widget_ops );
	}

	function widget( $args, $instance ) {
		extract( $args );

		if ( ! empty( $instance['title'] ) ) {
			$title = $instance['title'];
		} else {
			$tax   = get_taxonomy( 'question_tag' );
			$title = $tax->labels->name;
		}
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		echo $before_widget;

		if ( $title ) {
			echo $before_title . $title . $after_title;
		}

		echo '<div class="question-tagcloud">';
		wp_tag_cloud( array(
			'taxonomy'                  => 'question_tag',
			'topic_count_text_callback' => array( $this, 'count_text_callback' ),
		) );
		echo "</div>\n";
		echo $after_widget;
	}

	function count_text_callback( $count ) {
		return sprintf( _n( '1 Frage', '%s Fragen', $count ), number_format_i18n( $count ) );
	}

	function form( $instance ) {
		$instance = $this->parse_instance( $instance );
		$this->title_field( $instance['title'] );
	}

	function update( $new_instance, $old_instance ) {
		$instance          = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}
}


class QA_Widget_Categories extends QA_Widget_Helper {

	var $default_instance = array(
		'title' => '',
		'count' => false
	);

	function __construct() {
		$widget_ops = array( 'description' => __( 'Eine Liste der Fragenkategorien', QA_TEXTDOMAIN ) );
		parent::__construct( 'question_categories', __( 'Q&A: Fragenkategorien', QA_TEXTDOMAIN ), $widget_ops );
	}

	function widget( $args, $instance ) {
		extract( $args );

		if ( ! empty( $instance['title'] ) ) {
			$title = $instance['title'];
		} else {
			$tax   = get_taxonomy( 'question_category' );
			$title = $tax->labels->name;
		}
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		echo $before_widget;
		if ( $title ) {
			echo $before_title . $title . $after_title;
		}

		$cat_args = array(
			'taxonomy'     => 'question_category',
			'orderby'      => 'name',
			'hierarchical' => true,
			'show_count'   => $instance['count'],
			'title_li'     => ''
		);

		$cat_args = apply_filters( 'qa_category_widget_args', $cat_args, $instance );

		echo '<ul>';
		wp_list_categories( $cat_args );
		echo '</ul>';

		echo $after_widget;
	}

	function form( $instance ) {
		$instance = $this->parse_instance( $instance );
		$this->title_field( $instance['title'] );
		?>
		<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'count' ); ?>"
		       name="<?php echo $this->get_field_name( 'count' ); ?>"<?php checked( $instance['count'] ); ?> />
		<label
			for="<?php echo $this->get_field_id( 'count' ); ?>"><?php _e( 'Fragenzähler anzeigen', QA_TEXTDOMAIN ); ?></label>
		<br/>
		<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance          = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['count'] = ! empty( $new_instance['count'] );

		return $instance;
	}
}

class QA_Widget_Reputation extends QA_Widget_Helper {

	var $default_instance = array(
		'title'  => '',
		'number' => 5
	);

	function __construct() {
		$widget_ops = array( 'description' => __( 'Eine Liste der Benutzer mit den höchsten Reputationspunkten', QA_TEXTDOMAIN ) );
		parent::__construct ( 'question_reputation', __( 'Q&A: Benutzer mit dem höchsten Ansehen', QA_TEXTDOMAIN ), $widget_ops );
	}

	function widget( $args, $instance ) {
		extract( $args );
		$instance = $this->parse_instance( $instance );

		if ( ! empty( $instance['title'] ) ) {
			$title = $instance['title'];
		}
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		echo $before_widget;
		if ( $title ) {
			echo $before_title . $title . $after_title;
		}

		global $wpdb;
		$results = $wpdb->get_results( "SELECT * FROM " . $wpdb->usermeta . " WHERE meta_key='_qa_rep' AND meta_value > 0  ORDER BY CONVERT(meta_value, SIGNED) DESC LIMIT " . (int) $instance['number'] . " " );

		if ( $results ) {
			echo '<ul>';
			do_action( 'qa_reputation_widget_before', $results );
			foreach ( $results as $result ) {
				echo '<li class="qa-user-item">';
				the_qa_user_link( $result->user_id );
				echo " (" . $result->meta_value . ")";
				echo '</li>';
			}
			do_action( 'qa_reputation_widget_after', $results );
			echo '</ul>';
		}

		echo $after_widget;
	}

	function form( $instance ) {
		$instance = $this->parse_instance( $instance );
		$this->title_field( $instance['title'] );
		?>
		<label for="<?php echo $this->get_field_id( 'number' ); ?>">
			<?php _e( 'Anzahl der anzuzeigenden Benutzer:', QA_TEXTDOMAIN ); ?></label>
		<?php
		echo _qa_html( 'input', array(
			'type'  => 'text',
			'size'  => 2,
			'id'    => $this->get_field_id( 'number' ),
			'name'  => $this->get_field_name( 'number' ),
			'value' => $instance['number']
		) );
		?>
		<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance          = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['count'] = ! empty( $new_instance['count'] );

		return $instance;
	}
}

function qa_widgets_init() {
	if ( ! is_blog_installed() ) {
		return;
	}

	register_widget( 'QA_Widget_Questions' );

	register_widget( 'QA_Widget_Tags' );

	register_widget( 'QA_Widget_Categories' );

	register_widget( 'QA_Widget_Reputation' );
}

add_action( 'widgets_init', 'qa_widgets_init' );

