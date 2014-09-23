<?php

class PiwigoMediaWidget extends WP_Widget {

	public function __construct() {
		// widget actual processes
		parent::__construct(
	 		'PiwigoMediaWidget', // Base ID
			'PiwigoMedia Widget', // Name
			array( 'description' => __("Display Piwigo media on a sidebar widget", "piwigomedia") ) // Args
		);
	}

 	public function form( $instance ) {
	    $sites = array();
	    foreach (explode("\n", get_option('piwigomedia_piwigo_urls', '')) as $u) {
	        $tu = trim($u);
	        if (!empty($tu))
	            $sites[] = $tu;
	    }

		// outputs the options form on admin
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = 'PiwigoMedia';
		}
		$category = $instance['category'];
		$site = $instance['site'];
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'piwigomedia' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		<label for="<?php echo $this->get_field_id( 'site' ); ?>"><?php _e( 'Site:', 'piwigomedia' ); ?></label> 
		<select class="widefat" id="<?php echo $this->get_field_id( 'site' );?>" name="<?php echo $this->get_field_name( 'site' );?>">
		<?php
			foreach($sites as $s) {
				$selected = $s == $site ? "selected" : "";
				echo "<option $selected value=\"".$s."\">".$s."</option>";
			}
		?>
		</select>
		<?php if ( count($sites) > 0 ) { ?>
		<label for="<?php echo $this->get_field_id('category'); ?>"><?php _e( 'Category ID:', 'piwigomedia' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'category' ); ?>" name="<?php echo $this->get_field_name( 'category' ); ?>" type="text" value="<?php echo esc_attr( $category ); ?>" />

		</p>
		<?php }
	}

	public function update( $new_instance, $old_instance ) {
		// processes widget options to be saved
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['site'] = strip_tags( $new_instance['site'] );
		$instance['category'] = strip_tags( $new_instance['category'] );
		return $instance;
	}

	public function widget( $args, $instance ) {
		// outputs the content of the widget
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $before_widget;
		$site = $instance['site'];
		$category = $instance['category'];
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;
                $params = array(
                        "format" => "json",
                        "method" => "pwg.categories.getImages",
                        "cat_id" => $category,
                        "page" => 0,
                        "per_page" => 40);
                $res = pwm_curl_get($site."/ws.php", $params);
                $res = json_decode($res);
                if ($res->stat == "ok") {
                     $out = "";
                     $count = $res->result->images->count;
                     if ($count > 0) {
                     		$rand_idx = array();
	                     for($i=0; $i<$count; $i++) {
        	                $rand_idx[] = $i;
	                     }
        	             shuffle($rand_idx);

                             $out .= "<ul class=\"piwigomedia-category-widget\">";
			     for($i=0; $i<6;$i++) {
				$img = $res->result->images->_content[$rand_idx[$i]];
                                     $out .= "<li><a class=\"piwigomedia-single-image\" href=\"".$img->element_url."\"><img src=\"".$img->derivatives->thumb->url."\"></a></li>";
                             }
                             $out .= "</ul>";
                     }
		}
		echo $out;
		echo $after_widget;
	}

}

?>
