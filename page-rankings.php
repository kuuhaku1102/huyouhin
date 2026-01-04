<?php
/**
 * Template Name: Rankings Page
 * ランキングページ - カスタムテーブル wp_comp2 から取得
 */

get_header();
?>

<div class="page-header">
    <div class="container">
        <h1 class="page-title">不用品回収業者ランキング</h1>
        <p class="page-description">実際の利用者評価に基づいた、信頼できる業者ランキング</p>
    </div>
</div>

<main class="rankings-page">
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
                <p>管理画面の「業者情報」→「ロゴインポート」からスクレイピングデータをインポートしてください。</p>
            </div>
        <?php
        else :
            // データ取得（順位順）
            $companies = $wpdb->get_results("
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
                ORDER BY c.ranking ASC
            ");
            
            if (!empty($companies)) :
        ?>
        
        <section class="junk-ranking">
            <?php foreach ($companies as $company) : 
                // ローカルロゴURLを取得（インポート済みの場合）
                $logo_url = '';
                if ($company->attachment_id) {
                    $logo_url = wp_get_attachment_image_url($company->attachment_id, 'medium');
                }
                // なければ外部URLを使用
                if (!$logo_url && !empty($company->logo_image_url)) {
                    $logo_url = $company->logo_image_url;
                }
            ?>
                <div class="junk-ranking-item">
                    
                    <!-- 順位 -->
                    <div class="rank">
                        <span class="rank-number"><?php echo esc_html($company->ranking); ?></span>
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
            <?php endforeach; ?>
        </section>
        
        <?php else : ?>
            <p style="text-align: center; padding: 60px 20px; color: #6b7280;">現在、表示できるデータがありません。</p>
        <?php endif; ?>
        
        <?php endif; ?>
    </div>
</main>

<style>
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
