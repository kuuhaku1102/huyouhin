<?php
/**
 * Template Name: Prefecture Page
 * 都道府県別ページテンプレート
 */

get_header();

// ページタイトルから都道府県名を取得
$prefecture_name = get_the_title();
?>

<div class="page-header">
    <div class="container">
        <h1 class="page-title"><?php echo esc_html($prefecture_name); ?>の不用品回収業者</h1>
        <p class="page-description"><?php echo esc_html($prefecture_name); ?>で利用できる信頼できる不用品回収業者をご紹介</p>
    </div>
</div>

<main class="prefecture-page">
    <div class="container">
        
        <?php
        global $wpdb;
        $table_name = $wpdb->prefix . 'comp2';
        $media_table = $wpdb->prefix . 'comp2_media';
        
        // テーブルが存在するか確認
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") === $table_name;
        
        if (!$table_exists) :
        ?>
            <div class="notice" style="background: #fff3cd; border: 1px solid #ffc107; border-radius: 8px; padding: 20px; margin: 40px 0;">
                <h3 style="margin-top: 0;">⚠️ データがまだインポートされていません</h3>
                <p>管理画面からデータをインポートしてください。</p>
            </div>
        <?php
        else :
            // 都道府県名で対応エリアを検索
            $companies = $wpdb->get_results($wpdb->prepare("
                SELECT
                    c.ranking,
                    c.company_id,
                    c.company_name,
                    c.logo_image_url,
                    c.total_rating_star,
                    c.total_rating_text,
                    c.review_count,
                    c.summary,
                    c.service_content,
                    c.service_area,
                    c.price_info,
                    c.recommended_points,
                    c.official_url,
                    m.attachment_id
                FROM {$table_name} c
                LEFT JOIN {$media_table} m ON c.company_id = m.company_id
                WHERE c.service_area LIKE %s
                ORDER BY c.ranking ASC
            ", '%' . $wpdb->esc_like($prefecture_name) . '%'));
            
            if (!empty($companies)) :
        ?>
        
        <!-- 対応業者数 -->
        <div class="prefecture-stats">
            <p><strong><?php echo esc_html($prefecture_name); ?>対応の業者:</strong> <?php echo count($companies); ?>社</p>
        </div>
        
        <?php
        // アフィリエイトバナーを取得
        $banner_table = $wpdb->prefix . 'affiliate_banners';
        $banner_table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$banner_table}'") === $banner_table;
        
        if ($banner_table_exists) {
            // 該当都道府県のバナーを取得（全国対応も含む）
            $banners = $wpdb->get_results($wpdb->prepare("
                SELECT * FROM {$banner_table}
                WHERE is_active = 1
                AND (target_prefectures = '' 
                     OR target_prefectures IS NULL 
                     OR target_prefectures LIKE %s)
                ORDER BY display_order ASC, id DESC
                LIMIT 3
            ", '%' . $wpdb->esc_like($prefecture_name) . '%'));
            
            if (!empty($banners)) :
        ?>
        <!-- Pickupバナー -->
        <section class="pickup-banners">
            <h2 class="pickup-title">🎯 <?php echo esc_html($prefecture_name); ?>のおすすめサービス</h2>
            <div class="pickup-grid">
                <?php foreach ($banners as $banner) : ?>
                    <a href="<?php echo esc_url($banner->affiliate_url); ?>" 
                       class="pickup-banner" 
                       target="_blank" 
                       rel="noopener noreferrer nofollow">
                        <?php if (!empty($banner->banner_image_url)) : ?>
                            <img src="<?php echo esc_url($banner->banner_image_url); ?>" 
                                 alt="<?php echo esc_attr($banner->title); ?>" 
                                 loading="lazy">
                        <?php else : ?>
                            <div class="banner-text">
                                <h3><?php echo esc_html($banner->title); ?></h3>
                                <span class="banner-cta">詳細を見る →</span>
                            </div>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
        <?php 
            endif;
        }
        ?>
        
        <!-- 業者一覧 -->
        <section class="junk-ranking">
            <?php 
            $rank = 1;
            foreach ($companies as $company) : 
                // ローカルロゴURLを取得
                $logo_url = '';
                if ($company->attachment_id) {
                    $logo_url = wp_get_attachment_image_url($company->attachment_id, 'medium');
                }
                if (!$logo_url && !empty($company->logo_image_url)) {
                    $logo_url = $company->logo_image_url;
                }
            ?>
                <div class="junk-ranking-item">
                    
                    <!-- 順位 -->
                    <div class="rank">
                        <span class="rank-number"><?php echo $rank; ?></span>
                        <span class="rank-label">位</span>
                    </div>
                    
                    <div class="company-content">
                        <!-- ロゴ -->
                        <?php if (!empty($logo_url)) : ?>
                            <div class="logo">
                                <img
                                    src="<?php echo esc_url($logo_url); ?>"
                                    alt="<?php echo esc_attr($company->company_name); ?>"
                                    loading="lazy"
                                >
                            </div>
                        <?php endif; ?>
                        
                        <!-- 会社名 -->
                        <h3 class="company-name">
                            <?php echo esc_html($company->company_name); ?>
                        </h3>
                        
                        <!-- 評価 -->
                        <?php if (!is_null($company->total_rating_star)) : ?>
                            <div class="rating">
                                <span class="stars">
                                    <?php
                                    $rating = floatval($company->total_rating_star);
                                    $full_stars = floor($rating);
                                    $half_star = ($rating - $full_stars) >= 0.5;
                                    
                                    for ($i = 0; $i < $full_stars; $i++) {
                                        echo '★';
                                    }
                                    if ($half_star) {
                                        echo '☆';
                                    }
                                    ?>
                                </span>
                                <span class="rating-number"><?php echo esc_html($company->total_rating_star); ?></span>
                                <?php if (!empty($company->total_rating_text)) : ?>
                                    <span class="rating-text"><?php echo esc_html($company->total_rating_text); ?></span>
                                <?php endif; ?>
                                <?php if (!empty($company->review_count)) : ?>
                                    <span class="review-count">(<?php echo esc_html($company->review_count); ?>件)</span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                        <!-- 概要 -->
                        <?php if (!empty($company->summary)) : ?>
                            <p class="summary">
                                <?php echo esc_html($company->summary); ?>
                            </p>
                        <?php endif; ?>
                        
                        <!-- サービス内容 -->
                        <?php if (!empty($company->service_content)) : ?>
                            <div class="service">
                                <h4>サービス内容</h4>
                                <p><?php echo nl2br(esc_html($company->service_content)); ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <!-- 対応エリア -->
                        <?php if (!empty($company->service_area)) : ?>
                            <div class="area">
                                <h4>対応エリア</h4>
                                <p><?php echo esc_html($company->service_area); ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <!-- 料金 -->
                        <?php if (!empty($company->price_info)) : ?>
                            <div class="price">
                                <h4>料金</h4>
                                <p><?php echo nl2br(esc_html($company->price_info)); ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <!-- おすすめポイント -->
                        <?php if (!empty($company->recommended_points)) : ?>
                            <div class="recommend">
                                <h4>おすすめポイント</h4>
                                <p><?php echo nl2br(esc_html($company->recommended_points)); ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <!-- 公式サイト -->
                        <?php if (!empty($company->official_url)) : ?>
                            <a
                                href="<?php echo esc_url($company->official_url); ?>"
                                class="official-link"
                                target="_blank"
                                rel="noopener noreferrer"
                            >
                                公式サイトを見る →
                            </a>
                        <?php endif; ?>
                    </div>
                    
                </div>
            <?php 
                $rank++;
            endforeach; 
            ?>
        </section>
        
        <?php else : ?>
            <div class="no-results">
                <p><?php echo esc_html($prefecture_name); ?>に対応している業者が見つかりませんでした。</p>
                <p><a href="<?php echo home_url('/rankings/'); ?>" class="btn btn-primary">全国ランキングを見る</a></p>
            </div>
        <?php endif; ?>
        
        <?php endif; ?>
        
        <!-- 都道府県一覧へのリンク -->
        <div class="prefecture-nav">
            <h2>他の都道府県を見る</h2>
            <div class="prefecture-links">
                <?php
                $prefectures = array(
                    '北海道', '青森県', '岩手県', '宮城県', '秋田県', '山形県', '福島県',
                    '茨城県', '栃木県', '群馬県', '埼玉県', '千葉県', '東京都', '神奈川県',
                    '新潟県', '富山県', '石川県', '福井県', '山梨県', '長野県',
                    '岐阜県', '静岡県', '愛知県', '三重県',
                    '滋賀県', '京都府', '大阪府', '兵庫県', '奈良県', '和歌山県',
                    '鳥取県', '島根県', '岡山県', '広島県', '山口県',
                    '徳島県', '香川県', '愛媛県', '高知県',
                    '福岡県', '佐賀県', '長崎県', '熊本県', '大分県', '宮崎県', '鹿児島県', '沖縄県'
                );
                
                foreach ($prefectures as $pref) {
                    if ($pref !== $prefecture_name) {
                        $pref_page = get_page_by_title($pref, OBJECT, 'page');
                        if ($pref_page) {
                            echo '<a href="' . get_permalink($pref_page->ID) . '" class="prefecture-link">' . esc_html($pref) . '</a>';
                        }
                    }
                }
                ?>
            </div>
        </div>
    </div>
</main>

<style>
.prefecture-stats {
    background: #f0f9ff;
    border-left: 4px solid #667eea;
    padding: 20px;
    margin: 30px 0;
    border-radius: 8px;
}

.prefecture-stats p {
    margin: 0;
    font-size: 18px;
    color: #1a202c;
}

.no-results {
    text-align: center;
    padding: 60px 20px;
}

.no-results p {
    font-size: 18px;
    color: #6b7280;
    margin-bottom: 20px;
}

.prefecture-nav {
    margin-top: 60px;
    padding-top: 40px;
    border-top: 2px solid #e5e7eb;
}

.prefecture-nav h2 {
    font-size: 24px;
    margin-bottom: 20px;
    color: #1a202c;
}

.prefecture-links {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 10px;
}

.prefecture-link {
    display: block;
    padding: 10px 15px;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    text-align: center;
    text-decoration: none;
    color: #4b5563;
    transition: all 0.3s ease;
}

.prefecture-link:hover {
    background: #667eea;
    color: white;
    border-color: #667eea;
    transform: translateY(-2px);
}

/* ランキングアイテムのスタイルは page-rankings.php と共通 */
.junk-ranking {
    margin-top: 40px;
}

.junk-ranking-item {
    display: flex;
    gap: 30px;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
}

.junk-ranking-item:hover {
    box-shadow: 0 8px 24px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.rank {
    flex-shrink: 0;
    width: 80px;
    height: 80px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    color: white;
    font-weight: bold;
}

.rank-number {
    font-size: 36px;
    line-height: 1;
}

.rank-label {
    font-size: 14px;
    margin-top: 4px;
}

.company-content {
    flex: 1;
}

.logo {
    float: right;
    width: 150px;
    margin: 0 0 20px 20px;
}

.logo img {
    width: 100%;
    height: auto;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
}

.company-name {
    font-size: 24px;
    margin: 0 0 15px 0;
    color: #1a202c;
}

.rating {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 15px;
    font-size: 16px;
}

.stars {
    color: #fbbf24;
    font-size: 20px;
}

.rating-number {
    font-weight: bold;
    color: #1a202c;
}

.rating-text {
    color: #6b7280;
}

.review-count {
    color: #9ca3af;
    font-size: 14px;
}

.summary {
    font-size: 16px;
    line-height: 1.8;
    color: #4b5563;
    margin-bottom: 20px;
}

.service, .area, .price, .recommend {
    margin-bottom: 20px;
}

.service h4, .area h4, .price h4, .recommend h4 {
    font-size: 16px;
    font-weight: bold;
    color: #1a202c;
    margin: 0 0 8px 0;
    padding-bottom: 8px;
    border-bottom: 2px solid #667eea;
}

.service p, .area p, .price p, .recommend p {
    font-size: 15px;
    line-height: 1.8;
    color: #4b5563;
    margin: 0;
}

.official-link {
    display: inline-block;
    margin-top: 20px;
    padding: 12px 30px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: bold;
    transition: all 0.3s ease;
}

.official-link:hover {
    transform: translateX(5px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

@media (max-width: 768px) {
    .prefecture-links {
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    }
    
    .junk-ranking-item {
        flex-direction: column;
        padding: 20px;
    }
    
    .rank {
        width: 60px;
        height: 60px;
        margin: 0 auto 20px;
    }
    
    .rank-number {
        font-size: 28px;
    }
    
    .logo {
        float: none;
        width: 100%;
        max-width: 200px;
        margin: 0 auto 20px;
    }
    
    .company-name {
        font-size: 20px;
        text-align: center;
    }
}
</style>

<?php get_footer(); ?>
