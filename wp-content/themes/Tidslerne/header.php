<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php bloginfo('name'); ?></title>
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<header class="site-header">
  <nav class="main-nav">

 


   
    <?php
      wp_nav_menu([
        'theme_location' => 'main-menu',
        'menu_class' => 'menu',
      ]);
    ?>
    <button class="burger" onclick="document.querySelector('.menu').classList.toggle('active')">☰</button>


    <div class="search-container">
      <button id="search-button" class="search-icon" aria-label="Search">
        <?php
         
          $page = get_page_by_title('Page1');
          if ($page) {
            $page_id = $page->ID;
            $icon = get_field('search_icon', $page_id);
            if ($icon) {
              echo '<img src="' . esc_url($icon['url']) . '" alt="Search" style="width: 20px; height: 20px;">';
            } 
          } 
        ?>
      </button>

      <form id="search-form" method="get" action="<?php echo home_url('/'); ?>">
        <input type="text" name="s" class="search-field">
        <button type="submit" class="search-submit">OK</button>
      </form>
    </div>
    

  </nav>

  <button class="burger-btn" onclick="toggleMenu()">
  <span></span>
  <span></span>
  <span></span>
</button>

<script>
  function toggleMenu() {
    const menu = document.getElementById("mobileMenu");
    const body = document.body;
    const burger = document.querySelector(".burger-btn");

    const isOpen = menu.classList.toggle("active");
    body.classList.toggle("no-scroll", isOpen);
    burger.style.display = isOpen ? "none" : "block";
  }
</script>




<div class="mobile-menu" id="mobileMenu">
  <div class="menu-header">
    <span class="menu-logo">Tid<span style="color:#93326D;">søerne</span></span>
    <button class="close-btn" onclick="toggleMenu()">×</button>
  </div>
  
  <input type="text" placeholder="Søge" class="search-input" />


  <div class="menu-scroll-wrapper">
    <ul class="mobile-menu-list">
      <li><a href="<?php echo site_url('/'); ?>">HOME</a></li>
      <li>TIDSLERNE 
        <ul class="submenu">
          <li>Bliv medlem af Tidslerne</li>
          <li>Hvem er Tidslerne</li>
          <li>MedlemsPortal</li>
          <li>Kontakt os</li>
          <li>Ring til Tidsellinjen</li>
          <li>Vedtœegter</li>
          <li>Bestyrelsen</li>
          <li>Kredsene</li>
          <li>Støt Tidslerne</li>
          <li>Bladet Tidslerne</li>
          <li>Videobiblioteket</li>
        </ul>
      </li>
      <li>BEHANDLINGER</li>
      <li><a href="<?php echo site_url('kost-krop'); ?>">KOST & KROP</a></li>
      <li>ARRANGEMENTER</li>
      <li>INFO</li>
      <li><a href="<?php echo site_url('/login'); ?>">LOGIN</a></li>
    </ul>
  </div>
</div>

  
  <div id="custom-dropdown" class="custom-dropdown">
  <ul>
    <li><a href="#">Bliv medlem af Tidslerne</a></li>
    <li><a href="#">Hvem er Tidslerne</a></li>
    <li><a href="#">MedlemsPortal</a></li>
    <li><a href="#">Kontakt os</a></li>
    <li><a href="#">Ring til Tidsellinjen</a></li>
    <li><a href="#">Vedtœegter</a></li>
    <li><a href="#">Bestyrelsen</a></li>
    <li><a href="#">Kredsene</a></li>
    <li><a href="#">Støt Tidslerne</a></li>
    <li><a href="#">Bladet Tidslerne</a></li>
    <li><a href="#">Videobiblioteket</a></li>
  </ul>
</div>


<form id="search-form" action="" method="get">
  <input type="text" id="search-field" name="s" class="search-field" placeholder="Søge…">
  <button type="submit" class="search-submit">OK</button>
</form>

<script>
document.addEventListener('DOMContentLoaded', function(){
  var form = document.getElementById('search-form');
  if (!form) return;
  form.addEventListener('submit', function(e){
    e.preventDefault();
   
    var q = form.querySelector('input[name="s"]').value.trim().toLowerCase();
    if (!q) return;
  
    var slug = q.replace(/\s+/g,'-').replace(/[^a-z0-9\-]/g,'');
    
    window.location.href = '<?php echo esc_js( home_url() ); ?>/' + encodeURIComponent(slug) + '/';
  });
});
</script>






</header>
