<?php
/**
 * GoDaddy Reseller Store icons widget class.
 *
 * Handles the Reseller store icons widget.
 *
 * @class    Reseller_Store/ProductIcons
 * @package  Reseller_Store/Plugin
 * @category Class
 * @author   GoDaddy
 * @since    NEXT
 */

namespace Reseller_Store;

if ( ! defined( 'ABSPATH' ) ) {

	// @codeCoverageIgnoreStart
	exit;
	// @codeCoverageIgnoreEnd
}

final class Product_Icons {

	const DOMAINS = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
  <title>Domains</title>
  <circle class="svg-fill-primary-o" cx="12" cy="12" r="10.5"/>
  <path class="svg-fill-grey" d="M12,23A11,11,0,1,1,23,12,11,11,0,0,1,12,23ZM12,2A10,10,0,1,0,22,12,10,10,0,0,0,12,2Z"/>
  <path class="svg-fill-grey" d="M12,23a.5.5,0,0,1-.19,0c-.23-.1-5.75-2.48-5.75-11S11.58,1.13,11.82,1a.5.5,0,0,1,.68.47v21A.5.5,0,0,1,12,23ZM11.5,2.32c-1.4.86-4.44,3.45-4.44,9.61s3,8.79,4.44,9.71V2.32Z"/>
  <path class="svg-fill-grey" d="M12,23a.5.5,0,0,1-.5-.5V1.5A.5.5,0,0,1,12.18,1c.23.09,5.76,2.35,5.76,10.9s-5.51,10.93-5.74,11A.52.52,0,0,1,12,23Zm.5-20.68V21.66c1.4-.9,4.44-3.56,4.44-9.72S13.9,3.19,12.5,2.32Z"/>
  <path class="svg-fill-grey" d="M1.5,11.5h21v1H1.5Z"/>
</svg>';

	const EMAIL = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
  <title>Email</title>
  <path class="svg-fill-white" d="M21.5,19H2.5a1,1,0,0,1-1-1V5a1,1,0,0,1,1-1h19a1,1,0,0,1,1,1V18A1,1,0,0,1,21.5,19Z"/>
  <path class="svg-fill-grey" d="M14.08,11.21l.71-.71,7.79,7.79-.71.71ZM1.41,18.3,9.21,10.5l.71.71L2.12,19Z"/>
  <path class="svg-fill-primary-o" d="M21.35,4H19l-7.16,6.05L5,4H2.35a1,1,0,0,0-.27,0l8.46,7.51a2,2,0,0,0,2.62,0l8.76-7.4A1,1,0,0,0,21.35,4Z"/>
  <path class="svg-fill-grey" d="M21.5,19.52H2.5A1.5,1.5,0,0,1,1,18V5a1.5,1.5,0,0,1,1.5-1.5h19A1.5,1.5,0,0,1,23,5V18A1.5,1.5,0,0,1,21.5,19.52Zm-19-15A.5.5,0,0,0,2,5V18a.5.5,0,0,0,.5.5h19A.5.5,0,0,0,22,18V5a.5.5,0,0,0-.5-.5Z"/>
  <path class="svg-fill-grey" d="M12,13.07a1.5,1.5,0,0,1-1-.38L1.67,4.4l.66-.75L11.69,12a.5.5,0,0,0,.65,0l9.34-7.89.65.76L13,12.72a1.5,1.5,0,0,1-1,.35Z"/>
</svg>';

