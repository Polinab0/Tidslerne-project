<?php
/*
Template Name: Page One
*/
get_header(); ?>

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
<main class="site-main">

  <!-- Картинка из ACF -->
  <?php
  $image_url = get_field('page1_image_');
  if ($image_url) {
    echo '<img src="' . esc_url($image_url) . '" alt="Page image" class="page1-img">';
  } else {
    echo '<p style="color:red;">Картинка не выбрана в ACF.</p>';
  }
  ?>

  <!-- Стандартный контент страницы -->
  <div class="content">
    <?php the_content(); ?>
  </div>

  </main>

<hr class="full-line"> <!-- ВНЕ .site-main -->




  <!-- Первый блок — Page Blocks -->
  <div class="page-blocks">
    <?php
    $blocks = new WP_Query([
      'post_type' => 'page_block',
      'posts_per_page' => -1
    ]);

    if ($blocks->have_posts()) :
      while ($blocks->have_posts()) : $blocks->the_post();

        $title = get_field('block_title');
        $subtitle = get_field('block_subtitle');
        $text = get_field('block_content');
    ?>
        <div >
          <h1><?php echo esc_html($title); ?></h1>
          <h2><?php echo esc_html($subtitle); ?></h2>
          <div><?php echo $text; ?></div>
        </div>
    <?php endwhile;
      wp_reset_postdata();
    else :
      echo '<p>Пока нет блоков</p>';
    endif;
    ?>
  </div>

  <!-- Второй блок — Page Sections -->

  <div class="custom-blocks">
  <?php
  $blocks = new WP_Query([
    'post_type' => 'custom_block',
    'posts_per_page' => -1
  ]);

  while ($blocks->have_posts()) : $blocks->the_post();
    $image = get_field('block_image');
    $title = get_field('block_title');
    $text = get_field('block_text');
  ?>
    <div class="block">
      <?php if ($image): ?>
        <img class="block-image" src="<?php echo esc_url($image['url']); ?>" alt="">
      <?php endif; ?>

      <?php if ($title): ?>
        <h2 class="block-title"><?php echo esc_html($title); ?></h2>
      <?php endif; ?>

      <?php if ($text): ?>
        <p class="block-text"><?php echo esc_html($text); ?></p>
      <?php endif; ?>
    </div>
  <?php endwhile; wp_reset_postdata(); ?>
</div>






<div class="card-section">
  <div class="card-grid">
    <?php
    $cards = new WP_Query([
      'post_type' => 'card_block',
      'posts_per_page' => 4,
    ]);

    if ($cards->have_posts()) :
      while ($cards->have_posts()) : $cards->the_post();
        $icon = get_field('icon');
        $title = get_field('title');
        $description = get_field('description');
    ?>
      <div class="card-box">
        <?php if ($icon): ?>
          <img src="<?php echo esc_url($icon['url']); ?>" alt="icon" class="card-icon">
        <?php endif; ?>
        <h3 class="card-title"><?php echo esc_html($title); ?></h3>
        <p class="card-description"><?php echo esc_html($description); ?></p>
      </div>
    <?php endwhile; wp_reset_postdata(); ?>
  </div>

  <div class="card-button-wrapper">
    <a href="#" class="card-button">Se mere</a>
  </div>
</div>
<?php endif; ?>






<!-- Блок с заголовком и кнопками -->
<div class="title-buttons-block">
  <div class="left-title">
    
    <?php
      $title = get_field('buttons_block_title');
      if ($title) {
        echo '<h2>' . esc_html($title) . '</h2>';
      }
    ?>
  </div>

  <div class="right-buttons">
    <?php
    $buttons = new WP_Query([
      'post_type' => 'button_item',
      'posts_per_page' => 3,
    ]);

    if ($buttons->have_posts()) :
      while ($buttons->have_posts()) : $buttons->the_post();
        $text = get_field('button_text');
        $link = get_field('button_link');
    ?>
      <a href="<?php echo esc_url($link); ?>" class="btn-box"><?php echo esc_html($text); ?></a>
    <?php endwhile; wp_reset_postdata(); endif; ?>
  </div>
</div>





<div class="article-section">
<div class="article-grid-wrapper">
  <div class="article-grid">
    <?php
    $articles = new WP_Query([
      'post_type' => 'article_block',
      'posts_per_page' => 4
    ]);

    if ($articles->have_posts()) :
      while ($articles->have_posts()) : $articles->the_post();
        $image = get_field('article_image');
        $title = get_field('article_title');
        $desc = get_field('article_description');
    ?>
      <div class="article-box">
        <?php if ($image): ?>
          <img src="<?php echo esc_url($image['url']); ?>" alt="" class="article-img">
        <?php endif; ?>
        <h3 class="article-title"><?php echo esc_html($title); ?></h3>
        <p class="article-desc"><?php echo esc_html($desc); ?></p>
      </div>
    <?php endwhile; wp_reset_postdata(); endif; ?>
  </div>
</div>
</div>
<div class="scroll-line-container">
  <div class="scroll-line-with-arrow"></div>
</div>






<!-- Заголовок блока -->
<div class="information-title-block">
  <?php
  $title = get_field('information_section_title');
  if ($title) {
    echo '<h2 class="info-title">' . esc_html($title) . '</h2>';
  }
  ?>
</div>

