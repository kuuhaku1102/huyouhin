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
    <h1>
      <a href="<?php echo esc_url(home_url('/')); ?>">
        <?php bloginfo('name'); ?>
      </a>
    </h1>

    <nav aria-label="Global Navigation">
      <?php
        wp_nav_menu([
          'theme_location' => 'global',
          'container' => false,
          'fallback_cb' => function () {
            echo '<ul><li><a href="' . esc_url(home_url('/')) . '">Home</a></li></ul>';
          },
        ]);
      ?>
      
      <!-- 都道府県ドロップダウン -->
      <div class="prefecture-dropdown">
        <button class="prefecture-btn" aria-haspopup="true" aria-expanded="false">
          <span>都道府県から探す</span>
          <svg width="12" height="8" viewBox="0 0 12 8" fill="none" xmlns="http://www.w3.org/2000/svg">
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
