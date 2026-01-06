<?php
/**
 * Template Name: Affiliate Manager
 * アフィリエイトバナー管理ページ
 */

// 管理者権限チェック
if (!current_user_can('manage_options')) {
    wp_die('このページにアクセスする権限がありません。');
}

get_header();

global $wpdb;
$table_name = $wpdb->prefix . 'affiliate_banners';

// テーブル作成（初回アクセス時）
$charset_collate = $wpdb->get_charset_collate();
$sql = "CREATE TABLE IF NOT EXISTS $table_name (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL COMMENT 'バナータイトル',
  banner_image_url TEXT COMMENT 'バナー画像URL',
  affiliate_url TEXT NOT NULL COMMENT 'アフィリエイトリンクURL',
  target_prefectures TEXT COMMENT '対応都道府県（カンマ区切り）',
  display_order INT DEFAULT 0 COMMENT '表示順序',
  is_active TINYINT(1) DEFAULT 1 COMMENT '有効/無効',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) $charset_collate COMMENT='アフィリエイトバナー管理';";

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);

// フォーム送信処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['affiliate_action'])) {
    check_admin_referer('affiliate_banner_action');
    
    $action = $_POST['affiliate_action'];
    
    if ($action === 'add' || $action === 'edit') {
        $data = array(
            'title' => sanitize_text_field($_POST['title']),
            'banner_image_url' => esc_url_raw($_POST['banner_image_url']),
            'affiliate_url' => esc_url_raw($_POST['affiliate_url']),
            'target_prefectures' => sanitize_text_field($_POST['target_prefectures']),
            'display_order' => intval($_POST['display_order']),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        );
        
        if ($action === 'add') {
            $wpdb->insert($table_name, $data);
            $message = 'バナーを追加しました。';
        } else {
            $id = intval($_POST['banner_id']);
            $wpdb->update($table_name, $data, array('id' => $id));
            $message = 'バナーを更新しました。';
        }
    } elseif ($action === 'delete') {
        $id = intval($_POST['banner_id']);
        $wpdb->delete($table_name, array('id' => $id));
        $message = 'バナーを削除しました。';
    }
}

// バナー一覧取得
$banners = $wpdb->get_results("SELECT * FROM $table_name ORDER BY display_order ASC, id DESC");

// 編集モード
$edit_banner = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $edit_banner = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $edit_id));
}

// 都道府県リスト
$prefectures = array(
    '北海道', '青森県', '岩手県', '宮城県', '秋田県', '山形県', '福島県',
    '茨城県', '栃木県', '群馬県', '埼玉県', '千葉県', '東京都', '神奈川県',
    '新潟県', '富山県', '石川県', '福井県', '山梨県', '長野県', '岐阜県', '静岡県', '愛知県',
    '三重県', '滋賀県', '京都府', '大阪府', '兵庫県', '奈良県', '和歌山県',
    '鳥取県', '島根県', '岡山県', '広島県', '山口県',
    '徳島県', '香川県', '愛媛県', '高知県',
    '福岡県', '佐賀県', '長崎県', '熊本県', '大分県', '宮崎県', '鹿児島県', '沖縄県'
);
?>

