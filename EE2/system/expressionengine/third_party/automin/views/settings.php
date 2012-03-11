<?=form_open($form_action)?>
	
	<table class="mainTable" border="0" cellspacing="0" cellpadding="0">

		<thead>
			<tr>
				<th colspan="2">Settings</th>
			</tr>
		</thead>

		<tbody>

			<tr class="even">
				<td width="33%">
					<strong>AutoMin Enabled?</strong><br>
					<small>Will disable all AutoMin functions.</small>
				</td>
				<td>
					<?= form_dropdown('automin_enabled', array('No', 'Yes'), @$automin_settings['automin_enabled']) ?>
				</td>
			</tr>

			<tr class="even">
				<td width="33%">
					<strong>Caching Enabled?</strong><br>
					<small>Useful in development.</small>
				</td>
				<td>
					<?= form_dropdown('caching_enabled', array('No', 'Yes'), @$automin_settings['caching_enabled']) ?>
				</td>
			</tr>

			<tr class="even">
				<td width="33%">
					<strong>Compress HTML Markup?</strong><br>
					<small>Compresses your template output.</small>
				</td>
				<td>
					<?= form_dropdown('compress_html', array('No', 'Yes'), @$automin_settings['compress_html']) ?>
				</td>
			</tr>

			<tr class="odd">
				<td width="33%">
					<strong>Path to Cache Directory</strong><br>
					<small>Something like, /var/www/vhosts/domain.com/httpdocs/automin/</small>
				</td>
				<td>
					<?=form_input(array('type' => 'text', 'name' => 'cache_path'), @$automin_settings['cache_path'])?>
				</td>
			</tr>

			<tr class="odd">
				<td width="33%">
					<strong>URL to Cache Directory</strong><br>
					<small>Something like, /automin/</small>
				</td>
				<td>
					<?=form_input(array('type' => 'text', 'name' => 'cache_url'), @$automin_settings['cache_url'])?>
				</td>
			</tr>

			<tr>
				<td>&nbsp;</td>
				<td>
					<?=form_submit(array('name' => 'submit', 'value' => lang('submit'), 'class' => 'submit'))?>
				</td>
			</tr>
			
		</tbody>

	</table>

<?=form_close()?>