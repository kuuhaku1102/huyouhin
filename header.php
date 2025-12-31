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
    </nav>
  </div>
</header>
