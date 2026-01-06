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
            $item_index = 0;
            foreach ($companies as $company) :
                $item_index++;
                $hidden_class = ($item_index > 5) ? ' style="display:none;"' : ''; 
                // ローカルロゴURLを取得
                $logo_url = '';
                if ($company->attachment_id) {
                    $logo_url = wp_get_attachment_image_url($company->attachment_id, 'medium');
                }
                if (!$logo_url && !empty($company->logo_image_url)) {
                    $logo_url = $company->logo_image_url;
                }
            ?>
                <div class="junk-ranking-item" data-index="<?php echo $item_index; ?>"<?php echo $hidden_class; ?>>
                    
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
            
            <!-- もっと見るボタン -->
            <?php if (count($companies) > 5) : ?>
            <div class="load-more-container">
                <button id="loadMoreBtn" class="load-more-btn" data-loaded="5" data-total="<?php echo count($companies); ?>">
                    もっと見る（残り<?php echo count($companies) - 5; ?>件）
                </button>
            </div>
            <?php endif; ?>
        </section>
        
        <?php else : ?>
            <div class="no-results">
                <p><?php echo esc_html($prefecture_name); ?>に対応している業者が見つかりませんでした。</p>
                <p><a href="<?php echo home_url('/rankings/'); ?>" class="btn btn-primary">全国ランキングを見る</a></p>
            </div>
        <?php endif; ?>
        
        <?php endif; ?>
        
        <!-- SEOコンテンツセクション -->
        <?php if (!empty($companies)) : ?>
        <section class="seo-content">
            <?php
            // 都道府県別のSEOコンテンツを取得
            $seo_content = get_prefecture_seo_content($prefecture_name);
            if ($seo_content) :
            ?>
            
            <!-- 不用品処分の方法 -->
            <div class="seo-section">
                <h2><?php echo esc_html($prefecture_name); ?>の不用品処分の方法</h2>
                <p><?php echo nl2br(esc_html($seo_content['disposal_method'])); ?></p>
            </div>
            
            <!-- 業者選びのポイント -->
            <div class="seo-section">
                <h2><?php echo esc_html($prefecture_name); ?>で不用品回収業者を選ぶポイント</h2>
                <ul class="checklist">
                    <li>処分したい不用品に対応しているか確認する</li>
                    <li>不用品の量に合わせた料金プランがあるか</li>
                    <li>買取サービスがあるか（費用削減のチャンス）</li>
                    <li>口コミや評判をチェックする</li>
                    <li>希望の日時に対応してもらえるか</li>
                </ul>
            </div>
            
            <!-- 利用がおすすめな人 -->
            <div class="seo-section">
                <h2><?php echo esc_html($prefecture_name); ?>で不用品回収業者の利用がおすすめな人</h2>
                <ul class="recommend-list">
                    <li><strong>自治体では処分できない不用品がある場合</strong><br>
                    家電リサイクル法対象品や処理困難物も対応可能</li>
                    <li><strong>すぐに処分したい場合</strong><br>
                    即日対応可能な業者も多数</li>
                    <li><strong>大量の不用品を一度に処分したい場合</strong><br>
                    引っ越しや遺品整理、ゴミ屋敷の片付けに最適</li>
                    <li><strong>重い家具や家電を処分したい場合</strong><br>
                    運び出しから処分まで全てお任せ</li>
                    <li><strong>不用品回収以外の作業も依頼したい場合</strong><br>
                    清掃やハウスクリーニングも同時依頼可能</li>
                </ul>
            </div>
            
            <!-- 費用を抑える方法 -->
            <div class="seo-section">
                <h2><?php echo esc_html($prefecture_name); ?>で不用品回収の費用を抑える方法</h2>
                <div class="tips-grid">
                    <div class="tip-card">
                        <h3>💡 相見積もりを取る</h3>
                        <p>複数の業者から見積もりを取り、料金とサービス内容を比較しましょう。</p>
                    </div>
                    <div class="tip-card">
                        <h3>💡 追加料金を確認</h3>
                        <p>階段料金や駐車料金など、追加費用の有無を事前に確認しておきましょう。</p>
                    </div>
                    <div class="tip-card">
                        <h3>💡 買取サービスを活用</h3>
                        <p>まだ使える家具や家電は買取してもらい、処分費用を相殺しましょう。</p>
                    </div>
                    <div class="tip-card">
                        <h3>💡 割引制度を利用</h3>
                        <p>WEB割引や早期予約割引など、各種キャンペーンを活用しましょう。</p>
                    </div>
                </div>
            </div>
            
            <!-- 費用相場 -->
            <div class="seo-section">
                <h2><?php echo esc_html($prefecture_name); ?>の不用品回収費用相場</h2>
                <div class="price-table">
                    <table>
                        <thead>
                            <tr>
                                <th>トラックサイズ</th>
                                <th>積載量の目安</th>
                                <th>料金相場</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>軽トラック</td>
                                <td>1R～1K程度</td>
                                <td><?php echo esc_html($seo_content['price_light_truck']); ?></td>
                            </tr>
                            <tr>
                                <td>1.5tトラック</td>
                                <td>1K～1DK程度</td>
                                <td><?php echo esc_html($seo_content['price_1t_truck']); ?></td>
                            </tr>
                            <tr>
                                <td>2tトラック</td>
                                <td>1DK～2DK程度</td>
                                <td><?php echo esc_html($seo_content['price_2t_truck']); ?></td>
                            </tr>
                            <tr>
                                <td>4tトラック</td>
                                <td>2DK～3LDK程度</td>
                                <td><?php echo esc_html($seo_content['price_4t_truck']); ?></td>
                            </tr>
                        </tbody>
                    </table>
                    <p class="note">※料金は業者や不用品の種類により異なります。正確な料金は見積もりでご確認ください。</p>
                </div>
            </div>
            
            <!-- まとめ -->
            <div class="seo-section summary-section">
                <h2>まとめ：<?php echo esc_html($prefecture_name); ?>で最適な不用品回収業者を選ぼう</h2>
                <p><?php echo nl2br(esc_html($seo_content['summary'])); ?></p>
                <div class="cta-box">
                    <p class="cta-text">上記のランキングから、あなたのニーズに合った業者を見つけて、まずは無料見積もりを依頼してみましょう！</p>
                    <a href="#top" class="cta-button">ランキングに戻る ↑</a>
                </div>
            </div>
            
            <?php endif; ?>
        </section>
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

