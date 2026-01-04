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
        <?php else : ?>
            <p><code><?php echo esc_html($table_name); ?></code> テーブルの logo_image_url からロゴ画像をメディアライブラリにインポートします。</p>
            
            <form method="post" action="">
                <?php wp_nonce_field('import_logos_action', 'import_logos_nonce'); ?>
                <input type="submit" name="import_logos" class="button button-primary button-large" value="ロゴをインポート" />
            </form>
            
            <?php
            if (isset($_POST['import_logos']) && check_admin_referer('import_logos_action', 'import_logos_nonce')) {
                import_all_company_logos_from_db();
            }
            ?>
        <?php endif; ?>
    </div>
    <?php
}

function import_all_company_logos_from_db() {
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
    
    // データ取得
    $companies = $wpdb->get_results("
        SELECT company_id, company_name, logo_image_url
        FROM {$table_name}
        WHERE logo_image_url IS NOT NULL AND logo_image_url != ''
    ");
    
    if (empty($companies)) {
        echo '<div class="notice notice-warning"><p>インポートするロゴがありません。</p></div>';
        return;
    }
    
    $imported = 0;
    $skipped = 0;
    $errors = 0;
    
    echo '<div class="notice notice-info"><p>インポート開始...</p></div>';
    echo '<ul style="max-height: 400px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; background: #f9f9f9;">';
    
    foreach ($companies as $company) {
        // すでにインポート済みか確認
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT attachment_id FROM {$media_table} WHERE company_id = %s",
            $company->company_id
        ));
        
        if ($existing) {
            $skipped++;
            echo '<li style="color: #999;">• スキップ: ' . esc_html($company->company_name) . ' (インポート済み)</li>';
            continue;
        }
        
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
    echo '完了: ' . $imported . '件インポート / ' . $skipped . '件スキップ / ' . $errors . '件エラー';
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
