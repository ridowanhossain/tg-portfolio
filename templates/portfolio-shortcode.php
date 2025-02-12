<?php
/**
 * Portfolio Shortcode Template
 */

// Query portfolio items
$args = array(
    'post_type' => 'tg_portfolio',
    'posts_per_page' => -1, // Show all portfolio items
);
$portfolio_query = new WP_Query($args);

if ($portfolio_query->have_posts()) :
?>
    <div class="tg-portfolio-archive">
        <div class="portfolio-grid">
            <?php
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
            ?>
        </div>
    </div>
<?php
else :
    echo '<p>No portfolio items found.</p>';
endif;

wp_reset_postdata(); // Reset the query
?>