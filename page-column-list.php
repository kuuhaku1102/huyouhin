<?php
/**
 * Template Name: Column List
 * コラム一覧ページ
 */

get_header();
?>

<main class="column-archive">
  <!-- ヒーローセクション -->
  <section class="archive-hero">
    <div class="container">
      <h1 class="archive-title">不用品回収コラム</h1>
      <p class="archive-description">不用品回収に関する役立つ情報をお届けします</p>
    </div>
  </section>

  <!-- 記事一覧 -->
  <section class="archive-content">
    <div class="container">
      <?php
      // 投稿を取得
      $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
      $args = array(
        'post_type' => 'post',
        'posts_per_page' => 12,
        'paged' => $paged,
        'orderby' => 'date',
        'order' => 'DESC'
      );
      $query = new WP_Query($args);

      if ($query->have_posts()) :
      ?>
        <div class="archive-grid">
          <?php while ($query->have_posts()) : $query->the_post(); ?>
            <article class="archive-card">
              <div class="archive-card-content">
                <div class="archive-card-meta">
                  <span class="archive-card-date">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M19 4H5C3.89543 4 3 4.89543 3 6V20C3 21.1046 3.89543 22 5 22H19C20.1046 22 21 21.1046 21 20V6C21 4.89543 20.1046 4 19 4Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M16 2V6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M8 2V6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M3 10H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <?php echo get_the_date(); ?>
                  </span>
                  
                  <?php
                  $categories = get_the_category();
                  if (!empty($categories)) :
                  ?>
                    <span class="archive-card-category"><?php echo esc_html($categories[0]->name); ?></span>
                  <?php endif; ?>
                </div>
                
                <h2 class="archive-card-title">
                  <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </h2>
                
                <p class="archive-card-excerpt">
                  <?php echo wp_trim_words(get_the_excerpt(), 60, '...'); ?>
                </p>
                
                <a href="<?php the_permalink(); ?>" class="archive-card-link">
                  続きを読む
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 5L19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </a>
              </div>
            </article>
          <?php endwhile; ?>
        </div>

        <!-- ページネーション -->
        <?php if ($query->max_num_pages > 1) : ?>
          <div class="archive-pagination">
            <?php
            echo paginate_links(array(
              'total' => $query->max_num_pages,
              'current' => $paged,
              'prev_text' => '&laquo; 前へ',
              'next_text' => '次へ &raquo;',
            ));
            ?>
          </div>
        <?php endif; ?>

      <?php
      else :
      ?>
        <p class="archive-no-posts">記事が見つかりません。</p>
      <?php
      endif;
      wp_reset_postdata();
      ?>
    </div>
  </section>
</main>

<?php get_footer(); ?>
