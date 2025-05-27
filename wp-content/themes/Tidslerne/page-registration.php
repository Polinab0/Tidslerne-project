<?php
/*
Template Name: Registration
*/
get_header(); ?>

<?php
// Получаем логотип и контент из ACF
$logo = get_field('registration_logo');
$btn1 = get_field('button_1_text');
$btn2 = get_field('button_2_text');
$btn3 = get_field('button_3_text');
$btn4 = get_field('button_4_text');
$login_text = get_field('login_button_text');
$login_url = get_field('login_button_url');
?>

<!-- Логотип -->
<?php if ($logo): ?>
  <div class="registration-logo">
    <img src="<?php echo esc_url($logo); ?>" alt="Logo">
  </div>
<?php endif; ?>

<!-- Панель -->
<div class="registration-wrapper">
  <div class="registration-panel">
    <button type="button"><?php echo esc_html($btn1); ?></button>
    <button type="button"><?php echo esc_html($btn2); ?></button>
    <button type="button"><?php echo esc_html($btn3); ?></button>
    <button type="button"><?php echo esc_html($btn4); ?></button>
  </div>

  <div class="login-btn-wrapper">
    <a href="<?php echo esc_url($login_url); ?>" class="login-btn"><?php echo esc_html($login_text); ?></a>
  </div>
</div>








<?php
$info_query = new WP_Query([
  'post_type' => 'registration_info',
  'posts_per_page' => 1
]);

if ($info_query->have_posts()) :
  while ($info_query->have_posts()) : $info_query->the_post(); ?>

  <div class="registration-info-section">
    <div class="info-top-icons">
      <?php
        $icon_left = get_field('icon_left');
        $icon_right = get_field('icon_right');
      ?>
      <?php if ($icon_left): ?>
        <img src="<?php echo esc_url($icon_left['url']); ?>" class="icon" alt="">
      <?php endif; ?>
      <?php if ($icon_right): ?>
        <img src="<?php echo esc_url($icon_right['url']); ?>" class="icon" alt="">
      <?php endif; ?>
    </div>

    <div class="info-columns">
      <div class="left-block">
        <h2><?php the_field('left_title'); ?></h2>
        <h4><?php the_field('left_subtitle_1'); ?></h4>
        <p><?php the_field('left_text_1'); ?></p>
        <h4><?php the_field('left_subtitle_2'); ?></h4>
        <p><?php the_field('left_text_2'); ?></p>
      </div>

      <div class="right-block">
        <h2><?php the_field('right_title'); ?></h2>
        <div class="table-wrap">
          <table class="custom-table">
            <thead>
              <tr>
                <th>Periode</th>
                <th>Medlemstype</th>
                <th>Beløb</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>01.01 – 31.12</td>
                <td>Støtte– eller patientmedlem</td>
                <td>dkr 225,-</td>
              </tr>
              <tr>
                <td>01.07 – 31.12</td>
                <td>Støtte– eller patientmedlem</td>
                <td>dkr 100,-</td>
              </tr>
              <tr>
                <td>01.01 – 31.12</td>
                <td>Husstandsmedlem (kun i forbindelse med støtte– eller patientmedlem)</td>
                <td>dkr 25,-</td>
              </tr>
            </tbody>
          </table>
        </div>
        <p><?php the_field('right_text'); ?></p>
      </div>
    </div>

    <div class="bottom-note">
      <p><?php the_field('bottom_text'); ?></p>
    </div>
  </div>

<?php endwhile; wp_reset_postdata(); endif; ?>











<?php
$form_query = new WP_Query([
  'post_type' => 'registration_form',
  'posts_per_page' => 1
]);

if ($form_query->have_posts()) :
  while ($form_query->have_posts()) : $form_query->the_post(); ?>

  <div class="registration-form-wrapper">
    <h2 class="form-title"><?php the_field('form_title'); ?></h2>

    <form action="" method="post" class="registration-form">
      <input type="text" name="first_name" placeholder="<?php the_field('first_name'); ?>">
      <input type="text" name="middle_name" placeholder="<?php the_field('middle_name'); ?>">
      <input type="text" name="last_name" placeholder="<?php the_field('last_name'); ?>">
      <input type="text" name="address" placeholder="<?php the_field('address'); ?>">
      <input type="text" name="postnr" placeholder="<?php the_field('postnr'); ?>">
      <input type="text" name="city" placeholder="<?php the_field('city'); ?>">

      <select name="country">
        <?php
        $choices = get_field_object('country')['choices'];
        foreach ($choices as $value => $label) {
          echo "<option value='$value'>$label</option>";
        }
        ?>
      </select>

      <input type="email" name="email" placeholder="<?php the_field('email'); ?>">
      <input type="text" name="phone" placeholder="<?php the_field('phone'); ?>">
      <input type="text" name="mobile" placeholder="<?php the_field('mobile'); ?>">

      <div class="birth-row">
        <select name="birth_day">
          <?php
          $days = get_field_object('birth_day')['choices'];
          foreach ($days as $value => $label) {
            echo "<option value='$value'>$label</option>";
          }
          ?>
        </select>

        <select name="birth_month">
          <?php
          $months = get_field_object('birth_month')['choices'];
          foreach ($months as $value => $label) {
            echo "<option value='$value'>$label</option>";
          }
          ?>
        </select>

        <select name="birth_year">
          <?php
          $years = get_field_object('birth_year')['choices'];
          foreach ($years as $value => $label) {
            echo "<option value='$value'>$label</option>";
          }
          ?>
        </select>
      </div>

      <select name="gender">
        <?php
        $genders = get_field_object('gender')['choices'];
        foreach ($genders as $value => $label) {
          echo "<option value='$value'>$label</option>";
        }
        ?>
      </select>

      <select name="region">
        <?php
        $regions = get_field_object('region')['choices'];
        foreach ($regions as $value => $label) {
          echo "<option value='$value'>$label</option>";
        }
        ?>
      </select>

      <div class="form-buttons">
  <button type="submit" class="submit-btn">Næste side</button>
  <a href="<?php echo site_url('/login'); ?>" class="login-link-btn">Login</a>
</div>
    </form>
  </div>

<?php endwhile; wp_reset_postdata(); endif; ?>







<?php get_footer(); ?>
