<div class="wrap">
<?php screen_icon(); 
	redirectify_save_settings();
?>
<h2>Redirectify Settings</h2>
<form method="post" action=""> 
<?php 
	settings_fields( 'redirectify_settings_group' ); 
	do_settings_fields( 'redirectify_settings_group' );
 ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Redirect Name</th>
        <td><input type="text" name="redirect_name" value="" /></td>
        </tr>
         
        <tr valign="top">
        <th scope="row">Redirect to URL</th>
        <td><input type="text" name="redirect_url" value="" /></td>
        </tr>
        
        <!--
<tr valign="top">
        <th scope="row">Redirect If?</th>
        <td>
	        <select name="case">
	        	<option>Not Logged In</option>
	        	<option>On Login</option>
	        </select>
        </td>
        </tr>
-->
    </table>
    <input type="hidden" name="action" value="create" />
	<?php 
		submit_button('Create Redirect');
	?>
</form>
<form action="" method="post" name="deleteRedirects">
	<table>
		<thead>
			<th></th>
			<th>Redirect Name</th>
			<th>Redirect URL</th>
		</thead>
		<tbody>
		<?php
			$table_name = $wpdb->prefix . 'redirectify_config';
			$redirects = $wpdb->get_results("SELECT * FROM $table_name");
			foreach ($redirects as $r){
				echo '<tr><td><input type="checkbox" name="id" value="'. $r->id . '" /></td>';
				echo '<td>' . $r->name. '</td>';
				echo '<td>' . $r->url . '</td></tr>';
			}
		?>
		</tbody>
	</table>
	<input type="hidden" name="action" value="delete" />
	<?php submit_button('Delete Redirect'); ?>
</form>
</div>