<div class="module module-<?php echo esc_attr( $this->slug ); ?>">
	<# if ( data.heading_tag == 'h1' ) { #>
		<h1 class="fancy-heading {{{ data.text_alignment }}}">
			<span class="maketable">
				<span class="addBorder"></span>
				<span class="fork-icon"></span>
				<span class="addBorder"></span>
			</span>
			<em class="sub-head">{{{ data.sub_heading }}}</em>
			<span class="main-head">{{{ data.heading }}}</span>
			<span class="bottomBorder"></span>
		</h1>
	<# } else { #>
		<{{{ data.heading_tag }}} class="fancy-heading {{{ data.text_alignment }}}">
			<span class="maketable">
				<span class="addBorder"></span>
				<em class="sub-head">{{{ data.sub_heading }}}</em>
				<span class="addBorder"></span>
			</span>
			<span class="main-head">{{{ data.heading }}}</span>
			<span class="maketable">
				<span class="addBorder"></span>
				<span class="fork-icon"></span>
				<span class="addBorder"></span>
			</span>
		</{{{ data.heading_tag }}}>
	<# } #>
</div>