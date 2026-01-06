<?php
/**
 * 不用品回収業者比較サイト - Functions
 * Theme Name: My Simple Theme
 */

// ===========================
// カスタム投稿タイプの登録
// ===========================

function fuyohin_register_post_types() {
    // 業者情報
    register_post_type('company', array(
        'labels' => array(
            'name' => '業者情報',
            'singular_name' => '業者',
            'add_new' => '新規追加',
            'add_new_item' => '新しい業者を追加',
            'edit_item' => '業者を編集',
            'view_item' => '業者を表示',
            'search_items' => '業者を検索',
        ),
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-building',
        'supports' => array('title', 'editor', 'thumbnail'),
        'rewrite' => array('slug' => 'companies'),
        'show_in_rest' => true,
    ));

    // 見積もり依頼
    register_post_type('quote_request', array(
        'labels' => array(
            'name' => '見積もり依頼',
            'singular_name' => '見積もり',
            'view_item' => '見積もりを表示',
            'search_items' => '見積もりを検索',
        ),
        'public' => false,
        'show_ui' => true,
        'menu_icon' => 'dashicons-email',
        'supports' => array('title'),
        'capability_type' => 'post',
        'capabilities' => array(
            'create_posts' => 'do_not_allow',
        ),
        'map_meta_cap' => true,
    ));

    // コラム
    register_post_type('column', array(
        'labels' => array(
            'name' => 'コラム',
            'singular_name' => 'コラム',
            'add_new' => '新規追加',
            'add_new_item' => '新しいコラムを追加',
            'edit_item' => 'コラムを編集',
        ),
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-media-document',
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
        'rewrite' => array('slug' => 'columns'),
        'show_in_rest' => true,
        'taxonomies' => array('column_category'), // カテゴリタクソノミーを追加
    ));

    // コラムカテゴリタクソノミー
    register_taxonomy('column_category', 'column', array(
        'labels' => array(
            'name' => 'コラムカテゴリ',
            'singular_name' => 'コラムカテゴリ',
            'search_items' => 'カテゴリを検索',
            'all_items' => 'すべてのカテゴリ',
            'edit_item' => 'カテゴリを編集',
            'update_item' => 'カテゴリを更新',
            'add_new_item' => '新しいカテゴリを追加',
        ),
        'hierarchical' => true, // 階層構造を有効化
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'column-category'),
        'show_in_rest' => true,
    ));

    // 都道府県タクソノミー
register_taxonomy('prefecture', 'company', array(
    'labels' => array(
        'name' => '都道府県',
        'singular_name' => '都道府県',
        'search_items' => '都道府県を検索',
        'all_items' => 'すべての都道府県',
        'edit_item' => '都道府県を編集',
        'update_item' => '都道府県を更新',
        'add_new_item' => '新しい都道府県を追加',
    ),
    'hierarchical' => true,
    'show_ui' => true,
    'show_admin_column' => true,
    'query_var' => true,
    'rewrite' => array('slug' => 'area'),
    'show_in_rest' => true,
));

register_post_type('review', array(
        'labels' => array(
            'name' => '口コミ',
            'singular_name' => '口コミ',
            'add_new' => '新規追加',
            'edit_item' => '口コミを編集',
        ),
        'public' => false,
        'show_ui' => true,
        'menu_icon' => 'dashicons-star-filled',
        'supports' => array('title', 'editor'),
    ));
}
add_action('init', 'fuyohin_register_post_types');

// ===========================
// カスタムフィールドの登録
// ===========================

function fuyohin_add_meta_boxes() {
    // 業者情報のメタボックス
    add_meta_box(
        'company_details',
        '業者詳細情報',
        'fuyohin_company_meta_box_callback',
        'company',
        'normal',
        'high'
    );

    // 見積もり依頼のメタボックス
    add_meta_box(
        'quote_details',
        '依頼詳細',
        'fuyohin_quote_meta_box_callback',
        'quote_request',
        'normal',
        'high'
    );

    // 口コミのメタボックス
    add_meta_box(
        'review_details',
        '口コミ詳細',
        'fuyohin_review_meta_box_callback',
        'review',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'fuyohin_add_meta_boxes');

// 業者情報メタボックスのコールバック
function fuyohin_company_meta_box_callback($post) {
    wp_nonce_field('fuyohin_company_meta', 'fuyohin_company_nonce');
    
    $rating = get_post_meta($post->ID, '_company_rating', true);
    $price_range = get_post_meta($post->ID, '_company_price_range', true);
    $phone = get_post_meta($post->ID, '_company_phone', true);
    $email = get_post_meta($post->ID, '_company_email', true);
    $website = get_post_meta($post->ID, '_company_website', true);
    $areas = get_post_meta($post->ID, '_company_areas', true);
    $ranking_position = get_post_meta($post->ID, '_ranking_position', true);
    $ranking_category = get_post_meta($post->ID, '_ranking_category', true);
    ?>
    <table class="form-table">
        <tr>
            <th><label for="company_rating">評価（1-5）</label></th>
            <td><input type="number" id="company_rating" name="company_rating" value="<?php echo esc_attr($rating); ?>" min="1" max="5" step="0.1" style="width: 100px;"></td>
        </tr>
        <tr>
            <th><label for="company_price_range">料金帯</label></th>
            <td><input type="text" id="company_price_range" name="company_price_range" value="<?php echo esc_attr($price_range); ?>" style="width: 100%;" placeholder="例: 10,000円〜50,000円"></td>
        </tr>
        <tr>
            <th><label for="company_phone">電話番号</label></th>
            <td><input type="text" id="company_phone" name="company_phone" value="<?php echo esc_attr($phone); ?>" style="width: 100%;"></td>
        </tr>
        <tr>
            <th><label for="company_email">メールアドレス</label></th>
            <td><input type="email" id="company_email" name="company_email" value="<?php echo esc_attr($email); ?>" style="width: 100%;"></td>
        </tr>
        <tr>
            <th><label for="company_website">ウェブサイト</label></th>
            <td><input type="url" id="company_website" name="company_website" value="<?php echo esc_attr($website); ?>" style="width: 100%;"></td>
        </tr>
        <tr>
            <th><label for="company_areas">対応エリア</label></th>
            <td><textarea id="company_areas" name="company_areas" rows="3" style="width: 100%;"><?php echo esc_textarea($areas); ?></textarea>
            <p class="description">カンマ区切りで入力（例: 東京都, 神奈川県, 埼玉県）</p></td>
        </tr>
        <tr>
            <th><label for="ranking_position">ランキング順位</label></th>
            <td><input type="number" id="ranking_position" name="ranking_position" value="<?php echo esc_attr($ranking_position); ?>" min="1" style="width: 100px;"></td>
        </tr>
        <tr>
            <th><label for="ranking_category">ランキングカテゴリ</label></th>
            <td>
                <select id="ranking_category" name="ranking_category" style="width: 200px;">
                    <option value="">選択してください</option>
                    <option value="overall" <?php selected($ranking_category, 'overall'); ?>>総合</option>
                    <option value="price" <?php selected($ranking_category, 'price'); ?>>料金</option>
                    <option value="speed" <?php selected($ranking_category, 'speed'); ?>>対応速度</option>
                    <option value="service" <?php selected($ranking_category, 'service'); ?>>サービス品質</option>
                </select>
            </td>
        </tr>
    </table>
    <?php
}

// 見積もり依頼メタボックスのコールバック
function fuyohin_quote_meta_box_callback($post) {
    $service_type = get_post_meta($post->ID, '_service_type', true);
    $volume_type = get_post_meta($post->ID, '_volume_type', true);
    $postal_code = get_post_meta($post->ID, '_postal_code', true);
    $name = get_post_meta($post->ID, '_customer_name', true);
    $phone = get_post_meta($post->ID, '_customer_phone', true);
    $email = get_post_meta($post->ID, '_customer_email', true);
    $preferred_date = get_post_meta($post->ID, '_preferred_date', true);
    $notes = get_post_meta($post->ID, '_notes', true);
    $status = get_post_meta($post->ID, '_status', true);
    ?>
    <table class="form-table">
        <tr>
            <th>サービス種別</th>
            <td><?php echo esc_html($service_type); ?></td>
        </tr>
        <tr>
            <th>物量</th>
            <td><?php echo esc_html($volume_type); ?></td>
        </tr>
        <tr>
            <th>郵便番号</th>
            <td><?php echo esc_html($postal_code); ?></td>
        </tr>
        <tr>
            <th>お名前</th>
            <td><?php echo esc_html($name); ?></td>
        </tr>
        <tr>
            <th>電話番号</th>
            <td><?php echo esc_html($phone); ?></td>
        </tr>
        <tr>
            <th>メールアドレス</th>
            <td><?php echo esc_html($email); ?></td>
        </tr>
        <tr>
            <th>希望日時</th>
            <td><?php echo esc_html($preferred_date); ?></td>
        </tr>
        <tr>
            <th>備考</th>
            <td><?php echo nl2br(esc_html($notes)); ?></td>
        </tr>
        <tr>
            <th><label for="quote_status">ステータス</label></th>
            <td>
                <select id="quote_status" name="quote_status" style="width: 200px;">
                    <option value="pending" <?php selected($status, 'pending'); ?>>未対応</option>
                    <option value="contacted" <?php selected($status, 'contacted'); ?>>連絡済み</option>
                    <option value="completed" <?php selected($status, 'completed'); ?>>完了</option>
                    <option value="cancelled" <?php selected($status, 'cancelled'); ?>>キャンセル</option>
                </select>
            </td>
        </tr>
    </table>
    <?php
}

// 口コミメタボックスのコールバック
function fuyohin_review_meta_box_callback($post) {
    wp_nonce_field('fuyohin_review_meta', 'fuyohin_review_nonce');
    
    $company_id = get_post_meta($post->ID, '_review_company_id', true);
    $rating = get_post_meta($post->ID, '_review_rating', true);
    $customer_name = get_post_meta($post->ID, '_review_customer_name', true);
    $approved = get_post_meta($post->ID, '_review_approved', true);
    
    $companies = get_posts(array('post_type' => 'company', 'posts_per_page' => -1));
    ?>
    <table class="form-table">
        <tr>
            <th><label for="review_company">業者</label></th>
            <td>
                <select id="review_company" name="review_company" style="width: 100%;">
                    <option value="">選択してください</option>
                    <?php foreach ($companies as $company): ?>
                        <option value="<?php echo $company->ID; ?>" <?php selected($company_id, $company->ID); ?>>
                            <?php echo esc_html($company->post_title); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="review_rating">評価（1-5）</label></th>
            <td><input type="number" id="review_rating" name="review_rating" value="<?php echo esc_attr($rating); ?>" min="1" max="5" step="1" style="width: 100px;"></td>
        </tr>
        <tr>
            <th><label for="review_customer_name">投稿者名</label></th>
            <td><input type="text" id="review_customer_name" name="review_customer_name" value="<?php echo esc_attr($customer_name); ?>" style="width: 100%;"></td>
        </tr>
        <tr>
            <th><label for="review_approved">承認状態</label></th>
            <td>
                <label>
                    <input type="checkbox" id="review_approved" name="review_approved" value="1" <?php checked($approved, '1'); ?>>
                    承認済み
                </label>
            </td>
        </tr>
    </table>
    <?php
}

// メタデータの保存
function fuyohin_save_meta_boxes($post_id) {
    // 自動保存の場合は処理しない
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    
    // 権限チェック
    if (!current_user_can('edit_post', $post_id)) return;

    // 業者情報の保存
    if (isset($_POST['fuyohin_company_nonce']) && wp_verify_nonce($_POST['fuyohin_company_nonce'], 'fuyohin_company_meta')) {
        if (isset($_POST['company_rating'])) {
            update_post_meta($post_id, '_company_rating', sanitize_text_field($_POST['company_rating']));
        }
        if (isset($_POST['company_price_range'])) {
            update_post_meta($post_id, '_company_price_range', sanitize_text_field($_POST['company_price_range']));
        }
        if (isset($_POST['company_phone'])) {
            update_post_meta($post_id, '_company_phone', sanitize_text_field($_POST['company_phone']));
        }
        if (isset($_POST['company_email'])) {
            update_post_meta($post_id, '_company_email', sanitize_email($_POST['company_email']));
        }
        if (isset($_POST['company_website'])) {
            update_post_meta($post_id, '_company_website', esc_url_raw($_POST['company_website']));
        }
        if (isset($_POST['company_areas'])) {
            update_post_meta($post_id, '_company_areas', sanitize_textarea_field($_POST['company_areas']));
        }
        if (isset($_POST['ranking_position'])) {
            update_post_meta($post_id, '_ranking_position', intval($_POST['ranking_position']));
        }
        if (isset($_POST['ranking_category'])) {
            update_post_meta($post_id, '_ranking_category', sanitize_text_field($_POST['ranking_category']));
        }
    }

    // 見積もり依頼の保存
    if (isset($_POST['quote_status'])) {
        update_post_meta($post_id, '_status', sanitize_text_field($_POST['quote_status']));
    }

    // 口コミの保存
    if (isset($_POST['fuyohin_review_nonce']) && wp_verify_nonce($_POST['fuyohin_review_nonce'], 'fuyohin_review_meta')) {
        if (isset($_POST['review_company'])) {
            update_post_meta($post_id, '_review_company_id', intval($_POST['review_company']));
        }
        if (isset($_POST['review_rating'])) {
            update_post_meta($post_id, '_review_rating', intval($_POST['review_rating']));
        }
        if (isset($_POST['review_customer_name'])) {
            update_post_meta($post_id, '_review_customer_name', sanitize_text_field($_POST['review_customer_name']));
        }
        $approved = isset($_POST['review_approved']) ? '1' : '0';
        update_post_meta($post_id, '_review_approved', $approved);
    }
}
add_action('save_post', 'fuyohin_save_meta_boxes');

// ===========================
// 見積もりフォーム処理
// ===========================

function fuyohin_handle_quote_form() {
    check_ajax_referer('fuyohin_quote_nonce', 'nonce');

    $service_type = sanitize_text_field($_POST['service_type']);
    $volume_type = sanitize_text_field($_POST['volume_type']);
    $postal_code = sanitize_text_field($_POST['postal_code']);
    $name = sanitize_text_field($_POST['name']);
    $phone = sanitize_text_field($_POST['phone']);
    $email = sanitize_email($_POST['email']);
    $preferred_date = sanitize_text_field($_POST['preferred_date']);
    $notes = sanitize_textarea_field($_POST['notes']);

    // 見積もり依頼を作成
    $post_id = wp_insert_post(array(
        'post_type' => 'quote_request',
        'post_title' => sprintf('見積もり依頼 - %s - %s', $name, date('Y/m/d H:i')),
        'post_status' => 'publish',
    ));

    if ($post_id) {
        update_post_meta($post_id, '_service_type', $service_type);
        update_post_meta($post_id, '_volume_type', $volume_type);
        update_post_meta($post_id, '_postal_code', $postal_code);
        update_post_meta($post_id, '_customer_name', $name);
        update_post_meta($post_id, '_customer_phone', $phone);
        update_post_meta($post_id, '_customer_email', $email);
        update_post_meta($post_id, '_preferred_date', $preferred_date);
        update_post_meta($post_id, '_notes', $notes);
        update_post_meta($post_id, '_status', 'pending');

        wp_send_json_success(array('message' => '見積もり依頼を受け付けました。'));
    } else {
        wp_send_json_error(array('message' => 'エラーが発生しました。'));
    }
}
add_action('wp_ajax_submit_quote', 'fuyohin_handle_quote_form');
add_action('wp_ajax_nopriv_submit_quote', 'fuyohin_handle_quote_form');

// ===========================
// テーマサポート
// ===========================

function fuyohin_theme_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');
    add_theme_support('html5', ['search-form', 'gallery', 'caption', 'script', 'style']);
    
    register_nav_menus(array(
        'primary' => 'メインメニュー',
        'footer' => 'フッターメニュー',
    ));
}
add_action('after_setup_theme', 'fuyohin_theme_setup');

