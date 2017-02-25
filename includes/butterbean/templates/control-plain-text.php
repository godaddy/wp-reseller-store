<?php // @codingStandardsIgnoreStart ?>
<label>
	<# if ( data.label ) { #>
		<span class="butterbean-label">{{ data.label }}</span>
	<# } #>

	<# if ( data.description ) { #>
		<span class="butterbean-description">{{{ data.description }}}</span>
	<# } #>

	<# if ( data.value ) { #>
		<span class="butterbean-plain-text-value" {{{ data.attr }}}>{{{ data.value }}}</span>
	<# } #>
</label>
<?php // @codingStandardsIgnoreEnd ?>
