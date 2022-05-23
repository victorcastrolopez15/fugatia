<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$container = get_theme_mod( 'understrap_container_type' );
?>

<div class="container-fluid fondo_azul_oscuro height_335 px-0 pt-3 mt-4">
	<div class="row mt-5">
		<div class="col-12">
			<h3 class="mayuscula font-size-18px text-center tipografia_naranja separacion"><?php the_field("pretitulo_footer");?></h3>
		</div>
	</div>
	<div class="row">
		<div class="col-12">
			<h2 class="font-size-30px tipografia_blanca text-center separacion"><?php the_field("titulo_footer");?></h2>
		</div>
	</div>
	<div class="row d-flex justify-content-center mt-3">
		<div class="col-6 p-0">
			<div class="container-fluid p-0">
				<div class="row footer_cards">
					<div class="centrar_footer margin_left1_5 p-0">
						<div class="col-6 width_100 margin_right_2rem cards_footer fondo_azul_oscuro_claro p-3 margin_right_2rem">
							<div class="container p-0">
								<div class="row">
									<div class="col-12">
										<img class="w-50" src="<?php the_field("logo_footer");?>" alt="">
									</div>
								</div>
								<div class="row mt-2">
									<div class="col-12">
										<h2 class="mayuscula font-size-20px tipografia_naranja"><?php the_field("texto_postfooter");?></h2>
									</div>
								</div>
								<div class="row margin_top_5">
									<div class="col-12">
										<h3 class="mayuscula font-size-16px tipografia_blanca">Tel. <a class="enlaces_footer" href="tel:+34638131094"><?php the_field("telefono_footer");?></a></h3>
									</div>
								</div>
								<div class="row mt-3">
									<div class="col-12">
										<h3 class="mayuscula font-size-16px tipografia_blanca">Email. <a class="enlaces_footer" href="mailto:INFO@FUGATIA.COM"><?php the_field("email_footer");?></a></h3>
									</div>
								</div>
								<div class="row mt-3">
									<div class="col-12">
										<h3 class="mayuscula font-size-16px tipografia_blanca">DIRECCIÓN. <a class="enlaces_footer" href="#"><?php the_field("direccion_footer");?></a></h3>
									</div>
								</div>
							</div>
						</div>
						<div class="col-6 width_100 margin_top_1rem cards_footer fondo_azul_oscuro_claro p-3 d-flex justify-content-center">
							<div class="width_95"><?php echo do_shortcode('[gravityform id="2" title="false"]');?></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row mt-5">
		<div class="col-12">
			<h2 class="mayuscula text-center font-size-18px tipografia_gris_claro">TAMBIÉN OFRECEMOS SERVICIO EN</h2>
		</div>
	</div>
	<div class="row">
		<div class="col-12">
			<div class="container">
				<div class="row">
					<div class="col-12" style="overflow-x: hidden;">
						<h2 class="mayuscula text-center d-inline-block line_height_1 font-size-16px tipografia_gris_claro centrar_footer">
							<span>ANTEQUERA</span>&nbsp;&nbsp;
							<span>MOLLINA</span>&nbsp;&nbsp;
							<span>MÁLAGA</span>&nbsp;&nbsp;
							<span>BENALMÁDENA</span>&nbsp;&nbsp;
							<span>TORREMOLINOS</span>&nbsp;&nbsp;
							<span>FUENGIROLA</span>&nbsp;&nbsp;
							<span>MIJAS</span>&nbsp;&nbsp;
							<span>MARBELLA</span>&nbsp;&nbsp;
							<span>TORROX</span>&nbsp;&nbsp;
							<span>NERJA</span>&nbsp;&nbsp;
							<span>ARCHIDONA</span>&nbsp;&nbsp;
							<span>RINCÓN DE LA VICTORIA</span>
						</h2>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row mt-3 fondo_azul_oscuro_claro">
		<div class="col-12 d-flex align-items-center height_60 p-0">
			<div class="container">
				<div class="row ">
					<div class="col-12">
						<?php
							wp_nav_menu(
								array(
									'theme_location'  => 'primary',
									'container_id'    => 'navbarNavDropdown',
									'menu_class'      => 'navbar-nav ml-auto',
									'fallback_cb'     => '',
									'menu_id'         => 'main-menu',
									'depth'           => 2,
									'walker'          => new Understrap_WP_Bootstrap_Navwalker(),
								)
							);
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php wp_footer(); ?>

</body>

</html>
