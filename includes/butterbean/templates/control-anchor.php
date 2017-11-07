<?php // @codingStandardsIgnoreStart ?>
<# if ( data.label ) { #>
	<label><span class="butterbean-label">{{ data.label }}</span></label>
<# } #>

<# if ( data.description ) { #>
	<p class="butterbean-description">{{{ data.description }}}</p>
<# } #>

<# if ( data.text ) { #>
	<span class="butterbean-anchor"><a {{{ data.attr }}}>{{{ data.text }}}</a></span>
<# } #>
<input type="submit" class="button button-primary" id="republish_product" name="republish_product" value="Reset">

<?php // @codingStandardsIgnoreEnd ?>
