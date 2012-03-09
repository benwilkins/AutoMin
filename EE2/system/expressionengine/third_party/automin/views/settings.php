<table class="mainTable" border="0" cellspacing="0" cellpadding="0">

	<thead>
		<tr>
			<th colspan="2">Settings</th>
		</tr>
	</thead>

	<tbody>

		<tr class="even">
			<td width="33%"><strong>AutoMin Enabled?</strong></td>
			<td>
				<?= form_dropdown('automin_enabled', array('No', 'Yes')) ?>
			</td>
		</tr>

		<tr class="even">
			<td width="33%"><strong>Caching Enabled?</strong></td>
			<td>
				<?= form_dropdown('caching_enabled', array('No', 'Yes')) ?>
			</td>
		</tr>

		<tr class="even">
			<td width="33%"><strong>Compress HTML Markup?</strong></td>
			<td>
				<?= form_dropdown('compress_html', array('No', 'Yes')) ?>
			</td>
		</tr>

		<tr class="odd">
			<td width="33%">Path to Cache Directory</td>
			<td>
				<?=form_input(array('type' => 'text', 'name' => 'cache_path'), '')?>
			</td>
		</tr>

		<tr class="odd">
			<td width="33%">URL to Cache Directory</td>
			<td>
				<?=form_input(array('type' => 'text', 'name' => 'cache_url'), '')?>
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