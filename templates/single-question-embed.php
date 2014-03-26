<!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width" />
<title><?php wp_title( '|', true, 'right' ); ?></title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<link rel="stylesheet" type="text/css" href="<?php echo get_stylesheet_uri(); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo trailingslashit( plugin_dir_url( __FILE__ ) ) . '../../dw-question-answer/inc/templates/default/assets/css/style.css' ?>">
<link rel="stylesheet" type="text/css" href="<?php echo trailingslashit( plugin_dir_url( __FILE__ ) ) . '../assets/css/dwqa-embed-question.css' ?>">
</head>

<body <?php body_class(); ?>>
	<?php global $post; $dwqa_embed; setup_postdata( $post ); ?>
	<?php $dwqa_embed->load_template('embed','question'); ?>
	<?php wp_reset_postdata(); ?>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo trailingslashit( plugin_dir_url( __FILE__ ) ) . '../assets/js/dwqa-embed-question.js' ?>"></script>
</body>
</html>