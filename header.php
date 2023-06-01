<!DOCTYPE html><html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">
  
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  
  <meta content="" name="description">
  <meta content="" name="keywords">

  
  <?php
  wp_head(); ?>
    
  
</head>
<body <?php body_class(); ?> >
  
    <section >
      <div class="hero-container">        
        <h1 style="font-size:2rem;text-align:center;text-transform: uppercase;">
          <?php echo get_bloginfo( 'name' ); ?>
        </h1>
        <h2 style="font-size:1.5rem;text-align:center;">
          <?php echo get_bloginfo( 'description' ); ?>
        </h2>        
      </div>
    </section>

    <hr>