<body spellcheck="false" class="fadein">
	<div class="bframe_adjustparent bframe_shortcut" id="contents_container">
		<div id="contents">
			<div class="main_container bframe_adjustwindow" data-param="margin:0" >
				<form name="F1" id="F1" method="post" action="index.php">
					<?php
						echo $this->tab_control->getHtml();
						echo $this->form->getHtml();
					?>
				</form>
			</div>
		</div>
	</div>
</body>
