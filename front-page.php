<?php get_header(); ?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <h1 class="hero-title">不用品回収の最安値がすぐに見つかる</h1>
        <p class="hero-subtitle">複数の優良業者から一括見積もり。簡単30秒で最適な業者が見つかります。</p>
        
        <div class="hero-cta">
            <a href="<?php echo home_url('/quote'); ?>" class="btn btn-primary">無料で見積もりを取る</a>
            <a href="<?php echo home_url('/rankings'); ?>" class="btn btn-secondary">おすすめランキング</a>
        </div>

        <div class="hero-stats">
            <div class="stat-item">
                <div class="stat-value">10,000+</div>
                <div class="stat-label">利用者数</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">500+</div>
                <div class="stat-label">提携業者</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">4.8</div>
                <div class="stat-label">平均評価</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">98%</div>
                <div class="stat-label">満足度</div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features">
    <div class="container">
        <h2 class="section-title">選ばれる理由</h2>
        
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">🔍</div>
                <h3 class="feature-title">簡単30秒見積もり</h3>
                <p class="feature-description">サービス種別、物量、郵便番号を入力するだけで、複数の業者から見積もりを取得できます。</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <h3 class="feature-title">信頼できるランキング</h3>
                <p class="feature-description">実際の利用者の評価と口コミに基づいた、公正なランキングを提供しています。</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">🛡️</div>
                <h3 class="feature-title">厳選された優良業者</h3>
                <p class="feature-description">独自の審査基準をクリアした、信頼できる業者のみを掲載しています。</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">💰</div>
                <h3 class="feature-title">最安値保証</h3>
                <p class="feature-description">複数業者を比較することで、最もお得な料金で不用品回収が可能です。</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">⚡</div>
                <h3 class="feature-title">即日対応可能</h3>
                <p class="feature-description">お急ぎの方にも対応。最短即日で不用品を回収できる業者をご紹介します。</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">📞</div>
                <h3 class="feature-title">24時間サポート</h3>
                <p class="feature-description">お問い合わせやご相談は24時間受付。安心してご利用いただけます。</p>
            </div>
        </div>
    </div>
</section>

<!-- Top Rankings Section -->
<section class="top-rankings">
    <div class="container">
        <h2 class="section-title">おすすめ業者ランキング</h2>
        
        <?php
        $args = array(
            'post_type' => 'company',
            'posts_per_page' => 3,
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
            <div class="ranking-cards">
                <?php while ($companies->have_posts()) : $companies->the_post(); 
                    $rating = get_post_meta(get_the_ID(), '_company_rating', true);
                    $price_range = get_post_meta(get_the_ID(), '_company_price_range', true);
                    $areas = get_post_meta(get_the_ID(), '_company_areas', true);
                ?>
                    <div class="ranking-card rank-<?php echo $rank; ?>">
                        <div class="rank-badge">
                            <span class="rank-number"><?php echo $rank; ?></span>
                            <span class="rank-label">位</span>
                        </div>
                        
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="company-image">
                                <?php the_post_thumbnail('medium'); ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="company-info">
                            <h3 class="company-name"><?php the_title(); ?></h3>
                            
                            <?php if ($rating) : ?>
                                <div class="company-rating">
                                    <span class="rating-stars"><?php echo str_repeat('★', floor($rating)) . str_repeat('☆', 5 - floor($rating)); ?></span>
                                    <span class="rating-value"><?php echo number_format($rating, 1); ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($price_range) : ?>
                                <div class="company-price">
                                    <span class="price-label">料金目安:</span>
                                    <span class="price-value"><?php echo esc_html($price_range); ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($areas) : ?>
                                <div class="company-areas">
                                    <span class="areas-label">対応エリア:</span>
                                    <span class="areas-value"><?php echo esc_html($areas); ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <div class="company-excerpt">
                                <?php the_excerpt(); ?>
                            </div>
                            
                            <a href="<?php the_permalink(); ?>" class="btn btn-outline">詳細を見る</a>
                        </div>
                    </div>
                <?php 
                    $rank++;
                endwhile; 
                wp_reset_postdata();
                ?>
            </div>
            
            <div class="section-cta">
                <a href="<?php echo home_url('/rankings'); ?>" class="btn btn-primary">すべてのランキングを見る</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Services Section -->
<section class="services">
    <div class="container">
        <h2 class="section-title">対応サービス</h2>
        
        <div class="services-grid">
            <div class="service-card">
                <h3 class="service-title">不用品回収</h3>
                <p class="service-description">家具、家電、日用品など、あらゆる不用品を回収します。</p>
            </div>
            
            <div class="service-card">
                <h3 class="service-title">遺品整理</h3>
                <p class="service-description">故人の遺品を丁寧に整理・処分いたします。</p>
            </div>
            
            <div class="service-card">
                <h3 class="service-title">ゴミ屋敷清掃</h3>
                <p class="service-description">大量のゴミや不用品の一括処分に対応します。</p>
            </div>
            
            <div class="service-card">
                <h3 class="service-title">生前整理</h3>
                <p class="service-description">将来に備えた整理整頓をサポートします。</p>
            </div>
        </div>
    </div>
</section>

<!-- Prefecture Links Section -->
<section class="prefecture-section">
    <div class="container">
        <h2 class="section-title">都道府県から業者を探す</h2>
        <p class="section-description">お住まいの地域で利用できる不用品回収業者を探す</p>
        
        <?php
        $prefecture_groups = array(
            '北海道・東北' => array('北海道', '青森県', '岩手県', '宮城県', '秋田県', '山形県', '福島県'),
            '関東' => array('茨城県', '栃木県', '群馬県', '埼玉県', '千葉県', '東京都', '神奈川県'),
            '中部' => array('新潟県', '富山県', '石川県', '福井県', '山梨県', '長野県', '岐阜県', '静岡県', '愛知県', '三重県'),
            '近畠' => array('滋賀県', '京都府', '大阪府', '兵庫県', '奈良県', '和歌山県'),
            '中国・四国' => array('鳥取県', '島根県', '岡山県', '広島県', '山口県', '徳島県', '香川県', '愛媛県', '高知県'),
            '九州・沖縄' => array('福岡県', '佐賀県', '長崎県', '熊本県', '大分県', '宮崎県', '鹿児島県', '沖縄県')
        );
        
        foreach ($prefecture_groups as $region => $prefectures) :
        ?>
            <div class="prefecture-group">
                <h3 class="prefecture-group-title"><?php echo esc_html($region); ?></h3>
                <div class="prefecture-links">
                    <?php
                    foreach ($prefectures as $prefecture) {
                        $page = get_page_by_title($prefecture, OBJECT, 'page');
                        if ($page) {
                            echo '<a href="' . get_permalink($page->ID) . '" class="prefecture-link">' . esc_html($prefecture) . '</a>';
                        } else {
                            echo '<span class="prefecture-link disabled">' . esc_html($prefecture) . '</span>';
                        }
                    }
                    ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- CTA Section -->
<section class="cta">
    <div class="container">
        <h2 class="cta-title">今すぐ無料で見積もりを取りましょう</h2>
        <p class="cta-description">複数の業者を比較して、最適な不用品回収業者を見つけましょう。</p>
        <a href="<?php echo home_url('/quote'); ?>" class="btn btn-primary btn-large">無料見積もりを取る</a>
    </div>
</section>

<?php get_footer(); ?>
