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
    ));

    // 口コミ
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