/* PC: 3列グリッドレイアウト */
@media (min-width: 769px) {
    .junk-ranking {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 30px;
    }
}

.junk-ranking-item {
    display: flex;
    flex-direction: column;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
    position: relative;
}

.junk-ranking-item:hover {
    box-shadow: 0 8px 24px rgba(0,0,0,0.1);
    transform: translateY(-4px);
}

.rank {
    position: absolute;
    top: 15px;
    left: 15px;
    width: 60px;
    height: 60px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 10px;
    color: white;
    font-weight: bold;
    z-index: 10;
}

.rank-number {
    font-size: 28px;
    line-height: 1;
}

.rank-label {
    font-size: 12px;
    margin-top: 2px;
}

.company-content {
    display: flex;
    flex-direction: column;
    height: 100%;
}

.logo {
    width: 100%;
    height: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
    padding: 15px;
    background: #f9fafb;
    border-radius: 8px;
}

.logo img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    border-radius: 6px;
}

.company-name {
    font-size: 18px;
    margin: 0 0 12px 0;
    color: #1a202c;
    font-weight: 600;
    min-height: 48px;
}

.rating {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 6px;
    margin-bottom: 12px;
    font-size: 14px;
}

.stars {
    color: #fbbf24;
    font-size: 18px;
}

.rating-number {
    font-weight: bold;
    color: #1a202c;
}

.rating-text {
    color: #6b7280;
    font-size: 13px;
}

.review-count {
    color: #9ca3af;
    font-size: 13px;
}

