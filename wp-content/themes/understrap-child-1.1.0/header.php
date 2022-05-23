<?php
/**
 * The header for our theme
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$bootstrap_version = get_theme_mod( 'understrap_bootstrap_version', 'bootstrap4' );
$navbar_type       = get_theme_mod( 'understrap_navbar_type', 'collapse' );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<link rel="profile" href="http://gmpg.org/xfn/11">
		<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
		<?php wp_head(); ?>
		<script src="https://kit.fontawesome.com/2cf3e50dd7.js" crossorigin="anonymous"></script>
		<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri();?>/css/estilos.css">
	</head>
	<body>
		<header>
			<div class="container">
				<div class="row mt-3">
					<div class="col-md-6 d-flex align-items-center p-0">
						<img class="logo" src="<?php the_field("logo_pagina");?>" alt="Logo">
					</div>
					<div class="col-md-6">
						<div class="container p-0">
							<div class="row div_1">
								<div class="col-md-6 fondo_gris d-flex justify-content-center align-items-center border_radius_header1">
									<p class="texto_black mb-0 p-2 font-size-16px"><?php the_field("texto1_div1");?></p>
								</div>
								<div class="col-md-6 fondo_azul_claro d-flex justify-content-center align-items-center border_radius_header2">
									<p class="texto_black text-center mb-0 tipografia_blanca p-2 font-size-17px"><?php the_field("texto2_div1");?></p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</header>