	const HOSTING = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
  <title>Hosting</title>
  <path class="svg-fill-white" d="M21,19.88A1.12,1.12,0,0,1,19.88,21H4.12A1.12,1.12,0,0,1,3,19.88V17.12A1.12,1.12,0,0,1,4.12,16H19.88A1.12,1.12,0,0,1,21,17.12Zm0-6.5a1.12,1.12,0,0,1-1.12,1.12H4.12A1.12,1.12,0,0,1,3,13.38V10.62A1.12,1.12,0,0,1,4.12,9.5H19.88A1.12,1.12,0,0,1,21,10.62Zm0-6.5A1.12,1.12,0,0,1,19.88,8H4.12A1.12,1.12,0,0,1,3,6.88V4.12A1.12,1.12,0,0,1,4.12,3H19.88A1.12,1.12,0,0,1,21,4.12Z"/>
  <path class="svg-fill-white" d="M21,19.88A1.12,1.12,0,0,1,19.88,21H4.12A1.12,1.12,0,0,1,3,19.88V17.12A1.12,1.12,0,0,1,4.12,16H19.88A1.12,1.12,0,0,1,21,17.12Z"/>
  <path class="svg-fill-primary-o" d="M21,13.38a1.12,1.12,0,0,1-1.12,1.12H4.12A1.12,1.12,0,0,1,3,13.38V10.62A1.12,1.12,0,0,1,4.12,9.5H19.88A1.12,1.12,0,0,1,21,10.62Z"/>
  <path class="svg-fill-white" d="M21,6.88A1.12,1.12,0,0,1,19.88,8H4.12A1.12,1.12,0,0,1,3,6.88V4.12A1.12,1.12,0,0,1,4.12,3H19.88A1.12,1.12,0,0,1,21,4.12Z"/>
  <path class="svg-fill-grey" d="M19.88,21.5H4.12A1.62,1.62,0,0,1,2.5,19.88V17.12A1.62,1.62,0,0,1,4.12,15.5H19.88a1.62,1.62,0,0,1,1.62,1.62v2.76A1.62,1.62,0,0,1,19.88,21.5Zm-15.76-5a.62.62,0,0,0-.62.62v2.76a.62.62,0,0,0,.62.62H19.88a.62.62,0,0,0,.62-.62V17.12a.62.62,0,0,0-.62-.62Z"/>
  <circle class="svg-fill-primary-o" cx="5.5" cy="18.5" r="0.5"/>
  <circle class="svg-fill-primary-o" cx="7" cy="18.5" r="0.5"/>
  <circle class="svg-fill-primary-o" cx="8.5" cy="18.5" r="0.5"/>
  <path class="svg-fill-grey" d="M19.88,15H4.12A1.62,1.62,0,0,1,2.5,13.38V10.62A1.62,1.62,0,0,1,4.12,9H19.88a1.62,1.62,0,0,1,1.62,1.62v2.76A1.62,1.62,0,0,1,19.88,15ZM4.12,10a.62.62,0,0,0-.62.62v2.76a.62.62,0,0,0,.62.62H19.88a.62.62,0,0,0,.62-.62V10.62a.62.62,0,0,0-.62-.62Z"/>
  <circle class="svg-fill-white" cx="5.5" cy="12" r="0.5"/>
  <circle class="svg-fill-white" cx="7" cy="12" r="0.5"/>
  <circle class="svg-fill-white" cx="8.5" cy="12" r="0.5"/>
  <path class="svg-fill-grey" d="M18.5,12.5h-3a.5.5,0,0,1,0-1h3a.5.5,0,0,1,0,1Zm1.38-4H4.12A1.62,1.62,0,0,1,2.5,6.88V4.12A1.62,1.62,0,0,1,4.12,2.5H19.88A1.62,1.62,0,0,1,21.5,4.12V6.88A1.62,1.62,0,0,1,19.88,8.5ZM4.12,3.5a.62.62,0,0,0-.62.62V6.88a.62.62,0,0,0,.62.62H19.88a.62.62,0,0,0,.62-.62V4.12a.62.62,0,0,0-.62-.62Z"/>
  <circle class="svg-fill-primary-o" cx="5.5" cy="5.5" r="0.5"/>
  <circle class="svg-fill-primary-o" cx="7" cy="5.5" r="0.5"/>
  <circle class="svg-fill-primary-o" cx="8.5" cy="5.5" r="0.5"/>
  <path class="svg-fill-grey" d="M18.5,6h-3a.5.5,0,0,1,0-1h3a.5.5,0,0,1,0,1Zm0,13h-3a.5.5,0,0,1,0-1h3a.5.5,0,0,1,0,1Z"/>
</svg>';

