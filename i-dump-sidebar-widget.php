<?php
/**
 * Plugin Name: i-Dump Sidebar Widget
 * Plugin URI: http://wordpress.org/extend/plugins/i-dump-sidebar-widget
 * Description: The i-Dump Sidebar Widget will show down your uploaded photos made with your iPhone in the sidebar. Of course you can setup your desired settings for the Widget. For this plugin you need the WP-Dump application, available in the AppStore on the iPhone and the i-Dump plugin.
 * Version: 1.1
 * Author: Joey Schuurbiers
 * Author URI: http://www.webdesign-support.nl
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * Add function to widgets_init that'll load our widget.
 * @since 0.1
 */
add_action( 'widgets_init', 'idump_load_widgets' );

/**
 * Register our widget.
 * 'i-Dump_Sidebar_Widget' is the widget class used below.
 *
 * @since 0.1
 */
function idump_load_widgets() {
	register_widget( 'iDump_Sidebar_Widget' );
}

function my_css() {
    echo '<link type="text/css" rel="stylesheet" href="' . get_bloginfo('wpurl') .'/wp-content/plugins/i-dump-sidebar-widget/style.css" />' . "\n";
}

add_action('wp_head', 'my_css');

/**
 * i-Dump Sidebar Widget class.
 * This class handles everything that needs to be handled with the widget:
 * the settings, form, display, and update.  Nice!
 *
 * @since 0.1
 */
class iDump_Sidebar_Widget extends WP_Widget {

