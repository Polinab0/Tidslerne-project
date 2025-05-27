<?php
/*
Template Name: LOGIN
*/
get_header(); ?>



<div class="login-signup-wrapper">
  <?php
  $blocks = new WP_Query([
    'post_type' => 'login_block',
    'posts_per_page' => 2
  ]);

  if ($blocks->have_posts()) :
    while ($blocks->have_posts()) : $blocks->the_post();
      $title = get_field('block_title');
      $subtitle = get_field('block_subtitle');
      $email = get_field('email_label');
      $password = get_field('password_label');
      $button = get_field('button_text');
      $bottom = get_field('bottom_text');
  ?>
    <div class="login-box">
      <h2><?php echo esc_html($title); ?></h2>
      <p><?php echo esc_html($subtitle); ?></p>
      <form>
        <input type="email" placeholder="<?php echo esc_attr($email); ?>">
        <input type="password" placeholder="<?php echo esc_attr($password); ?>">
        <button type="submit"><?php echo esc_html($button); ?></button>
      </form>
      <button type="botton" class="bottom-button"><?php echo esc_html($bottom); ?></button>
    </div>
  <?php endwhile; wp_reset_postdata(); endif; ?>
</div>

<?php 
  $page = get_page_by_title('Page1'); 
  if ($page) {
    $page_id = $page->ID;
    $title = get_field('navbox_title', $page_id);
    $text  = get_field('navbox_text', $page_id);
    $link  = get_field('navbox_link', $page_id);
    $btn   = get_field('navbox_button', $page_id);
  }
?>

<?php if ($title && $text && $link): ?>
  <div class="floating-box">
    <p><strong><?php echo esc_html($title); ?></strong></p>
    <p><?php echo esc_html($text); ?></p>
    <a href="<?php echo esc_url($link); ?>" class="floating-btn">
      <?php echo esc_html($btn ?: 'TRYK HER'); ?>
    </a>
  </div>
<?php endif; ?>





<?php get_footer(); ?>