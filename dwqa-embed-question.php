<?php  
/**
 * Plugin Name: DW Question Answer - Embed Question
 * Author: DesignWall 
 * Author URI: http://designwall.com/
 * Description: An addon for DW Question Answer plugin. Allow user embed question inside blog post with link or shortcode. Create an embed script for sharing.
 * Version: 1.0.2
 */


class DWQA_Embed {
    private $parent_post;
    private $depth;
    private $uri;
    private $path;

    public function __construct(){
        add_action( 'init', array( $this, 'dwqa_check' ) );
        $this->depth = 0;
        $this->uri = trailingslashit( plugin_dir_url( __FILE__ ) );
        $this->path = trailingslashit( plugin_dir_path( __FILE__ ) );
    }

    public function dwqa_check() {
        if ( class_exists( 'DW_Question_Answer' ) ) {
            add_filter( 'dwqa-load-template', array($this,'embed_question_template'), 10, 2 );
            add_filter( 'the_content', array($this, 'filter_content'), 9 );
            add_action( 'wp_head', array($this,'insert_meta_tag') );
            
            if( get_option('dwqa-embed-enable-social') ) {
                add_action( 'dwqa-question-content-footer', array( $this, 'show_sharer') );    
            }

            add_shortcode( 'dwqa_question', array($this, 'embed_shortcode') );
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts') );
            add_action( 'init', array( $this, 'load_languages') );
            add_action( 'admin_init', array( $this, 'register_setting') );
            add_action( 'admin_menu', array( $this, 'setting_menu') );
        } else {
            return;
        } 
    }

    public function load_languages(){
        load_plugin_textdomain( 'dwqa', false, $this->path . 'languages/' );
    }


    public function filter_content( $content ){
        if( $this->depth > 0 ) {
            return $content;
        }
        global $post, $dwqa_embed_loop;
        $this->depth++;
        $this->parent_post = $post;
        $content = preg_replace_callback('/(?<=\s|\n|\r|p>|br>|\/>)(\()?([\w]+?:\/\/(?:[\w\\x80-\\xff\#$%&~\/=?@\[\](+-]|[.,;:](?![\s<]|(\))?([\s]|$))|(?(1)\)(?![\s<.,;:]|$)|\)))+)/is', array($this,'make_embed_code'), $content);

        $this->depth = 0;
        return do_shortcode( wpautop($content) );
    } 

    public function make_embed_code( $matches ){
        $link = $matches[2];
        $site_link = get_bloginfo('url');
        if (strpos($link, $site_link) === false) {
            return $matches[0];
        } else {
            global $post;
            $parent_post = $post;
            $post_id = url_to_postid( $link );
            if( ! $post_id ) {
                return $matches[0];
            }
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

    public function enqueue_scripts(){
        wp_enqueue_style( 'dw-embed-question', $this->uri . 'assets/css/dwqa-embed-question.css');
    }

    static function html_cut($text, $max_length) {
        $tags   = array();
        $result = "";

        $is_open   = false;
        $grab_open = false;
        $is_close  = false;
        $in_double_quotes = false;
        $in_single_quotes = false;
        $tag = "";

        $i = 0;
        $stripped = 0;

        $stripped_text = strip_tags($text);

        while ($i < strlen($text) && $stripped < strlen($stripped_text) && $stripped < $max_length)
        {
            $symbol  = $text{$i};
            $result .= $symbol;

            switch ($symbol)
            {
               case '<':
                    $is_open   = true;
                    $grab_open = true;
                    break;

               case '"':
                   if ($in_double_quotes)
                       $in_double_quotes = false;
                   else
                       $in_double_quotes = true;

                break;

                case "'":
                  if ($in_single_quotes)
                      $in_single_quotes = false;
                  else
                      $in_single_quotes = true;

                break;

                case '/':
                    if ($is_open && !$in_double_quotes && !$in_single_quotes)
                    {
                        $is_close  = true;
                        $is_open   = false;
                        $grab_open = false;
                    }

                    break;

                case ' ':
                    if ($is_open)
                        $grab_open = false;
                    else
                        $stripped++;

                    break;

                case '>':
                    if ($is_open)
                    {
                        $is_open   = false;
                        $grab_open = false;
                        array_push($tags, $tag);
                        $tag = "";
                    }
                    else if ($is_close)
                    {
                        $is_close = false;
                        array_pop($tags);
                        $tag = "";
                    }

                    break;

                default:
                    if ($grab_open || $is_close)
                        $tag .= $symbol;

                    if (!$is_open && !$is_close)
                        $stripped++;
            }

            $i++;
        }

        while ($tags)
            $result .= "</".array_pop($tags).">";

        return $result;
    }

    public function setting_menu(){
        add_submenu_page( 'edit.php?post_type=dwqa-question', __('DWQA Embed Question Settings','dwqa'), __('DWQA Embed','dwqa'), 'manage_options', 'dwqa-embed-question-settings', array( $this, 'setting_page') );
    }

    public function setting_page() {
        ?>
        <div class="wrap">
            <h2><?php _e('Embed Settings','dwqa') ?></h2>
            <form action="options.php" method="post">
                <?php  
                    settings_fields( 'dwqa-embed-settings' );
                    do_settings_sections( 'dwqa-embed-settings' );
                    submit_button( __('Submit','dwqa') );
                ?>
            </form>
        </div>
        <?php
    }

    public function register_setting() {
        add_settings_section( 'dwqa-embed-settings', false, false, 'dwqa-embed-settings' );
        add_settings_field( 
            'dwqa-embed-enable-social', 
            __('Enable Social Share','dwqa'), 
            array( $this, 'dwqa_embed_enable_social_display'), 
            'dwqa-embed-settings', 
            'dwqa-embed-settings'
        );
        register_setting( 'dwqa-embed-settings', 'dwqa-embed-enable-social' );
    }

    public function dwqa_embed_enable_social_display() {
        ?>
        <input type="checkbox" name="dwqa-embed-enable-social" id="dwqa-embed-enable-social" <?php checked( 1, get_option('dwqa-embed-enable-social'), true ); ?> value="1">
        <?php
    }

    static function get_the_term_list( $id, $taxonomy, $before = '', $sep = '', $after = '' ) {
            $terms = get_the_terms( $id, $taxonomy );
    
            if ( is_wp_error( $terms ) )
                    return $terms;
    
            if ( empty( $terms ) )
                    return false;
    
            foreach ( $terms as $term ) {
                    $link = get_term_link( $term, $taxonomy );
                    if ( is_wp_error( $link ) )
                            return $link;
                    $term_links[] = '<a target="_blank" href="' . esc_url( $link ) . '" rel="tag">' . $term->name . '</a>';
            }
    
            $term_links = apply_filters( "term_links-$taxonomy", $term_links );
    
            return $before . join( $sep, $term_links ) . $after;
    }
}

$GLOBALS['dwqa_embed'] = new DWQA_Embed();


?>