// ===========================
// スクリプトとスタイルの読み込み
// ===========================

function fuyohin_enqueue_scripts() {
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap', array(), null);
    wp_enqueue_style('theme-style', get_stylesheet_uri(), array(), filemtime(get_template_directory() . '/style.css'));
    
    wp_enqueue_script('jquery');
    
    if (file_exists(get_template_directory() . '/js/main.js')) {
        wp_enqueue_script('theme-script', get_template_directory_uri() . '/js/main.js', array('jquery'), '1.0.0', true);
        
        wp_localize_script('theme-script', 'fuyohinAjax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('fuyohin_quote_nonce'),
        ));
    }
}
add_action('wp_enqueue_scripts', 'fuyohin_enqueue_scripts');

// ===========================
// ランキング管理画面
// ===========================

function fuyohin_add_ranking_management_page() {
    add_submenu_page(
        'edit.php?post_type=company',
        'ランキング管理',
        'ランキング管理',
        'manage_options',
        'ranking-management',
        'fuyohin_ranking_management_page'
    );
}
add_action('admin_menu', 'fuyohin_add_ranking_management_page');

function fuyohin_ranking_management_page() {
    // カテゴリ選択
    $current_category = isset($_GET['category']) ? sanitize_text_field($_GET['category']) : 'overall';
    
    // ランキング保存処理
    if (isset($_POST['save_ranking']) && check_admin_referer('save_ranking_order', 'ranking_nonce')) {
        $ranking_data = json_decode(stripslashes($_POST['ranking_order']), true);
        
        if (is_array($ranking_data)) {
            foreach ($ranking_data as $index => $company_id) {
                update_post_meta($company_id, '_ranking_position', $index + 1);
                update_post_meta($company_id, '_ranking_category', $current_category);
            }
            echo '<div class="notice notice-success"><p>ランキングを保存しました。</p></div>';
        }
    }
    
    // 現在のランキング取得
    $args = array(
        'post_type' => 'company',
        'posts_per_page' => -1,
        'meta_key' => '_ranking_position',
        'orderby' => 'meta_value_num',
        'order' => 'ASC',
        'meta_query' => array(
            array(
                'key' => '_ranking_category',
                'value' => $current_category,
            ),
        ),
    );
    $ranked_companies = new WP_Query($args);
    
    // ランキングに含まれていない業者
    $all_companies = get_posts(array(
        'post_type' => 'company',
        'posts_per_page' => -1,
        'post__not_in' => wp_list_pluck($ranked_companies->posts, 'ID'),
    ));
    
    ?>
    <div class="wrap">
        <h1>ランキング管理</h1>
        
        <!-- カテゴリタブ -->
        <h2 class="nav-tab-wrapper">
            <a href="?post_type=company&page=ranking-management&category=overall" class="nav-tab <?php echo $current_category === 'overall' ? 'nav-tab-active' : ''; ?>">総合ランキング</a>
            <a href="?post_type=company&page=ranking-management&category=price" class="nav-tab <?php echo $current_category === 'price' ? 'nav-tab-active' : ''; ?>">料金が安い</a>
            <a href="?post_type=company&page=ranking-management&category=speed" class="nav-tab <?php echo $current_category === 'speed' ? 'nav-tab-active' : ''; ?>">対応が早い</a>
            <a href="?post_type=company&page=ranking-management&category=service" class="nav-tab <?php echo $current_category === 'service' ? 'nav-tab-active' : ''; ?>">サービス品質</a>
        </h2>
        
        <div style="margin-top: 20px;">
            <p>ドラッグ&ドロップで業者の順位を変更できます。</p>
            
            <form method="post" id="ranking-form">
                <?php wp_nonce_field('save_ranking_order', 'ranking_nonce'); ?>
                <input type="hidden" name="save_ranking" value="1">
                <input type="hidden" name="ranking_order" id="ranking-order-input">
                
                <div style="display: flex; gap: 40px; margin-top: 20px;">
                    <!-- 現在のランキング -->
                    <div style="flex: 1;">
                        <h3>現在のランキング</h3>
                        <ul id="ranking-list" style="list-style: none; padding: 0; min-height: 100px; background: #f9f9f9; border: 1px solid #ddd; border-radius: 4px; padding: 10px;">
                            <?php 
                            if ($ranked_companies->have_posts()) :
                                $rank = 1;
                                while ($ranked_companies->have_posts()) : $ranked_companies->the_post();
                                    $rating = get_post_meta(get_the_ID(), '_company_rating', true);
                            ?>
                                <li class="ranking-item" data-id="<?php echo get_the_ID(); ?>" style="background: white; padding: 15px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 4px; cursor: move; display: flex; align-items: center; gap: 10px;">
                                    <span class="rank-number" style="font-size: 24px; font-weight: bold; color: #0073aa; min-width: 40px;"><?php echo $rank; ?></span>
                                    <div style="flex: 1;">
                                        <strong><?php the_title(); ?></strong>
                                        <?php if ($rating) : ?>
                                            <span style="color: #fbbf24; margin-left: 10px;">★ <?php echo number_format($rating, 1); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <span class="dashicons dashicons-menu" style="color: #999;"></span>
                                </li>
                            <?php 
                                $rank++;
                                endwhile;
                                wp_reset_postdata();
                            else :
                            ?>
                                <li style="padding: 20px; text-align: center; color: #999;">まだランキングに業者が登録されていません。</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    
                    <!-- 未ランクの業者 -->
                    <div style="flex: 1;">
                        <h3>未ランクの業者</h3>
                        <ul id="unranked-list" style="list-style: none; padding: 0; min-height: 100px; background: #f9f9f9; border: 1px solid #ddd; border-radius: 4px; padding: 10px;">
                            <?php 
                            if (!empty($all_companies)) :
                                foreach ($all_companies as $company) :
                                    $rating = get_post_meta($company->ID, '_company_rating', true);
                            ?>
                                <li class="ranking-item" data-id="<?php echo $company->ID; ?>" style="background: white; padding: 15px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 4px; cursor: move; display: flex; align-items: center; gap: 10px;">
                                    <div style="flex: 1;">
                                        <strong><?php echo esc_html($company->post_title); ?></strong>
                                        <?php if ($rating) : ?>
                                            <span style="color: #fbbf24; margin-left: 10px;">★ <?php echo number_format($rating, 1); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <span class="dashicons dashicons-menu" style="color: #999;"></span>
                                </li>
                            <?php 
                                endforeach;
                            else :
                            ?>
                                <li style="padding: 20px; text-align: center; color: #999;">すべての業者がランキングに含まれています。</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
                
                <p class="submit">
                    <button type="submit" class="button button-primary button-large">ランキングを保存</button>
                </p>
            </form>
        </div>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        // Sortable.jsの代わりにjQuery UI Sortableを使用
        $('#ranking-list, #unranked-list').sortable({
            connectWith: '.ranking-item',
            placeholder: 'sortable-placeholder',
            handle: '.dashicons-menu',
            update: function(event, ui) {
                updateRankNumbers();
            }
        }).disableSelection();
        
        function updateRankNumbers() {
            $('#ranking-list .ranking-item').each(function(index) {
                $(this).find('.rank-number').text(index + 1);
            });
        }
        
        $('#ranking-form').on('submit', function() {
            var rankingOrder = [];
            $('#ranking-list .ranking-item').each(function() {
                rankingOrder.push($(this).data('id'));
            });
            $('#ranking-order-input').val(JSON.stringify(rankingOrder));
        });
    });
    </script>
    
    <style>
    .sortable-placeholder {
        background: #e3f2fd;
        border: 2px dashed #2196f3;
        height: 60px;
        margin-bottom: 10px;
        border-radius: 4px;
    }
    .ranking-item:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    </style>
    <?php
}

