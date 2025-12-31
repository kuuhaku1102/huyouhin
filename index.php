<?php get_header(); ?>

<main class="container">
  <?php if ( have_posts() ) : ?>
    <?php while ( have_posts() ) : the_post(); ?>
      <article <?php post_class(); ?>>
        <h2>
          <a href="<?php the_permalink(); ?>">
            <?php the_title(); ?>
          </a>
        </h2>

        <div class="content">
          <?php the_content(); ?>
        </div>
      </article>
    <?php endwhile; ?>

    <div class="pagination">
      <?php the_posts_pagination(); ?>
    </div>
  テスト
  <?php else : ?>
    <p>記事が見つかりません。</p>
  <?php endif; ?>
</main>

<?php get_footer(); ?>
