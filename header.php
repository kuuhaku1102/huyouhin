<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<header>
  <div class="container">
    <div class="header-logo">
      <a href="<?php echo esc_url(home_url('/')); ?>">
        <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
          <rect width="32" height="32" rx="8" fill="#1e40af"/>
          <path d="M16 8L8 14V24H12V18H20V24H24V14L16 8Z" fill="white"/>
        </svg>
        <span class="site-name"><?php bloginfo('name'); ?></span>
      </a>
    </div>

    <nav class="header-nav" aria-label="Global Navigation">
      <a href="<?php echo esc_url(home_url('/')); ?>" class="nav-link">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M2 6L8 2L14 6V13C14 13.5304 13.7893 14.0391 13.4142 14.4142C13.0391 14.7893 12.5304 15 12 15H4C3.46957 15 2.96086 14.7893 2.58579 14.4142C2.21071 14.0391 2 13.5304 2 13V6Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M6 15V8H10V15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span>ホーム</span>
      </a>
      
      <a href="<?php echo esc_url(home_url('/rankings/')); ?>" class="nav-link">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M8 1L10.163 5.38L15 6.12L11.5 9.55L12.326 14.36L8 12.1L3.674 14.36L4.5 9.55L1 6.12L5.837 5.38L8 1Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span>ランキング</span>
      </a>
      
      <!-- 都道府県ドロップダウン -->
      <div class="prefecture-dropdown">
        <button class="nav-link prefecture-btn" aria-haspopup="true" aria-expanded="false">
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M8 1C4.134 1 1 4.134 1 8C1 11.866 4.134 15 8 15C11.866 15 15 11.866 15 8C15 4.134 11.866 1 8 1Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M1 8H15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M8 1C9.657 2.84 10.6 5.343 10.6 8C10.6 10.657 9.657 13.16 8 15C6.343 13.16 5.4 10.657 5.4 8C5.4 5.343 6.343 2.84 8 1Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          <span>都道府県</span>
          <svg class="dropdown-arrow" width="12" height="8" viewBox="0 0 12 8" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M1 1L6 6L11 1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </button>
        <div class="prefecture-menu" role="menu">
          <?php
          $prefectures = [
            '北海道・東北' => ['北海道', '青森県', '岩手県', '宮城県', '秋田県', '山形県', '福島県'],
            '関東' => ['茨城県', '栃木県', '群馬県', '埼玉県', '千葉県', '東京都', '神奈川県'],
            '中部' => ['新潟県', '富山県', '石川県', '福井県', '山梨県', '長野県', '岐阜県', '静岡県', '愛知県'],
            '近畿' => ['三重県', '滋賀県', '京都府', '大阪府', '兵庫県', '奈良県', '和歌山県'],
            '中国' => ['鳥取県', '島根県', '岡山県', '広島県', '山口県'],
            '四国' => ['徳島県', '香川県', '愛媛県', '高知県'],
            '九州・沖縄' => ['福岡県', '佐賀県', '長崎県', '熊本県', '大分県', '宮崎県', '鹿児島県', '沖縄県']
          ];
          
          foreach ($prefectures as $region => $prefs) {
            echo '<div class="prefecture-region">';
            echo '<h4>' . esc_html($region) . '</h4>';
            echo '<ul>';
            foreach ($prefs as $pref) {
              $pref_url = home_url('/' . $pref . '/');
              echo '<li><a href="' . esc_url($pref_url) . '">' . esc_html($pref) . '</a></li>';
            }
            echo '</ul>';
            echo '</div>';
          }
          ?>
        </div>
      </div>
      
      <a href="<?php echo esc_url(home_url('/quote/')); ?>" class="nav-link nav-cta">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M14 11.5C14 11.7652 13.8946 12.0196 13.7071 12.2071C13.5196 12.3946 13.2652 12.5 13 12.5H4L1 15V3C1 2.73478 1.10536 2.48043 1.29289 2.29289C1.48043 2.10536 1.73478 2 2 2H13C13.2652 2 13.5196 2.10536 13.7071 2.29289C13.8946 2.48043 14 2.73478 14 3V11.5Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span>無料見積もり</span>
      </a>
    </nav>
  </div>
</header>

<script>
// 都道府県ドロップダウンの開閉
document.addEventListener('DOMContentLoaded', function() {
  const btn = document.querySelector('.prefecture-btn');
  const menu = document.querySelector('.prefecture-menu');
  
  if (btn && menu) {
    btn.addEventListener('click', function(e) {
      e.stopPropagation();
      const isExpanded = btn.getAttribute('aria-expanded') === 'true';
      btn.setAttribute('aria-expanded', !isExpanded);
      menu.classList.toggle('active');
    });
    
    // 外側をクリックしたら閉じる
    document.addEventListener('click', function() {
      btn.setAttribute('aria-expanded', 'false');
      menu.classList.remove('active');
    });
    
    // メニュー内のクリックは伝播させない
    menu.addEventListener('click', function(e) {
      e.stopPropagation();
    });
  }
});
</script>
