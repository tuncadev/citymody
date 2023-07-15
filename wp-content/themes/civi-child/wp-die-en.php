<?php
if ( !headers_sent() ) {
	status_header( $r['response'] );
	nocache_headers();
	header( 'Content-Type: text/html; charset=utf-8' );
}

$text_direction = 'ltr';
if ( ( isset($r['text_direction']) && 'rtl' == $r['text_direction'] ) || ( function_exists( 'is_rtl' ) && is_rtl() ) ) :
	$text_direction = 'rtl';
endif;

?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" <?php if ( function_exists( 'language_attributes' ) && function_exists( 'is_rtl' ) ) language_attributes(); else echo "dir='$text_direction'"; ?>>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width">
		<?php
		if ( function_exists( 'wp_robots_no_robots' ) ) {
			wp_robots_no_robots( array( 'noindex' ) );
		}
		?>
		<title>Keyboard Action Error</title>
		<style type="text/css">
			@import url(https://fonts.googleapis.com/css?family=Arvo);

			/* SELECTED TEXT */
			::selection { background: #ff5e99; color: #FFFFFF; text-shadow: 0; }
			::-moz-selection { background: #ff5e99; color: #FFFFFF; }
			html {
				font-size: 18px;font-size: 1.13rem;
				-webkit-text-size-adjust: 100%;
				-ms-text-size-adjust: 100%;
			}
			html, input { font-family: "arvo", "Helvetica Neue", Helvetica, Arial, sans-serif; }
			body {
				background: #4A575A;
				margin: auto;
				color: #fff;
			}
			a {
				transition: all 200ms ease;
				opacity: 1;
			}
			a:hover {
				filter: alpha(opacity=50); /* IE7 */
				opacity: 0.6;
			}
			a, a:visited, a:active {
				color: #fff;
				text-decoration: none;
			}
			.unicorn {
				max-width: 100%;
				height: 400px;
				margin: 20px 0 0 26px;
			}
			.container {
				max-width: 600px;
				_width: 600px;
				margin: 0 auto 80px;
				text-align: center;
			}
			h1 {
				margin: 0;
			}
			h2 {
				font-size: 1rem;
				font-weight: 400;
				margin: 0;
				padding: 4px 0 10px;
				color: #aaa;
			}
			h3 {
				margin: 20px 0 8px;
				text-align: center;
				font-size: 20px;
				font-weight: 500;
				line-height: 1.4;
				padding: 0 30px;
			}
			.warning {
				margin: 0px 0 30px 0;
				padding: 0px 20px 8px;
			}
			.warning p {
				font-size: 0.75rem;
			}
			.warning a {
				display: block;
				margin-top: 30px;
				padding: 20px;
				background: #16A085;
			}
			.copyright {
				font-size: 12px;
				text-align: center;
			}

			/* SVG */
			.unicorn {
				max-width: 100%;
				height: 400px;

				background: url(data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZlcnNpb249IjEuMSIgdmlld0JveD0iMCAwIDUwNS43NiA1NDkuMzgiPgo8cG9seWdvbiBmaWxsPSJub25lIiBzdHJva2U9IiNGMUYyRjIiIHN0cm9rZS13aWR0aD0iMiIgc3Ryb2tlLW1pdGVybGltaXQ9IjEwIiBwb2ludHM9Ijg2LjgsMTQyIDE0LjIsMTgyLjQgMTUsMTM1LjIgODEuMSw4NCIvPgo8cG9seWdvbiBmaWxsPSJub25lIiBzdHJva2U9IiNGMUYyRjIiIHN0cm9rZS13aWR0aD0iMiIgc3Ryb2tlLW1pdGVybGltaXQ9IjEwIiBwb2ludHM9IjcuMSw4LjEgNjIuMSw5NCA4MS4yLDc5LjciLz4KPHBvbHlnb24gZmlsbD0ibm9uZSIgc3Ryb2tlPSIjRjFGMkYyIiBzdHJva2Utd2lkdGg9IjIiIHN0cm9rZS1taXRlcmxpbWl0PSIxMCIgcG9pbnRzPSI4NC4yLDgxLjcgOTAuNywxNDIuMyA3OC45LDMwMS45IDEzNi4yLDI3MS4zIDE2Mi44LDE3Mi45IDEyNC45LDk4LjciLz4KPHBvbHlnb24gZmlsbD0ibm9uZSIgc3Ryb2tlPSIjRjFGMkYyIiBzdHJva2Utd2lkdGg9IjIiIHN0cm9rZS1taXRlcmxpbWl0PSIxMCIgcG9pbnRzPSIxNjksMjUyLjMgMTM5LjYsMjY5LjEgMTY1LjQsMTc0LjMgMjAyLjMsMTc3LjciLz4KPHBvbHlnb24gZmlsbD0ibm9uZSIgc3Ryb2tlPSIjRjFGMkYyIiBzdHJva2Utd2lkdGg9IjIiIHN0cm9rZS1taXRlcmxpbWl0PSIxMCIgcG9pbnRzPSIxNjUuOCwyNTguNyA3OC45LDMwNy43IDExNS45LDMyNi40IDM2LjMsNTQyIDIwMC4zLDM3OS43IDE2OC42LDM4Mi40Ii8+Cjxwb2x5Z29uIGZpbGw9Im5vbmUiIHN0cm9rZT0iI0YxRjJGMiIgc3Ryb2tlLXdpZHRoPSIyIiBzdHJva2UtbWl0ZXJsaW1pdD0iMTAiIHBvaW50cz0iMTc0LjQsMzc3LjggMTg5LjksMzYzIDIwMS43LDM3NS44Ii8+Cjxwb2x5Z29uIGZpbGw9Im5vbmUiIHN0cm9rZT0iI0YxRjJGMiIgc3Ryb2tlLXdpZHRoPSIyIiBzdHJva2UtbWl0ZXJsaW1pdD0iMTAiIHBvaW50cz0iMTY5LjksMjU5LjMgMTcyLjQsMzc0IDIyMS4yLDMyOS4zIi8+Cjxwb2x5Z29uIGZpbGw9Im5vbmUiIHN0cm9rZT0iI0YxRjJGMiIgc3Ryb2tlLXdpZHRoPSIyIiBzdHJva2UtbWl0ZXJsaW1pdD0iMTAiIHBvaW50cz0iMTcxLjQsMjU1LjUgMjA0LjQsMjI3LjUgMjIxLjIsMzIzLjgiLz4KPHBvbHlnb24gZmlsbD0ibm9uZSIgc3Ryb2tlPSIjRjFGMkYyIiBzdHJva2Utd2lkdGg9IjIiIHN0cm9rZS1taXRlcmxpbWl0PSIxMCIgcG9pbnRzPSIyMDcuOSwyMjkgMjIxLjcsMzAzLjkgMjU1LjIsMjc5LjUiLz4KPHBvbHlnb24gZmlsbD0ibm9uZSIgc3Ryb2tlPSIjRjFGMkYyIiBzdHJva2Utd2lkdGg9IjIiIHN0cm9rZS1taXRlcmxpbWl0PSIxMCIgcG9pbnRzPSIyMjIuNCwzMDcuNSAyMjYuMiwzMjkuMyAxOTMuMiwzNjAuMyAxOTYuNiwzNjQgMjM0LjcsMzM2LjggMzYzLjQsMzI5LjkgMjQ4LjIsMjY2LjUgMjYwLjQsMjc5LjgiLz4KPHBvbHlnb24gZmlsbD0ibm9uZSIgc3Ryb2tlPSIjRjFGMkYyIiBzdHJva2Utd2lkdGg9IjIiIHN0cm9rZS1taXRlcmxpbWl0PSIxMCIgcG9pbnRzPSIxOTguMywzNjYgMjA1LjIsMzc0LjMgMzUyLjQsMzMzLjcgMjM1LjksMzM5LjgiLz4KPHBvbHlnb24gZmlsbD0ibm9uZSIgc3Ryb2tlPSIjRjFGMkYyIiBzdHJva2Utd2lkdGg9IjIiIHN0cm9rZS1taXRlcmxpbWl0PSIxMCIgcG9pbnRzPSIyMjYuMiwyNDMuOCAyNDAuOSwyNTkuMyAzNjMuNCwzMjYuOSAyNDguNywyMzQiLz4KPHBvbHlnb24gZmlsbD0ibm9uZSIgc3Ryb2tlPSIjRjFGMkYyIiBzdHJva2Utd2lkdGg9IjIiIHN0cm9rZS1taXRlcmxpbWl0PSIxMCIgcG9pbnRzPSIyNzkuMSwyNTQuNSAzNTkuNCwzMjAuMyAzMTIuNywyNTQuNSIvPgo8cG9seWdvbiBmaWxsPSJub25lIiBzdHJva2U9IiNGMUYyRjIiIHN0cm9rZS13aWR0aD0iMiIgc3Ryb2tlLW1pdGVybGltaXQ9IjEwIiBwb2ludHM9IjMxNi45LDI1NC41IDM3MS42LDMzMi40IDI3MC45LDM1OS43IDMxMC4zLDQxNy43IDM4My42LDQwMi40IDM3Ny42LDM1NC40IDUwMC45LDM5OCAzNzAuNiwyNjIuNyIvPgo8cG9seWdvbiBmaWxsPSJub25lIiBzdHJva2U9IiNGMUYyRjIiIHN0cm9rZS13aWR0aD0iMiIgc3Ryb2tlLW1pdGVybGltaXQ9IjEwIiBwb2ludHM9IjMxNi42LDQxOS43IDM5OC4zLDU0MiAzODIuMyw0MDYuNCIvPgo8L3N2Zz4K) no-repeat center center;
			}
			.four-oh-four {
				max-width: 150px;
				height: 120px;
				text-indent: -9999px;
				margin: auto;

				background: url(data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAxOTYuNjY4IDk1LjY2MSIgPgo8Zz4KCTxwYXRoIGZpbGw9IiNGRkZGRkYiIGQ9Ik00OS40NjMgOC4yMmwtMjYuMDkgNDYuOTY0SDYyLjc1djM3LjVoLTEyLjV2LTI1SDIuMTI3bDM2LjQxLTY1LjUzNkw0OS40NjMgOC4yMnogTTEzMS41IDQ4LjkgYzAgMTcuMjYxLTEzLjk4OSAzMS4yNS0zMS4yNSAzMS4yNWMtMTcuMjYyIDAtMzEuMjUtMTMuOTktMzEuMjUtMzEuMjVjMC0xNy4yNTggMTMuOTg5LTMxLjI1IDMxLjI1LTMxLjI1IEMxMTcuNTExIDE3LjcgMTMxLjUgMzEuNyAxMzEuNSA0OC45MzR6IE0xMTkgNDguOTMzYzAtMTAuMzQtOC40MS0xOC43NS0xOC43NS0xOC43NWMtMTAuMzQgMC0xOC43NTEgOC40MS0xOC43NTEgMTguOCBjMCAxMC4zIDguNCAxOC44IDE4LjggMTguNzUxQzExMC41OSA2Ny43IDExOSA1OS4zIDExOSA0OC45MzN6IE0xNTQuNjIgNTUuMTg0SDE5NHYzNy41aC0xMi41di0yNWgtNDguMTIxIGwzNi40MDgtNjUuNTM2bDEwLjkyNiA2LjA3MkwxNTQuNjIgNTUuMTg0eiIvPgo8L2c+Cjwvc3ZnPg==) no-repeat center center;
			}
			
			/* Responsive
			-------------------------------------------------------*/

			/* Desktop only */
			@media only screen and (min-width : 1800px) {
				h2 {
					font-size: 1.75rem;

				}
				.warning p {
					font-size: 0.88rem;
				}
				.unicorn {
					max-width: 100%;
					height: 400px;
					margin: 60px 0 0 26px;
				}
			}

			@media only screen and (max-width : 568px) {
				body {
					background: #16A085;
				}
				.warning a {
					background: #037c63;
				}
				h2 {
					color: #fff;
				}
			}

			@media only screen and (max-width : 320px) {
				.unicorn {
					height: 150px;
					margin: 20px 0 0 0px !important;
				}
				.four-oh-four {
					height:40px;
					margin: 10px auto 10px;
				}
				h2 {
					font-size: 0.88rem;
					font-weight: bold;
				}
				.warning {
					margin: 0;
				}
				.warning p {
					margin-top: 10px;
					font-size: 0.63rem;
				}
				.warning a {
					margin-top: 20px;
					font-size: 0.63rem;
				}
			}
		</style>
	</head>

	<body id="error-page">
		<div class="unicorn"></div>

		<div class="container">

			<h1><?php echo __("Oops!", "civichild"); ?></h1>

			<div class="warning">
				<h2><?php echo $message; ?></h2>

				<?php if ( isset( $r['back_link'] ) && $r['back_link'] ) : ?>
					<a href="javascript:history.back()"><?php echo __("Please go back to the previous page", "civichild"); ?></a>
				<?php endif; ?>
			</div>

			<div class="footer"><p class="copyright"><a href="https://www.citymody.com/"><?php echo __("Copyright @2023 Citymody. All rights reserved", "civichild"); ?></a></p></div>

		</div>
	</body>

</html>