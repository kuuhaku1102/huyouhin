<?php
/**
 * Template Name: Prefecture Archive
 * Description: 都道府県別の業者一覧ページ
 */

$term = get_queried_object();
$prefecture_name = $term->name;
$region = $term->description;

get_header();
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc_html($prefecture_name); ?>の不用品回収業者 | <?php bloginfo('name'); ?></title>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<!-- ヘッダー -->
<header class="site-header">
    <div class="container">
        <div class="header-content">
            <h1 class="site-title">
                <a href="<?php echo home_url(); ?>">不用品回収比較センター</a>
            </h1>
            <nav class="main-nav">
                <a href="<?php echo home_url(); ?>">トップ</a>
                <a href="<?php echo home_url('/rankings'); ?>">ランキング</a>
                <a href="<?php echo home_url('/quote'); ?>">見積もり</a>
            </nav>
        </div>
    </div>
</header>

<!-- パンくずリスト -->
<div class="breadcrumb">
    <div class="container">
        <a href="<?php echo home_url(); ?>">ホーム</a> &gt;
        <span><?php echo esc_html($region); ?></span> &gt;
        <span><?php echo esc_html($prefecture_name); ?></span>
    </div>
</div>

<!-- メインコンテンツ -->
<main class="site-main prefecture-archive">
    <div class="container">
        
        <!-- ページヘッダー -->
        <div class="page-header">
            <h1 class="page-title"><?php echo esc_html($prefecture_name); ?>の不用品回収業者</h1>
            <p class="page-description">
                <?php echo esc_html($prefecture_name); ?>で信頼できる優良で格安の不用品回収業者を探せます。
                一括見積もりで複数の業者を比較して、最適な業者を見つけましょう。
            </p>
        </div>

        <!-- 一括見積もりCTA -->
        <div class="cta-box">
            <h2>🎯 <?php echo esc_html($prefecture_name); ?>の業者に一括見積もり</h2>
            <p>30秒で簡単！<?php echo esc_html($prefecture_name); ?>対応の複数業者から無料で見積もりが取れます</p>
            <a href="<?php echo home_url('/quote'); ?>" class="btn btn-primary btn-large">
                無料で一括見積もりをする
            </a>
        </div>

        <!-- 業者一覧 -->
        <section class="companies-section">
            <h2 class="section-title"><?php echo esc_html($prefecture_name); ?>の不用品回収業者一覧</h2>
            
            <?php
            $args = array(
                'post_type' => 'company',
                'posts_per_page' => -1,
                'tax_query' => array(
                    array(
                        'taxonomy' => 'prefecture',
                        'field' => 'slug',
                        'terms' => $term->slug,
                    ),
                ),
                'meta_key' => '_ranking_position',
                'orderby' => 'meta_value_num',
                'order' => 'ASC',
            );
            
            $companies = new WP_Query($args);
            
            if ($companies->have_posts()) :
            ?>
                <div class="companies-grid">
                    <?php 
                    $rank = 1;
                    while ($companies->have_posts()) : $companies->the_post();
                        $rating = get_post_meta(get_the_ID(), '_company_rating', true);
                        $price_range = get_post_meta(get_the_ID(), '_company_price_range', true);
                        $phone = get_post_meta(get_the_ID(), '_company_phone', true);
                        $website = get_post_meta(get_the_ID(), '_company_website', true);
                        $areas = get_post_meta(get_the_ID(), '_company_areas', true);
                    ?>
                        <div class="company-card">
                            <?php if ($rank <= 3) : ?>
                                <div class="rank-badge rank-<?php echo $rank; ?>">
                                    <span class="rank-number"><?php echo $rank; ?></span>
                                    <span class="rank-label">位</span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="company-image">
                                    <?php the_post_thumbnail('medium'); ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="company-info">
                                <h3 class="company-name"><?php the_title(); ?></h3>
                                
                                <?php if ($rating) : ?>
                                    <div class="company-rating">
                                        <span class="stars">
                                            <?php 
                                            for ($i = 1; $i <= 5; $i++) {
                                                echo $i <= $rating ? '★' : '☆';
                                            }
                                            ?>
                                        </span>
                                        <span class="rating-number"><?php echo number_format($rating, 1); ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($price_range) : ?>
                                    <div class="company-price">
                                        <span class="label">料金目安:</span>
                                        <span class="value"><?php echo esc_html($price_range); ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="company-description">
                                    <?php echo wp_trim_words(get_the_excerpt(), 50, '...'); ?>
                                </div>
                                
                                <?php if ($areas) : ?>
                                    <div class="company-areas">
                                        <span class="label">対応エリア:</span>
                                        <span class="value"><?php echo esc_html($areas); ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="company-actions">
                                    <?php if ($website) : ?>
                                        <a href="<?php echo esc_url($website); ?>" class="btn btn-outline" target="_blank" rel="noopener">
                                            公式サイト
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($phone) : ?>
                                        <a href="tel:<?php echo esc_attr($phone); ?>" class="btn btn-primary">
                                            📞 <?php echo esc_html($phone); ?>
                                        </a>
                                    <?php endif; ?>
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
                <div class="no-companies">
                    <p>現在、<?php echo esc_html($prefecture_name); ?>の業者情報はまだ登録されていません。</p>
                    <p>一括見積もりフォームからお問い合わせいただくと、対応可能な業者をご紹介いたします。</p>
                    <a href="<?php echo home_url('/quote'); ?>" class="btn btn-primary">
                        一括見積もりをする
                    </a>
                </div>
            <?php endif; ?>
        </section>

        <!-- <?php echo esc_html($prefecture_name); ?>の不用品回収について -->
        <section class="prefecture-info">
            <h2 class="section-title"><?php echo esc_html($prefecture_name); ?>で不用品回収業者を選ぶポイント</h2>
            
            <div class="info-grid">
                <div class="info-card">
                    <h3>✅ 許可証の確認</h3>
                    <p>一般廃棄物収集運搬業許可または産業廃棄物収集運搬業許可を持っている業者を選びましょう。</p>
                </div>
                
                <div class="info-card">
                    <h3>💰 明確な料金体系</h3>
                    <p>追加料金の有無、キャンセル料など、料金体系が明確な業者を選びましょう。</p>
                </div>
                
                <div class="info-card">
                    <h3>⭐ 口コミ・評判</h3>
                    <p>実際に利用した人の口コミや評判を確認して、信頼できる業者を選びましょう。</p>
                </div>
                
                <div class="info-card">
                    <h3>📞 対応の早さ</h3>
                    <p>見積もりや問い合わせへの対応が早い業者は、作業もスムーズに進みます。</p>
                </div>
            </div>
        </section>

        <!-- 料金相場 -->
        <section class="pricing-section">
            <h2 class="section-title"><?php echo esc_html($prefecture_name); ?>の不用品回収 料金相場</h2>
            
            <div class="pricing-table-wrapper">
                <table class="pricing-table">
                    <thead>
                        <tr>
                            <th>物量目安</th>
                            <th>間取り</th>
                            <th>料金相場</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>単品</td>
                            <td>-</td>
                            <td>3,000円〜5,000円</td>
                        </tr>
                        <tr>
                            <td>大型家具・家電1個・小物3個</td>
                            <td>1R〜1K</td>
                            <td>5,000円〜10,000円</td>
                        </tr>
                        <tr>
                            <td>大型家具・家電3個・小物5個</td>
                            <td>1DK〜1LDK</td>
                            <td>15,000円〜30,000円</td>
                        </tr>
                        <tr>
                            <td>大型家具・家電5個・小物10個</td>
                            <td>2K〜2LDK</td>
                            <td>30,000円〜80,000円</td>
                        </tr>
                        <tr>
                            <td>大型家具・家電10個・小物10個</td>
                            <td>3DK〜3LDK</td>
                            <td>100,000円〜150,000円</td>
                        </tr>
                        <tr>
                            <td>大型家具・家電10個・小物20個</td>
                            <td>一軒家一棟</td>
                            <td>150,000円〜</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <p class="pricing-note">
                ※上記は目安です。実際の料金は物量や作業内容により異なります。<br>
                正確な料金は一括見積もりでご確認ください。
            </p>
        </section>

        <!-- 他の都道府県へのリンク -->
        <section class="other-prefectures">
            <h2 class="section-title">他の都道府県から探す</h2>
            
            <?php
            $regions = array(
                '北海道地方' => array(),
                '東北地方' => array(),
                '関東地方' => array(),
                '中部地方' => array(),
                '関西地方' => array(),
                '中国地方' => array(),
                '四国地方' => array(),
                '九州・沖縄地方' => array(),
            );
            
            $all_prefectures = get_terms(array(
                'taxonomy' => 'prefecture',
                'hide_empty' => false,
            ));
            
            foreach ($all_prefectures as $pref) {
                if (isset($regions[$pref->description])) {
                    $regions[$pref->description][] = $pref;
                }
            }
            
            foreach ($regions as $region_name => $prefs) :
                if (empty($prefs)) continue;
            ?>
                <div class="region-group">
                    <h3><?php echo esc_html($region_name); ?></h3>
                    <div class="prefecture-links">
                        <?php foreach ($prefs as $pref) : ?>
                            <a href="<?php echo get_term_link($pref); ?>" class="prefecture-link">
                                <?php echo esc_html($pref->name); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </section>

    </div>
</main>

<!-- フッター -->
<footer class="site-footer">
    <div class="container">
        <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. All rights reserved.</p>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
