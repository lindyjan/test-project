(function($){        
	
	/*
	*  Repeater
	*
	*  This field type requires some extra logic for its settings
	*
	*  @type	function
	*  @date	24/10/13
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	var pdc_settings_repeater = pdc.field_group.field_object.extend({
		
		type: 'repeater',
		
		actions: {
			'render_settings': 'render'
		},
		
		events: {
			'change .pdc-field-setting-layout input':		'_change_layout',
			'focus .pdc-field-setting-collapsed select':	'_focus_collapsed'
		},
		
		focus: function(){
			
			this.$fields = this.$field.find('.pdc-field-list:first');
			
		},
		
		render: function(){
			
			this.render_layout();
			this.render_collapsed();
			
		},
		
		render_layout: function(){
			
			// vars
			var layout = this.setting('layout input:checked').val();
			
			
			// update data
			this.$fields.attr('data-layout', layout);
			
		},
		
		render_collapsed: function(){
			
			// vars
			var $select = this.setting('collapsed select');
			
			
			// collapsed
			var choices = [];
			
			
			// keep 'null' choice
			choices.push({
				'label': $select.find('option[value=""]').text(),
				'value': ''
			});
			
			
			// loop
			this.$fields.children('.pdc-field-object').each(function(){
				
				// vars
				var $field = $(this);
				
				
				// append
				choices.push({
					'label': $field.find('.field-label:first').val(),
					'value': $field.attr('data-key')
				});
				
			});
			
			
			// render
			pdc.render_select( $select, choices );
			
		},
		
		_change_layout: function( e ){
			
			this.render_layout();
			
		},
		
		_focus_collapsed: function( e ){
			
			this.render_collapsed();
			
		}
		
	});
	
	
	/*
	*  flexible_content
	*
	*  description
	*
	*  @type	function
	*  @date	25/09/2015
	*  @since	5.2.3
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	var pdc_settings_flexible_content = pdc.field_group.field_object.extend({
		
		type: 'flexible_content',
		
		actions: {
			'render_settings':		'render'
		},
					
		render: function(){
			
			// reference
			var self = this,
				$field = this.$field;
			
			
			// sortable
			if( ! this.$settings.hasClass('ui-sortable') ) {
				
				// add sortable
				this.$settings.sortable({
					items					: '> .pdc-field-setting-fc_layout',
					handle					: '[data-name="pdc-fc-reorder"]',
					forceHelperSize			: true,
					forcePlaceholderSize	: true,
					scroll					: true,
					start : function (event, ui) {
						
						pdc.do_action('sortstart', ui.item, ui.placeholder);
						
		   			},
		   			
		   			stop : function (event, ui) {
					
						pdc.do_action('sortstop', ui.item, ui.placeholder);
						
						// save flexible content (layout order has changed)
						pdc.field_group.save_field( $field );
						
		   			}
				});
				
			}
			
			
			// render layouts
			this.$settings.children('.pdc-field-setting-fc_layout').each(function(){
				
				self.layout.render( $(this) );
					
			});
			
		},
		
		
		layout: null
		
	});
	
	
	pdc_settings_flexible_content.layout = pdc.model.extend({
		
		actions: {
			'update_field_parent':	'update_field_parent'
		},
		
		events: {
			'change .pdc-fc-meta-display select':		'_change_display',
			'blur .pdc-fc-meta-label input':			'_blur_label',
			'click a[data-name="pdc-fc-add"]':			'_add',
			'click a[data-name="pdc-fc-duplicate"]':	'_duplicate',
			'click a[data-name="pdc-fc-delete"]':		'_delete'
		},
		
		event: function( e ){
			
			return e.$el.closest('.pdc-field-setting-fc_layout');
			
		},
			
		update_meta: function( $field, $layout ){
			
			pdc.field_group.update_field_meta( $field, 'parent_layout', $layout.attr('data-id') );
			
		},
		
		delete_meta: function( $field ){
			
			pdc.field_group.delete_field_meta( $field, 'parent_layout' );
			
		},
		
		
		/*
		*  update_field_parent
		*
		*  this function will update a sub field's 'parent_layout' meta data
		*
		*  @type	function
		*  @date	16/11/16
		*  @since	5.5.0
		*
		*  @param	$post_id (int)
		*  @return	$post_id (int)
		*/
		
		update_field_parent: function( $el, $parent ){			
			
			// vars
			var $layout = $el.closest('.pdc-field-setting-fc_layout');
			
			
			// bail early if not a sub field of a flexible content field
			// - don't save field as lack of 'parent' will avoid any issues with field's 'parent_layout' setting
			if( !$layout.exists() ) {
				
				return this.delete_meta( $el );
				
			}
			
			
			// update meta
			this.update_meta( $el, $layout );
						
			
			// save field
			// - parent_layout meta needs to be saved within the post_content serialized array
			pdc.field_group.save_field( $el );
						
		},
		
		
		/*
		*  render
		*
		*  This function will update the field list class
		*
		*  @type	function
		*  @date	8/04/2014
		*  @since	5.0.0
		*
		*  @param	$field_list
		*  @return	n/a
		*/
		
		render: function( $el ){
			
			// reference
			var self = this;
			
			
			// vars
			var $key = $el.find('.pdc-fc-meta-key:first input'),
				$fields = $el.find('.pdc-field-list:first'),
				display = $el.find('.pdc-fc-meta-display:first select').val();
			
			
			// update key
			// - both duplicate and add function need this
			$key.val( $el.attr('data-id') );
			
			
			// update data
			$fields.attr('data-layout', display);
			
			
			// update meta
			$fields.children('.pdc-field-object').each(function(){
				
				self.update_meta( $(this), $el );
				
			});
			
		},
		
		
		/*
		*  events
		*
		*  description
		*
		*  @type	function
		*  @date	25/09/2015
		*  @since	5.2.3
		*
		*  @param	$post_id (int)
		*  @return	$post_id (int)
		*/
		
		_change_display: function( $el ){
			
			this.render( $el );
			
		},
		
		_blur_label: function( $el ){
			
			// vars
			var $label = $el.find('.pdc-fc-meta-label:first input'),
				$name = $el.find('.pdc-fc-meta-name:first input');
			
			
			// only if name is empty
			if( $name.val() == '' ) {
				
				// vars
				var s = $label.val();
				
				
				// sanitize
				s = pdc.str_sanitize(s);
				
				
				// update name
				$name.val( s ).trigger('change');
				
			}
			
		},
		
		_add: function( $el ){
			
			// duplicate
			var $el2 = pdc.duplicate({
				$el: $el,
				after: function( $el, $el2 ){
					
					// remove sub fields
					$el2.find('.pdc-field-object').remove();
					
					
					// show add new message
					$el2.find('.no-fields-message').show();
					
					
					// reset layout meta values
					$el2.find('.pdc-fc-meta input').val('');
					
				}
			});
			
			
			// render layout
			this.render( $el2 );
			
			
			// save field
			pdc.field_group.save_field( $el.closest('.pdc-field-object') );
			
		},
		
		_duplicate: function( $el ){
			
			// duplicate
			$el2 = pdc.duplicate( $el );
			
			
			// fire action 'duplicate_field' and allow pdc.pro logic to clean sub fields
			pdc.do_action('duplicate_field', $el2);
					
			
			// render layout
			this.render( $el2 );
			
			
			// save field
			pdc.field_group.save_field( $el.closest('.pdc-field-object') );
			
		},
		
		_delete: function( $el ){
			
			// validate
			if( $el.siblings('.pdc-field-setting-fc_layout').length == 0 ) {
			
				alert( pdc._e('flexible_content','layout_warning') );
				
				return false;
				
			}
			
			
			// delete fields
			$el.find('.pdc-field-object').each(function(){
				
				// delete without animation
				pdc.field_group.delete_field( $(this), false );
				
			});
			
			
			// remove tr
			pdc.remove_tr( $el );
			
			
			// save field
			pdc.field_group.save_field( $el.closest('.pdc-field-object') );
				
		}
		
	});
	
	
	/*
	*  clone
	*
	*  This field type requires some extra logic for its settings
	*
	*  @type	function
	*  @date	24/10/13
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	var pdc_settings_clone = pdc.field_group.field_object.extend({
		
		type: 'clone',
		
		actions: {
			'render_settings': 'render'
		},
		
		events: {
			'change .pdc-field-setting-display select':			'render_display',
			'change .pdc-field-setting-prefix_label input':		'render_prefix_label',
			'change .pdc-field-setting-prefix_name input':		'render_prefix_name'
		},
		
		render: function(){
			
			// render
			this.render_display();
			this.render_prefix_label();
			this.render_prefix_name();
			
		},
		
		render_display: function(){
			
			// vars
			var display = this.setting('display select').val()
			
			
			// update data
			this.$field.attr('data-display', display);
			
		},
		
		render_prefix_label: function(){
			
			// vars
			var s = '%field_label%';
			
			
			// is checked
			if( this.setting('prefix_label input[type="checkbox"]').prop('checked') ) {
				
				s = this.setting('label input[type="text"]').val() + ' ' + s;
				
			}
			
			
			// update code
			this.setting('prefix_label code').html( s );
			
		},
		
		render_prefix_name: function(){
			
			// vars
			var s = '%field_name%';
			
			
			// is checked
			if( this.setting('prefix_name input[type="checkbox"]').prop('checked') ) {
				
				s = this.setting('name input[type="text"]').val() + '_' + s;
				
			}
			
			
			// update code
			this.setting('prefix_name code').html( s );
			
		},
		
		select2: null
			
	});
	
	pdc_settings_clone.select2 = pdc.model.extend({
		
		filters: {
			'select2_args':			'select2_args',
			'select2_ajax_data':	'select2_ajax_data'
		},
		
		select2_args: function( select2_args, $select, args ){
			
			// bail early if not clone
			if( args.ajax_action !== 'pdc/fields/clone/query' ) return select2_args;
			
			
			// remain open on select
			select2_args.closeOnSelect = false;
			
			
			// return
			return select2_args;
		},
		
		select2_ajax_data: function( data, args, params ){
			
			// bail early if not clone
			if( args.ajax_action !== 'pdc/fields/clone/query' ) return data;
			
			
			// find current fields
			var fields = {};
			
			
			// loop
			$('.pdc-field-object').each(function(){
				
				// vars
				var $el = $(this),
					key = $el.data('key'),
					type = $el.data('type'),
					label = $el.find('.field-label:first').val(),
					$ancestors = $el.parents('.pdc-field-object');
				
				
				// label
				fields[ key ] = {
					'key': key,
					'type': type,
					'label': label,
					'ancestors': $ancestors.length
				};
				
			});
			
			
			// append fields
			data.fields = fields;
			
			
			// append title
			data.title = $('#title').val();
			
			
			// return
			return data;
			
		}
		
	});

})(jQuery);

// @codekit-prepend "../js/field-group.js";

