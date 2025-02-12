<?php
/**
 * Right Media Layout
 */
?>
<div class="portfolio-layout-right">
    <div class="portfolio-content">
        <h1><?php the_title(); ?></h1>
        <div class="portfolio-description">
            <?php the_content(); ?>
        </div>
    </div>
    <div class="portfolio-media">
        <?php
        // Display the featured image
        if (has_post_thumbnail()) {
            the_post_thumbnail('large');
        }
        ?>
    </div>
</div>