	const WORDPRESS = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
  <title>WordPress</title>
  <path class="svg-fill-primary-o" d="M21,20a1,1,0,0,1-1,1H4a1,1,0,0,1-1-1V17a1,1,0,0,1,1-1H20a1,1,0,0,1,1,1Z"/>
  <path class="svg-fill-grey" d="M20,21.5H4A1.5,1.5,0,0,1,2.5,20V17A1.5,1.5,0,0,1,4,15.5H20A1.5,1.5,0,0,1,21.5,17v3A1.5,1.5,0,0,1,20,21.5Zm-16-5a.5.5,0,0,0-.5.5v3a.5.5,0,0,0,.5.5H20a.5.5,0,0,0,.5-.5V17a.5.5,0,0,0-.5-.5Z"/>
  <circle class="svg-fill-white" cx="5.5" cy="18.5" r="0.5"/>
  <circle class="svg-fill-white" cx="7" cy="18.5" r="0.5"/>
  <circle class="svg-fill-white" cx="8.5" cy="18.5" r="0.5"/>
  <path class="svg-fill-grey" d="M18.5,19h-3a.5.5,0,0,1,0-1h3a.5.5,0,0,1,0,1Z"/>
  <path class="svg-fill-wordpress" d="M15.48,8.24a2.71,2.71,0,0,0-.43-1.42,2.4,2.4,0,0,1-.51-1.21.89.89,0,0,1,.86-.91h.07a5.14,5.14,0,0,0-7.77,1H8c.54,0,1.37-.07,1.37-.07a.21.21,0,0,1,0,.42l-.59,0,1.87,5.57,1.13-3.38L11,6.09l-.54,0a.21.21,0,0,1,0-.42s.85.07,1.35.07,1.37-.07,1.37-.07a.21.21,0,0,1,0,.42l-.59,0,1.86,5.53.51-1.72a4.81,4.81,0,0,0,.39-1.66Z"/>
  <path class="svg-fill-wordpress" d="M12,2.5a6,6,0,1,0,6,6A6,6,0,0,0,12,2.5Zm0,11.73A5.72,5.72,0,1,1,17.73,8.5,5.73,5.73,0,0,1,12,14.23Z"/>
  <path class="svg-fill-wordpress" d="M6.85,8.5a5.15,5.15,0,0,0,2.9,4.63L7.3,6.4A5.13,5.13,0,0,0,6.85,8.5Zm5.24.45-1.54,4.49a5.15,5.15,0,0,0,3.16-.08l0-.07ZM16.52,6a4,4,0,0,1,0,.53,4.86,4.86,0,0,1-.39,1.84l-1.57,4.54A5.15,5.15,0,0,0,16.52,6Z"/>
</svg>';

	const WEBSITES = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
  <title>Websites</title>
  <path class="svg-fill-white" d="M22.5,8.5l0,10a1,1,0,0,1-1,1h-19a1,1,0,0,1-1-1l0-10Z"/>
  <path class="svg-fill-primary-o" d="M22.5,5.48v3h-21v-3a1,1,0,0,1,1-1h19A1,1,0,0,1,22.5,5.48Z"/>
  <path class="svg-fill-grey" d="M23,8.5h0v-3A1.49,1.49,0,0,0,21.5,4h-19A1.49,1.49,0,0,0,1,5.5l0,13A1.49,1.49,0,0,0,2.51,20h19A1.49,1.49,0,0,0,23,18.5ZM2.47,5h19a.49.49,0,0,1,.5.5V8H2V5.5A.49.49,0,0,1,2.47,5ZM21.53,19h-19a.49.49,0,0,1-.5-.5L2,9H22l0,9.51A.49.49,0,0,1,21.53,19Z"/>
  <path class="svg-fill-grey" d="M15.5,6.5A.5.5,0,1,1,16,7a.5.5,0,0,1-.5-.5m2,0A.5.5,0,1,1,18,7a.5.5,0,0,1-.5-.5m2,0A.5.5,0,1,1,20,7a.5.5,0,0,1-.5-.5"/>
  <path class="svg-fill-primary-o" d="M12,11,8.5,13h7Z"/>
  <path class="svg-fill-primary-o" d="M15.5,16.5H15v-3h.5a.5.5,0,0,0,.25-.93l-1.25-.71V11a.5.5,0,0,0-1,0v.28l-1.25-.72a.5.5,0,0,0-.5,0l-3.5,2a.5.5,0,0,0,.25.93H9v3H8.5a.5.5,0,0,0,0,1h7a.5.5,0,0,0,0-1Z"/>
  <path class="svg-fill-white" d="M13.5,14.5h-3a.25.25,0,0,1,0-.5h3a.25.25,0,0,1,0,.5Zm0,1h-3a.25.25,0,0,1,0-.5h3a.25.25,0,0,1,0,.5Zm0,1h-3a.25.25,0,0,1,0-.5h3a.25.25,0,0,1,0,.5ZM12,12l-1.5,1h3Z"/>
  <path class="svg-fill-white" d="M13.5,13.25h-3a.25.25,0,0,1-.14-.46l1.5-1a.25.25,0,0,1,.28,0l1.5,1a.25.25,0,0,1-.14.46Z"/>
</svg>';

