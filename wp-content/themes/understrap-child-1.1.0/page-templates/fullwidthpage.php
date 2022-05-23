<?php
/**
 * Template Name: Full Width Page
 *
 * Template for displaying a page without sidebar even if a sidebar widget is published.
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();
?>

<main>
	<div class="container">
		<div class="row mt-4">
			<div class="col-lg-8">
				<div class="container p-0">
					<div class="row text_center">
						<div class="col-12 p-0">
							<h1 class="mayuscula font-size-16px tipografia_naranja separacion"><?php the_field("pretitulo");?></h1>
						</div>
					</div>
					<div class="row text_center">
						<div class="col-12 p-0">
							<h2 class="font-size-34px separacion"><?php the_title();?></h2>
						</div>
					</div>
					<div class="row mt-3 centrar2">
						<div class="col-8 p-0">
							<div class="row">
								<div class="col-12">
									<p class="font-size-16px texto_regular"> 
										<span class="tipografia_naranja"><i class="fa-solid fa-check"></i></span>&nbsp;&nbsp;&nbsp;&nbsp;<?php the_field("texto1_postitulo");?>
									</p>
								</div>
							</div>
							<div class="row">
								<div class="col-12">
									<p class="font-size-16px texto_regular"> 
										<span class="tipografia_naranja"><i class="fa-solid fa-check"></i></span>&nbsp;&nbsp;&nbsp;&nbsp;<?php the_field("texto2_postitulo");?>
									</p>
								</div>
							</div>
							<div class="row">
								<div class="col-12">
									<p class="font-size-16px texto_regular"> 
										<span class="tipografia_naranja"><i class="fa-solid fa-check"></i></span>&nbsp;&nbsp;&nbsp;&nbsp;<?php the_field("texto3_postitulo");?>
									</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-4">
				<div class="container p-0">
					<div class="row centrar">
						<div class="col-12 fondo_blanco d-flex flex-column align-items-center w-75 p-0 form1">
							<h2 class="mayuscula font-size-16px text-center tipografia_naranja separacion mt-3"><?php the_field("texto1_form1");?></h2>
							<h3 class="text-center font-size-17px"><?php the_field("texto2_form1");?></h3>
							<div class="width_95">
								<?php echo do_shortcode('[gravityform id="1" title="false"]')?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="fondo1 height_440 margin_top_17"></div>
	<div class="container mt-5 margin_bottom_47">
		<div class="row">
			<div class="col-12">
				<h3 class="mayuscula font-size-16px text-center tipografia_naranja separacion"><?php the_field("pretitulo2");?></h3>
			</div>
		</div>
		<div class="row">
			<div class="col-12">
				<h2 class="font-size-34px text-center separacion"><?php the_field("titulo2");?></h2>
			</div>
		</div>
		<div class="row mt-5">
			<div class="col-md-3">
				<div class="d-flex flex-column align-items-center">
					<div class="cards_img">
						<img class="w-100" src="<?php the_field("Imagen1");?>" class="card-img-top" alt="Imagen2">
					</div>
					<div class="card-body">
						<h5 class="card-title font-size-16px text-center line_height_1"><?php the_field("texto_postimagen1");?></h5>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="d-flex flex-column align-items-center">
					<div class="cards_img">
						<img class="w-100" src="<?php the_field("imagen2");?>" class="card-img-top" alt="Imagen3">
					</div>
					<div class="card-body">
						<h5 class="card-title font-size-16px text-center line_height_1"><?php the_field("texto_postimagen2");?></h5>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="d-flex flex-column align-items-center">
					<div class="cards_img">
						<img class="w-100" src="<?php the_field("imagen3");?>" class="card-img-top" alt="Imagen4">
					</div>
					<div class="card-body">
						<h5 class="card-title font-size-16px text-center line_height_1"><?php the_field("texto_postimagen3");?></h5>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="d-flex flex-column align-items-center">
					<div class="cards_img">
						<img class="w-100" src="<?php the_field("imagen4");?>" class="card-img-top" alt="Imagen5">
					</div>
					<div class="card-body">
						<h5 class="card-title font-size-16px text-center line_height_1"><?php the_field("texto_postimagen4");?></h5>
					</div>
				</div>
			</div>
		</div>
		<div class="row mt-5">
			<div class="col-12 text-center">
				<button class="font-size-16px boton_asistencia">SOLICITAR ASISTENCIA</button>
			</div>
		</div>
	</div>
	<div class="container-fluid fondo_gris_claro">
		<div class="row">
			<div class="col-12">
				<div class="container my-5">
					<div class="row">
						<div class="col-12">
							<h3 class="mayuscula font-size-16px text-center tipografia_naranja separacion"><?php the_field("pretitulo3");?></h3>
						</div>
					</div>
					<div class="row mt-3">
						<div class="col-12">
							<h2 class="font-size-34px text-center separacion separacion"><?php the_field("titulo3");?></h2>
						</div>
					</div>
					<div class="row mt-3">
						
						<!-- CARROUSEL 1 -->
						<div class="my-3 scroll">
							<div class="swipe-scroll mb-3">
								<!-- <div class="tamano_cards mx-2 bordes_menos_redondeados">
									<div class="container p-2">
										<div class="row">
											<div class="col-lg-12 cards_opiniones p-3">
												<h4 class="font-size-16px">Fátima Arjona</h4>
												<p class="texto_regular font-size-16px">Málaga</p>
												<p class="texto_light font-size-15px">Lograron descubrir la fuga después de haber contactado a varios fontaneros. Fueron muy precisos y solo hubo que descubrir una losa.</p>
												<p class="texto_italic font-size-15px">Particular</p>
											</div>
										</div>
									</div>
								</div>

								<div class="tamano_cards mx-2 bordes_menos_redondeados">
									<div class="container p-2">
										<div class="row">
											<div class="col-lg-12 cards_opiniones p-3">
												<h4 class="font-size-16px">Fátima Arjona</h4>
												<p class="texto_regular font-size-16px">Málaga</p>
												<p class="texto_light font-size-15px">Lograron descubrir la fuga después de haber contactado a varios fontaneros. Fueron muy precisos y solo hubo que descubrir una losa.</p>
												<p class="texto_italic font-size-15px">Particular</p>
											</div>
										</div>
									</div>
								</div>

								<div class="tamano_cards mx-2 bordes_menos_redondeados">
									<div class="container p-2">
										<div class="row">
											<div class="col-lg-12 cards_opiniones p-3">
												<h4 class="font-size-16px">Fátima Arjona</h4>
												<p class="texto_regular font-size-16px">Málaga</p>
												<p class="texto_light font-size-15px">Lograron descubrir la fuga después de haber contactado a varios fontaneros. Fueron muy precisos y solo hubo que descubrir una losa.</p>
												<p class="texto_italic font-size-15px">Particular</p>
											</div>
										</div>
									</div>
								</div> -->


								<div class="tamano_cards mx-2 bordes_menos_redondeados">
									<div class="container p-2">
										<div class="row">
											<div class="col-lg-12 cards_opiniones p-3">
												<h4 class="font-size-16px"><?php the_field("nombre_comentario1");?></h4>
												<p class="texto_regular font-size-16px"><?php the_field("lugar_comentario1");?></p>
												<p class="texto_light font-size-15px"><?php the_field("descripcion_comentario1");?></p>
												<p class="texto_italic font-size-15px"><?php the_field("modo_comentario1");?></p>
											</div>
										</div>
									</div>
								</div>

								<div class="tamano_cards mx-2 bordes_menos_redondeados">
									<div class="container p-2">
										<div class="row">
											<div class="col-lg-12 cards_opiniones p-3">
												<h4 class="font-size-16px"><?php the_field("nombre_comentario2");?></h4>
												<p class="texto_regular font-size-16px"><?php the_field("lugar_comentario2");?></p>
												<p class="texto_light font-size-15px"><?php the_field("descripcion_comentario2");?></p>
												<p class="texto_italic font-size-15px"><?php the_field("modo_comentario2");?></p>
											</div>
										</div>
									</div>
								</div>

								<div class="tamano_cards mx-2 bordes_menos_redondeados">
									<div class="container p-2">
										<div class="row">
											<div class="col-lg-12 cards_opiniones p-3">
												<h4 class="font-size-16px"><?php the_field("nombre_comentario3");?></h4>
												<p class="texto_regular font-size-16px"><?php the_field("lugar_comentario3");?></p>
												<p class="texto_light font-size-15px"><?php the_field("descripcion_comentario3");?></p>
												<p class="texto_italic font-size-15px"><?php the_field("modo_comentario3");?></p>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>	

					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="container">
		<div class="row mt-5">
			<div class="col-12">
				<h3 class="mayuscula font-size-16px text-center tipografia_naranja separacion"><?php the_field("pretitulo4");?></h3>
			</div>
		</div>
		<div class="row">
			<div class="col-12">
				<h2 class="font-size-34px text-center separacion"><?php the_field("titulo4");?></h2>
			</div>
		</div>
		<div class="row mt-4 d-flex justify-content-center">
			<div class="col-8 p-0">
				<p class="texto_light font-size-16px">
					<?php the_field("texto_postitulo4");?>
				</p>
			</div>
		</div>
	</div>
</main>

<?php
get_footer();
?>
