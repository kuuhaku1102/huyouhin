<?php get_header(); ?>

<!-- Column Archive Hero -->
<section class="column-hero">
    <div class="container">
        <h1 class="hero-title">不用品回収コラム</h1>
        <p class="hero-subtitle">不用品回収に関する役立つ情報をお届けします</p>
    </div>
</section>

<!-- Column List Section -->
<section class="column-list-section">
    <div class="container">
        <?php
        // ページネーション用のパラメータ
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        
        // 投稿を取得
        $args = array(
            'post_type' => 'post',
            'posts_per_page' => 12,
            'paged' => $paged,
            'orderby' => 'date',
            'order' => 'DESC',
        );
        
        $column_query = new WP_Query($args);
        
        if ($column_query->have_posts()) :
        ?>
            <div class="column-grid">
                <?php while ($column_query->have_posts()) : $column_query->the_post(); ?>
                    <article class="column-card">
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="column-thumbnail">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('medium_large'); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <div class="column-content">
                            <div class="column-meta">
                                <time class="column-date" datetime="<?php echo get_the_date('c'); ?>">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12.6667 2.66667H3.33333C2.59695 2.66667 2 3.26362 2 4V13.3333C2 14.0697 2.59695 14.6667 3.33333 14.6667H12.6667C13.403 14.6667 14 14.0697 14 13.3333V4C14 3.26362 13.403 2.66667 12.6667 2.66667Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M10.6667 1.33333V4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M5.33333 1.33333V4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M2 6.66667H14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <?php echo get_the_date('Y年n月j日'); ?>
                                </time>
                                
                                <?php
                                $categories = get_the_category();
                                if (!empty($categories)) :
                                ?>
                                    <span class="column-category">
                                        <?php echo esc_html($categories[0]->name); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <h2 class="column-title">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_title(); ?>
                                </a>
                            </h2>
                            
                            <div class="column-excerpt">
                                <?php echo wp_trim_words(get_the_excerpt(), 60, '...'); ?>
                            </div>
                            
                            <a href="<?php the_permalink(); ?>" class="column-read-more">
                                続きを読む
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6 12L10 8L6 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
            
            <!-- Pagination -->
            <?php if ($column_query->max_num_pages > 1) : ?>
                <div class="column-pagination">
                    <?php
                    echo paginate_links(array(
                        'total' => $column_query->max_num_pages,
                        'current' => $paged,
                        'prev_text' => '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 12L6 8L10 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg> 前へ',
                        'next_text' => '次へ <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 12L10 8L6 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
                        'type' => 'list',
                    ));
                    ?>
                </div>
            <?php endif; ?>
            
            <?php wp_reset_postdata(); ?>
        <?php else : ?>
            <div class="no-posts">
                <p>まだ記事がありません。</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>
