<?php  /**
 * Module Widget
 *
 * This file is used to register and display the Hatch - Portfolios widget.
 *
 * @package Hatch
 * @since Hatch 1.0
 */
if( !class_exists( 'Hatch_Widget_Column_Widget' ) ) {
	class Hatch_Widget_Column_Widget extends WP_Widget {

		/**
		*  Widget variables
		*/
		private $widget_title = 'Dynamic Sidebars';
		private $widget_id = 'widget-columns';
		private $post_type = '';
		private $taxonomy = '';
		private $warning = '<div class="container instructions instruction-warning t-center"><p class="push-top push-bottom">Select a column count, then click Save &amp; Publish to start adding widgets to this widget area.</p></div>';
		public $checkboxes = array();

		/**
		*  Widget construction
		*/
		function Hatch_Widget_Column_Widget(){
			/* Widget settings. */
			$widget_ops = array( 'classname' => 'obox-hatch-' . $this->widget_id .'-widget', 'description' => 'This widget is used to display your ' . $this->widget_title . '.' );

			/* Widget control settings. */
			$control_ops = array( 'width' => 1000, 'height' => NULL, 'id_base' => HATCH_THEME_SLUG . '-widget-' . $this->widget_id );

			/* Create the widget. */
			$this->WP_Widget( HATCH_THEME_SLUG . '-widget-' . $this->widget_id , $this->widget_title . ' Widget', $widget_ops, $control_ops );
		}

		/**
		*  Widget front end display
		*/
		function widget( $args, $instance ) {

			// Turn $args array into variables.
			extract( $args );

			// $instance Defaults
			$instance_defaults = array (
				'title' => NULL,
				'excerpt' => NULL,
				'title_alignment' => 't-left',
				'title_size' => '',
				'columns' => 'columns-3',
				'columns' => 'columns-4',
				'module_ids' => '1,2,3,4',
				'modules' => array()
			);

			$instance = wp_parse_args( $instance , $instance_defaults );

			// Turn $instance into an object named $widget, makes for neater code
			$widget = (object) $instance;

			// Set the span class for each column
			$col_count = str_ireplace('columns-', '', $widget->columns );
			$span_class = 'span-' . ( 12/ $col_count ); ?>

			<section class="widget row" id="<?php echo $widget_id; ?>">
				<?php if( empty( $widget->modules ) ) { ?>
					<?php _e( $this->warning , HATCH_THEME_SLUG ); // @TODO: Add a notice here about saving the widget before adding to the new sidebars ?>
				<?php }  else { ?>
					<div class="row <?php if( isset( $widget->layout ) && 'boxed' == $widget->layout ) echo 'container'; ?> push-bottom-large">
						<?php $col = 1; ?>
						<?php foreach ( $widget->modules as $key => $module) {
							$module = (object) $module; ?>
							<?php if( $col <= $col_count ) { ?>
								<div class="column <?php echo $span_class; ?>">
									<?php dynamic_sidebar( $widget_id . '-' . $key ); ?>
								</div>
							<?php } ?>
							<?php $col++; ?>
						<?php } ?>
					</div>
				<?php }?>

			</section>
			<!-- Front-end HTML Here
			<?php print_r( $instance ); ?>
			 -->

		<?php }

		/**
		*  Widget update
		*/

		function update($new_instance, $old_instance) {
			if ( isset( $this->checkboxes ) ) {
				foreach( $this->checkboxes as $cb ) {
					if( isset( $old_instance[ $cb ] ) ) {
						$old_instance[ $cb ] = strip_tags( $new_instance[ $cb ] );
					}
				} // foreach checkboxes
			} // if checkboxes
			return $new_instance;
		}

		/**
		*  Widget form
		*
		* We use regulage HTML here, it makes reading the widget much easier than if we used just php to echo all the HTML out.
		*
		*/
		function form( $instance ){

			// Initiate Widget Inputs
			$widget_elements = new Hatch_Widget_Elements();

			// $instance Defaults
			$instance_defaults = array (
				'title' => NULL,
				'excerpt' => NULL,
				'title_alignment' => 't-left',
				'title_size' => '',
				'columns' => 'columns-4',
				'module_ids' => '1,2,3,4'
			);

			// Parse $instance
			$instance_args = wp_parse_args( $instance, $instance_defaults );
			extract( $instance_args, EXTR_SKIP ); ?>
			<div class="hatch-container-large">

				<?php $widget_elements->header( array(
					'title' =>'Module',
					'icon_class' =>'module'
				) ); ?>

				<div class="hatch-container-large">

					<ul class="hatch-accordions">
						<li class="hatch-accordion-item">

							<?php $widget_elements->accordian_title(
								array(
									'title' => __( 'Content' , HATCH_THEME_SLUG ),
									'tooltip' => __(  'Place your help text here please.', HATCH_THEME_SLUG )
								)
							); ?>

							<section class="hatch-accordion-section hatch-content">
								<div class="hatch-row hatch-push-bottom clearfix">
									<?php if( !isset( $instance['module_ids'] ) ) { ?>
										<p class="hatch-form-item">
											<?php _e( $this->warning , HATCH_THEME_SLUG ); // @TODO: Add a notice here about saving the widget before adding to the new sidebars ?>
										</p>
									<?php } ?>
									<p class="hatch-form-item">
										<label for="<?php echo $this->get_field_id( 'columns' ) . '_module_columns'; ?>"><?php _e( 'Columns' , HATCH_THEME_SLUG ); ?></label>
										<?php echo $widget_elements->input(
											array(
												'type' => 'select',
												'name' => $this->get_field_name( 'layout' ) ,
												'id' => $this->get_field_id( 'layout' ) . '_module_columns',
												'value' => ( isset( $layout ) ) ? $layout : NULL,
												'options' => array(
													'fullwidth' => __( 'Full Width' , HATCH_THEME_SLUG ),
													'boxed' => __( 'Boxed' , HATCH_THEME_SLUG )
												)
											)
										); ?>
									</p>
									<p class="hatch-form-item">
										<label for="<?php echo $this->get_field_id( 'columns' ) . '_module_columns'; ?>"><?php _e( 'Columns' , HATCH_THEME_SLUG ); ?></label>
										<?php echo $widget_elements->input(
											array(
												'type' => 'select-icons',
												'name' => $this->get_field_name( 'columns' ) ,
												'id' => $this->get_field_id( 'columns' ) . '_module_columns',
												'data' => array( 'module_list' => '#module_list_' . $this->number ),
												'value' => ( isset( $columns ) ) ? $columns : NULL,
												'options' => array(
													'columns-1' => __( '1 Column' , HATCH_THEME_SLUG ),
													'columns-2' => __( '2 Column' , HATCH_THEME_SLUG ),
													'columns-3' => __( '3 Column' , HATCH_THEME_SLUG ),
													'columns-4' => __( '4 Column' , HATCH_THEME_SLUG )
												)
											)
										); ?>
									</p>
								</div>

								<?php echo $widget_elements->input(
									array(
										'type' => 'hidden',
										'name' => $this->get_field_name( 'module_ids' ) ,
										'id' => 'module_ids_input_' . $this->number,
										'value' => ( isset( $module_ids ) ) ? $module_ids : NULL
									)
								); ?>

								<?php // If we have some banners, let's break out their IDs into an array
								if( isset( $module_ids ) && '' != $module_ids ) $modules = explode( ',' , $module_ids ); ?>
								<div class="hatch-row hatch-<?php echo $columns; ?>" id="module_list_<?php echo $this->number; ?>" data-id_base="<?php echo $this->id_base; ?>" data-number="<?php echo $this->number; ?>">

									<?php // Start the column counter from 0
									$this->column_count = 0; ?>

									<?php foreach( $modules as $module ) {
										$this->module_item( array(
												'id_base' => $this->id_base ,
												'number' => $this->number ) ,
												$module ,
												( isset( $instance[ 'modules' ][ $module ] ) ) ? $instance[ 'modules' ][ $module ] : NULL );
									} ?>
								</div>
							</section>
						</li>
					</ul>

				</div>
			</div>
		<?php } // Form

		function module_item( $widget_details = array() , $module_guid = NULL , $instance = NULL ){

			// Update count for the columns
			$this->column_count++;

			// $instance Defaults
			$instance_defaults = array (
				'columns' => 4,
				'title' => 'Widget Area ' . $this->column_count,
				'excerpt' => NULL
			);

			// Parse $instance
			$instance_args = wp_parse_args( $instance, $instance_defaults );
			extract( $instance_args, EXTR_SKIP );

			// If there is no GUID create one. There should always be one but this is a fallback
			if( ! isset( $module_guid ) ) $module_guid = rand( 1 , 1000 );

			// Initiate Widget Inputs
			$widget_elements = new Hatch_Widget_Elements();

			// Turn the widget details into an object, it makes the code cleaner
			$widget_details = (object) $widget_details;  ?>
			<div class="hatch-column hatch-span hatch-span-position-<?php echo $this->column_count; ?>" data-guid="<?php echo $module_guid; ?>">
				<small class="hatch-drag"></small>
				<!-- Widget Column Extention -->
				<p class="hatch-form-item">
					<?php echo $widget_elements->input(
						array(
							'type' => 'text',
							'name' => 'widget-' . $widget_details->id_base . '[' . $widget_details->number . '][modules][' . $module_guid . '][title]' ,
							'id' => 'widget-' . $widget_details->id_base . '-' . $widget_details->number . '-' . $module_guid . '-title' ,
							'value' => ( isset( $title ) ) ? $title : NULL,
							'placeholder' => __( 'Enter Sidebar Title' , HATCH_THEME_SLUG ),
							'class' => 'hatch-text'
						)
					); ?>
				</p>
			</div>
		<?php }
	} // Class

	// Add our function to the widgets_init hook.
	 register_widget("Hatch_Widget_Column_Widget");
}