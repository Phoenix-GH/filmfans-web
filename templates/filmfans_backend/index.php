<?php defined( '_JEXEC' ) or die; 

include_once JPATH_THEMES.'/'.$this->template.'/logic.php';

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

        jQuery(function($) {

            var win = $(window);

            $('#menubutton').sidr({
                name: 'sidr-main',
                renaming: false,
                source: '#sidebar'
            });

            win.on('resize', function() {
                var c = $('#content');
                var h = $(window).height();
                c.css('min-height', h + 'px');
            });
            win.trigger('resize');

            win.on('scroll resize', (function() {
                $('.to-top').toggleClass('to-top-shown', win.scrollTop() > 50);
            }).debounceSoft(300, 310));

            $('[data-toggle="tooltip"]').tooltip()
        });
    /*]]>*/
    </script>
</head>
  
<body class="<?php echo ($is_front ? 'front' : 'site').' '.(!empty($active) ? $active->alias : '').' '.$pageclass; ?>" data-spy="scroll" data-target=".side_links">

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

    <header>
        <nav class="navbar navbar-default navbar-inverse">
            <div class="container">
                <button class="navbar-toggle" type="button" id="menubutton">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="<?php echo JUri::root(true); ?>">
                    <img src="<?php echo $tpath; ?>/images/tmpl/logo-small.png" alt="Home"/>
                </a>
            </div>
        </nav>
    </header>


    <!-- Main Content -->
    <div class="maincontent container-fluid container-xs-height">

        <div class="row row-xs-height">
            <div class="sidebar col-xs-height">
                <div id="sidebar">

                    <a class="navbar-brand" href="<?php echo JUri::root(true); ?>">
                        <img src="<?php echo $tpath; ?>/images/tmpl/logo-small.png" alt="Home"/>
                    </a>
                    <div class="clearfix"></div>

                    <div class="sbnav">
                        <?php if (JFactory::getUser()->get('id')) { ?>
                        <jdoc:include type="modules" name="sidebar-logged" />
                        <?php } else { ?>
                        <jdoc:include type="modules" name="sidebar" />
                        <?php } ?>
                    </div>
                    <div class="clearfix"></div>

                    <div class="sblinks">
                        <jdoc:include type="modules" name="bottom-spot1" />
                        <jdoc:include type="modules" name="bottom-spot2" />
                        <div class="half"></div>
                        <jdoc:include type="modules" name="bottom-spot3" />
                        <jdoc:include type="modules" name="bottom-spot4" />
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="container-fluid col-xs-height">
                <div class="content" id="content">
                    <jdoc:include type="message" />
                    <jdoc:include type="component" />
                </div>
            </div>
        </div>
    </div>

    <a class="to-top" href="javascript:void(0)" onclick="jQuery('html, body').finish().animate({'scrollTop' : 0});"></a>

    <jdoc:include type="modules" name="debug" />
</body>

</html>
