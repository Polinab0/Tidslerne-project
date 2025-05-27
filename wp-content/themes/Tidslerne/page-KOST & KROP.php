<?php
/*
Template Name: KOST & KROP
*/

get_header();
?>


<!-- Зеленая картинка (не фон) -->
<?php $bg_image = get_field('kostrag_bg'); ?>
<?php if ($bg_image): ?>
  <div class="kostrad-hero" style="background-image: url('<?php echo esc_url($bg_image); ?>');"></div>
<?php endif; ?>

<!-- Белый блок с карточками -->
<div class="kostrad-card-container">
  <div class="cards-grid">

    <?php
    $cards = new WP_Query([
      'post_type' => 'kost_card',
      'posts_per_page' => -1
    ]);
    if ($cards->have_posts()):
      while ($cards->have_posts()): $cards->the_post();
        $img = get_field('card_image');
        $title = get_field('card_title');
        $date = get_field('card_date');
        $text = get_field('card_text');
    ?>
      <div class="kost-card">
        <?php if ($img): ?>
          <img src="<?php echo esc_url($img['url']); ?>" alt="">
        <?php endif; ?>
        <h4><?php echo esc_html($title); ?></h4>
        <p class="date"><?php echo esc_html($date); ?></p>
        <p class="excerpt"><?php echo esc_html($text); ?></p>
      </div>
    <?php endwhile; wp_reset_postdata(); endif; ?>

  </div>
</div>




<?php get_footer(); ?>