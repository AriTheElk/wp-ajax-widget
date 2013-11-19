<?php
/*  Copyright 2013  iGARET  (email : garetmckinley@me.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/






?>

<div class="wrap">
<form action="options.php" method="post">
<?php settings_fields( 'wpsa-settings' ); ?>
<?php do_settings_sections( 'wpsa-settings' ); ?>

<?php
$position = get_option('wpsa_aa_position', 'below-content');
$aboveSelected = ($position == 'above-content') ? 'selected' : '';
$belowSelected = ($position == 'below-content') ? 'selected' : '';
$aaURL = get_option('wpsa_aa_url', 'google.com');
?>

<h2>WP Super Ajax Auto-Load Settings</h2>

<table class="form-table" width="100%" cellpadding="10">
	<tbody>
		<tr>
			<td scope="row" align="left">
				<label>Auto-Append Position: </label>
				<select name="wpsa_aa_position">
					<option value="disabled">Disabled</option>
					<option value="above-content" <?=$aboveSelected?>>Above Content</option>
					<option value="below-content" <?=$belowSelected?>>Below Content</option>
				</select>
			</td>
		</tr>
		<tr>
			<td scope="row" align="left">
				<label>Auto-Append URL: </label>
				<input type="text" name="wpsa_aa_url" value="<?=$aaURL?>" />
			</td>
		</tr>
	</tbody>
</table>
<?php submit_button(); ?>
</form>
</div>