<?php  
/**
 * Plugin Name: DW Question Answer - Embed Question
 * Author: DesignWall 
 * Author URI: http://designwall.com/
 * Description: An addon for DW Question Answer plugin. Allow user embed question inside blog post with link or shortcode. Create an embed script for sharing.
 * Version: 0.0.1
 */


class DWQA_Embed {
    private $parent_post;
    private $depth;
    private $uri;
    private $path;

    public function __construct(){
        if( ! function_exists('dwqa_activate') ) {
            return false;
        }
        $this->depth = 0;
        $this->uri = plugin_dir_url( __FILE__ );
        $this->path = plugin_dir_path( __FILE__ );

        add_filter( 'dwqa-load-template', array($this,'embed_question_template'), 10, 2 );
        add_filter( 'the_content', array($this, 'filter_content') );
        add_action( 'wp_head', array($this,'insert_meta_tag') );
        add_action( 'wp_enqueue_scripts', array($this, 'enqueue_scripts') );
        add_action( 'dwqa-question-content-footer', array( $this, 'show_sharer') );
        add_shortcode( 'dwqa_question', array($this, 'embed_shortcode') );
        
        if( isset($_REQUEST['dwqa-embed']) && $_REQUEST['dwqa-embed'] ) {
            add_filter( 'show_admin_bar', '__return_false' );
        }

        if( isset($_REQUEST['dwqa-embed']) && $_REQUEST['dwqa-embed'] ){
            remove_filter( 'wp_footer', 'dwpb',100 );
        }
    }

    public function enqueue_scripts(){
        wp_enqueue_style( 'dwqa-embed-question', $this->uri . 'assets/css/dwqa-embed-question.css' );
        wp_enqueue_script( 'dwqa-embed-question', $this->uri . 'assets/js/dwqa-embed-question.js', array('jquery'), false, true );
    }

    public function filter_content( $content ){
        if( $this->depth > 0 ) {
            return $content;
        }
        global $post, $dwqa_embed_loop;
        $this->depth++;
        $this->parent_post = $post;
        $content = preg_replace_callback('#(?<=[\s>])(\()?([\w]+?://(?:[\w\\x80-\\xff\#$%&~/=?@\[\](+-]|[.,;:](?![\s<]|(\))?([\s]|$))|(?(1)\)(?![\s<.,;:]|$)|\)))+)#is', array($this,'make_embed_code'), $content);

        $this->depth = 0;
        return do_shortcode($content);
    } 

    public function make_embed_code( $matches ){
        $link = $matches[0];
        $site_link = get_bloginfo('url');
        if (strpos($link, $site_link) === false) {
            return $matches[0];
        } else {
            global $post;
            $parent_post = $post;
            $post_id = url_to_postid( $link );
            if( 'dwqa-question' == get_post_type( $post_id ) && $post_id != $post->ID ) {
                $post = get_post( $post_id );
                setup_postdata( $post );
                $embed_code = '';
                $template = 'question';
                if( ! $this->parent_post ) {
                    return $matches[0];
                }
                $parent_post_type = get_post_type( $this->parent_post->ID );
                if( 'dwqa-question' == $parent_post_type || 'dwqa-answer' == $parent_post_type ) {
                    $template = 'question-small';
                }
                ob_start();
                $this->load_template( 'embed', $template );
                $embed_code = ob_get_contents();
                ob_end_clean();
                $this->reset_postdata($parent_post);
                return $embed_code;
            }
        }
        return $matches[0];
    }

    public function insert_meta_tag() {
        if( is_singular( 'dwqa-question' ) ) {
            global $post;
            $avatar = $this->get_avatar_url(get_avatar($post->post_author, 200, false));
            if( $avatar ) {
                echo '<meta content="'.$avatar.'" property="og:image">';
            }
        }
    }

    public function get_avatar_url($get_avatar){
        preg_match('/src="([^"]*)"/i', $get_avatar, $matches);
        if( isset($matches[1]) ) {
            return $matches[1];
        }
        return false;
    }

    public function embed_shortcode( $atts, $content = "" ){
        extract( shortcode_atts( array(
            'id'    => false
        ), $atts ) );
        if( $content ) {
            $id = url_to_postid( $content );
        }
        if( ! $id ) {
            return false;   
        }
        global $post;
        $parent_post = $post;
        if( 'dwqa-question' == get_post_type( $id ) && $id != $post->ID ) {
            $post = get_post( $id );
            setup_postdata( $post );
            $embed_code = '';
            if( ! is_wp_error( $post ) ) {
                $this->depth = 3;
                ob_start();
                $this->load_template( 'embed', 'question' );
                $embed_code = ob_get_contents();
                ob_end_clean();
                $this->depth = 0;
            }
            $this->reset_postdata($parent_post);
            return $embed_code;
        }
    }

    public function embed_question_template( $template, $name ){
        if( is_singular( 'dwqa-question' ) && isset($_REQUEST['dwqa-embed']) && $_REQUEST['dwqa-embed'] ) {
            return $this->load_template( 'single', 'question-embed', false );
        }
        return $template;
    }

    public function show_sharer(){
        $this->load_template( 'question', 'sharer' );
    }
    public function load_template( $name, $extend = false, $include = true ){
        $check = true;
        if( $extend ) {
            $name .= '-' . $extend;
        }
        $template = get_stylesheet_directory() . '/dwqa-templates/'.$name.'.php';
        if( ! file_exists($template) ) {
            $template = $this->path . 'templates/' .$name.'.php';
        }

        $template = apply_filters( 'dwqa-load-embed-template', $template, $name );
        if( ! $template ) {
            return false;
        }
        if( ! $include ) {
            return $template;
        }
        include $template;
    }

    public function reset_postdata( $post_data ){
        global $post;
        $post = $post_data;
        setup_postdata( $post );
    }

}

$GLOBALS['dwqa_embed'] = new DWQA_Embed();


?>