<?php get_header(); ?>

<?php while (have_posts()) : the_post(); ?>

<!-- Article Header -->
<article class="article-single">
    <div class="article-header">
        <div class="container-narrow">
            <?php
            $categories = get_the_category();
            if (!empty($categories)) :
            ?>
                <div class="article-category">
                    <a href="<?php echo esc_url(get_category_link($categories[0]->term_id)); ?>">
                        <?php echo esc_html($categories[0]->name); ?>
                    </a>
                </div>
            <?php endif; ?>
            
            <h1 class="article-title"><?php the_title(); ?></h1>
            
            <div class="article-meta">
                <time class="article-date" datetime="<?php echo get_the_date('c'); ?>">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12.6667 2.66667H3.33333C2.59695 2.66667 2 3.26362 2 4V13.3333C2 14.0697 2.59695 14.6667 3.33333 14.6667H12.6667C13.403 14.6667 14 14.0697 14 13.3333V4C14 3.26362 13.403 2.66667 12.6667 2.66667Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M10.6667 1.33333V4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M5.33333 1.33333V4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M2 6.66667H14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <?php echo get_the_date('Y年n月j日'); ?>
                </time>
                
                <span class="article-update">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14 8C14 11.3137 11.3137 14 8 14C4.68629 14 2 11.3137 2 8C2 4.68629 4.68629 2 8 2C11.3137 2 14 4.68629 14 8Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M8 4V8L10.5 9.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    更新: <?php echo get_the_modified_date('Y年n月j日'); ?>
                </span>
            </div>
        </div>
    </div>
    
    <?php if (has_post_thumbnail()) : ?>
        <div class="article-featured-image">
            <div class="container-narrow">
                <?php the_post_thumbnail('large'); ?>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Article Content -->
    <div class="article-content">
        <div class="container-narrow">
            <?php the_content(); ?>
        </div>
    </div>
    
    <!-- Article Tags -->
    <?php
    $tags = get_the_tags();
    if ($tags) :
    ?>
        <div class="article-tags">
            <div class="container-narrow">
                <div class="tags-label">タグ:</div>
                <div class="tags-list">
                    <?php foreach ($tags as $tag) : ?>
                        <a href="<?php echo esc_url(get_tag_link($tag->term_id)); ?>" class="tag-item">
                            #<?php echo esc_html($tag->name); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Article Footer -->
    <div class="article-footer">
        <div class="container-narrow">
            <div class="article-cta">
                <h3 class="cta-title">不用品回収の見積もりを取る</h3>
                <p class="cta-description">複数の優良業者から一括見積もり。簡単30秒で最適な業者が見つかります。</p>
                <a href="<?php echo home_url('/quote'); ?>" class="btn btn-primary btn-large">
                    無料で見積もりを取る
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M7.5 15L12.5 10L7.5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Related Posts -->
    <?php
    $related_args = array(
        'post_type' => 'post',
        'posts_per_page' => 3,
        'post__not_in' => array(get_the_ID()),
        'orderby' => 'rand',
    );
    
    // 同じカテゴリーの記事を優先
    if (!empty($categories)) {
        $related_args['category__in'] = array($categories[0]->term_id);
    }
    
    $related_query = new WP_Query($related_args);
    
    if ($related_query->have_posts()) :
    ?>
        <div class="related-posts">
            <div class="container-narrow">
                <h3 class="related-title">関連記事</h3>
                <div class="related-grid">
                    <?php while ($related_query->have_posts()) : $related_query->the_post(); ?>
                        <article class="related-card">
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="related-thumbnail">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('medium'); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            
                            <div class="related-content">
                                <time class="related-date">
                                    <?php echo get_the_date('Y.m.d'); ?>
                                </time>
                                <h4 class="related-post-title">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_title(); ?>
                                    </a>
                                </h4>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
        <?php wp_reset_postdata(); ?>
    <?php endif; ?>
</article>

<?php endwhile; ?>

<?php get_footer(); ?>