// 管理画面でjQuery UIを読み込む
function fuyohin_admin_enqueue_scripts($hook) {
    if ($hook === 'company_page_ranking-management') {
        wp_enqueue_script('jquery-ui-sortable');
    }
}
add_action('admin_enqueue_scripts', 'fuyohin_admin_enqueue_scripts');

// ===========================
// 47都道府県の初期データ登録
// ===========================

function fuyohin_insert_prefectures() {
    $prefectures = array(
        '北海道' => array('地方' => '北海道地方'),
        '青森県' => array('地方' => '東北地方'),
        '岩手県' => array('地方' => '東北地方'),
        '宮城県' => array('地方' => '東北地方'),
        '秋田県' => array('地方' => '東北地方'),
        '山形県' => array('地方' => '東北地方'),
        '福島県' => array('地方' => '東北地方'),
        '茨城県' => array('地方' => '関東地方'),
        '栃木県' => array('地方' => '関東地方'),
        '群馬県' => array('地方' => '関東地方'),
        '埼玉県' => array('地方' => '関東地方'),
        '千葉県' => array('地方' => '関東地方'),
        '東京都' => array('地方' => '関東地方'),
        '神奈川県' => array('地方' => '関東地方'),
        '新潟県' => array('地方' => '中部地方'),
        '富山県' => array('地方' => '中部地方'),
        '石川県' => array('地方' => '中部地方'),
        '福井県' => array('地方' => '中部地方'),
        '山梨県' => array('地方' => '中部地方'),
        '長野県' => array('地方' => '中部地方'),
        '岐阜県' => array('地方' => '中部地方'),
        '静岡県' => array('地方' => '中部地方'),
        '愛知県' => array('地方' => '中部地方'),
        '三重県' => array('地方' => '関西地方'),
        '滋賀県' => array('地方' => '関西地方'),
        '京都府' => array('地方' => '関西地方'),
        '大阪府' => array('地方' => '関西地方'),
        '兵庫県' => array('地方' => '関西地方'),
        '奈良県' => array('地方' => '関西地方'),
        '和歌山県' => array('地方' => '関西地方'),
        '鳥取県' => array('地方' => '中国地方'),
        '島根県' => array('地方' => '中国地方'),
        '岡山県' => array('地方' => '中国地方'),
        '広島県' => array('地方' => '中国地方'),
        '山口県' => array('地方' => '中国地方'),
        '徳島県' => array('地方' => '四国地方'),
        '香川県' => array('地方' => '四国地方'),
        '愛媛県' => array('地方' => '四国地方'),
        '高知県' => array('地方' => '四国地方'),
        '福岡県' => array('地方' => '九州・沖縄地方'),
        '佐賀県' => array('地方' => '九州・沖縄地方'),
        '長崎県' => array('地方' => '九州・沖縄地方'),
        '熊本県' => array('地方' => '九州・沖縄地方'),
        '大分県' => array('地方' => '九州・沖縄地方'),
        '宮崎県' => array('地方' => '九州・沖縄地方'),
        '鹿児島県' => array('地方' => '九州・沖縄地方'),
        '沖縄県' => array('地方' => '九州・沖縄地方'),
    );
    
    foreach ($prefectures as $name => $data) {
        if (!term_exists($name, 'prefecture')) {
            wp_insert_term($name, 'prefecture', array(
                'description' => $data['地方'],
            ));
        }
    }
}
add_action('init', 'fuyohin_insert_prefectures');

// ===========================
// ロゴ画像インポート機能
// ===========================

// 外部URLからメディアライブラリに画像をインポート
function import_logo_from_url($image_url, $company_id, $company_name) {
    if (empty($image_url)) {
        return false;
    }
    
    require_once(ABSPATH . 'wp-admin/includes/media.php');
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    
    // 画像をダウンロード
    $tmp = download_url($image_url);
    
    if (is_wp_error($tmp)) {
        return false;
    }
    
    $file_array = array(
        'name' => basename($image_url),
        'tmp_name' => $tmp
    );
    
    // メディアライブラリにアップロード
    $attachment_id = media_handle_sideload($file_array, 0, $company_name . ' ロゴ');
    
    @unlink($file_array['tmp_name']);
    
    if (is_wp_error($attachment_id)) {
        return false;
    }
    
    return $attachment_id;
}

// 管理画面にロゴインポートページを追加
add_action('admin_menu', 'add_logo_import_page');
function add_logo_import_page() {
    add_submenu_page(
        'edit.php?post_type=company',
        'ロゴ一括インポート',
        'ロゴインポート',
        'manage_options',
        'import-logos',
        'logo_import_page_callback'
    );
}

