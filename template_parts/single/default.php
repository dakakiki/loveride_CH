<?php
/**
 * Single default: title and content for post types without a dedicated part.
 *
 * @package loveride
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<header class="page-header">
		<h1><?php the_title(); ?></h1>
	</header>

	<div class="page-content">
		<div class="wysiwyg">
			<?php the_content(); ?>
		</div>
	</div>

</article>