	const SEO = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
  <title>Search Engine Visibility</title>
  <path class="svg-fill-grey" d="M19.43,19.93a.5.5,0,0,1-.35-.15L14.6,15.3a.5.5,0,0,1,.71-.71l4.48,4.48a.5.5,0,0,1-.35.85Z"/>
  <circle class="svg-fill-white" cx="10" cy="10" r="7"/>
  <path class="svg-fill-grey" d="M10,17.5A7.5,7.5,0,1,1,17.5,10,7.51,7.51,0,0,1,10,17.5Zm0-14A6.5,6.5,0,1,0,16.5,10,6.51,6.51,0,0,0,10,3.5Zm10.5,18a1,1,0,0,1-.71-.29l-2.5-2.5a1,1,0,1,1,1.38-1.45l0,0,2.5,2.5a1,1,0,0,1-.71,1.71Z"/>
  <path class="svg-fill-primary-o" d="M13.87,10c0,1.59-2.29,2.88-3.87,2.88S6.12,11.59,6.12,10,8.41,7.13,10,7.13,13.87,8.41,13.87,10Z"/>
  <circle class="svg-fill-grey" cx="10" cy="10" r="1.5"/>
</svg>';

	const EMAIL_MARKETING = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
  <title>Email Marketing</title>
  <path class="svg-fill-white" d="M20.5,11.08l-9.35,5.41a1,1,0,0,1-1.36-.36L6.23,9.93A1,1,0,0,1,6.6,8.57l9.34-5.41a1,1,0,0,1,1.36.36h0l3.57,6.2a1,1,0,0,1-.36,1.36Z"/>
  <path class="svg-fill-primary-o" d="M3,15.73a.5.5,0,0,1-.25-.94l2.75-1.56a.5.5,0,1,1,.49.87L3.25,15.67a.49.49,0,0,1-.24.06ZM3,18.6a.5.5,0,0,1-.25-.93l4-2.27a.5.5,0,1,1,.49.87l-4,2.27A.5.5,0,0,1,3,18.6Zm0,2.86a.5.5,0,0,1-.25-.93l5.24-3a.5.5,0,1,1,.49.87l-5.24,3a.5.5,0,0,1-.25.07Z"/>
  <path class="svg-fill-grey" d="M14.48,9.29l.26-1L20.6,9.9l-.26,1ZM9.92,16.08l1.53-5.73,1,.26-1.53,5.73Z"/>
  <path class="svg-fill-primary-o" d="M13.47,10.47a.61.61,0,0,0,.68-.39l2.54-7a1,1,0,0,0-.74.1L6.6,8.57a1,1,0,0,0-.46.61l7.34,1.29Zm1.1-6L13,8.86,8,8Z"/>
  <path class="svg-fill-grey" d="M10.67,17.12a1.5,1.5,0,0,1-1.3-.75L5.8,10.18a1.5,1.5,0,0,1,.55-2l9.34-5.41a1.49,1.49,0,0,1,2,.55l3.57,6.2a1.5,1.5,0,0,1-.55,2l-9.35,5.41a1.48,1.48,0,0,1-.74.2ZM16.44,3.53a.49.49,0,0,0-.24.07L6.85,9a.5.5,0,0,0-.18.68l3.57,6.2a.49.49,0,0,0,.67.18h0l9.34-5.4a.49.49,0,0,0,.18-.68h0l-3.57-6.2a.49.49,0,0,0-.43-.25Z"/>
  <path class="svg-fill-grey" d="M13.58,11l-.19,0L6.3,9.72l.17-1L13.56,10a.11.11,0,0,0,.12-.07l2.4-6.61.94.34-2.4,6.61a1.11,1.11,0,0,1-1,.73Z"/>
</svg>';