function logo_import_page_callback() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'comp2';
    $media_table = $wpdb->prefix . 'comp2_media';
    
    // テーブルが存在するか確認
    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") === $table_name;
    
    ?>
    <div class="wrap">
        <h1>ロゴ画像一括インポート</h1>
        
        <?php if (!$table_exists) : ?>
            <div class="notice notice-error">
                <p>データベーステーブル <code><?php echo esc_html($table_name); ?></code> が見つかりません。</p>
                <p>スクレイピングスクリプトを実行してデータをインポートしてください。</p>
            </div>
        <?php else : 
            // 全件数を取得
            $total_count = $wpdb->get_var("SELECT COUNT(*) FROM {$table_name} WHERE logo_image_url IS NOT NULL AND logo_image_url != ''");
            $imported_count = $wpdb->get_var("SELECT COUNT(*) FROM {$media_table}");
            $remaining = $total_count - $imported_count;
            $batch_size = 10; // 一度に10件ずつ処理
        ?>
            <div class="logo-import-status">
                <h2>インポート状況</h2>
                <p><strong>全体:</strong> <?php echo $total_count; ?>件</p>
                <p><strong>完了:</strong> <?php echo $imported_count; ?>件</p>
                <p><strong>残り:</strong> <?php echo $remaining; ?>件</p>
                
                <?php if ($remaining > 0) : ?>
                    <div class="notice notice-info">
                        <p>⚠️ タイムアウトを防ぐため、<?php echo $batch_size; ?>件ずつインポートします。</p>
                        <p>「次の<?php echo min($batch_size, $remaining); ?>件をインポート」ボタンを繰り返しクリックしてください。</p>
                    </div>
                <?php else : ?>
                    <div class="notice notice-success">
                        <p>✅ すべてのロゴのインポートが完了しました!</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <?php if ($remaining > 0) : ?>
                <form method="post" action="" id="logo-import-form">
                    <?php wp_nonce_field('import_logos_batch_action', 'import_logos_batch_nonce'); ?>
                    <input type="submit" name="import_logos_batch" class="button button-primary button-large" value="次の<?php echo min($batch_size, $remaining); ?>件をインポート" />
                    <input type="button" class="button" value="リセット" onclick="if(confirm('進行状況をリセットしますか?')) { window.location.href='<?php echo add_query_arg('reset_import', '1'); ?>'; }" />
                </form>
            <?php endif; ?>
            
            <?php
            // リセット処理
            if (isset($_GET['reset_import'])) {
                $wpdb->query("TRUNCATE TABLE {$media_table}");
                echo '<div class="notice notice-success"><p>進行状況をリセットしました。</p></div>';
                echo '<meta http-equiv="refresh" content="1">';
            }
            
            // バッチインポート処理
            if (isset($_POST['import_logos_batch']) && check_admin_referer('import_logos_batch_action', 'import_logos_batch_nonce')) {
                import_logos_batch($batch_size);
                echo '<meta http-equiv="refresh" content="1">';
            }
            ?>
        <?php endif; ?>
    </div>
    
    <style>
    .logo-import-status {
        background: white;
        border: 1px solid #ccc;
        border-radius: 8px;
        padding: 20px;
        margin: 20px 0;
    }
    .logo-import-status h2 {
        margin-top: 0;
    }
    .logo-import-status p {
        font-size: 16px;
        margin: 10px 0;
    }
    </style>
    <?php
}

