<?php get_search_form(); ?>

<form role="search" method="get" action="<?php echo esc_url (home_url('/')); ?>">
<input type="search" name="s" placeholder="Search...">
<button type="submit">Search</button>
</form>

<?php get_header(); ?>

<h1>Search Results for: <?php echo get_search_query(): ?></h1>

<?php if (have_posts()): ?>
<ul>
<?php while(have_posts ()): the_post(); ?>
<?php
$title = get_the_title();
$link = get_the_permalink(); ?>

<li>
<a href="<?php echo esc_url($link) ?>"><?php echo esc_html($title) ?></a>
</li>

<?php endwhile; ?>
</ul>

<?php else ; ?> 
<p>sp-No results found.</p>
<?php endif; ?>

<?php get_footer(); ?>