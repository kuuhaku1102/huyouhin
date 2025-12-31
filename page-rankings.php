<?php
/**
 * Template Name: Rankings Page
 */
get_header();
?>

<div class="page-header">
    <div class="container">
        <h1 class="page-title">おすすめランキング</h1>
        <p class="page-description">実際の利用者評価に基づいた、信頼できる業者ランキング</p>
    </div>
</div>

<div class="rankings-page">
    <div class="container">
        <!-- Category Tabs -->
        <div class="ranking-tabs">
            <button class="tab-button active" data-category="overall">総合ランキング</button>
            <button class="tab-button" data-category="price">料金が安い</button>
            <button class="tab-button" data-category="speed">対応が早い</button>
            <button class="tab-button" data-category="service">サービス品質</button>
        </div>

        <!-- Overall Ranking -->
        <div class="ranking-content" data-category="overall">
            <?php
            $args = array(
                'post_type' => 'company',
                'posts_per_page' => -1,
                'meta_key' => '_ranking_position',
                'orderby' => 'meta_value_num',
                'order' => 'ASC',
                'meta_query' => array(
                    array(
                        'key' => '_ranking_category',
                        'value' => 'overall',
                    ),
                ),
            );
            $companies = new WP_Query($args);
            
            if ($companies->have_posts()) :
                $rank = 1;
            ?>
                <div class="ranking-list">
                    <?php while ($companies->have_posts()) : $companies->the_post(); 
                        $rating = get_post_meta(get_the_ID(), '_company_rating', true);
                        $price_range = get_post_meta(get_the_ID(), '_company_price_range', true);
                        $phone = get_post_meta(get_the_ID(), '_company_phone', true);
                        $website = get_post_meta(get_the_ID(), '_company_website', true);
                        $areas = get_post_meta(get_the_ID(), '_company_areas', true);
                    ?>
                        <div class="ranking-item">
                            <div class="rank-number-large"><?php echo $rank; ?></div>
                            
                            <div class="company-details">
                                <?php if (has_post_thumbnail()) : ?>
                                    <div class="company-thumbnail">
                                        <?php the_post_thumbnail('thumbnail'); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="company-main">
                                    <h2 class="company-title"><?php the_title(); ?></h2>
                                    
                                    <?php if ($rating) : ?>
                                        <div class="rating-display">
                                            <span class="stars"><?php echo str_repeat('★', floor($rating)) . str_repeat('☆', 5 - floor($rating)); ?></span>
                                            <span class="rating-number"><?php echo number_format($rating, 1); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="company-content">
                                        <?php the_content(); ?>
                                    </div>
                                    
                                    <div class="company-meta">
                                        <?php if ($price_range) : ?>
                                            <div class="meta-item">
                                                <span class="meta-label">💰 料金目安:</span>
                                                <span class="meta-value"><?php echo esc_html($price_range); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($areas) : ?>
                                            <div class="meta-item">
                                                <span class="meta-label">📍 対応エリア:</span>
                                                <span class="meta-value"><?php echo esc_html($areas); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($phone) : ?>
                                            <div class="meta-item">
                                                <span class="meta-label">📞 電話:</span>
                                                <span class="meta-value"><?php echo esc_html($phone); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($website) : ?>
                                            <div class="meta-item">
                                                <span class="meta-label">🌐 ウェブサイト:</span>
                                                <span class="meta-value"><a href="<?php echo esc_url($website); ?>" target="_blank" rel="noopener">公式サイトへ</a></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="company-actions">
                                        <a href="<?php the_permalink(); ?>" class="btn btn-outline">詳細を見る</a>
                                        <a href="<?php echo home_url('/quote'); ?>" class="btn btn-primary">見積もり依頼</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php 
                        $rank++;
                    endwhile; 
                    wp_reset_postdata();
                    ?>
                </div>
            <?php else : ?>
                <p class="no-results">現在、ランキングに掲載されている業者はありません。</p>
            <?php endif; ?>
        </div>

        <!-- Price Ranking -->
        <div class="ranking-content" data-category="price" style="display: none;">
            <?php
            $args['meta_query'][0]['value'] = 'price';
            $companies = new WP_Query($args);
            
            if ($companies->have_posts()) :
                $rank = 1;
                while ($companies->have_posts()) : $companies->the_post();
                    // Same structure as above
                    include(locate_template('template-parts/ranking-item.php'));
                    $rank++;
                endwhile;
                wp_reset_postdata();
            else :
                echo '<p class="no-results">現在、このカテゴリにランキングはありません。</p>';
            endif;
            ?>
        </div>

        <!-- Speed Ranking -->
        <div class="ranking-content" data-category="speed" style="display: none;">
            <?php
            $args['meta_query'][0]['value'] = 'speed';
            $companies = new WP_Query($args);
            
            if ($companies->have_posts()) :
                $rank = 1;
                while ($companies->have_posts()) : $companies->the_post();
                    include(locate_template('template-parts/ranking-item.php'));
                    $rank++;
                endwhile;
                wp_reset_postdata();
            else :
                echo '<p class="no-results">現在、このカテゴリにランキングはありません。</p>';
            endif;
            ?>
        </div>

        <!-- Service Ranking -->
        <div class="ranking-content" data-category="service" style="display: none;">
            <?php
            $args['meta_query'][0]['value'] = 'service';
            $companies = new WP_Query($args);
            
            if ($companies->have_posts()) :
                $rank = 1;
                while ($companies->have_posts()) : $companies->the_post();
                    include(locate_template('template-parts/ranking-item.php'));
                    $rank++;
                endwhile;
                wp_reset_postdata();
            else :
                echo '<p class="no-results">現在、このカテゴリにランキングはありません。</p>';
            endif;
            ?>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $('.tab-button').on('click', function() {
        var category = $(this).data('category');
        
        // Update active tab
        $('.tab-button').removeClass('active');
        $(this).addClass('active');
        
        // Show corresponding content
        $('.ranking-content').hide();
        $('.ranking-content[data-category="' + category + '"]').show();
    });
});
</script>

<?php get_footer(); ?>