function import_logos_batch($batch_size = 10) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'comp2';
    $media_table = $wpdb->prefix . 'comp2_media';
    
    // メディア管理テーブルがなければ作成
    $wpdb->query("
        CREATE TABLE IF NOT EXISTS {$media_table} (
            id INT AUTO_INCREMENT PRIMARY KEY,
            company_id VARCHAR(50) NOT NULL UNIQUE,
            attachment_id BIGINT(20) NOT NULL,
            original_url TEXT,
            imported_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // 実行時間を延長
    set_time_limit(300); // 5分
    
    // 未インポートの業者を取得(バッチサイズ分)
    $companies = $wpdb->get_results($wpdb->prepare("
        SELECT c.company_id, c.company_name, c.logo_image_url
        FROM {$table_name} c
        LEFT JOIN {$media_table} m ON c.company_id = m.company_id
        WHERE c.logo_image_url IS NOT NULL 
        AND c.logo_image_url != ''
        AND m.attachment_id IS NULL
        LIMIT %d
    ", $batch_size));
    
    if (empty($companies)) {
        echo '<div class="notice notice-success"><p>✅ すべてのロゴのインポートが完了しました!</p></div>';
        return;
    }
    
    $imported = 0;
    $errors = 0;
    
    echo '<div class="notice notice-info"><p>インポート中...</p></div>';
    echo '<ul style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; background: #f9f9f9;">';
    
    foreach ($companies as $company) {
        echo '<li>処理中: ' . esc_html($company->company_name) . '...';
        flush();
        
        $attachment_id = import_logo_from_url($company->logo_image_url, $company->company_id, $company->company_name);
        
        if ($attachment_id) {
            // メディア管理テーブルに保存
            $wpdb->insert($media_table, array(
                'company_id' => $company->company_id,
                'attachment_id' => $attachment_id,
                'original_url' => $company->logo_image_url,
            ));
            
            $imported++;
            echo ' <span style="color: green;">✓ 成功</span></li>';
        } else {
            $errors++;
            echo ' <span style="color: red;">✗ 失敗</span></li>';
        }
        
        flush();
    }
    
    echo '</ul>';
    echo '<div class="notice notice-success"><p>';
    echo 'このバッチ: ' . $imported . '件成功 / ' . $errors . '件失敗';
    echo '</p></div>';
}

// カスタムテーブルからローカル画像URLを取得
function get_company_local_logo_url($company_id) {
    global $wpdb;
    $media_table = $wpdb->prefix . 'comp2_media';
    
    $logo_id = $wpdb->get_var($wpdb->prepare(
        "SELECT attachment_id FROM {$media_table} WHERE company_id = %s",
        $company_id
    ));
    
    if ($logo_id) {
        return wp_get_attachment_image_url($logo_id, 'medium');
    }
    
    return false;
}


// ===========================
// ランキングページ自動作成
// ===========================

function create_rankings_page() {
    // すでに存在するか確認
    $page = get_page_by_path('rankings');
    
    if (!$page) {
        $page_id = wp_insert_post(array(
            'post_title' => 'おすすめランキング',
            'post_name' => 'rankings',
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_content' => '',
            'page_template' => 'page-rankings.php'
        ));
        
        if ($page_id) {
            update_post_meta($page_id, '_wp_page_template', 'page-rankings.php');
        }
    }
}
add_action('after_switch_theme', 'create_rankings_page');

// ===========================
// wp_comp2 → company投稿タイプ 自動同期
// ===========================

// 管理画面にデータ同期ページを追加
add_action('admin_menu', 'add_data_sync_page');
function add_data_sync_page() {
    add_submenu_page(
        'edit.php?post_type=company',
        'データ同期',
        'データ同期',
        'manage_options',
        'sync-company-data',
        'data_sync_page_callback'
    );
}

function data_sync_page_callback() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'comp2';
    
    // テーブルが存在するか確認
    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") === $table_name;
    
    ?>
    <div class="wrap">
        <h1>業者データ同期</h1>
        
        <?php if (!$table_exists) : ?>
            <div class="notice notice-error">
                <p>データベーステーブル <code><?php echo esc_html($table_name); ?></code> が見つかりません。</p>
                <p>スクレイピングを実行してデータをインポートしてください。</p>
            </div>
        <?php else : ?>
            <p><code><?php echo esc_html($table_name); ?></code> テーブルのデータを業者情報（company投稿タイプ）に同期します。</p>
            <p><strong>注意:</strong> 既存の業者データは上書きされます。</p>
            
            <form method="post" action="">
                <?php wp_nonce_field('sync_company_data_action', 'sync_company_data_nonce'); ?>
                <input type="submit" name="sync_company_data" class="button button-primary button-large" value="データを同期" />
            </form>
            
            <?php
            if (isset($_POST['sync_company_data']) && check_admin_referer('sync_company_data_action', 'sync_company_data_nonce')) {
                sync_company_data_from_db();
            }
            ?>
        <?php endif; ?>
    </div>
    <?php
}

function sync_company_data_from_db() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'comp2';
    $media_table = $wpdb->prefix . 'comp2_media';
    
    // データ取得
    $companies = $wpdb->get_results("
        SELECT
            c.*,
            m.attachment_id as logo_attachment_id
        FROM {$table_name} c
        LEFT JOIN {$media_table} m ON c.company_id = m.company_id
        ORDER BY c.ranking ASC
    ");
    
    if (empty($companies)) {
        echo '<div class="notice notice-warning"><p>同期するデータがありません。</p></div>';
        return;
    }
    
    $synced = 0;
    $errors = 0;
    
    echo '<div class="notice notice-info"><p>同期開始...</p></div>';
    echo '<ul style="max-height: 400px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; background: #f9f9f9;">';
    
    foreach ($companies as $company) {
        echo '<li>処理中: ' . esc_html($company->company_name) . '...';
        flush();
        
        // company_idで既存投稿を検索
        $existing_post = $wpdb->get_var($wpdb->prepare(
            "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_company_db_id' AND meta_value = %s",
            $company->company_id
        ));
        
        // 投稿データ
        $post_data = array(
            'post_title' => $company->company_name,
            'post_content' => $company->summary,
            'post_status' => 'publish',
            'post_type' => 'company',
        );
        
        if ($existing_post) {
            // 更新
            $post_data['ID'] = $existing_post;
            $post_id = wp_update_post($post_data);
        } else {
            // 新規作成
            $post_id = wp_insert_post($post_data);
        }
        
        if (is_wp_error($post_id) || !$post_id) {
            $errors++;
            echo ' <span style="color: red;">✗ 失敗</span></li>';
            continue;
        }
        
        // メタフィールドを保存（カラムマッピング）
        update_post_meta($post_id, '_company_db_id', $company->company_id);
        update_post_meta($post_id, '_company_rating', $company->total_rating_star);
        update_post_meta($post_id, '_company_rating_text', $company->total_rating_text);
        update_post_meta($post_id, '_company_review_count', $company->review_count);
        update_post_meta($post_id, '_company_phone', ''); // DBにない場合
        update_post_meta($post_id, '_company_email', ''); // DBにない場合
        update_post_meta($post_id, '_company_website', $company->official_url);
        update_post_meta($post_id, '_company_areas', $company->service_area);
        update_post_meta($post_id, '_company_price_range', $company->price_info);
        update_post_meta($post_id, '_company_service_content', $company->service_content);
        update_post_meta($post_id, '_company_recommended_points', $company->recommended_points);
        update_post_meta($post_id, '_ranking_position', $company->ranking);
        update_post_meta($post_id, '_ranking_category', 'overall');
        
        // ロゴをアイキャッチ画像に設定
        if ($company->logo_attachment_id) {
            set_post_thumbnail($post_id, $company->logo_attachment_id);
        }
        
        $synced++;
        echo ' <span style="color: green;">✓ 成功</span></li>';
        flush();
    }
    
    echo '</ul>';
    echo '<div class="notice notice-success"><p>';
    echo '完了: ' . $synced . '件同期 / ' . $errors . '件エラー';
    echo '</p></div>';
}


// ===========================
// 47都道府県ページ自動作成
// ===========================

function create_prefecture_pages() {
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
    
    foreach ($prefectures as $prefecture) {
        // すでに存在するか確認
        $page = get_page_by_title($prefecture, OBJECT, 'page');
        
        if (!$page) {
            $page_id = wp_insert_post(array(
                'post_title' => $prefecture,
                'post_name' => sanitize_title($prefecture),
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_content' => '',
                'page_template' => 'page-prefecture.php'
            ));
            
            if ($page_id) {
                update_post_meta($page_id, '_wp_page_template', 'page-prefecture.php');
            }
        }
    }
}
add_action('after_switch_theme', 'create_prefecture_pages');
add_action('init', 'create_prefecture_pages_on_init');

function create_prefecture_pages_on_init() {
    // 一度だけ実行するためのフラグチェック
    if (get_option('prefecture_pages_created') !== 'yes') {
        create_prefecture_pages();
        update_option('prefecture_pages_created', 'yes');
    }
}

// 管理画面に都道府県ページ作成ボタンを追加
add_action('admin_menu', 'add_prefecture_pages_menu');
function add_prefecture_pages_menu() {
    add_submenu_page(
        'edit.php?post_type=company',
        '都道府県ページ作成',
        '都道府県ページ',
        'manage_options',
        'create-prefecture-pages',
        'prefecture_pages_callback'
    );
}

function prefecture_pages_callback() {
    ?>
    <div class="wrap">
        <h1>都道府県別ページ作成</h1>
        <p>47都道府県の固定ページを一括作成します。各ページには対応エリアの業者が自動表示されます。</p>
        
        <form method="post" action="">
            <?php wp_nonce_field('create_prefecture_pages_action', 'create_prefecture_pages_nonce'); ?>
            <input type="submit" name="create_prefecture_pages" class="button button-primary button-large" value="都道府県ページを作成" />
        </form>
        
        <?php
        if (isset($_POST['create_prefecture_pages']) && check_admin_referer('create_prefecture_pages_action', 'create_prefecture_pages_nonce')) {
            create_prefecture_pages();
            echo '<div class="notice notice-success"><p>47都道府県のページを作成しました！</p></div>';
            
            // 作成されたページのリストを表示
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
            
            echo '<h2>作成されたページ:</h2>';
            echo '<ul style="columns: 3; column-gap: 20px;">';
            foreach ($prefectures as $prefecture) {
                $page = get_page_by_title($prefecture, OBJECT, 'page');
                if ($page) {
                    echo '<li><a href="' . get_permalink($page->ID) . '" target="_blank">' . esc_html($prefecture) . '</a></li>';
                }
            }
            echo '</ul>';
        }
        ?>
    </div>
    <?php
}


/**
 * 都道府県別のSEOコンテンツを取得
 */
function get_prefecture_seo_content($prefecture_name) {
    $seo_data = array(
        '北海道' => array(
            'disposal_method' => '北海道では、札幌市を中心に不用品回収サービスが充実しています。自治体の粗大ゴミ回収は事前申込制で、指定のゴミ袋に入らないものが対象となります。札幌市では大型ゴミ処理手数料シール（200円～1,800円）を購入して処分します。冬季の積雪期間中は回収が制限される地域もあるため、不用品回収業者の利用が便利です。',
            'price_light_truck' => '15,000円～25,000円',
            'price_1t_truck' => '30,000円～45,000円',
            'price_2t_truck' => '50,000円～70,000円',
            'price_4t_truck' => '80,000円～120,000円',
            'summary' => '北海道で不用品回収業者を選ぶ際は、冬季の対応可能性や広域対応の有無を確認しましょう。札幌市以外の地域では業者数が限られるため、早めの予約がおすすめです。買取サービスを活用すれば、処分費用を抑えることができます。'
        ),
        '青森県' => array(
            'disposal_method' => '青森県では、青森市・八戸市・弘前市を中心に不用品回収サービスが展開されています。自治体の粗大ゴミ回収は事前申込制で、処理券（300円～1,000円程度）を購入して処分します。冬季は積雪の影響で回収が困難になる場合があるため、不用品回収業者の利用が効率的です。',
            'price_light_truck' => '14,000円～24,000円',
            'price_1t_truck' => '28,000円～43,000円',
            'price_2t_truck' => '48,000円～68,000円',
            'price_4t_truck' => '75,000円～115,000円',
            'summary' => '青森県で不用品回収業者を選ぶ際は、冬季対応や広域サービスの有無を確認しましょう。地域によっては業者数が限られるため、複数社から見積もりを取ることをおすすめします。'
        ),
        '岩手県' => array(
            'disposal_method' => '岩手県では、盛岡市を中心に不用品回収サービスが利用できます。自治体の粗大ゴミ回収は事前申込制で、処理券を購入して処分します。県内は広域なため、地域によっては出張料金が発生する場合があります。',
            'price_light_truck' => '14,000円～24,000円',
            'price_1t_truck' => '28,000円～43,000円',
            'price_2t_truck' => '48,000円～68,000円',
            'price_4t_truck' => '75,000円～115,000円',
            'summary' => '岩手県で不用品回収業者を選ぶ際は、対応エリアと出張料金を事前に確認しましょう。盛岡市以外の地域では業者数が限られるため、早めの予約がおすすめです。'
        ),
        '宮城県' => array(
            'disposal_method' => '宮城県では、仙台市を中心に多数の不用品回収業者が営業しています。自治体の粗大ゴミ回収は事前申込制で、処理券（200円～1,000円程度）を購入して処分します。仙台市では戸別収集と持込処分の両方が利用可能です。',
            'price_light_truck' => '15,000円～25,000円',
            'price_1t_truck' => '30,000円～45,000円',
            'price_2t_truck' => '50,000円～70,000円',
            'price_4t_truck' => '80,000円～120,000円',
            'summary' => '宮城県で不用品回収業者を選ぶ際は、仙台市内であれば複数の業者から相見積もりを取ることが可能です。買取サービスを活用して、処分費用を抑えましょう。'
        ),
        '秋田県' => array(
            'disposal_method' => '秋田県では、秋田市を中心に不用品回収サービスが展開されています。自治体の粗大ゴミ回収は事前申込制で、処理券を購入して処分します。冬季は積雪の影響で回収が制限される場合があります。',
            'price_light_truck' => '14,000円～24,000円',
            'price_1t_truck' => '28,000円～43,000円',
            'price_2t_truck' => '48,000円～68,000円',
            'price_4t_truck' => '75,000円～115,000円',
            'summary' => '秋田県で不用品回収業者を選ぶ際は、冬季対応の可否を確認しましょう。地域によっては業者数が限られるため、早めの予約がおすすめです。'
        ),
        '山形県' => array(
            'disposal_method' => '山形県では、山形市を中心に不用品回収サービスが利用できます。自治体の粗大ゴミ回収は事前申込制で、処理券を購入して処分します。冬季は積雪の影響で回収が困難になる場合があります。',
            'price_light_truck' => '14,000円～24,000円',
            'price_1t_truck' => '28,000円～43,000円',
            'price_2t_truck' => '48,000円～68,000円',
            'price_4t_truck' => '75,000円～115,000円',
            'summary' => '山形県で不用品回収業者を選ぶ際は、冬季対応や出張料金を確認しましょう。複数社から見積もりを取ることをおすすめします。'
        ),
        '福島県' => array(
            'disposal_method' => '福島県では、福島市・郡山市・いわき市を中心に不用品回収サービスが展開されています。自治体の粗大ゴミ回収は事前申込制で、処理券を購入して処分します。県内は広域なため、地域によって対応業者が異なります。',
            'price_light_truck' => '14,000円～24,000円',
            'price_1t_truck' => '28,000円～43,000円',
            'price_2t_truck' => '48,000円～68,000円',
            'price_4t_truck' => '75,000円～115,000円',
            'summary' => '福島県で不用品回収業者を選ぶ際は、対応エリアを事前に確認しましょう。主要都市では複数の業者から相見積もりを取ることが可能です。'
        ),
        '茨城県' => array(
            'disposal_method' => '茨城県では、水戸市・つくば市・日立市を中心に不用品回収サービスが利用できます。自治体の粗大ゴミ回収は事前申込制で、処理券を購入して処分します。首都圏に近いため、東京の業者も対応可能な場合があります。',
            'price_light_truck' => '15,000円～25,000円',
            'price_1t_truck' => '30,000円～45,000円',
            'price_2t_truck' => '50,000円～70,000円',
            'price_4t_truck' => '80,000円～120,000円',
            'summary' => '茨城県で不用品回収業者を選ぶ際は、首都圏対応の業者も検討しましょう。複数社から見積もりを取り、サービス内容を比較することをおすすめします。'
        ),
        '栃木県' => array(
            'disposal_method' => '栃木県では、宇都宮市を中心に不用品回収サービスが展開されています。自治体の粗大ゴミ回収は事前申込制で、処理券を購入して処分します。首都圏に近いため、東京の業者も対応可能な場合があります。',
            'price_light_truck' => '15,000円～25,000円',
            'price_1t_truck' => '30,000円～45,000円',
            'price_2t_truck' => '50,000円～70,000円',
            'price_4t_truck' => '80,000円～120,000円',
            'summary' => '栃木県で不用品回収業者を選ぶ際は、宇都宮市内であれば複数の業者から相見積もりを取ることが可能です。買取サービスを活用して、処分費用を抑えましょう。'
        ),
        '群馬県' => array(
            'disposal_method' => '群馬県では、前橋市・高崎市を中心に不用品回収サービスが利用できます。自治体の粗大ゴミ回収は事前申込制で、処理券を購入して処分します。首都圏に近いため、東京の業者も対応可能な場合があります。',
            'price_light_truck' => '15,000円～25,000円',
            'price_1t_truck' => '30,000円～45,000円',
            'price_2t_truck' => '50,000円～70,000円',
            'price_4t_truck' => '80,000円～120,000円',
            'summary' => '群馬県で不用品回収業者を選ぶ際は、対応エリアと料金を比較しましょう。複数社から見積もりを取ることをおすすめします。'
        ),
        '埼玉県' => array(
            'disposal_method' => '埼玉県では、さいたま市・川口市・川越市を中心に多数の不用品回収業者が営業しています。首都圏のため業者の選択肢が豊富で、競争により料金も比較的リーズナブルです。自治体の粗大ゴミ回収は事前申込制で、処理券を購入して処分します。',
            'price_light_truck' => '16,000円～26,000円',
            'price_1t_truck' => '32,000円～47,000円',
            'price_2t_truck' => '52,000円～72,000円',
            'price_4t_truck' => '85,000円～125,000円',
            'summary' => '埼玉県で不用品回収業者を選ぶ際は、複数の業者から相見積もりを取り、料金とサービス内容を比較しましょう。口コミや評判も参考にして、信頼できる業者を選ぶことが大切です。'
        ),
        '千葉県' => array(
            'disposal_method' => '千葉県では、千葉市・船橋市・柏市を中心に多数の不用品回収業者が営業しています。首都圏のため業者の選択肢が豊富です。自治体の粗大ゴミ回収は事前申込制で、処理券を購入して処分します。',
            'price_light_truck' => '16,000円～26,000円',
            'price_1t_truck' => '32,000円～47,000円',
            'price_2t_truck' => '52,000円～72,000円',
            'price_4t_truck' => '85,000円～125,000円',
            'summary' => '千葉県で不用品回収業者を選ぶ際は、複数の業者から相見積もりを取り、料金とサービス内容を比較しましょう。口コミや評判も参考にして、信頼できる業者を選ぶことが大切です。'
        ),
        '東京都' => array(
            'disposal_method' => '東京都では、23区を中心に非常に多くの不用品回収業者が営業しています。業者の選択肢が最も豊富で、即日対応や深夜対応など多様なサービスが利用できます。自治体の粗大ゴミ回収は事前申込制で、粗大ゴミ処理券を購入して処分します。',
            'price_light_truck' => '18,000円～28,000円',
            'price_1t_truck' => '35,000円～50,000円',
            'price_2t_truck' => '55,000円～75,000円',
            'price_4t_truck' => '90,000円～130,000円',
            'summary' => '東京都で不用品回収業者を選ぶ際は、複数の業者から相見積もりを取り、料金とサービス内容を比較しましょう。口コミや評判も参考にして、信頼できる業者を選ぶことが大切です。'
        ),
        '神奈川県' => array(
            'disposal_method' => '神奈川県では、横浜市・川崎市・相模原市を中心に多数の不用品回収業者が営業しています。首都圏のため業者の選択肢が豊富で、サービスも充実しています。自治体の粗大ゴミ回収は事前申込制で、処理券を購入して処分します。',
            'price_light_truck' => '16,000円～26,000円',
            'price_1t_truck' => '32,000円～47,000円',
            'price_2t_truck' => '52,000円～72,000円',
            'price_4t_truck' => '85,000円～125,000円',
            'summary' => '神奈川県で不用品回収業者を選ぶ際は、複数の業者から相見積もりを取り、料金とサービス内容を比較しましょう。口コミや評判も参考にして、信頼できる業者を選ぶことが大切です。'
        ),
        '新潟県' => array(
            'disposal_method' => '新潟県では、新潟市を中心に不用品回収サービスが展開されています。自治体の粗大ゴミ回収は事前申込制で、処理券を購入して処分します。冬季は積雪の影響で回収が制限される場合があります。',
            'price_light_truck' => '14,000円～24,000円',
            'price_1t_truck' => '28,000円～43,000円',
            'price_2t_truck' => '48,000円～68,000円',
            'price_4t_truck' => '75,000円～115,000円',
            'summary' => '新潟県で不用品回収業者を選ぶ際は、複数の業者から相見積もりを取り、料金とサービス内容を比較しましょう。口コミや評判も参考にして、信頼できる業者を選ぶことが大切です。'
        ),
        '富山県' => array(
            'disposal_method' => '富山県では、富山市を中心に不用品回収サービスが利用できます。自治体の粗大ゴミ回収は事前申込制で、処理券を購入して処分します。県内は広域なため、地域によって対応業者が異なります。',
            'price_light_truck' => '14,000円～24,000円',
            'price_1t_truck' => '28,000円～43,000円',
            'price_2t_truck' => '48,000円～68,000円',
            'price_4t_truck' => '75,000円～115,000円',
            'summary' => '富山県で不用品回収業者を選ぶ際は、複数の業者から相見積もりを取り、料金とサービス内容を比較しましょう。口コミや評判も参考にして、信頼できる業者を選ぶことが大切です。'
        ),
        '石川県' => array(
            'disposal_method' => '石川県では、金沢市を中心に不用品回収サービスが展開されています。自治体の粗大ゴミ回収は事前申込制で、処理券を購入して処分します。金沢市内では複数の業者から選択可能です。',
            'price_light_truck' => '14,000円～24,000円',
            'price_1t_truck' => '28,000円～43,000円',
            'price_2t_truck' => '48,000円～68,000円',
            'price_4t_truck' => '75,000円～115,000円',
            'summary' => '石川県で不用品回収業者を選ぶ際は、複数の業者から相見積もりを取り、料金とサービス内容を比較しましょう。口コミや評判も参考にして、信頼できる業者を選ぶことが大切です。'
        ),
        '福井県' => array(
            'disposal_method' => '福井県では、福井市を中心に不用品回収サービスが利用できます。自治体の粗大ゴミ回収は事前申込制で、処理券を購入して処分します。地域によっては業者数が限られるため、早めの予約がおすすめです。',
            'price_light_truck' => '14,000円～24,000円',
            'price_1t_truck' => '28,000円～43,000円',
            'price_2t_truck' => '48,000円～68,000円',
            'price_4t_truck' => '75,000円～115,000円',
            'summary' => '福井県で不用品回収業者を選ぶ際は、複数の業者から相見積もりを取り、料金とサービス内容を比較しましょう。口コミや評判も参考にして、信頼できる業者を選ぶことが大切です。'
        ),
        '山梨県' => array(
            'disposal_method' => '山梨県では、甲府市を中心に不用品回収サービスが展開されています。自治体の粗大ゴミ回収は事前申込制で、処理券を購入して処分します。首都圏に近いため、東京の業者も対応可能な場合があります。',
            'price_light_truck' => '15,000円～25,000円',
            'price_1t_truck' => '30,000円～45,000円',
            'price_2t_truck' => '50,000円～70,000円',
            'price_4t_truck' => '80,000円～120,000円',
            'summary' => '山梨県で不用品回収業者を選ぶ際は、複数の業者から相見積もりを取り、料金とサービス内容を比較しましょう。口コミや評判も参考にして、信頼できる業者を選ぶことが大切です。'
        ),
        '長野県' => array(
            'disposal_method' => '長野県では、長野市・松本市を中心に不用品回収サービスが利用できます。自治体の粗大ゴミ回収は事前申込制で、処理券を購入して処分します。県内は広域なため、地域によって対応業者が異なります。',
            'price_light_truck' => '14,000円～24,000円',
            'price_1t_truck' => '28,000円～43,000円',
            'price_2t_truck' => '48,000円～68,000円',
            'price_4t_truck' => '75,000円～115,000円',
            'summary' => '長野県で不用品回収業者を選ぶ際は、複数の業者から相見積もりを取り、料金とサービス内容を比較しましょう。口コミや評判も参考にして、信頼できる業者を選ぶことが大切です。'
        ),
        '岐阜県' => array(
            'disposal_method' => '岐阜県では、岐阜市を中心に不用品回収サービスが展開されています。自治体の粗大ゴミ回収は事前申込制で、処理券を購入して処分します。名古屋圏に近いため、愛知県の業者も対応可能な場合があります。',
            'price_light_truck' => '15,000円～25,000円',
            'price_1t_truck' => '30,000円～45,000円',
            'price_2t_truck' => '50,000円～70,000円',
            'price_4t_truck' => '80,000円～120,000円',
            'summary' => '岐阜県で不用品回収業者を選ぶ際は、複数の業者から相見積もりを取り、料金とサービス内容を比較しましょう。口コミや評判も参考にして、信頼できる業者を選ぶことが大切です。'
        ),
        '静岡県' => array(
            'disposal_method' => '静岡県では、静岡市・浜松市を中心に不用品回収サービスが利用できます。自治体の粗大ゴミ回収は事前申込制で、処理券を購入して処分します。県内は広域なため、地域によって対応業者が異なります。',
            'price_light_truck' => '15,000円～25,000円',
            'price_1t_truck' => '30,000円～45,000円',
            'price_2t_truck' => '50,000円～70,000円',
            'price_4t_truck' => '80,000円～120,000円',
            'summary' => '静岡県で不用品回収業者を選ぶ際は、複数の業者から相見積もりを取り、料金とサービス内容を比較しましょう。口コミや評判も参考にして、信頼できる業者を選ぶことが大切です。'
        ),
        '愛知県' => array(
            'disposal_method' => '愛知県では、名古屋市を中心に多数の不用品回収業者が営業しています。業者の選択肢が豊富で、競争により料金も比較的リーズナブルです。自治体の粗大ゴミ回収は事前申込制で、処理券を購入して処分します。',
            'price_light_truck' => '16,000円～26,000円',
            'price_1t_truck' => '32,000円～47,000円',
            'price_2t_truck' => '52,000円～72,000円',
            'price_4t_truck' => '85,000円～125,000円',
            'summary' => '愛知県で不用品回収業者を選ぶ際は、複数の業者から相見積もりを取り、料金とサービス内容を比較しましょう。口コミや評判も参考にして、信頼できる業者を選ぶことが大切です。'
        ),
        '三重県' => array(
            'disposal_method' => '三重県では、津市・四日市市を中心に不用品回収サービスが展開されています。自治体の粗大ゴミ回収は事前申込制で、処理券を購入して処分します。名古屋圏に近いため、愛知県の業者も対応可能な場合があります。',
            'price_light_truck' => '15,000円～25,000円',
            'price_1t_truck' => '30,000円～45,000円',
            'price_2t_truck' => '50,000円～70,000円',
            'price_4t_truck' => '80,000円～120,000円',
            'summary' => '三重県で不用品回収業者を選ぶ際は、複数の業者から相見積もりを取り、料金とサービス内容を比較しましょう。口コミや評判も参考にして、信頼できる業者を選ぶことが大切です。'
        ),
        '滋賀県' => array(
            'disposal_method' => '滋賀県では、大津市を中心に不用品回収サービスが利用できます。自治体の粗大ゴミ回収は事前申込制で、処理券を購入して処分します。京都・大阪圏に近いため、関西の業者も対応可能な場合があります。',
            'price_light_truck' => '15,000円～25,000円',
            'price_1t_truck' => '30,000円～45,000円',
            'price_2t_truck' => '50,000円～70,000円',
            'price_4t_truck' => '80,000円～120,000円',
            'summary' => '滋賀県で不用品回収業者を選ぶ際は、複数の業者から相見積もりを取り、料金とサービス内容を比較しましょう。口コミや評判も参考にして、信頼できる業者を選ぶことが大切です。'
        ),
        '京都府' => array(
            'disposal_method' => '京都府では、京都市を中心に多数の不用品回収業者が営業しています。業者の選択肢が豊富で、サービスも充実しています。自治体の粗大ゴミ回収は事前申込制で、処理券を購入して処分します。',
            'price_light_truck' => '16,000円～26,000円',
            'price_1t_truck' => '32,000円～47,000円',
            'price_2t_truck' => '52,000円～72,000円',
            'price_4t_truck' => '85,000円～125,000円',
            'summary' => '京都府で不用品回収業者を選ぶ際は、複数の業者から相見積もりを取り、料金とサービス内容を比較しましょう。口コミや評判も参考にして、信頼できる業者を選ぶことが大切です。'
        ),
        '大阪府' => array(
            'disposal_method' => '大阪府では、大阪市を中心に非常に多くの不用品回収業者が営業しています。業者の選択肢が豊富で、即日対応など多様なサービスが利用できます。自治体の粗大ゴミ回収は事前申込制で、処理券を購入して処分します。',
            'price_light_truck' => '16,000円～26,000円',
            'price_1t_truck' => '32,000円～47,000円',
            'price_2t_truck' => '52,000円～72,000円',
            'price_4t_truck' => '85,000円～125,000円',
            'summary' => '大阪府で不用品回収業者を選ぶ際は、複数の業者から相見積もりを取り、料金とサービス内容を比較しましょう。口コミや評判も参考にして、信頼できる業者を選ぶことが大切です。'
        ),
        '兵庫県' => array(
            'disposal_method' => '兵庫県では、神戸市を中心に多数の不用品回収業者が営業しています。業者の選択肢が豊富で、サービスも充実しています。自治体の粗大ゴミ回収は事前申込制で、処理券を購入して処分します。',
            'price_light_truck' => '16,000円～26,000円',
            'price_1t_truck' => '32,000円～47,000円',
            'price_2t_truck' => '52,000円～72,000円',
            'price_4t_truck' => '85,000円～125,000円',
            'summary' => '兵庫県で不用品回収業者を選ぶ際は、複数の業者から相見積もりを取り、料金とサービス内容を比較しましょう。口コミや評判も参考にして、信頼できる業者を選ぶことが大切です。'
        ),
        '奈良県' => array(
            'disposal_method' => '奈良県では、奈良市を中心に不用品回収サービスが展開されています。自治体の粗大ゴミ回収は事前申込制で、処理券を購入して処分します。大阪圏に近いため、大阪の業者も対応可能な場合があります。',
            'price_light_truck' => '15,000円～25,000円',
            'price_1t_truck' => '30,000円～45,000円',
            'price_2t_truck' => '50,000円～70,000円',
            'price_4t_truck' => '80,000円～120,000円',
            'summary' => '奈良県で不用品回収業者を選ぶ際は、複数の業者から相見積もりを取り、料金とサービス内容を比較しましょう。口コミや評判も参考にして、信頼できる業者を選ぶことが大切です。'
        ),
        '和歌山県' => array(
            'disposal_method' => '和歌山県では、和歌山市を中心に不用品回収サービスが利用できます。自治体の粗大ゴミ回収は事前申込制で、処理券を購入して処分します。地域によっては業者数が限られるため、早めの予約がおすすめです。',
            'price_light_truck' => '14,000円～24,000円',
            'price_1t_truck' => '28,000円～43,000円',
            'price_2t_truck' => '48,000円～68,000円',
            'price_4t_truck' => '75,000円～115,000円',
            'summary' => '和歌山県で不用品回収業者を選ぶ際は、複数の業者から相見積もりを取り、料金とサービス内容を比較しましょう。口コミや評判も参考にして、信頼できる業者を選ぶことが大切です。'
        ),
        '鳥取県' => array(
            'disposal_method' => '鳥取県では、鳥取市を中心に不用品回収サービスが展開されています。自治体の粗大ゴミ回収は事前申込制で、処理券を購入して処分します。地域によっては業者数が限られるため、早めの予約がおすすめです。',
            'price_light_truck' => '14,000円～24,000円',
            'price_1t_truck' => '28,000円～43,000円',
            'price_2t_truck' => '48,000円～68,000円',
            'price_4t_truck' => '75,000円～115,000円',
            'summary' => '鳥取県で不用品回収業者を選ぶ際は、複数の業者から相見積もりを取り、料金とサービス内容を比較しましょう。口コミや評判も参考にして、信頼できる業者を選ぶことが大切です。'
        ),
        '島根県' => array(
            'disposal_method' => '島根県では、松江市を中心に不用品回収サービスが利用できます。自治体の粗大ゴミ回収は事前申込制で、処理券を購入して処分します。地域によっては業者数が限られるため、早めの予約がおすすめです。',
            'price_light_truck' => '14,000円～24,000円',
            'price_1t_truck' => '28,000円～43,000円',
            'price_2t_truck' => '48,000円～68,000円',
            'price_4t_truck' => '75,000円～115,000円',
            'summary' => '島根県で不用品回収業者を選ぶ際は、複数の業者から相見積もりを取り、料金とサービス内容を比較しましょう。口コミや評判も参考にして、信頼できる業者を選ぶことが大切です。'
        ),
        '岡山県' => array(
            'disposal_method' => '岡山県では、岡山市を中心に不用品回収サービスが展開されています。自治体の粗大ゴミ回収は事前申込制で、処理券を購入して処分します。岡山市内では複数の業者から選択可能です。',
            'price_light_truck' => '15,000円～25,000円',
            'price_1t_truck' => '30,000円～45,000円',
            'price_2t_truck' => '50,000円～70,000円',
            'price_4t_truck' => '80,000円～120,000円',
            'summary' => '岡山県で不用品回収業者を選ぶ際は、複数の業者から相見積もりを取り、料金とサービス内容を比較しましょう。口コミや評判も参考にして、信頼できる業者を選ぶことが大切です。'
        ),
        '広島県' => array(
            'disposal_method' => '広島県では、広島市を中心に多数の不用品回収業者が営業しています。業者の選択肢が豊富で、サービスも充実しています。自治体の粗大ゴミ回収は事前申込制で、処理券を購入して処分します。',
            'price_light_truck' => '16,000円～26,000円',
            'price_1t_truck' => '32,000円～47,000円',
            'price_2t_truck' => '52,000円～72,000円',
            'price_4t_truck' => '85,000円～125,000円',
            'summary' => '広島県で不用品回収業者を選ぶ際は、複数の業者から相見積もりを取り、料金とサービス内容を比較しましょう。口コミや評判も参考にして、信頼できる業者を選ぶことが大切です。'
        ),
        '山口県' => array(
            'disposal_method' => '山口県では、山口市・下関市を中心に不用品回収サービスが利用できます。自治体の粗大ゴミ回収は事前申込制で、処理券を購入して処分します。地域によって対応業者が異なります。',
            'price_light_truck' => '14,000円～24,000円',
            'price_1t_truck' => '28,000円～43,000円',
            'price_2t_truck' => '48,000円～68,000円',
            'price_4t_truck' => '75,000円～115,000円',
            'summary' => '山口県で不用品回収業者を選ぶ際は、複数の業者から相見積もりを取り、料金とサービス内容を比較しましょう。口コミや評判も参考にして、信頼できる業者を選ぶことが大切です。'
        ),
        '徳島県' => array(
            'disposal_method' => '徳島県では、徳島市を中心に不用品回収サービスが展開されています。自治体の粗大ゴミ回収は事前申込制で、処理券を購入して処分します。地域によっては業者数が限られるため、早めの予約がおすすめです。',
            'price_light_truck' => '14,000円～24,000円',
            'price_1t_truck' => '28,000円～43,000円',
            'price_2t_truck' => '48,000円～68,000円',
            'price_4t_truck' => '75,000円～115,000円',
            'summary' => '徳島県で不用品回収業者を選ぶ際は、複数の業者から相見積もりを取り、料金とサービス内容を比較しましょう。口コミや評判も参考にして、信頼できる業者を選ぶことが大切です。'
        ),
        '香川県' => array(
            'disposal_method' => '香川県では、高松市を中心に不用品回収サービスが利用できます。自治体の粗大ゴミ回収は事前申込制で、処理券を購入して処分します。高松市内では複数の業者から選択可能です。',
            'price_light_truck' => '14,000円～24,000円',
            'price_1t_truck' => '28,000円～43,000円',
            'price_2t_truck' => '48,000円～68,000円',
            'price_4t_truck' => '75,000円～115,000円',
            'summary' => '香川県で不用品回収業者を選ぶ際は、複数の業者から相見積もりを取り、料金とサービス内容を比較しましょう。口コミや評判も参考にして、信頼できる業者を選ぶことが大切です。'
        ),
        '愛媛県' => array(
            'disposal_method' => '愛媛県では、松山市を中心に不用品回収サービスが展開されています。自治体の粗大ゴミ回収は事前申込制で、処理券を購入して処分します。松山市内では複数の業者から選択可能です。',
            'price_light_truck' => '14,000円～24,000円',
            'price_1t_truck' => '28,000円～43,000円',
            'price_2t_truck' => '48,000円～68,000円',
            'price_4t_truck' => '75,000円～115,000円',
            'summary' => '愛媛県で不用品回収業者を選ぶ際は、複数の業者から相見積もりを取り、料金とサービス内容を比較しましょう。口コミや評判も参考にして、信頼できる業者を選ぶことが大切です。'
        ),
        '高知県' => array(
            'disposal_method' => '高知県では、高知市を中心に不用品回収サービスが利用できます。自治体の粗大ゴミ回収は事前申込制で、処理券を購入して処分します。地域によっては業者数が限られるため、早めの予約がおすすめです。',
            'price_light_truck' => '14,000円～24,000円',
            'price_1t_truck' => '28,000円～43,000円',
            'price_2t_truck' => '48,000円～68,000円',
            'price_4t_truck' => '75,000円～115,000円',
            'summary' => '高知県で不用品回収業者を選ぶ際は、複数の業者から相見積もりを取り、料金とサービス内容を比較しましょう。口コミや評判も参考にして、信頼できる業者を選ぶことが大切です。'
        ),
        '福岡県' => array(
            'disposal_method' => '福岡県では、福岡市を中心に多数の不用品回収業者が営業しています。業者の選択肢が豊富で、サービスも充実しています。自治体の粗大ゴミ回収は事前申込制で、処理券を購入して処分します。',
            'price_light_truck' => '16,000円～26,000円',
            'price_1t_truck' => '32,000円～47,000円',
            'price_2t_truck' => '52,000円～72,000円',
            'price_4t_truck' => '85,000円～125,000円',
            'summary' => '福岡県で不用品回収業者を選ぶ際は、複数の業者から相見積もりを取り、料金とサービス内容を比較しましょう。口コミや評判も参考にして、信頼できる業者を選ぶことが大切です。'
        ),
        '佐賀県' => array(
            'disposal_method' => '佐賀県では、佐賀市を中心に不用品回収サービスが展開されています。自治体の粗大ゴミ回収は事前申込制で、処理券を購入して処分します。福岡圏に近いため、福岡の業者も対応可能な場合があります。',
            'price_light_truck' => '14,000円～24,000円',
            'price_1t_truck' => '28,000円～43,000円',
            'price_2t_truck' => '48,000円～68,000円',
            'price_4t_truck' => '75,000円～115,000円',
            'summary' => '佐賀県で不用品回収業者を選ぶ際は、複数の業者から相見積もりを取り、料金とサービス内容を比較しましょう。口コミや評判も参考にして、信頼できる業者を選ぶことが大切です。'
        ),
        '長崎県' => array(
            'disposal_method' => '長崎県では、長崎市を中心に不用品回収サービスが利用できます。自治体の粗大ゴミ回収は事前申込制で、処理券を購入して処分します。地域によっては業者数が限られるため、早めの予約がおすすめです。',
            'price_light_truck' => '14,000円～24,000円',
            'price_1t_truck' => '28,000円～43,000円',
            'price_2t_truck' => '48,000円～68,000円',
            'price_4t_truck' => '75,000円～115,000円',
            'summary' => '長崎県で不用品回収業者を選ぶ際は、複数の業者から相見積もりを取り、料金とサービス内容を比較しましょう。口コミや評判も参考にして、信頼できる業者を選ぶことが大切です。'
        ),
        '熊本県' => array(
            'disposal_method' => '熊本県では、熊本市を中心に不用品回収サービスが展開されています。自治体の粗大ゴミ回収は事前申込制で、処理券を購入して処分します。熊本市内では複数の業者から選択可能です。',
            'price_light_truck' => '15,000円～25,000円',
            'price_1t_truck' => '30,000円～45,000円',
            'price_2t_truck' => '50,000円～70,000円',
            'price_4t_truck' => '80,000円～120,000円',
            'summary' => '熊本県で不用品回収業者を選ぶ際は、複数の業者から相見積もりを取り、料金とサービス内容を比較しましょう。口コミや評判も参考にして、信頼できる業者を選ぶことが大切です。'
        ),
        '大分県' => array(
            'disposal_method' => '大分県では、大分市を中心に不用品回収サービスが利用できます。自治体の粗大ゴミ回収は事前申込制で、処理券を購入して処分します。地域によって対応業者が異なります。',
            'price_light_truck' => '14,000円～24,000円',
            'price_1t_truck' => '28,000円～43,000円',
            'price_2t_truck' => '48,000円～68,000円',
            'price_4t_truck' => '75,000円～115,000円',
            'summary' => '大分県で不用品回収業者を選ぶ際は、複数の業者から相見積もりを取り、料金とサービス内容を比較しましょう。口コミや評判も参考にして、信頼できる業者を選ぶことが大切です。'
        ),
        '宮崎県' => array(
            'disposal_method' => '宮崎県では、宮崎市を中心に不用品回収サービスが展開されています。自治体の粗大ゴミ回収は事前申込制で、処理券を購入して処分します。地域によっては業者数が限られるため、早めの予約がおすすめです。',
            'price_light_truck' => '14,000円～24,000円',
            'price_1t_truck' => '28,000円～43,000円',
            'price_2t_truck' => '48,000円～68,000円',
            'price_4t_truck' => '75,000円～115,000円',
            'summary' => '宮崎県で不用品回収業者を選ぶ際は、複数の業者から相見積もりを取り、料金とサービス内容を比較しましょう。口コミや評判も参考にして、信頼できる業者を選ぶことが大切です。'
        ),
        '鹿児島県' => array(
            'disposal_method' => '鹿児島県では、鹿児島市を中心に不用品回収サービスが利用できます。自治体の粗大ゴミ回収は事前申込制で、処理券を購入して処分します。離島部では対応が限られる場合があります。',
            'price_light_truck' => '14,000円～24,000円',
            'price_1t_truck' => '28,000円～43,000円',
            'price_2t_truck' => '48,000円～68,000円',
            'price_4t_truck' => '75,000円～115,000円',
            'summary' => '鹿児島県で不用品回収業者を選ぶ際は、複数の業者から相見積もりを取り、料金とサービス内容を比較しましょう。口コミや評判も参考にして、信頼できる業者を選ぶことが大切です。'
        ),
        '沖縄県' => array(
            'disposal_method' => '沖縄県では、那覇市を中心に不用品回収サービスが展開されています。自治体の粗大ゴミ回収は事前申込制で、処理券を購入して処分します。離島部では対応が限られる場合があります。',
            'price_light_truck' => '16,000円～26,000円',
            'price_1t_truck' => '32,000円～47,000円',
            'price_2t_truck' => '52,000円～72,000円',
            'price_4t_truck' => '85,000円～125,000円',
            'summary' => '沖縄県で不用品回収業者を選ぶ際は、複数の業者から相見積もりを取り、料金とサービス内容を比較しましょう。口コミや評判も参考にして、信頼できる業者を選ぶことが大切です。'
        )
    );
    
    // デフォルトのコンテンツ（データがない都道府県用）
    $default_content = array(
        'disposal_method' => $prefecture_name . 'では、自治体の粗大ゴミ回収サービスと民間の不用品回収業者の両方が利用できます。自治体の回収は事前申込制で、処理券を購入して処分します。大量の不用品や自治体で処分できないものは、不用品回収業者の利用が便利です。',
        'price_light_truck' => '15,000円～25,000円',
        'price_1t_truck' => '30,000円～45,000円',
        'price_2t_truck' => '50,000円～70,000円',
        'price_4t_truck' => '80,000円～120,000円',
        'summary' => $prefecture_name . 'で不用品回収業者を選ぶ際は、複数の業者から相見積もりを取り、料金とサービス内容を比較しましょう。口コミや評判も参考にして、信頼できる業者を選ぶことが大切です。'
    );
    
    return isset($seo_data[$prefecture_name]) ? $seo_data[$prefecture_name] : $default_content;
}

// ===========================
// noindex設定（2026年SEO対策）
// ===========================

function fuyohin_noindex_pages() {
    // タグページ
    if (is_tag()) {
        echo '<meta name="robots" content="noindex, follow">' . "\n";
    }
    
    // 日付アーカイブ
    if (is_date()) {
        echo '<meta name="robots" content="noindex, follow">' . "\n";
    }
    
    // 著者アーカイブ
    if (is_author()) {
        echo '<meta name="robots" content="noindex, follow">' . "\n";
    }
    
    // ページネーション2ページ目以降
    if (is_paged() && get_query_var('paged') > 1) {
        echo '<meta name="robots" content="noindex, follow">' . "\n";
    }
    
    // 内部検索結果
    if (is_search()) {
        echo '<meta name="robots" content="noindex, follow">' . "\n";
    }
}
add_action('wp_head', 'fuyohin_noindex_pages', 1);
