<html>
	<head>



		<?php
        $url  = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $url .= $_SERVER['SERVER_NAME'];
        $url .= ":".$_SERVER['SERVER_PORT'];
        $url .= $_SERVER['REQUEST_URI'];

        $url =dirname($url)."/timetable.php";
?>
<script>
if (window.confirm('This page has new link: <?php echo $url ?>'))
{
window.location.href="<?php echo $url ?>";
};
				</script>
			</head>
			<body>
				<?php
        echo "<p><a href=\"$url\">$url</a></p>";
         ?>
	</body>
