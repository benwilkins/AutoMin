<div class="pageContents moduleWrap">

	<? if ($_strMessage): ?>
		<script type="text/javascript">
			jQuery(function($){
				$.ee_notice('<?php echo addslashes($_strMessage);?>',{open: true, type:"success"});
				setTimeout(function(){ $.ee_notice.destroy(); }, 3000);
			});
		</script>
	<? endif; ?>

    <?=$this->load->view($_strContentView)?>
</div>