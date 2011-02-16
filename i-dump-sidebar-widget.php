<?php
/**
 * Plugin Name: i-Dump Sidebar Widget
 * Plugin URI: http://wordpress.org/extend/plugins/i-dump-sidebar-widget
 * Description: The i-Dump Sidebar Widget will show down your uploaded photos made with your iPhone in the sidebar. Of course you can setup your desired settings for the Widget. For this plugin you need the WP-Dump application, available in the AppStore on the iPhone and the i-Dump plugin.
 * Version: 1.2
 * Author: Joey Schuurbiers
 * Author URI: http://www.webdesign-support.com
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
echo "<style type=\"text/css\">
.idump-sidebar-widget{
overflow:hidden;
}
.idump-sidebar-widget img{
margin:0 5px 5px 0;
float:left;
background-color:#F8F8F8;
}
.error_widget{
color:red;
text-decoration:none;
}
.error_widget:hover{
color:red;
text-decoration:underline;
}
</style>";
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
		
		$result = mysql_query(" SHOW TABLES LIKE 'iphoto' ");
		if( mysql_num_rows($result) ) {

        while ($record = mysql_fetch_object($sql)){

				if ($record->text == ''){
				$mark = date('Y-m-d', $record->date);
				}else{
				$mark = $record->text ;
				}
			
            $blog_url = get_bloginfo('wpurl');
            $file = $blog_url . '/wp-content/uploads/i-dump-uploads/' . $record->file; 
            $filethumb = $blog_url . '/wp-content/uploads/i-dump-uploads/thumbnails/' . $record->file;
            $width = $instance['width'];
            $height = $instance['height'];
			$bordercolor = $instance['bordercolor'];
			$borderpx = $instance['borderpx'];
			$borderstyle = $instance['borderstyle'];
                        
            echo '<a href="' . $file . '"><img style="border:' . $borderpx . 'px ' . $borderstyle . ' ' . $bordercolor . ';" src="' . $filethumb . '" alt="' . $mark . '" width="' . $width . '" height="' . $height . '" /></a>';
        }
  } else {
	echo 'Please install <a class="error_widget" target="_blank" href="http://wordpress.org/extend/plugins/i-dump-iphone-to-wordpress-photo-uploader">i-Dump Plugin</a> first';

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
		$instance['bordercolor'] = strip_tags( $new_instance['bordercolor'] );
		$instance['borderpx'] = strip_tags( $new_instance['borderpx'] );
		$instance['borderstyle'] = strip_tags( $new_instance['borderstyle'] );

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
		$defaults = array( 'title' => __('i-Dump Sidebar Widget', 'title'), 'limit' => __('4', 'limit'), 'width' => __('80', 'width'), 'height' => __('80','height'), 'bordercolor' => __('#00ADEE','bordercolor'), 'borderpx' => __('2','borderpx'), 'borderstyle' => __('solid','borderstyle'));
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<table cellspacing="" cellpadding="0" border="0">
			<tr>
				<td class="widtitle" style="background-color:#F1F1F1;width:246px;height:30px;font-weight:bold;padding:0 0 0 12px;font-size:11px;">Settings</td>
			</tr>
		</table>
		<table cellspacing="" cellpadding="0" border="0">
			<tr>
				<td><label for="widget-recent-posts-__i__-title">Title:</label></td>
			</tr>
			<tr>
				<td><input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" style="width:222px;" value="<?php echo $instance['title']; ?>" /></td>
			</tr>
		</table>
		<table cellspacing="" cellpadding="0" border="0" style="padding:0 0 15px 0;">
			<tr>
				<td><label style="float:left;width:174px;" for="widget-recent-posts-__i__-number">Amount to show:</label></td>
				<td><input style="text-align:right;" id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" type="text" value="<?php echo $instance['limit']; ?>" size="3" /></td>
			</tr>
			<tr>
				<td><label style="float:left;width:174px;" for="widget-recent-posts-__i__-number">Width:</label></td>
				<td><input style="text-align:right;" id="<?php echo $this->get_field_id( 'width' ); ?>" name="<?php echo $this->get_field_name( 'width' ); ?>" type="text" value="<?php echo $instance['width']; ?>" size="3" /></td>
			</tr>
			<tr>
				<td><label style="float:left;width:174px;" for="widget-recent-posts-__i__-number">Height:</label></td>
				<td><input style="text-align:right;" id="<?php echo $this->get_field_id( 'height' ); ?>" name="<?php echo $this->get_field_name( 'height' ); ?>" type="text" value="<?php echo $instance['height']; ?>" size="3" /></td>
			</tr>
		</table>
		<table cellspacing="" cellpadding="0" border="0">
			<tr>
				<td class="widtitle" style="background-color:#F1F1F1;width:246px;height:30px;font-weight:bold;padding:0 0 0 12px;font-size:11px;">CSS</td>
			</tr>
		</table>
		<table cellspacing="" cellpadding="0" border="0">
			<tr>
				<td><label style="float:left;width:160px;" for="widget-recent-posts-__i__-number">Border width:</label></td>
				<td><input class="widefat" maxlength="2" style="text-align:center;width:25px;" id="<?php echo $this->get_field_id( 'borderpx' ); ?>" name="<?php echo $this->get_field_name( 'borderpx' ); ?>" type="text" value="<?php echo $instance['borderpx']; ?>" size="2" /></td>
				<td><input style="text-align:center;width:25px;" class="widefat" value="px" size="2" disabled /></td>
			</tr>
		</table>
		<table cellspacing="" cellpadding="0" border="0">
			<tr>
				<td><label style="float:left;width:161px;" for="widget-recent-posts-__i__-number">Border style:</label></td>
				<td><input style="text-align:right;" class="widefat" id="<?php echo $this->get_field_id( 'borderstyle' ); ?>" name="<?php echo $this->get_field_name( 'borderstyle' ); ?>" type="text" value="<?php echo $instance['borderstyle']; ?>" size="7" /></td>
			</tr>
		</table>
		<table cellspacing="" cellpadding="0" border="0">
			<tr>
				<td><label style="float:left;width:150px;" for="widget-recent-posts-__i__-number">Border color:</label></td>
				<td><input  style="text-align:left;" class="widefat" id="<?php echo $this->get_field_id( 'bordercolor' ); ?>" name="<?php echo $this->get_field_name( 'bordercolor' ); ?>" type="text" value="<?php echo $instance['bordercolor']; ?>" size="7" /></td>
			</tr>
		</table>
		<br/>
		<table cellspacing="" cellpadding="0" border="0">
			<tr>
				<td style="font-size:9px;text-align:center;">Thank you for using i-Dump Sidebar Widget!</td>
			</tr>
			<tr>
				<td style="text-align:center;"><a target="_blank" href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=QWSSG9TCANPE6&lc=US&item_name=Please%20keep%20us%20alive%21&item_number=Joey%20Schuurbiers&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donate_SM%2egif%3aNonHosted"><img src="https://www.paypal.com/en_US/i/btn/btn_donate_SM.gif" alt="Please keep us alive!" /></a></td>
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