	const SSL = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
  <title>SSL</title>
  <path class="svg-fill-white" d="M21.5,19.5H2.5a1,1,0,0,1-1-1V5.5a1,1,0,0,1,1-1h19a1,1,0,0,1,1,1v13A1,1,0,0,1,21.5,19.5Z"/>
  <path class="svg-fill-grey" d="M21.5,20H2.5A1.5,1.5,0,0,1,1,18.5V5.5A1.5,1.5,0,0,1,2.5,4h19A1.5,1.5,0,0,1,23,5.5v13A1.5,1.5,0,0,1,21.5,20ZM2.5,5a.5.5,0,0,0-.5.5v13a.5.5,0,0,0,.5.5h19a.5.5,0,0,0,.5-.5V5.5a.5.5,0,0,0-.5-.5Z"/>
  <path class="svg-fill-black" d="M12,17H5.5a.5.5,0,0,1,0-1H12a.5.5,0,0,1,0,1Zm0-2H5.5a.5.5,0,0,1,0-1H12a.5.5,0,0,1,0,1Z"/>
  <path class="svg-fill-primary-o" d="M17.5,16.5A1.5,1.5,0,1,1,19,15,1.5,1.5,0,0,1,17.5,16.5Zm0-2a.5.5,0,1,0,.5.5A.5.5,0,0,0,17.5,14.5Z"/>
  <path class="svg-fill-primary-o" d="M18.5,17.5a.5.5,0,0,1-.35-.15l-.65-.65-.65.65a.5.5,0,0,1-.71-.71l1-1a.5.5,0,0,1,.71,0l1,1a.5.5,0,0,1-.35.85ZM6.57,8.37a1.2,1.2,0,0,1,.49-1A2,2,0,0,1,8.31,7a2.73,2.73,0,0,1,.85.13,1.83,1.83,0,0,1,.65.36.36.36,0,0,1,.13.22.23.23,0,0,1-.07.19l-.41.42a.21.21,0,0,1-.17.07.39.39,0,0,1-.21-.09,1.44,1.44,0,0,0-.39-.21A1.22,1.22,0,0,0,8.28,8a.72.72,0,0,0-.37.08.25.25,0,0,0-.14.22.22.22,0,0,0,.08.17.65.65,0,0,0,.26.11l.77.2a1.91,1.91,0,0,1,.94.48,1.14,1.14,0,0,1,.31.82,1.26,1.26,0,0,1-.49,1,2,2,0,0,1-1.3.4,3,3,0,0,1-1-.16,1.93,1.93,0,0,1-.73-.44.37.37,0,0,1-.13-.24.24.24,0,0,1,.1-.21l.49-.38A.23.23,0,0,1,7.25,10a.36.36,0,0,1,.19.11,1.34,1.34,0,0,0,.94.38.7.7,0,0,0,.38-.09.28.28,0,0,0,.14-.24A.25.25,0,0,0,8.8,10a1,1,0,0,0-.36-.14l-.78-.19a1.5,1.5,0,0,1-.81-.45,1.18,1.18,0,0,1-.28-.81Zm3.87,0a1.2,1.2,0,0,1,.49-1A2,2,0,0,1,12.17,7a2.72,2.72,0,0,1,.85.13,1.83,1.83,0,0,1,.65.36.36.36,0,0,1,.13.22.23.23,0,0,1-.07.19l-.41.42a.21.21,0,0,1-.17.07.39.39,0,0,1-.21-.09,1.44,1.44,0,0,0-.39-.21A1.22,1.22,0,0,0,12.14,8a.71.71,0,0,0-.36.08.25.25,0,0,0-.14.22.22.22,0,0,0,.08.17.65.65,0,0,0,.26.11l.77.2a1.91,1.91,0,0,1,.94.48,1.14,1.14,0,0,1,.31.82,1.26,1.26,0,0,1-.49,1,2,2,0,0,1-1.3.4,3,3,0,0,1-1-.16,1.93,1.93,0,0,1-.73-.44.37.37,0,0,1-.13-.24.24.24,0,0,1,.1-.21l.48-.38a.23.23,0,0,1,.18-.06.36.36,0,0,1,.19.11,1.34,1.34,0,0,0,.94.38.7.7,0,0,0,.39-.09.28.28,0,0,0,.15-.24.25.25,0,0,0-.11-.21,1,1,0,0,0-.36-.14l-.78-.19a1.5,1.5,0,0,1-.81-.45,1.18,1.18,0,0,1-.28-.81Zm7.07,2.18v.71a.28.28,0,0,1-.05.15s-.08.09-.12.09H14.49s0-.06,0-.09,0-.11,0-.15v-4s-.07-.13,0-.16,0-.09,0-.09h.8s.11.06.14.09a.31.31,0,0,1,.07.16V10.5h1.83s.09,0,.12,0,.05,0,.05.06Z"/>
</svg>';

