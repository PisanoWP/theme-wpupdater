  <!-- ======= Footer ======= -->
  <footer>
  
    
  

  
  <hr>
  <?php
  if ( 'production' !== wp_get_environment_type() ):
    echo '<p style="text-align:center;">=>' . wp_get_environment_type() . '<=</p>';
  endif; ?>


   <?php 
   wp_footer(); ?>

</body>
</html>