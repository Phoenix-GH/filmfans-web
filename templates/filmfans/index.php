<?php defined( '_JEXEC' ) or die; 

include_once JPATH_THEMES.'/'.$this->template.'/logic.php';

$user = JFactory::getUser();

?><!doctype html>

<html lang="<?php echo $this->language; ?>" xmlns:fb="http://ogp.me/ns/fb#">

<head>
    <jdoc:include type="head" />
    <meta property="fb:app_id" content="<?php echo $pglobal->get('fb-app'); ?>>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <link rel="apple-touch-icon-precomposed" href="<?php echo $tpath; ?>/images/apple/apple-touch-icon-57x57-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo $tpath; ?>/images/apple/apple-touch-icon-72x72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo $tpath; ?>/images/apple/apple-touch-icon-114x114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo $tpath; ?>/images/apple/apple-touch-icon-144x144-precomposed.png">

    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script type="text/javascript">
    /*<![CDATA[*/
        var ffSiteURL = '<?php echo jURI::base(); ?>';
    /*]]>*/
    </script>
</head>
  
<body class="ffmain <?php echo ($is_front ? 'front' : 'site').' '.(!empty($active) ? $active->alias : '').' '.$pageclass; ?><?php if ($this->_short) echo ' ffshort'; ?>">

    <div id="fb-root"></div>
    <script>
        if (typeof FB == 'undefined') {
            window.fbAsyncInit = function () {
                FB.init({
                    appId: '<?php echo $pglobal->get('fb-app'); ?>',
                    xfbml: true,
                    version: 'v2.2'
                });
            };

            (function (d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) {
                    return;
                }
                js = d.createElement(s);
                js.id = id;
                js.src = "//connect.facebook.net/en_US/sdk.js";
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));
        }
    </script>

    <div class="stick-header"></div>
    <header class="intro-header"<?php if (false && $is_front) { ?> style="background-image: url('<?php echo $tpath; ?>/images/banner-main<?php //echo rand(1, 5); ?>1.jpg')"<?php } ?>>

        <?php if ($is_front) { ?>

            <div class="darker1"></div>

            <jdoc:include type="modules" name="front-slider" style="raw" />
        <?php } ?>

        <nav class="navbar navbar-default navbar-custom">
            <div class="container-fluid">
                <div class="navbar-header">
                    <a class="navbar-brand" href="<?php echo JUri::root(true); ?>">
                        <img src="<?php echo $tpath; ?>/images/tmpl/logo-small.png" alt="Home" />
                    </a>
                </div>
                <?php if ($user->get('id')) { ?>
                <div class="profile-top">
                    <a href="<?php echo FilmFansHelper::routeMenuItem('miProfile'); ?>">
                        <span><?php echo $user->get('name'); ?></span>
                        <b><img src="<?php $_tmp = FilmFansHelper::getAvatar(null, $user); echo $_tmp[0]; ?>" alt="" /></b>
                    </a>
                    <jdoc:include type="modules" name="topnav-logged" />
                </div>
                <div class="profile-top-mobile">
                    <jdoc:include type="modules" name="topnav-logged" />
                </div>
                <?php } else { ?>
                <jdoc:include type="modules" name="topnav" />
                <?php } ?>
                <div class="clearfix"></div>

                <ul class=" share-nav navbar-right">
                    <li><a href="<?php echo JUri::root(); ?>about/#get-app" class="ios"></a></li>
                    <li><a href="<?php echo JUri::root(); ?>about/#get-app" class="android"></a></li>
                    <li><a href="https://facebook.com/FilmFans" class="facebook" target="_blank"></a></li>
                    <li><a href="https://twitter.com/FilmFans" class="twitter" target="_blank"></a></li>
                    <li><a href="https://instagram.com/filmfans" class="instagram" target="_blank"></a></li>
                </ul>
            </div>
        </nav>

        <div class="container search-container">
            <div class="row">
                <div class="site-heading">
                    <?php if ($is_front) { ?>
                    <div class="darker2"></div>
                    <h1><span>Find your favorite movies</span></h1>
                    <?php } ?>

                    <form method="get" action="<?php echo $search_url; ?>" id="search-form-cycle" data-select="#ffservice1s" data-effects="<?php echo $is_front ? 1 : 0; ?>">
                        <input type="hidden" name="service" value="filmfans" />
                        <div class="search-bar">
                            <input type="text" name="term" value="<?php echo $app->input->getString('term'); ?>" placeholder="Search on FilmFans" data-placeholder="<?php echo JText::_('TPL_FILMFANS_SEARCH_PROVIDER'); ?>" />
                            <input type="submit" value="Go" />
                            <img class="s-arrow" src="<?php echo $tpath; ?>/images/tmpl/search-arrow<?php if (!$is_front) echo '-stroke'; ?>.png" alt="" />
                        </div>
                        <?php if (!$this->_short) { ?>
                        <div class="search-types"><div class="search-types-items"><!--
                            <?php foreach ($services as $st) { ?>
                            --><div class="search-type"><span class="label <?php if ($service == $st) echo ' selected' ?>" data-placeholder="<?php echo JText::_('TPL_FILMFANS_SEARCH_PROVIDER_'.$st); ?>" data-value="<?php echo $st; ?>">
                                <img class="unactive" src="<?php echo $tpath; ?>/images/icons/search-<?php echo $st; ?>.png" alt="<?php echo $st; ?>" />
                                <img class="active" src="<?php echo $tpath; ?>/images/icons/search-<?php echo $st; ?>-active.png" alt="<?php echo $st; ?>" />
                            </span></div><!--
                            <?php } ?>
                        --></div></div>
                        <select name="service" id="ffservice1s">
                            <?php foreach ($services as $st) { ?>
                            <option value="<?php echo $st; ?>"<?php if ($service == $st) echo 'selected="selected"' ?>><?php echo $st; ?></option>
                            <?php } ?>
                        </select>
                        <?php } ?>
                    </form>
                </div>
            </div>
        </div>
        <?php if (!$is_front && !$this->_short) { ?>
            <jdoc:include type="modules" name="subnav" />
        <?php } ?>
    </header>

    <!-- Main Content -->
    <main class="container<?php if ($this->_short) echo '-fluid'; ?> maincontent" role="main">
        <jdoc:include type="message" />
        <jdoc:include type="component" />
    </main>

    <footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-12  col-md-12 col-sm-12">
                    <div class="footer_links">
                        <jdoc:include type="modules" name="bottom-spot1" />
                        <jdoc:include type="modules" name="bottom-spot2" />
                        <div class="half"></div>
                        <jdoc:include type="modules" name="bottom-spot3" />
                        <jdoc:include type="modules" name="bottom-spot4" />
                    </div>
                    <div class="footer_logo">
                       	<img src="<?php echo $tpath; ?>/images/tmpl/logo-dark.png" alt="" >
                       <p class="copyright"><span>Copyright &copy; <?php echo date('Y'); ?> FILMFANS.</span> All rights reserved.</p>
                   </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </footer>
    <jdoc:include type="modules" name="debug" />
</body>

</html>