<div class="affiliate-manager-page">
    <div class="container">
        <h1 class="page-title">アフィリエイトバナー管理</h1>
        
        <?php if (isset($message)): ?>
            <div class="admin-message success"><?php echo esc_html($message); ?></div>
        <?php endif; ?>
        
        <!-- バナー登録/編集フォーム -->
        <div class="admin-form-card">
            <h2><?php echo $edit_banner ? 'バナー編集' : 'バナー新規登録'; ?></h2>
            <form method="post" class="affiliate-form">
                <?php wp_nonce_field('affiliate_banner_action'); ?>
                <input type="hidden" name="affiliate_action" value="<?php echo $edit_banner ? 'edit' : 'add'; ?>">
                <?php if ($edit_banner): ?>
                    <input type="hidden" name="banner_id" value="<?php echo esc_attr($edit_banner->id); ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="title">バナータイトル <span class="required">*</span></label>
                    <input type="text" id="title" name="title" required 
                           value="<?php echo $edit_banner ? esc_attr($edit_banner->title) : ''; ?>"
                           placeholder="例: おすすめ不用品回収業者">
                </div>
                
                <div class="form-group">
                    <label for="banner_image_url">バナー画像URL</label>
                    <input type="url" id="banner_image_url" name="banner_image_url" 
                           value="<?php echo $edit_banner ? esc_attr($edit_banner->banner_image_url) : ''; ?>"
                           placeholder="https://example.com/banner.jpg">
                    <small>メディアライブラリからアップロードした画像のURLを貼り付けてください</small>
                </div>
                
                <div class="form-group">
                    <label for="affiliate_url">アフィリエイトリンクURL <span class="required">*</span></label>
                    <input type="url" id="affiliate_url" name="affiliate_url" required 
                           value="<?php echo $edit_banner ? esc_attr($edit_banner->affiliate_url) : ''; ?>"
                           placeholder="https://example.com/affiliate">
                </div>
                
                <div class="form-group">
                    <label for="target_prefectures">対応都道府県</label>
                    <input type="text" id="target_prefectures" name="target_prefectures" 
                           value="<?php echo $edit_banner ? esc_attr($edit_banner->target_prefectures) : ''; ?>"
                           placeholder="東京都,神奈川県,埼玉県,千葉県">
                    <small>カンマ区切りで入力してください。空欄の場合は全国対応として扱われます。</small>
                </div>
                
                <div class="form-group">
                    <label for="display_order">表示順序</label>
                    <input type="number" id="display_order" name="display_order" 
                           value="<?php echo $edit_banner ? esc_attr($edit_banner->display_order) : '0'; ?>">
                    <small>数字が小さいほど上に表示されます</small>
                </div>
                
                <div class="form-group checkbox-group">
                    <label>
                        <input type="checkbox" name="is_active" value="1" 
                               <?php echo (!$edit_banner || $edit_banner->is_active) ? 'checked' : ''; ?>>
                        有効にする
                    </label>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <?php echo $edit_banner ? '更新' : '登録'; ?>
                    </button>
                    <?php if ($edit_banner): ?>
                        <a href="<?php echo esc_url(remove_query_arg('edit')); ?>" class="btn btn-secondary">キャンセル</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        
        <!-- バナー一覧 -->
        <div class="admin-table-card">
            <h2>登録済みバナー一覧</h2>
            <?php if (empty($banners)): ?>
                <p class="no-data">まだバナーが登録されていません。</p>
            <?php else: ?>
                <table class="affiliate-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>タイトル</th>
                            <th>対応地域</th>
                            <th>表示順</th>
                            <th>状態</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($banners as $banner): ?>
                            <tr>
                                <td><?php echo esc_html($banner->id); ?></td>
                                <td><?php echo esc_html($banner->title); ?></td>
                                <td>
                                    <?php 
                                    if (empty($banner->target_prefectures)) {
                                        echo '全国';
                                    } else {
                                        $prefs = explode(',', $banner->target_prefectures);
                                        echo esc_html(implode(', ', array_slice($prefs, 0, 3)));
                                        if (count($prefs) > 3) echo '...';
                                    }
                                    ?>
                                </td>
                                <td><?php echo esc_html($banner->display_order); ?></td>
                                <td>
                                    <span class="status-badge <?php echo $banner->is_active ? 'active' : 'inactive'; ?>">
                                        <?php echo $banner->is_active ? '有効' : '無効'; ?>
                                    </span>
                                </td>
                                <td class="actions">
                                    <a href="?edit=<?php echo esc_attr($banner->id); ?>" class="btn-edit">編集</a>
                                    <form method="post" style="display:inline;" onsubmit="return confirm('本当に削除しますか？');">
                                        <?php wp_nonce_field('affiliate_banner_action'); ?>
                                        <input type="hidden" name="affiliate_action" value="delete">
                                        <input type="hidden" name="banner_id" value="<?php echo esc_attr($banner->id); ?>">
                                        <button type="submit" class="btn-delete">削除</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.affiliate-manager-page {
    padding: 3rem 0;
    background: #f8f9fa;
    min-height: 100vh;
}

.page-title {
    font-size: 2.5rem;
    margin-bottom: 2rem;
    color: #2c3e50;
}

.admin-message {
    padding: 1rem 1.5rem;
    border-radius: 8px;
    margin-bottom: 2rem;
}

.admin-message.success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.admin-form-card,
.admin-table-card {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.admin-form-card h2,
.admin-table-card h2 {
    font-size: 1.5rem;
    margin-bottom: 1.5rem;
    color: #2c3e50;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: #2c3e50;
}

.required {
    color: #e74c3c;
}

.form-group input[type="text"],
.form-group input[type="url"],
.form-group input[type="number"] {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 1rem;
}

.form-group small {
    display: block;
    margin-top: 0.5rem;
    color: #6c757d;
    font-size: 0.875rem;
}

.checkbox-group label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: normal;
}

.form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}

.btn {
    padding: 0.75rem 2rem;
    border: none;
    border-radius: 6px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    transition: all 0.3s;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.affiliate-table {
    width: 100%;
    border-collapse: collapse;
}

.affiliate-table th,
.affiliate-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid #dee2e6;
}

.affiliate-table th {
    background: #f8f9fa;
    font-weight: 600;
    color: #2c3e50;
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.875rem;
    font-weight: 600;
}

.status-badge.active {
    background: #d4edda;
    color: #155724;
}

.status-badge.inactive {
    background: #f8d7da;
    color: #721c24;
}

.actions {
    display: flex;
    gap: 0.5rem;
}

.btn-edit,
.btn-delete {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 4px;
    font-size: 0.875rem;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.3s;
}

.btn-edit {
    background: #007bff;
    color: white;
}

.btn-edit:hover {
    background: #0056b3;
}

.btn-delete {
    background: #dc3545;
    color: white;
}

.btn-delete:hover {
    background: #c82333;
}

.no-data {
    text-align: center;
    color: #6c757d;
    padding: 2rem;
}
</style>

<?php get_footer(); ?>
