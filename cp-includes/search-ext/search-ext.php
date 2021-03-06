<?php
/*
Plugin name: Add ID to search
*/


function search_by_title_content_excerpt_id( $search, &$wp_query )
{
    global $wpdb;
    if ( empty( $search ) )
        return $search; // skip processing - no search term in query
    $q = $wp_query->query_vars;
	//print_r($q);

    $n = ! empty( $q['exact'] ) ? '' : '%';
    $search =
    $searchand = '';
    foreach ( (array) $q['search_terms'] as $term ) {
        $term = esc_sql( like_escape( $term ) );
        $search .= "{$searchand}(($wpdb->posts.post_title LIKE '{$n}{$term}{$n}') OR ($wpdb->posts.post_content LIKE '{$n}{$term}{$n}') OR ($wpdb->posts.post_content_filtered LIKE '{$n}{$term}{$n}') OR ($wpdb->posts.ID = '{$term}'))";
        $searchand = ' AND ';
    }
    if ( ! empty( $search ) ) {
        $search = " AND ({$search}) ";
        if ( ! is_user_logged_in() )
            $search .= " AND ($wpdb->posts.post_password = '') ";
    }

	return $search;
} add_filter( 'posts_search', 'search_by_title_content_excerpt_id', 500, 2 );
