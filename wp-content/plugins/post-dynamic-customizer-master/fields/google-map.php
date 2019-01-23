<?php

/*
*  ACF Google Map Field Class
*
*  All the logic for this field type
*
*  @class 		pdc_field_google_map
*  @extends		pdc_field
*  @package		ACF
*  @subpackage	Fields
*/

if( ! class_exists('pdc_field_google_map') ) :

class pdc_field_google_map extends pdc_field {
	
	
	/*
	*  __construct
	*
	*  This function will setup the field type data
	*
	*  @type	function
	*  @date	5/03/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function __construct() {
		
		// vars
		$this->name = 'google_map';
		$this->label = __("Google Map",'pdc');
		$this->category = 'jquery';
		$this->defaults = array(
			'height'		=> '',
			'center_lat'	=> '',
			'center_lng'	=> '',
			'zoom'			=> ''
		);
		$this->default_values = array(
			'height'		=> '400',
			'center_lat'	=> '-37.81411',
			'center_lng'	=> '144.96328',
			'zoom'			=> '14'
		);
		$this->l10n = array(
			'locating'			=> __("Locating",'pdc'),
			'browser_support'	=> __("Sorry, this browser does not support geolocation",'pdc'),
		);
		
		
		// do not delete!
    	parent::__construct();
	}
	
	
	/*
	*  render_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field - an array holding all the field's data
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/
	
	function render_field( $field ) {
		
		// validate value
		if( empty($field['value']) ) {
			
			$field['value'] = array();
			
		}
		
		
		// value
		$field['value'] = wp_parse_args($field['value'], array(
			'address'	=> '',
			'lat'		=> '',
			'lng'		=> ''
		));
		
		
		// default options
		foreach( $this->default_values as $k => $v ) {
		
			if( empty($field[ $k ]) ) {
			
				$field[ $k ] = $v;
				
			}
				
		}
		
		
		// vars
		$atts = array(
			'id'			=> $field['id'],
			'class'			=> "pdc-google-map {$field['class']}",
			'data-lat'		=> $field['center_lat'],
			'data-lng'		=> $field['center_lng'],
			'data-zoom'		=> $field['zoom'],
		);
		
		
		// has value
		if( $field['value']['address'] ) {
		
			$atts['class'] .= ' -value';
			
		}
		
?>
<div <?php pdc_esc_attr_e($atts); ?>>
	
	<div class="pdc-hidden">
		<?php foreach( $field['value'] as $k => $v ): ?>
			<input type="hidden" class="input-<?php echo $k; ?>" name="<?php echo esc_attr($field['name']); ?>[<?php echo $k; ?>]" value="<?php echo esc_attr( $v ); ?>" />
		<?php endforeach; ?>
	</div>
	
	<div class="title pdc-soh">
		
		<div class="actions pdc-soh-target">
			<a href="#" data-name="search" class="pdc-icon -search grey" title="<?php _e("Search", 'pdc'); ?>"></a>
			<a href="#" data-name="clear" class="pdc-icon -cancel grey" title="<?php _e("Clear location", 'pdc'); ?>"></a>
			<a href="#" data-name="locate" class="pdc-icon -location grey" title="<?php _e("Find current location", 'pdc'); ?>"></a>
		</div>
		
		<input class="search" type="text" placeholder="<?php _e("Search for address...",'pdc'); ?>" value="<?php echo $field['value']['address']; ?>" />
		<i class="pdc-loading"></i>
				
	</div>
	
	<div class="canvas" style="height: <?php echo $field['height']; ?>px"></div>
	
</div>
<?php
		
	}
	
		
	/*
	*  render_field_settings()
	*
	*  Create extra options for your field. This is rendered when editing a field.
	*  The value of $field['name'] can be used (like bellow) to save extra data to the $field
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field	- an array holding all the field's data
	*/
	
	function render_field_settings( $field ) {
		
		// center_lat
		pdc_render_field_setting( $field, array(
			'label'			=> __('Center','pdc'),
			'instructions'	=> __('Center the initial map','pdc'),
			'type'			=> 'text',
			'name'			=> 'center_lat',
			'prepend'		=> 'lat',
			'placeholder'	=> $this->default_values['center_lat']
		));
		
		
		// center_lng
		pdc_render_field_setting( $field, array(
			'label'			=> __('Center','pdc'),
			'instructions'	=> __('Center the initial map','pdc'),
			'type'			=> 'text',
			'name'			=> 'center_lng',
			'prepend'		=> 'lng',
			'placeholder'	=> $this->default_values['center_lng'],
			'_append' 		=> 'center_lat'
		));
		
		
		// zoom
		pdc_render_field_setting( $field, array(
			'label'			=> __('Zoom','pdc'),
			'instructions'	=> __('Set the initial zoom level','pdc'),
			'type'			=> 'text',
			'name'			=> 'zoom',
			'placeholder'	=> $this->default_values['zoom']
		));
		
		
		// allow_null
		pdc_render_field_setting( $field, array(
			'label'			=> __('Height','pdc'),
			'instructions'	=> __('Customise the map height','pdc'),
			'type'			=> 'text',
			'name'			=> 'height',
			'append'		=> 'px',
			'placeholder'	=> $this->default_values['height']
		));
		
	}
	
	
	/*
	*  validate_value
	*
	*  description
	*
	*  @type	function
	*  @date	11/02/2014
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function validate_value( $valid, $value, $field, $input ){
		
		// bail early if not required
		if( ! $field['required'] ) {
			
			return $valid;
			
		}
		
		
		if( empty($value) || empty($value['lat']) || empty($value['lng']) ) {
			
			return false;
			
		}
		
		
		// return
		return $valid;
		
	}
	
	
	/*
	*  update_value()
	*
	*  This filter is appied to the $value before it is updated in the db
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value - the value which will be saved in the database
	*  @param	$post_id - the $post_id of which the value will be saved
	*  @param	$field - the field array holding all the field options
	*
	*  @return	$value - the modified value
	*/
	
	function update_value( $value, $post_id, $field ) {
	
		if( empty($value) || empty($value['lat']) || empty($value['lng']) ) {
			
			return false;
			
		}
		
		
		// return
		return $value;
	}
	
	
	/*
   	*  input_admin_footer
   	*
   	*  description
   	*
   	*  @type	function
   	*  @date	6/03/2014
   	*  @since	5.0.0
   	*
   	*  @param	$post_id (int)
   	*  @return	$post_id (int)
   	*/
   	
   	function input_admin_footer() {
	   	
	   	// bail ealry if no qneueu
	   	if( !pdc_get_setting('enqueue_google_maps') ) return;
	   	
	   	
	   	// vars
	   	$api = array(
			'key'		=> pdc_get_setting('google_api_key'),
			'client'	=> pdc_get_setting('google_api_client'),
			'libraries'	=> 'places',
			'ver'		=> 3,
			'callback'	=> ''
	   	);
	   	
	   	
	   	// filter
	   	$api = apply_filters('pdc/fields/google_map/api', $api);
	   	
	   	
	   	// remove empty
	   	if( empty($api['key']) ) unset($api['key']);
	   	if( empty($api['client']) ) unset($api['client']);
	   	
	   	
	   	// construct url
	   	$url = add_query_arg($api, 'https://maps.googleapis.com/maps/api/js');
	   	
?>
<script type="text/javascript">
	if( pdc ) pdc.fields.google_map.url = '<?php echo $url; ?>';
</script>
<?php
	
   	}
   	
}


// initialize
pdc_register_field_type( new pdc_field_google_map() );

endif; // class_exists check

?>