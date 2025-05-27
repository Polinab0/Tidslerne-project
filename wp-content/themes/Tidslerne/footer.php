<?php wp_footer(); ?>


<!-- Подключаем простой JavaScript -->
<script>
  document.addEventListener('DOMContentLoaded', function () {
    console.log('✅ JavaScript загружен и работает!');

    // === Поиск ===
    const searchBtn = document.getElementById('search-button');
    const searchForm = document.getElementById('search-form');

    if (searchBtn && searchForm) {
      searchBtn.addEventListener('click', function (e) {
        e.preventDefault();
        searchForm.style.display = (searchForm.style.display === 'block') ? 'none' : 'block';
      });

      document.addEventListener('click', function (e) {
        if (!searchForm.contains(e.target) && !searchBtn.contains(e.target)) {
          searchForm.style.display = 'none';
        }
      });
    }

    // === Выпадающее меню TIDSLERNE ===
    const menuItem = document.querySelector('.menu li a[href="#"]');
    const dropdown = document.getElementById('custom-dropdown');

    if (menuItem && dropdown) {
      menuItem.addEventListener('click', function (e) {
        e.preventDefault();
        dropdown.style.display = (dropdown.style.display === 'block') ? 'none' : 'block';
      });

      document.addEventListener('click', function (e) {
        if (!dropdown.contains(e.target) && !menuItem.contains(e.target)) {
          dropdown.style.display = 'none';
        }
      });
    }
  });
</script>







<footer class="site-footer">
  <?php
  $footer = new WP_Query([
    'post_type' => 'footer_block',
    'posts_per_page' => 1
  ]);
  if ($footer->have_posts()) :
    while ($footer->have_posts()) : $footer->the_post();
  ?>

  <!-- 5 колонок -->
  <div class="footer-top">
    <div class="footer-column">
      <h3><?php the_field('section1_title'); ?></h3>
      <p><?php the_field('section1_content'); ?></p>
    </div>
    <div class="footer-column">
      <h3><?php the_field('section2_title'); ?></h3>
      <p><?php the_field('section2_line1'); ?></p>
      <p><?php the_field('section2_line2'); ?></p>
    </div>
    <div class="footer-column">
      <h3><?php the_field('section3_title'); ?></h3>
      <p><?php the_field('section3_content'); ?></p>
    </div>
    <div class="footer-column">
      <h3><?php the_field('section4_title'); ?></h3>
      <p>
  <?php $phone_icon = get_field('footer_phone_icon'); ?>
  <?php if ($phone_icon): ?>
    <img src="<?php echo esc_url($phone_icon['url']); ?>" class="footer-icon" alt="">
  <?php endif; ?>
  <?php the_field('footer_phone_text'); ?>
</p>

<p>
  <?php $email_icon = get_field('footer_email_icon'); ?>
  <?php if ($email_icon): ?>
    <img src="<?php echo esc_url($email_icon['url']); ?>" class="footer-icon" alt="">
  <?php endif; ?>
  <?php the_field('footer_email_text'); ?>
</p>

<p>
  <?php $email_icon2 = get_field('footer_email_icon_2'); ?>
  <?php if ($email_icon2): ?>
    <img src="<?php echo esc_url($email_icon2['url']); ?>" class="footer-icon" alt="">
  <?php endif; ?>
  <?php the_field('footer_email_text_2'); ?>
</p>

<p>
  <?php $cvr_icon = get_field('footer_cvr_icon'); ?>
  <?php if ($cvr_icon): ?>
    <img src="<?php echo esc_url($cvr_icon['url']); ?>" class="footer-icon" alt="">
  <?php endif; ?>
  <?php the_field('footer_cvr_text'); ?>
</p>
    </div>
    <div class="footer-column footer-map-column">
  <h3><?php the_field('section5_title'); ?></h3>

  <?php $map = get_field('map_image'); ?>
  <?php if ($map): ?>
    <img src="<?php echo esc_url($map['url']); ?>" class="footer-map" alt="Map">
  <?php endif; ?>

  <p><?php the_field('map_address'); ?></p>
</div>
</div> <!-- end of .footer-top -->
  <!-- Линия -->
  <div class="footer-line-short"></div>

  <!-- Социальные иконки -->
  <div class="footer-socials">
  <?php $facebook_icon = get_field('footer_facebook_icon'); ?>
  <a href="<?php the_field('footer_facebook_link'); ?>">
    <?php if ($facebook_icon): ?>
      <img src="<?php echo esc_url($facebook_icon['url']); ?>" alt="">
    <?php endif; ?>
  </a>

  <?php $mail_icon = get_field('footer_mail_icon'); ?>
  <a href="<?php the_field('footer_mail_link'); ?>">
    <?php if ($mail_icon): ?>
      <img src="<?php echo esc_url($mail_icon['url']); ?>" alt="">
    <?php endif; ?>
  </a>

  <?php $instagram_icon = get_field('footer_instagram_icon'); ?>
  <a href="<?php the_field('footer_instagram_link'); ?>">
    <?php if ($instagram_icon): ?>
      <img src="<?php echo esc_url($instagram_icon['url']); ?>" alt="">
    <?php endif; ?>
  </a>

  <?php $youtube_icon = get_field('footer_youtube_icon'); ?>
  <a href="<?php the_field('footer_youtube_link'); ?>">
    <?php if ($youtube_icon): ?>
      <img src="<?php echo esc_url($youtube_icon['url']); ?>" alt="">
    <?php endif; ?>
  </a>

  <?php $x_icon = get_field('footer_x_icon'); ?>
  <a href="<?php the_field('footer_x_link'); ?>">
    <?php if ($x_icon): ?>
      <img src="<?php echo esc_url($x_icon['url']); ?>" alt="">
    <?php endif; ?>
  </a>

  <?php $pinterest_icon = get_field('footer_pinterest_icon'); ?>
  <a href="<?php the_field('footer_pinterest_link'); ?>">
    <?php if ($pinterest_icon): ?>
      <img src="<?php echo esc_url($pinterest_icon['url']); ?>" alt="">
    <?php endif; ?>
  </a>
</div>

  <!-- Белая линия -->
  <div class="footer-line white"></div>

  <!-- Нижние ссылки -->
  <div class="footer-bottom">
    <p><?php the_field('footer_link_row_one_text'); ?>
      <a href="<?php the_field('footer_link_row_one_url'); ?>"><?php the_field('footer_link_row_one_link_text'); ?></a>
    </p>
    <p><?php the_field('footer_link_row_two_text'); ?>
      <a href="<?php the_field('footer_link_row_two_url'); ?>"><?php the_field('footer_link_row_two_link_text'); ?></a>
    </p>
  </div>

  <?php endwhile; wp_reset_postdata(); endif; ?>
</footer>

</body>
</html>


</body>
</html>
