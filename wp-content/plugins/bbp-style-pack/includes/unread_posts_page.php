<?php

/**
 * Display Unread Topics Index
 *
 * This function generates an index page that lists all unread topics
 * the logged-in user has access to. It includes individual "Mark as Read"
 * buttons for each topic, as well as a "Mark All as Read" button that
 * comes with a JavaScript confirmation popup. When a topic is marked as 
 * read, the page refreshes and displays a confirmation message with a 
 * link to the marked topic.
 *
 * Usage:
 * - Call this function in the appropriate template or use a shortcode [bsp-display-unreads-index]
 *   to display the unread topics index on a page.
 * - Ensure the user is logged in to access the unread topics.
 *
 * @return void
 */

function bsp_display_unreads_index() {
    if (is_user_logged_in()) {
		// Start an output buffer
		ob_start();
						
       // Gets all topics that the user has access to
        $args = array(
            'post_type'   => bbp_get_topic_post_type(),
            'post_status' => bbp_get_public_status_id(),
            'posts_per_page' => -1 
        );
		
		
		// handling POSTs
        if (isset($_POST['mark_as_read'])) {
            $topic_id = intval($_POST['topic_id']);
            $topic_title = bbp_get_topic_title($topic_id);
            bsp_ur_update_last_topic_visit($topic_id);
            // Store the marked topic details in a session variable
            $_SESSION['marked_topic'] = array(
                'title' => $topic_title,
                'permalink' => bbp_get_topic_permalink($topic_id)
            );
            wp_redirect($_SERVER['REQUEST_URI']);
        }
		

        if (isset($_POST['mark_all_as_read'])) {
            if (bbp_has_topics($args)) {
                while (bbp_topics()) {
                    bbp_the_topic();
                    if (bsp_is_topic_unread(bbp_get_topic_id())) {
                        bsp_ur_update_last_topic_visit(bbp_get_topic_id());
                    }
                }
            }
            wp_redirect($_SERVER['REQUEST_URI']);
        }
		
		if (isset($_SESSION['marked_topic'])) {
                $marked_topic = $_SESSION['marked_topic'];
                echo '<p>Marked <a href="' . esc_url($marked_topic['permalink']) . '">' . esc_html($marked_topic['title']) . '</a> as read</p>';
                unset($_SESSION['marked_topic']);
		}
		echo '<form id="mark_all_as_read_form" method="post" action="">';
		echo '<input type="hidden" name="mark_all_as_read" value="1">';
		echo '<button type="button" id="mark-all-read">Mark All as Read</button>';
		echo '</form>';
        $topic_count = 0;

       if (bbp_has_topics($args)) {
		
            echo '<table border="1" cellpadding="10" cellspacing="0">';
            echo '<thead>';
            echo '<tr>';
            echo '<th>Topic</th>';
			echo '<th>Forum</th>';
            echo '<th>Latest Reply By</th>';
            echo '<th>Latest Reply Time</th>';
            echo '<th>Action</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
       
            while (bbp_topics()) {
					
                bbp_the_topic();
				if (bsp_is_topic_unread(bbp_get_topic_id())) {
					$topic_count++;
                    $topic_id = bbp_get_topic_id();
                    $topic_title = bbp_get_topic_title();
                    $topic_permalink = bbp_get_topic_permalink();
					$forum_id = bbp_get_topic_forum_id();
					$forum_name = bbp_get_forum_title($forum_id);
					$forum_permalink = bbp_get_forum_permalink($forum_id);
					$latest_reply_author = bbp_get_reply_author_display_name(bbp_get_topic_last_reply_id());
					$latest_reply_time = bbp_get_topic_last_active_time( $topic_id );
					
                    echo '<tr>';
                    echo '<td><a href="' . esc_url($topic_permalink) . '">' . esc_html($topic_title) . '</a></td>';
					echo '<td><a href="' . esc_url($forum_permalink) . '">' . esc_html($forum_name) . '</a></td>';
					echo '<td>' . esc_html($latest_reply_author) . '</td>';
                    echo '<td>' . esc_html($latest_reply_time) . '</td>';
                    echo '<td>';
                    echo '<form method="post" action="">';
                    echo '<input type="hidden" name="topic_id" value="' . esc_attr($topic_id) . '">';
                    echo '<input type="submit" name="mark_as_read" value="Mark as Read">';
                    echo '</form>';
                    echo '</td>';
                    echo '</tr>';
				
                }
            }

            echo '</tbody>';
            echo '</table>';
            
            
            echo '<p>' . $topic_count . ' unread topics found.</p>';
        } else {
            echo 'No unread topics found.';
        }

        echo "
        <script type='text/javascript'>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('mark-all-read').addEventListener('click', function() {
                const confirmMessage = 'Are you sure you want to mark all $topic_count as read?';
                if (confirm(confirmMessage)) {
                    document.getElementById('mark_all_as_read_form').submit();
                }
            });
        });
        </script>";
    } else {
        echo 'Please log in to view unread topics.';
    }
	// return the current buffer
		return ob_get_clean();	
}