	/**
	 * Widget setup.
	 */
	function iDump_Sidebar_Widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'idump-sidebar-widget', 'description' => __('You need the i-Dump plugin before you can let this widget work.', 'example') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'idump-sidebar-widget' );

		/* Create the widget. */
		$this->WP_Widget( 'idump-sidebar-widget', __('iDump Sidebar Widget', 'idump-sidebar-widget'), $widget_ops, $control_ops );
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		$limit = $instance['limit'];

		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;

        echo '<div class="idump-sidebar-widget">';

        /* Check if the idump table is present and count the images */
        //$querycount = "SELECT id FROM `iphoto`";
        //$sqlcount = @mysql_query($querycount);
        //$count = @mysql_num_rows($sqlcount);
		
        //if ( ! $count ) {
        //   printf(__('<span style="color:red;"><strong>ERROR!</strong><br/><a class="error" target="_blank" href="http://wordpress.org/extend/plugins/i-dump-iphone-to-wordpress-photo-uploader">i-Dump iPhone Photo Uploader</a> is not installed or you have no pictures added yet.</span>'));
        //} 

        $query = "SELECT * FROM `iphoto` ORDER BY `id` DESC LIMIT " . $limit;
        $sql = mysql_query($query);
		
        while ($record = mysql_fetch_object($sql)){

           // $time = date('d-m-Y', $record->date);
            $blog_url = get_bloginfo('wpurl');
            $file = $blog_url . '/wp-content/plugins/i-dump-iphone-to-wordpress-photo-uploader/files/' . $record->file; 
            $filethumb = $blog_url . '/wp-content/plugins/i-dump-iphone-to-wordpress-photo-uploader/files/thumb/' . $record->file;
            $width = $instance['width'];
            $height = $instance['height'];
                        
            echo '<a href="' . $file . '"><img src="' . $filethumb . '" alt="' . $time . '" width="' . $width . '" height="' . $height . '" /></a>';
        }
  
        echo '</div>';
        
     //   printf( '<div class="link-to-album"><a href="' . $idumpurl . '">' . __('View all %1$s photos', 'count') . '</a></div>', $count );

		/* After widget (defined by themes). */
		echo $after_widget;
	}

	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['limit'] = strip_tags( $new_instance['limit'] );
        $instance['height'] = strip_tags( $new_instance['height'] );
        $instance['width'] = strip_tags( $new_instance['width'] );

		return $instance;
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {

        ?>
        
        <?php
		/* Set up some default widget settings. */
		$defaults = array( 'title' => __('i-Dump Sidebar Widget', 'title'), 'limit' => __('4', 'limit'), 'width' => __('80', 'width'), 'height' => __('80','height') );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<table cellspacing="" cellpadding="0" border="0">
			<tr>
				<td style="text-align:center;"><small><a target="_blank" href="http://itunes.apple.com/us/app/wp-dump/id413231620?mt=8&ls=1">Do you already have the WP-Dump application on your iPhone?</a></small><td>
			</tr>
		</table>
		<br/>
		<table cellspacing="" cellpadding="0" border="0">
			<tr>
				<td><label for="widget-recent-posts-__i__-title">Title:</label></td>
			</tr>
			<tr>
				<td><input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" style="width:222px;" value="<?php echo $instance['title']; ?>" /></td>
			</tr>
		</table>
		<table cellspacing="" cellpadding="0" border="0">
			<tr>
				<td><label style="float:left;width:174px;" for="widget-recent-posts-__i__-number">Amount to show:</label></td>
				<td><input id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" type="text" value="<?php echo $instance['limit']; ?>" size="3" /></td>
			</tr>
			<tr>
				<td><label style="float:left;width:174px;" for="widget-recent-posts-__i__-number">Width:</label></td>
				<td><input id="<?php echo $this->get_field_id( 'width' ); ?>" name="<?php echo $this->get_field_name( 'width' ); ?>" type="text" value="<?php echo $instance['width']; ?>" size="3" /></td>
			</tr>
			<tr>
				<td><label style="float:left;width:174px;" for="widget-recent-posts-__i__-number">Height:</label></td>
				<td><input id="<?php echo $this->get_field_id( 'height' ); ?>" name="<?php echo $this->get_field_name( 'height' ); ?>" type="text" value="<?php echo $instance['height']; ?>" size="3" /></td>
			</tr>
		</table>
		<br/>
		<table cellspacing="" cellpadding="0" border="0">
			<tr>
				<td style="font-size:9px;text-align:center;">Thank you for using i-Dump Sidebar Widget!</td>
			</tr>
			<tr>
				<td style="text-align:center;"><form action="https://www.paypal.com/cgi-bin/webscr" method="post"><input type="hidden" name="cmd" value="_s-xclick"><input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----MIIHZwYJKoZIhvcNAQcEoIIHWDCCB1QCAQExggEwMIIBLAIBADCBlDCBjjELMAkGA1UEBhMCVVMxCzAJBgNVBAgTAkNBMRYwFAYDVQQHEw1Nb3VudGFpbiBWaWV3MRQwEgYDVQQKEwtQYXlQYWwgSW5jLjETMBEGA1UECxQKbGl2ZV9jZXJ0czERMA8GA1UEAxQIbGl2ZV9hcGkxHDAaBgkqhkiG9w0BCQEWDXJlQHBheXBhbC5jb20CAQAwDQYJKoZIhvcNAQEBBQAEgYAipVYZ7eWTY/U/zTf4sTCR/bK/NxH0sgCeijCPnP/zFd4K8mPBkMJPt5nBy7VMBVt3wS5DvPXu119X+XAKJ3cqIj738B5iggRYPJQEpKOrsQAJ4mqSWHtoHfrzZsYr0gduv3N67T0fKk4Z2M9ks7z6BW63bPkokckgFcTe3PczUzELMAkGBSsOAwIaBQAwgeQGCSqGSIb3DQEHATAUBggqhkiG9w0DBwQI5oCiafnQOFSAgcCxP3cr5gm3FNnnmJH1iPhn9FDrw7l2TLRbxcpJDnpwc8Hvs7HlpBKvfjRmxhmxtQC8m/CukJYU4r6sKmLbW9Jvi9qfCaG7KmLHZXgQoc1e0kWkbQJNzgfdryzn9ImtcxTdYAbKhyQArUzVpFtngWOWdbUcK1/XKFNCauSGGhD3TIxrBlTF3ETA3iMMXo4Hzc/pi9sjTUVFr6hoeEoN/TtH7DCUywLC06RTJBIhUNI7VJzMRL8hVR/U4MY3K4FEBkCgggOHMIIDgzCCAuygAwIBAgIBADANBgkqhkiG9w0BAQUFADCBjjELMAkGA1UEBhMCVVMxCzAJBgNVBAgTAkNBMRYwFAYDVQQHEw1Nb3VudGFpbiBWaWV3MRQwEgYDVQQKEwtQYXlQYWwgSW5jLjETMBEGA1UECxQKbGl2ZV9jZXJ0czERMA8GA1UEAxQIbGl2ZV9hcGkxHDAaBgkqhkiG9w0BCQEWDXJlQHBheXBhbC5jb20wHhcNMDQwMjEzMTAxMzE1WhcNMzUwMjEzMTAxMzE1WjCBjjELMAkGA1UEBhMCVVMxCzAJBgNVBAgTAkNBMRYwFAYDVQQHEw1Nb3VudGFpbiBWaWV3MRQwEgYDVQQKEwtQYXlQYWwgSW5jLjETMBEGA1UECxQKbGl2ZV9jZXJ0czERMA8GA1UEAxQIbGl2ZV9hcGkxHDAaBgkqhkiG9w0BCQEWDXJlQHBheXBhbC5jb20wgZ8wDQYJKoZIhvcNAQEBBQADgY0AMIGJAoGBAMFHTt38RMxLXJyO2SmS+Ndl72T7oKJ4u4uw+6awntALWh03PewmIJuzbALScsTS4sZoS1fKciBGoh11gIfHzylvkdNe/hJl66/RGqrj5rFb08sAABNTzDTiqqNpJeBsYs/c2aiGozptX2RlnBktH+SUNpAajW724Nv2Wvhif6sFAgMBAAGjge4wgeswHQYDVR0OBBYEFJaffLvGbxe9WT9S1wob7BDWZJRrMIG7BgNVHSMEgbMwgbCAFJaffLvGbxe9WT9S1wob7BDWZJRroYGUpIGRMIGOMQswCQYDVQQGEwJVUzELMAkGA1UECBMCQ0ExFjAUBgNVBAcTDU1vdW50YWluIFZpZXcxFDASBgNVBAoTC1BheVBhbCBJbmMuMRMwEQYDVQQLFApsaXZlX2NlcnRzMREwDwYDVQQDFAhsaXZlX2FwaTEcMBoGCSqGSIb3DQEJARYNcmVAcGF5cGFsLmNvbYIBADAMBgNVHRMEBTADAQH/MA0GCSqGSIb3DQEBBQUAA4GBAIFfOlaagFrl71+jq6OKidbWFSE+Q4FqROvdgIONth+8kSK//Y/4ihuE4Ymvzn5ceE3S/iBSQQMjyvb+s2TWbQYDwcp129OPIbD9epdr4tJOUNiSojw7BHwYRiPh58S1xGlFgHFXwrEBb3dgNbMUa+u4qectsMAXpVHnD9wIyfmHMYIBmjCCAZYCAQEwgZQwgY4xCzAJBgNVBAYTAlVTMQswCQYDVQQIEwJDQTEWMBQGA1UEBxMNTW91bnRhaW4gVmlldzEUMBIGA1UEChMLUGF5UGFsIEluYy4xEzARBgNVBAsUCmxpdmVfY2VydHMxETAPBgNVBAMUCGxpdmVfYXBpMRwwGgYJKoZIhvcNAQkBFg1yZUBwYXlwYWwuY29tAgEAMAkGBSsOAwIaBQCgXTAYBgkqhkiG9w0BCQMxCwYJKoZIhvcNAQcBMBwGCSqGSIb3DQEJBTEPFw0xMTAxMjExMzMzMzlaMCMGCSqGSIb3DQEJBDEWBBQVdESyjQhKV/IpfbqGm8D+EaRsEDANBgkqhkiG9w0BAQEFAASBgGqMfdyWUFvc6Ms2QmpcjaepinHggoynvNRnUbn07ofdxx+yEJD3vg8dEFlOu/Z57y5jAM0dF2xRookbh530vztPgph5zbyXWeIVahY5Ya+tCkJyMEY6Y4rYW/5bmt3fWaDMwhvRNFU5V3W6remICkSyL3ATJQvPpQS7XSy/Y/Sy-----END PKCS7-----"><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=QWSSG9TCANPE6&lc=US&item_name=Please%20keep%20the%20i%2dDump%20Sidebar%20Widget%20alive%21%20Thank%20you%20very%20much%21&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donate_SM%2egif%3aNonHosted"><input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!"></a><img alt="" border="0" src="https://www.paypal.com/nl_NL/i/scr/pixel.gif" width="1" height="1"></form></td>
			</tr>
		</table>
        
        <?php
		//$querycount = "SELECT id FROM `iphoto`";
		//$sqlcount = mysql_query($querycount) or die ( mysql_error( ) );
		//$count = mysql_num_rows($sqlcount);
        ?>
  
        <!--<small>There are <?//=$count;?> pictures in your database.</small>-->

	<?php
	}
  }
?>