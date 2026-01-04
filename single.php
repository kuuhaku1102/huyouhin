<?php get_header(); ?>

<?php while (have_posts()) : the_post(); ?>

<!-- Article Header -->
<article class="article-single" itemscope itemtype="https://schema.org/Article">
    <!-- パンくずリスト -->
    <div class="breadcrumb-wrapper">
        <div class="container-narrow">
            <nav class="breadcrumb" aria-label="パンくずリスト">
                <a href="<?php echo home_url(); ?>">ホーム</a>
                <span class="breadcrumb-separator">/</span>
                <a href="<?php echo home_url('/column'); ?>">コラム</a>
                <?php
                $categories = get_the_category();
                if (!empty($categories)) :
                ?>
                    <span class="breadcrumb-separator">/</span>
                    <a href="<?php echo esc_url(get_category_link($categories[0]->term_id)); ?>">
                        <?php echo esc_html($categories[0]->name); ?>
                    </a>
                <?php endif; ?>
                <span class="breadcrumb-separator">/</span>
                <span class="breadcrumb-current"><?php the_title(); ?></span>
            </nav>
        </div>
    </div>

    <div class="article-header">
        <div class="container-narrow">
            <?php if (!empty($categories)) : ?>
                <div class="article-category">
                    <a href="<?php echo esc_url(get_category_link($categories[0]->term_id)); ?>">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20 7H4C2.89543 7 2 7.89543 2 9V19C2 20.1046 2.89543 21 4 21H20C21.1046 21 22 20.1046 22 19V9C22 7.89543 21.1046 7 20 7Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M16 21V5C16 4.46957 15.7893 3.96086 15.4142 3.58579C15.0391 3.21071 14.5304 3 14 3H10C9.46957 3 8.96086 3.21071 8.58579 3.58579C8.21071 3.96086 8 4.46957 8 5V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <?php echo esc_html($categories[0]->name); ?>
                    </a>
                </div>
            <?php endif; ?>
            
            <h1 class="article-title" itemprop="headline"><?php the_title(); ?></h1>
            
            <div class="article-meta">
                <div class="meta-item">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19 4H5C3.89543 4 3 4.89543 3 6V20C3 21.1046 3.89543 22 5 22H19C20.1046 22 21 21.1046 21 20V6C21 4.89543 20.1046 4 19 4Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M16 2V6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M8 2V6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M3 10H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <time class="article-date" datetime="<?php echo get_the_date('c'); ?>" itemprop="datePublished">
                        公開: <?php echo get_the_date('Y年n月j日'); ?>
                    </time>
                </div>
                
                <div class="meta-item">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M23 3C23 3 21 5 18 5C15 5 13 3 10 3C7 3 5 5 5 5V20C5 20 7 18 10 18C13 18 15 20 18 20C21 20 23 18 23 18V3Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M5 3V20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <time class="article-update" datetime="<?php echo get_the_modified_date('c'); ?>" itemprop="dateModified">
                        更新: <?php echo get_the_modified_date('Y年n月j日'); ?>
                    </time>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Article Content -->
    <div class="article-body">
        <div class="container-narrow">
            <div class="article-content" itemprop="articleBody">
                <?php the_content(); ?>
            </div>
        </div>
    </div>
    
    <!-- Article Tags -->
    <?php
    $tags = get_the_tags();
    if ($tags) :
    ?>
        <div class="article-tags">
            <div class="container-narrow">
                <div class="tags-wrapper">
                    <div class="tags-label">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20.59 13.41L13.42 20.58C13.2343 20.766 13.0137 20.9135 12.7709 21.0141C12.5281 21.1148 12.2678 21.1666 12.005 21.1666C11.7422 21.1666 11.4819 21.1148 11.2391 21.0141C10.9963 20.9135 10.7757 20.766 10.59 20.58L2 12V2H12L20.59 10.59C20.9625 10.9647 21.1716 11.4716 21.1716 12C21.1716 12.5284 20.9625 13.0353 20.59 13.41Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M7 7H7.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        タグ
                    </div>
                    <div class="tags-list">
                        <?php foreach ($tags as $tag) : ?>
                            <a href="<?php echo esc_url(get_tag_link($tag->term_id)); ?>" class="tag-item" rel="tag">
                                #<?php echo esc_html($tag->name); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Article Footer CTA -->
    <div class="article-footer">
        <div class="container-narrow">
            <div class="article-cta">
                <div class="cta-icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M21 10C21 17 12 23 12 23C12 23 3 17 3 10C3 7.61305 3.94821 5.32387 5.63604 3.63604C7.32387 1.94821 9.61305 1 12 1C14.3869 1 16.6761 1.94821 18.364 3.63604C20.0518 5.32387 21 7.61305 21 10Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12 13C13.6569 13 15 11.6569 15 10C15 8.34315 13.6569 7 12 7C10.3431 7 9 8.34315 9 10C9 11.6569 10.3431 13 12 13Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <h3 class="cta-title">お近くの優良業者を探す</h3>
                <p class="cta-description">複数の優良業者から一括見積もり。簡単30秒で最適な業者が見つかります。</p>
                <a href="<?php echo home_url('/quote'); ?>" class="btn btn-primary btn-large">
                    無料で見積もりを取る
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12 5L19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
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
                <h3 class="related-title">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4 19.5C4 18.837 4.26339 18.2011 4.73223 17.7322C5.20107 17.2634 5.83696 17 6.5 17H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M6.5 2H20V22H6.5C5.83696 22 5.20107 21.7366 4.73223 21.2678C4.26339 20.7989 4 20.163 4 19.5V4.5C4 3.83696 4.26339 3.20107 4.73223 2.73223C5.20107 2.26339 5.83696 2 6.5 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    関連記事
                </h3>
                <div class="related-grid">
                    <?php while ($related_query->have_posts()) : $related_query->the_post(); ?>
                        <article class="related-card">
                            <div class="related-content">
                                <time class="related-date">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M19 4H5C3.89543 4 3 4.89543 3 6V20C3 21.1046 3.89543 22 5 22H19C20.1046 22 21 21.1046 21 20V6C21 4.89543 20.1046 4 19 4Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M16 2V6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M8 2V6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M3 10H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <?php echo get_the_date('Y.m.d'); ?>
                                </time>
                                <h4 class="related-post-title">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_title(); ?>
                                    </a>
                                </h4>
                                <a href="<?php the_permalink(); ?>" class="related-link">
                                    続きを読む
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M12 5L19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </a>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
        <?php wp_reset_postdata(); ?>
    <?php endif; ?>
    
    <!-- 構造化データ -->
    <meta itemprop="author" content="<?php bloginfo('name'); ?>">
    <meta itemprop="publisher" content="<?php bloginfo('name'); ?>">
</article>

<?php endwhile; ?>

<?php get_footer(); ?>
