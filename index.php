<?php
/*
    Plugin Name: Featured Professor Block
    Version: 1.0.0
    Author: Bilal Demir
    Author URI: https://bilaldemir.dev
    Text Domain: featured-professor
    Domain Path: /languages
 */

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once plugin_dir_path(__FILE__) . 'inc/generateProfessorHTML.php';
require_once plugin_dir_path(__FILE__) . 'inc/relatedPostsHTML.php';

class FeaturedProfessor {
  function __construct() {
    add_action('init', [$this, 'onInit']);
    add_action('rest_api_init', [$this, 'professorHTML']);

    add_filter('the_content', [$this, 'filterContent']);
  }

  function onInit() {
    load_plugin_textdomain(
        'featured-professor',
        false,
        dirname(plugin_basename(__FILE__)) . '/languages'
    );

    register_meta('post', 'featured_professor', array(
      'show_in_rest' => true,
      'single' => false,
      'type' => 'number'
    ));

    wp_register_script('featuredProfessorScript', plugin_dir_url(__FILE__) . 'build/index.js', array('wp-blocks', 'wp-i18n', 'wp-editor'));
    wp_register_style('featuredProfessorStyle', plugin_dir_url(__FILE__) . 'build/index.css');

    wp_set_script_translations(
        'featuredProfessorScript',
        'featured-professor',
        plugin_dir_path(__FILE__) . '/languages'
    );

    register_block_type('ourplugin/featured-professor', array(
      'render_callback' => [$this, 'renderCallback'],
      'editor_script' => 'featuredProfessorScript',
      'editor_style' => 'featuredProfessorStyle'
    ));
  }

  function professorHTML() {
    register_rest_route('featuredProfessor/v1', 'getHTML', array(
      'methods' => WP_REST_SERVER::READABLE,
      'callback' => [$this, 'professorHTMLCallback']
    ));
  }

  function professorHTMLCallback($data) {
    $id = $data['id'] ?? null;

    if(!$id) return null;

    return generateProfessorHTML($id);
  }

  function renderCallback($attributes) {
      $id = $attributes['professorId'] ?? null;
      if($id) {
          wp_enqueue_style('featuredProfessorStyle');

          return generateProfessorHTML($id);
      } else {
          return null;
      }
  }

  function filterContent($content) {
      if (is_singular('professor') && in_the_loop() && is_main_query()) {
          return 'ok';
          return $content . relatedPostsHTML(get_the_ID());
      }

      return $content;
  }
}

$featuredProfessor = new FeaturedProfessor();