.summary {
    font-size: 14px;
    line-height: 1.7;
    color: #4b5563;
    margin-bottom: 15px;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.service, .area, .price, .recommend {
    margin-bottom: 15px;
}

.service h4, .area h4, .price h4, .recommend h4 {
    font-size: 14px;
    font-weight: bold;
    color: #1a202c;
    margin: 0 0 6px 0;
    padding-bottom: 6px;
    border-bottom: 2px solid #667eea;
}

.service p, .area p, .price p, .recommend p {
    font-size: 13px;
    line-height: 1.6;
    color: #4b5563;
    margin: 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.official-link {
    display: block;
    text-align: center;
    margin-top: auto;
    padding: 12px 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: bold;
    font-size: 14px;
    transition: all 0.3s ease;
}

.official-link:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

/* SEOコンテンツセクション */
.seo-content {
    margin-top: 60px;
    padding-top: 60px;
    border-top: 3px solid #667eea;
}

.seo-section {
    margin-bottom: 50px;
    background: white;
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.seo-section h2 {
    font-size: 28px;
    color: #1a202c;
    margin: 0 0 25px 0;
    padding-bottom: 15px;
    border-bottom: 3px solid #667eea;
}

.seo-section h3 {
    font-size: 20px;
    color: #1a202c;
    margin: 20px 0 10px 0;
}

.seo-section p {
    font-size: 16px;
    line-height: 1.9;
    color: #4b5563;
    margin-bottom: 15px;
}

.checklist {
    list-style: none;
    padding: 0;
    margin: 20px 0;
}

.checklist li {
    padding: 15px 15px 15px 45px;
    margin-bottom: 12px;
    background: #f0f9ff;
    border-left: 4px solid #667eea;
    border-radius: 6px;
    position: relative;
    font-size: 16px;
    line-height: 1.8;
}

.checklist li:before {
    content: "✓";
    position: absolute;
    left: 15px;
    top: 15px;
    color: #667eea;
    font-weight: bold;
    font-size: 20px;
}

.recommend-list {
    list-style: none;
    padding: 0;
    margin: 20px 0;
}

.recommend-list li {
    padding: 20px;
    margin-bottom: 15px;
    background: #fef3c7;
    border-left: 5px solid #f59e0b;
    border-radius: 8px;
    font-size: 15px;
    line-height: 1.8;
}

.recommend-list li strong {
    display: block;
    font-size: 17px;
    color: #1a202c;
    margin-bottom: 8px;
}

.tips-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
    margin-top: 25px;
}

.tip-card {
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    padding: 25px;
    border-radius: 10px;
    border: 2px solid #bae6fd;
}

.tip-card h3 {
    font-size: 18px;
    color: #1a202c;
    margin: 0 0 12px 0;
}

.tip-card p {
    font-size: 15px;
    line-height: 1.7;
    color: #4b5563;
    margin: 0;
}

.price-table {
    margin-top: 25px;
}

.price-table table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.price-table thead {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.price-table th {
    padding: 18px 15px;
    text-align: left;
    font-weight: bold;
    font-size: 16px;
}

.price-table td {
    padding: 18px 15px;
    border-bottom: 1px solid #e5e7eb;
    font-size: 15px;
    color: #4b5563;
}

.price-table tbody tr:last-child td {
    border-bottom: none;
}

.price-table tbody tr:hover {
    background: #f9fafb;
}

.price-table .note {
    margin-top: 15px;
    font-size: 14px;
    color: #6b7280;
    font-style: italic;
}

.summary-section {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    border: 3px solid #f59e0b;
}

.cta-box {
    margin-top: 30px;
    padding: 30px;
    background: white;
    border-radius: 10px;
    text-align: center;
}

.cta-text {
    font-size: 17px;
    color: #1a202c;
    margin-bottom: 20px;
    font-weight: 500;
}

.cta-button {
    display: inline-block;
    padding: 15px 40px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    text-decoration: none;
    border-radius: 50px;
    font-weight: bold;
    font-size: 16px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

.cta-button:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
}

@media (max-width: 768px) {
    .seo-section {
        padding: 25px 20px;
    }
    
    .seo-section h2 {
        font-size: 22px;
    }
    
    .tips-grid {
        grid-template-columns: 1fr;
    }
    
    .price-table table {
        font-size: 14px;
    }
    
    .price-table th,
    .price-table td {
        padding: 12px 10px;
    }
    
    .prefecture-links {
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    }
    
    .junk-ranking-item {
        padding: 20px;
        margin-bottom: 20px;
    }
    
    .rank {
        width: 50px;
        height: 50px;
        top: 10px;
        left: 10px;
    }
    
    .rank-number {
        font-size: 24px;
    }
    
    .rank-label {
        font-size: 10px;
    }
    
    .logo {
        height: 100px;
        margin-bottom: 15px;
    }
    
    .company-name {
        font-size: 16px;
        min-height: auto;
    }
    
    .rating {
        font-size: 13px;
    }
    
    .summary {
        font-size: 13px;
        -webkit-line-clamp: 4;
    }
    
    .service h4, .area h4, .price h4, .recommend h4 {
        font-size: 13px;
    }
    
    .service p, .area p, .price p, .recommend p {
        font-size: 12px;
        -webkit-line-clamp: 3;
    }
    
    .official-link {
        padding: 10px 16px;
        font-size: 13px;
    }
}

/* もっと見るボタン */
.load-more-container {
    text-align: center;
    margin: 40px 0;
    grid-column: 1 / -1;
}

.load-more-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 16px 48px;
    font-size: 16px;
    font-weight: 600;
    border-radius: 50px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

.load-more-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
}

.load-more-btn:active {
    transform: translateY(0);
}

.load-more-btn.hidden {
    display: none;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loadMoreBtn = document.getElementById('loadMoreBtn');
    
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function() {
            const loaded = parseInt(this.getAttribute('data-loaded'));
            const total = parseInt(this.getAttribute('data-total'));
            const nextLoad = Math.min(loaded + 5, total);
            
            // 次の5件を表示
            for (let i = loaded + 1; i <= nextLoad; i++) {
                const item = document.querySelector('.junk-ranking-item[data-index="' + i + '"]');
                if (item) {
                    item.style.display = 'flex';
                    // フェードインアニメーション
                    item.style.opacity = '0';
                    setTimeout(function() {
                        item.style.transition = 'opacity 0.5s ease';
                        item.style.opacity = '1';
                    }, 10);
                }
            }
            
            // ボタンのテキストを更新
            this.setAttribute('data-loaded', nextLoad);
            const remaining = total - nextLoad;
            
            if (remaining > 0) {
                this.textContent = 'もっと見る（残り' + remaining + '件）';
            } else {
                this.textContent = 'すべて表示しました';
                this.disabled = true;
                this.style.opacity = '0.5';
                this.style.cursor = 'not-allowed';
                // 2秒後にボタンを非表示
                setTimeout(function() {
                    loadMoreBtn.style.transition = 'opacity 0.3s ease';
                    loadMoreBtn.style.opacity = '0';
                    setTimeout(function() {
                        loadMoreBtn.style.display = 'none';
                    }, 300);
                }, 2000);
            }
        });
    }
});
</script>

<?php get_footer(); ?>
