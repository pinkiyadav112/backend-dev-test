<?php
/**
 * @author     Kings Of The Web
 * @year       2022
 * @package    english.codedegree.com
 * @subpackage ${NAMESPACE}
 */


// check if user is logged in outside of WordPress.
if ( ! function_exists( 'is_user_logged_in' ) ) {
	require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
}

// check if user is logged in.
if ( ! is_user_logged_in() ) {
	return;
}

// check if user is admin.
if ( ! current_user_can( 'administrator' ) && ! current_user_can( 'editor' ) ) {
	return;
}


// read the names of all directories inside this directory.
$dirs = array_filter( glob( '*' ), 'is_dir' );
?>

	<!-- add html page styled with bootstrap tables. -->
	<html>
	<head>
		<title>Available Lottie Animations</title>
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
			  integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z"
			  crossorigin="anonymous">
		<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"
				integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj"
				crossorigin="anonymous"></script>
		<link rel="stylesheet"
			  href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.18.1/bootstrap-table.min.css">
		<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.18.1/bootstrap-table.min.js"></script>

		<style>
			td {
				cursor: pointer;
			}

			td span {
				position: relative;
				left: 1%;
				display: none;
				border-bottom: 1px dotted black;
			}
		</style>
	</head>
	<body>
	<div class='container'>
		<div class='row'>
			<div class='col-12'>
				<h1>Available Lottie Animations</h1>
				<!-- create ordered table with bootstrap tables -->
				<table class='table table-striped'>
					<thead>
					<tr>
						<th scope='col'>Name</th>
						<th scope='col'>Preview</th>
					</tr>
					</thead>
					<tbody>
					<?php foreach ( $dirs as $key => $dir ) : ?>
						<tr>
							<td
									class="name"
									data-key="<?php echo $key; ?>"
									onclick="copyMe(<?php echo $key; ?>)"
									data-name="<?php echo $dir; ?>"><?php echo $dir; ?></td>
							<td>
								<!-- add an iframe with the lottie animation -->
								<iframe id="lottie-<?php echo $key; ?>" src="<?php echo $dir . '/demo/data.html'; ?>"
										style="width: 100%; height: 100%;"></iframe>

							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>

			</div>
		</div>

		<script>
			$(function () {
				$('table').bootstrapTable({
					search: true,
					pagination: true,
					showColumns: true,
					pageSize: 10,
					// show entries.
					pageList: [10, 25, 50, 100, 200, 500],
					// show columns.
					columns: [{
						field: 'name',
						title: 'Name',
						sortable: true
					}, {
						field: 'preview',
						title: 'Preview',
						sortable: false
					}]
				});
			});

			// get all the table td with class name.
			let tds = document.querySelectorAll('td.name');
			console.log(tds);
			// loop through all the tds.
			tds.forEach(td => {
				td.addEventListener('click', e => {
					let text = td.getAttribute('data-name');
					console.log(text);

					// copy to clipboard.
					navigator.clipboard.writeText(text);
				})
			})
		</script>

	</body>
	</html>
<?php