	const WEBSITE_SECURITY = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
  <title>Website Security</title>
  <path class="svg-fill-white" d="M4.5,6.68,11.88,3,19.5,6.92v3.63A11.19,11.19,0,0,1,11.88,21h0A11.16,11.16,0,0,1,4.5,10.4Z"/>
  <path class="svg-fill-grey" d="M11.88,21.45a.5.5,0,0,1-.17,0A11.59,11.59,0,0,1,4,10.39V6.68a.5.5,0,0,1,.28-.45L11.66,2.6a.5.5,0,0,1,.45,0l7.62,3.87a.5.5,0,0,1,.27.45v3.63a11.77,11.77,0,0,1-8,10.88ZM5,7V10.4a10.59,10.59,0,0,0,6.88,10A10.76,10.76,0,0,0,19,10.54V7.23L11.88,3.61Z"/>
  <path class="svg-fill-primary-o" d="M11,14.66,9.18,12.91a.66.66,0,0,1,0-.94.68.68,0,0,1,1,0l.89.84L13.88,10a.68.68,0,0,1,1,0,.66.66,0,0,1,0,.94Z"/>
</svg>';

	const DEDICATED_IP = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
  <title>Dedicated IP</title>
  <path class="svg-fill-primary-o" d="M10.5,2.5h5.16a4.82,4.82,0,0,1,4.66,4.75A4.8,4.8,0,0,1,15.71,12H13.5v2.5h-3ZM17,7.25c0-.9-.4-1.25-1.33-1.25H13.5V8.5h2.21C16.64,8.5,17,8.15,17,7.25ZM5.5,2.5H9v12H5.5ZM21,20a1,1,0,0,1-1,1H4a1,1,0,0,1-1-1V17a1,1,0,0,1,1-1H20a1,1,0,0,1,1,1Z"/>
  <path class="svg-fill-grey" d="M20,21.5H4A1.5,1.5,0,0,1,2.5,20V17A1.5,1.5,0,0,1,4,15.5H20A1.5,1.5,0,0,1,21.5,17v3A1.5,1.5,0,0,1,20,21.5Zm-16-5a.5.5,0,0,0-.5.5v3a.5.5,0,0,0,.5.5H20a.5.5,0,0,0,.5-.5V17a.5.5,0,0,0-.5-.5Z"/>
  <circle class="svg-fill-white" cx="5.5" cy="18.5" r="0.5"/>
  <circle class="svg-fill-white" cx="7" cy="18.5" r="0.5"/>
  <circle class="svg-fill-white" cx="8.5" cy="18.5" r="0.5"/>
  <path class="svg-fill-grey" d="M18.5,19h-3a.5.5,0,0,1,0-1h3a.5.5,0,0,1,0,1Z"/>
</svg>
';

