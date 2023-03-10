<?php

function load_scripts() {

    wp_enqueue_script('ajax', get_template_directory_uri() . '/js/script.js', array('jquery'), null, true);

    wp_localize_script('ajax', 'wpAjax', array(
        'ajaxUrl' => admin_url('admin-ajax.php')
    ));

}

add_action( 'wp_enqueue_scripts', 'load_scripts' );


function filter_resources_test () {

    $category = !empty($_POST['category']) ? $_POST['category'] : '*';
    $order = !empty($_POST['order']) ? $_POST['order'] : 'title';

    $render = 'ASC';

    if ($order == 'title') {
        $render = 'ASC';
    } else {
        $render = 'DESC';
    }
    
    if ($category == "*") {

        $the_paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
        
        $args = array(
            'post_type' => 'cpt_resources',
            'posts_per_page' => 4,
            'orderby' => $order,
            'order' => $render,
            'paged' => $the_paged
        );

        $the_query = new WP_Query($args);

        while ($the_query->have_posts()) {
            $the_query->the_post();

        ?>

        <div class="single-content">
            <img class="p2" src="<?php the_post_thumbnail_url(); ?>">
            <?php
                $field = get_field('resource_types');

                if ($field == 'PDF') {
                    ?>
                        <p class="single-content-heading"><a href="<?php the_field('choose_a_file_for_pdf'); ?>"><?php the_title(); ?></a></p>
                    <?php
                } else {
                    ?>
                        <p class="single-content-heading"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></p>
                    <?php
                }
            ?>
            <span class="file-type">
                <?php echo get_field('resource_types'); ?>
            </span>
        </div>

        <?php  }

        $wp_query = $the_query;

        $total_pages = $wp_query->max_num_pages;

        if ( $total_pages > 1) {
            $big = 999999999;

            $pagination = array(
                // 'base' => get_pagenum_link(1) . '%_%',
                // 'format' => '/page/%#%',
                // 'base' => @add_query_arg('paged','%#%'),
                'base' => str_replace( $big, '%#%', get_pagenum_link( $big ) ),
                'format' => '?paged=%#%',
                'mid-size' => 1,
                'current' => $the_paged,
                'total' => $total_pages,
                'prev_text' => __( '<< Previous' ),
                'next_text' => __( 'Next >>' )
            );

            ?>
            <div id="resourcesPagination">
                <?php 
                    echo paginate_links($pagination);
                    wp_reset_postdata();
                ?>
            </div>

            <?php
        }

    }

    if ($category != null && $category != '*') {

        $the_paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
 
        $args = array(
            'post_type' => 'cpt_resources',
            'posts_per_page' => 4,
            'paged' => $the_paged,
            'orderby' => $order,
            'order' => $render,
            'tax_query' => array(
                array(
                    'taxonomy' => 'resources-categories',
                    'field' => 'term_id',
                    'terms' => array($category)
                )
            )
        );

        $the_query = new WP_Query($args);

        while ($the_query->have_posts()) {
            $the_query->the_post();

        ?>

        <div class="single-content">
            <img class="p2" src="<?php the_post_thumbnail_url(); ?>">
            <?php
                $field = get_field('resource_types');

                if ($field == 'PDF') {
                    ?>
                        <p class="single-content-heading"><a href="<?php the_field('choose_a_file_for_pdf'); ?>"><?php the_title(); ?></a></p>
                    <?php
                } else {
                    ?>
                        <p class="single-content-heading"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></p>
                    <?php
                }
            ?>
            <span class="file-type">
                <?php echo get_field('resource_types'); ?>
            </span>
        </div>

        <?php  }

        $wp_query = $the_query;

        $total_pages = $wp_query->max_num_pages;

        if ( $total_pages > 1) {
            $the_paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

            $pagination = array(
                'base' => @add_query_arg('paged','%#%'),
                'format' => '?paged=%#%',
                'mid-size' => 1,
                'current' => $the_paged,
                'total' => $total_pages,
                'prev_next' => True,
                'prev_text' => __( '<< Previous' ),
                'next_text' => __( 'Next >>' )
            );

            ?>
            <div id="resourcesPagination">
                <?php 
                    echo paginate_links($pagination);
                    wp_reset_postdata();
                ?>
            </div>

            <?php
        }

    }



    die();
}


add_action('wp_ajax_filter', 'filter_resources_test'); // wp_ajax_{action}
add_action('wp_ajax_nopriv_filter', 'filter_resources_test'); // wp_ajax_nopriv_{action}


?>