<!-- Карточки -->
<div class="information-section">
  <div class="information-grid-wrapper">
    <div class="information-grid">
      <?php
      $cards = new WP_Query([
        'post_type' => 'info_card',
        'posts_per_page' => 4,
      ]);

      if ($cards->have_posts()) :
        while ($cards->have_posts()) : $cards->the_post();
          $img = get_field('image');
          $title = get_field('title');
          $date = get_field('date');
      ?>
        <div class="info-box">
          <?php if ($img): ?>
            <img src="<?php echo esc_url($img['url']); ?>" class="info-img" alt="">
          <?php endif; ?>
          <h3 class="info-card-title"><?php echo esc_html($title); ?></h3>
          <p class="info-date"><?php echo esc_html($date); ?></p>
        </div>
      <?php endwhile; wp_reset_postdata(); endif; ?>
    </div>
  </div>
</div>
<div class="scroll-line-container">
  <div class="scroll-line-with-arrow"></div>
</div>












<!-- Заголовок блока -->
<div class="story-title-block">
  <?php
  $title = get_field('story_block_title');
  if ($title) {
    echo '<h2 class="story-title">' . esc_html($title) . '</h2>';
  }
  ?>
</div>

<!-- Карточки -->
<div class="story-section">
  <div class="story-grid">
    <?php
    $stories = new WP_Query([
      'post_type' => 'story',
      'posts_per_page' => 3,
    ]);

    if ($stories->have_posts()) :
      while ($stories->have_posts()) : $stories->the_post();

        $avatar = get_field('avatar');
        $name = get_field('name');
        $email = get_field('email');
        $date = get_field('date');
        $desc = get_field('description');
    ?>
      <div class="story-box">
        <div class="story-top">
          <?php if ($avatar): ?>
            <img src="<?php echo esc_url($avatar['url']); ?>" class="story-img" alt="avatar">
          <?php endif; ?>
          <div class="story-info">
            <h3 class="story-name"><?php echo esc_html($name); ?></h3>
            <p class="story-email"><?php echo esc_html($email); ?></p>
            <p class="story-date"><?php echo esc_html($date); ?></p>
          </div>
        </div>
        <p class="story-desc"><?php echo esc_html($desc); ?></p>
      </div>
    <?php endwhile;
      wp_reset_postdata();
    endif;
    ?>
  </div>
</div>

<!-- Линия Scroll -->
<div class="scroll-line-container">
  <div class="scroll-line-with-arrow"></div>
</div>









<!-- Feedback form -->
<div class="feedback-block">
  <div class="feedback-header">
    <h1><?php the_field('feedback_title'); ?></h1>
    <p><?php the_field('feedback_subtitle'); ?></p>
  </div>

  <form class="feedback-form" method="post">
    <textarea placeholder="Tekst ..."></textarea>

    <div class="feedback-side">
      <div class="file-buttons">
        <?php $camera = get_field('feedback_icon_camera'); ?>
        <?php $png = get_field('feedback_icon_png'); ?>
        <?php $gif = get_field('feedback_icon_gif'); ?>
        <?php $jpg = get_field('feedback_icon_jpg'); ?>

        <?php if ($camera): ?>
          <button type="button"><img src="<?php echo esc_url($camera['url']); ?>" class="icon-img" alt="Camera"></button>
        <?php endif; ?>
        <?php if ($png): ?>
          <button type="button"><img src="<?php echo esc_url($png['url']); ?>" class="icon-img" alt="PNG"></button>
        <?php endif; ?>
        <?php if ($gif): ?>
          <button type="button"><img src="<?php echo esc_url($gif['url']); ?>" class="icon-img" alt="GIF"></button>
        <?php endif; ?>
        <?php if ($jpg): ?>
          <button type="button"><img src="<?php echo esc_url($jpg['url']); ?>" class="icon-img" alt="JPG"></button>
        <?php endif; ?>
      </div>

      <button type="submit" class="send-btn">Sende</button>
    </div>
  </form>
</div>







<!-- Заголовок -->
<?php
$title = get_field('video_section_title');
if ($title) {
  echo '<h2 class="video-section-title">' . esc_html($title) . '</h2>';
}
?>

<!-- Видео блок -->
<div class="video-section-wrapper">
  <div class="video-left-column">
    <?php
    $videos = new WP_Query([
      'post_type' => 'video_block',
      'posts_per_page' => 2
    ]);

    if ($videos->have_posts()) :
      while ($videos->have_posts()) : $videos->the_post();
        $link = get_field('video_iframe');
        if ($link) {
          echo '<div class="video-small">' . wp_oembed_get($link) . '</div>';
        }
      endwhile;
      wp_reset_postdata();
    endif;
    ?>
  </div>

  <div class="video-right-column">
    <?php
    $videos = new WP_Query([
      'post_type' => 'video_block',
      'posts_per_page' => 1,
      'offset' => 2
    ]);

    if ($videos->have_posts()) :
      while ($videos->have_posts()) : $videos->the_post();
        $link = get_field('video_iframe');
        if ($link) {
          echo '<div class="video-large">' . wp_oembed_get($link) . '</div>';
        }
      endwhile;
      wp_reset_postdata();
    endif;
    ?>

    <!-- Кнопка -->
    <div class="video-button-wrapper">
      <a href="#" class="video-button">Se mere</a>
    </div>
  </div>
</div>





<?php get_footer(); ?>
