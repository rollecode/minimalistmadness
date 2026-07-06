<?php
/**
 * Single diary
 *
 * A template for single post.
 *
 * @Author:		Roni Laukkarinen
 * @Date:   		2021-11-16 09:38:48
 * @Last Modified by:   Roni Laukkarinen
 * @Last Modified time: 2023-01-24 00:01:40
 *
 * @package minimalistmadness
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 */

namespace Air_Light;

get_header();

get_template_part( 'template-parts/loop-single-diary' );

get_footer();