	const ADD_PRODUCT = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
  <title>Additional Products</title>
  <path class="svg-fill-white" d="M18.5,22.5H5.5a1,1,0,0,1-1-1V2.5a1,1,0,0,1,1-1h13a1,1,0,0,1,1,1v19A1,1,0,0,1,18.5,22.5Z"/>
  <path class="svg-fill-grey" d="M18.5,23H5.5A1.5,1.5,0,0,1,4,21.5V2.5A1.5,1.5,0,0,1,5.5,1h13A1.5,1.5,0,0,1,20,2.5v19A1.5,1.5,0,0,1,18.5,23ZM5.5,2a.5.5,0,0,0-.5.5v19a.5.5,0,0,0,.5.5h13a.5.5,0,0,0,.5-.5V2.5a.5.5,0,0,0-.5-.5Z"/>
  <path class="svg-fill-primary-o" d="M9,7.5h6V10H9Z"/>
  <path class="svg-fill-grey" d="M4.5,11.5h15v1H4.5Zm10.5-1H9a.5.5,0,0,1-.5-.5V7.5A.5.5,0,0,1,9,7h6a.5.5,0,0,1,.5.5V10A.5.5,0,0,1,15,10.5Zm-5.5-1h5V8h-5Z"/>
  <path class="svg-fill-primary-o" d="M9,18h6v2.5H9Z"/>
  <path class="svg-fill-grey" d="M15,21H9a.5.5,0,0,1-.5-.5V18a.5.5,0,0,1,.5-.5h6a.5.5,0,0,1,.5.5v2.5A.5.5,0,0,1,15,21ZM9.5,20h5V18.5h-5ZM15,6H9A.5.5,0,0,1,9,5h6a.5.5,0,0,1,0,1Zm0,10.5H9a.5.5,0,0,1,0-1h6a.5.5,0,0,1,0,1Z"/>
</svg>';


	/**
	 * Get a Product Icon by post
	 *
	 * @since NEXT
	 *
	 * @param object $post       The product post for which you want product icon returned.
	 * @param string $image_type The type of image you want to get.
	 * @param string $class_name CSS class name.
	 *
	 * @return string               Returns html for the image.
	 */
	public static function get_product_icon( $post, $image_type = 'icon', $class_name = '' ) {

		if ( 'none' === $image_type ) {

			return '';

		}

		if ( 'icon' === $image_type ) {

			$image_id = rstore_get_product_meta( $post->ID, 'imageId' );
			return self::get_icon( $image_id, $class_name );

		}

		return get_the_post_thumbnail( $post->ID, $image_type );

	}

	/**
	 * Get a Product Icon by post
	 *
	 * @since NEXT
	 *
	 * @param object $image_id   The product post for which you want product icon returned.
	 * @param string $class_name CSS class name.
	 *
	 * @return string            Returns svg html for the image.
	 */
	public static function get_icon( $image_id, $class_name = '' ) {

			$content = sprintf( '<div class="rstore-product-icons %s">', esc_attr( $class_name ) );

		switch ( $image_id ) {

			case 'domains':
			case '1':
				$content .= self::DOMAINS;
				break;

			case 'hosting':
			case '3':
				$content .= self::HOSTING;
				break;

			case 'email':
			case '4':
				$content .= self::EMAIL;
				break;

			case 'websites':
			case '6':
				$content .= self::WEBSITES;
				break;

			case 'seo':
			case '7':
				$content .= self::SEO;
				break;

			case 'ssl':
			case '33':
				$content .= self::SSL;
				break;

			// @codingStandardsIgnoreStart
			case 'wordpress':
			// @codingStandardsIgnoreEnd
			case '46':
				$content .= self::WORDPRESS;
				break;

			case 'email_marketing':
			case 'email-marketing':
				$content .= self::EMAIL_MARKETING;
				break;

			case 'website-backup':
			case 'website-security':
				$content .= self::WEBSITE_SECURITY;
				break;

			case 'dedicated-ip':
				$content .= self::DEDICATED_IP;
				break;

			default:
				$content .= self::ADD_PRODUCT;
				break;
		}

			$content .= '</div>';

			return $content;

	}

}
