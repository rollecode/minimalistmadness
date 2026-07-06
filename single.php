<?php
/**
 * Single
 *
 * The template for displaying a single post.
 *
 * @Author:		Roni Laukkarinen
 * @Date:   		2022-09-19 11:07:30
 * @Last Modified by:   Roni Laukkarinen
 * @Last Modified time: 2022-12-09 10:47:53
 *
 * @package minimalistmadness
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 */

namespace Air_Light;

get_header();

get_template_part( 'template-parts/loop-single' );

get_footer();
