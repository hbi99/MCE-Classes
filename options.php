<?php

global $MCE_Classes;

$font_list = $MCE_Classes->get_font_list();

// scripts
wp_enqueue_style( array( "MCE_Classes-admin_style" ) );
wp_enqueue_script( array( "MCE_Classes-admin_script" ) );

?>
<div class="wrap mce_classes">

	<h2>Editor Classes <a class="nolink add-new-h2" href="/add-font-family/">Add New</a></h2>
	<div id="poststuff">

		<table class="widefat row_template">
			<thead>
				<tr>
					<td class="field_order"><input type="text" class="field_font_order" name="field_font_order" readonly value="1"/></td>
					<td class="field_name"><input type="text" class="field_font_name" name="field_font_name" placeholder="Font name"/></td>
					<td class="field_class"><input type="text" class="field_font_class" name="field_font_class" placeholder="Font classname"/></td>
					<td class="field_remove"><a class="nolink button" href="/remove-font-family/">Remove</a></td>
				</tr>
			</thead>
		</table>

		<div id="post-body" class="metabox-holder columns-2">
			<div id="post-body-content">
				<div class="fields_header">
					<table class="widefat">
						<thead>
							<tr>
								<th class="field_order">Font Order</th>
								<th class="field_name">Font Name</th>
								<th class="field_class">Classname</th>
								<th class="field_remove">&#160;</th>
							</tr>
						</thead>
					</table>
				</div>
				
				<ul class="sortable">
					<?php foreach( $font_list as $font_id => $font ) : list( $meta, $order ) = explode( '-', $font_id ); ?>
					<li>
						<table class="widefat">
							<thead>
								<tr>
									<td class="field_order"><a class="nolink mce_row_toggler" href="/toggle-row/"></a><input type="text" class="field_font_order" name="field_font_order" readonly value="<?php echo $order; ?>"/></td>
									<td class="field_name"><input type="text" class="field_font_name" name="field_font_name" value="<?php echo $font['name']; ?>"/></td>
									<td class="field_class"><input type="text" class="field_font_class" name="field_font_class" value="<?php echo $font['classname']; ?>"/></td>
									<td class="field_remove"><a class="nolink button" href="/remove-font-family/">Remove</a></td>
								</tr>
							</thead>
						</table>
						<div class="mce_extended">
							<textarea class="field_font_extended" placeholder="CSS rules"><?php echo $font['css']; ?></textarea>
						</div>
					</li>
					<?php endforeach; ?>
				</ul>

				<div class="progress"></div>

				<a class="button button-primary button-large nolink" href="/save-font-families/">Save</a>
			</div>
		</div>
	</div>
</div>
