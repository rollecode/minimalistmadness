<?php
/**
 * Main loop for the 404 view. Included by both the classic 404.php
 * template and the minimalistmadness/loop-404 block (FSE path).
 *
 * @package minimalistmadness
 */

namespace Air_Light;
?>

<div  class="content-area">
	<main role="main" id="main" class="site-main">

    <section class="block block-page block-not-found">

      <div class="container container-article">
        <div>

          <div class="container container-article article-content">
            <h1 id="content">Sivua ei löydy</h1>
            <p>Vaikuttaisi siltä, että sivu on siirretty tai poistettu. <a href="<?php echo esc_url( get_home_url() ); ?>">Tästä takaisin etusivulle</a>.</p>
          </div>

        </div>
      </div>

   </section><!-- .error-404 -->

 </div><!-- .container -->
</main><!-- #main -->
</div><!-- #primary -->

