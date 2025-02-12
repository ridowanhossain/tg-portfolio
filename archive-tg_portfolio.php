<?php
/**
 * Portfolio Archive Template
 */

get_header(); // Include the theme's header

// Get the posts per page setting
$posts_per_page = get_option('tg_portfolio_posts_per_page', 10); // Default to 10 posts per page

// Query portfolio items
$args = array(
    'post_type' => 'tg_portfolio',
    'posts_per_page' => $posts_per_page === 0 ? -1 : $posts_per_page, // Show all posts if set to 0
);
$portfolio_query = new WP_Query($args);
?>

<div class="tg-portfolio-archive">
    <h1>Portfolio Archive</h1>
    <div class="portfolio-grid">
        <?php
        if ($portfolio_query->have_posts()) :
            while ($portfolio_query->have_posts()) : $portfolio_query->the_post();
                ?>
                <div class="portfolio-item">
                    <a href="<?php the_permalink(); ?>">
                        <?php
                        if (has_post_thumbnail()) {
                            the_post_thumbnail('medium');
                        }
                        ?>
                        <h2><?php the_title(); ?></h2>
                    </a>
                </div>
                <?php
            endwhile;
        else :
            echo '<p>No portfolio items found.</p>';
        endif;
        ?>
    </div>
</div>

<?php
get_footer(); // Include the theme's footer