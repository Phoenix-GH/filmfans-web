<?php
// no direct access
defined('_JEXEC') or die;
?>

<div id="wowslider-container1">
	<div class="ws_images"><ul>
        <?php $cc = 0; foreach ($slides as $k=>$v) { $cc++; ?>
		<li><a href="<?php echo $v['link']; ?>"><img src="<?php echo $v['image']; ?>" alt="<?php echo $v['title']; ?>" title="<?php echo $v['title']; ?>" id="wows1_<?php echo $cc; ?>"/></a></li>
        <?php } ?>
	</ul></div>
    <div class="ws_shadow"></div>
</div>
<script src="<?php echo JUri::base(true); ?>/media/filmfans/slider/wowslider.js" type="text/javascript"></script>
<script src="<?php echo JUri::base(true); ?>/media/filmfans/slider/script.js" type="text/javascript"></script>

<script type="text/javascript">
/*<![CDATA[*/
	jQuery( function($) {
		$("#wowslider-container1").wowSlider({
			effect: "glass_parallax",
			prev: "",
			next: "",
			duration: <?php echo $params->get('speed', 2000); ?>,
			delay: <?php echo $params->get('delay', 6000); ?>,
			width: 980,
			height: <?php echo $params->get('height', 540); ?>,
			autoPlay: true,
			autoPlayVideo: false,
			playPause: false,
			stopOnHover: false,
			loop: false,
			bullets: 0,
			caption: false,
			captionEffect: "parallax",
			controls: false,
			responsive: 3,
			fullScreen: false,
			gestures: 1,
			onBeforeStep: 0,
			images: 0
		});
	});
/*]]>*/
</script>