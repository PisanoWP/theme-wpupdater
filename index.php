<?php 
get_header(); ?>
  
  <main id="main">

  
  <div class="container">
  <?php
  if (have_posts()): ?>
    <h2>Últimas entradas</h2>

    <?php 
	  while (have_posts()): the_post(); 
    
      ?>
      <section class="inner-page">
      
      <div class="container">
        <h3><a href="<?php the_permalink();?>" title="<?php echo esc_attr(get_the_title());?>"><?php the_title();?></a></h3>
      </div>
    
      <div class="container d-flex flex-column flex-md-row">                
        <div class="">
          <?php
          the_post_thumbnail('[150, 150]', ['class' => 'services-thumbnail-image img-thumbnail', 'title' => 'Feature image'] ); ?>    
        
      </div>
        <div class="container">
          <?php
          the_content(); ?>
    
        </div>
      </div>
      <footer class="container d-flex justify-content-start  justify-content-md-end">
        <?php the_terms( $post->ID, 'post_tag', 'Etiquetas: ', ' / ' ); ?>
      </footer>
      
    </section>

    <?php
    endwhile;  
        
  else:?>
    <div> No se ha recuperado nada con este criterio</div>

  <?php 
  endif; ?>
  </div>

</main><!-- End #main -->

<?php get_footer(); ?>
