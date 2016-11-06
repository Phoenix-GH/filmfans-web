<?php
defined('_JEXEC') or die('Restricted access');

JHtml::_('script', 'media/filmfans/js/jquery.scrollTo.min.js');
JHtml::_('stylesheet', 'media/filmfans/css/monopage.css');

$media = JUri::base(true).'/media/filmfans/page/';

$sections = array_flip($this->params->get('sections', array()));

?>

<script type="text/javascript">
/*<![CDATA[*/
    jQuery(function($) {
        $('.scroll-to').click(function(e) {
            e.preventDefault();
            $.scrollTo(jQuery(this).attr('href'), 500, { axis:'y' });
        });
        $('body').scrollspy();
    });
/*]]>*/
</script>

<div class="front_content_blocks">
	<?php if (array_key_exists('about', $sections)) { ?>
    <section id="about" class="page front_content_block cntr">
        <a name="about"></a>
        <h1 class="front_content_heading">
            <span>What is</span> #FILMFANS
        </h1>
        <?php if (!empty($this->about)) echo $this->about->introtext; ?>
    </section>
	<?php } ?>
	<?php if (array_key_exists('get-app', $sections)) { ?>
    <section id="get-app" class="page front_content_block cntr no-bottom-padding shadow_bottom">
        <a name="get-app"></a>
        <h1 class="front_content_heading">
            <span><?php if (!empty($this->app)) echo $this->app->title; ?></span>
        </h1>
        <?php if (!empty($this->app)) echo $this->app->introtext; ?>
        <div class="row">
            <div class="col-md-6 col-xs-12 text-right"><a href="<?php echo $this->params->get('linkAppIOS'); ?>"><img class="btn-img" alt="apple" src="<?php echo $media; ?>btn_apple.png" /></a></div>
            <div class="col-md-6 col-xs-12 text-left"><a href="<?php echo $this->params->get('linkAppAndroid'); ?>"><img class="btn-img" alt="android" src="<?php echo $media; ?>btn_android.png" /></a></div>
        </div>
        <img class="img-responsive" alt="mobiles" src="<?php echo $media; ?>mobiles.png" />
    </section>
	<?php } ?>
	<?php if (array_key_exists('browser-extention', $sections)) { ?>
    <section id="browser-extention" class="page front_content_block cntr half-side-padding">
        <a name="browser-extention"></a>
        <h1 class="front_content_heading">
            Browser Extenstion
        </h1>
        <p class="sub-heading">Download the extension for your favorite browser</p>
        <div class="text-center">
            <a href="<?php echo $this->params->get('linkExtChrome'); ?>" class="browser-box browser-chrome">
                <div class="browser-check"></div>
            </a>
            <a href="<?php echo $this->params->get('linkExtFF'); ?>" class="browser-box browser-firefox">
                <div class="browser-check"></div>
            </a>
            <a href="<?php echo $this->params->get('linkExtIE'); ?>" class="browser-box browser-ie">
                <div class="browser-check"></div>
            </a>
            <a href="<?php echo $this->params->get('linkExtOpera'); ?>" class="browser-box browser-opera">
                <div class="browser-check"></div>
            </a>
            <a href="<?php echo $this->params->get('linkExtSafari'); ?>" class="browser-box browser-safari">
                <div class="browser-check"></div>
            </a>
        </div>
        <img class="img-responsive" alt="ipad" src="<?php echo $media; ?>ipad.png" style="margin-top: 50px;" />
    </section>
	<?php } ?>
	<?php if (array_key_exists('invite', $sections)) { ?>
    <section id="invite" class="page front_content_block cntr">
        <a name="invite"></a>
        <h1 class="front_content_heading">
            Invite Friends
        </h1>
        <p class="sub-heading">Invite by email, via facebook or twitter</p>
        <form class="form-invite">
            <input type="text" placeholder="Your email" />
            <input type="submit" value="Invite" />
        </form>
        <div class="text-left">
            <div class="email-container">peter.roger@gmail.com</div>
            <div class="email-container">mike.alan@gmail.com</div>
            <div class="email-container">maria.sands@hotmail.com</div>
            <div class="email-container">john.coltrain@msn.com</div>
            <div class="email-container">sofiatemptra@yahoo.com</div>
            <div class="email-container">godspeed@gmail.com</div>
        </div>
        <div>
            <div class="button-small">Delete all</div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="big-facebook-container">
                    <div class="big-facebook-icon"></div>
                    <div class="big-facebook-content">Invite via Facebook</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="big-twitter-container">
                    <div class="big-twitter-icon"></div>
                    <div class="big-twitter-content">Invite via Twitter</div>
                </div>
            </div>
        </div>
    </section>
	<?php } ?>
	<?php if (array_key_exists('advertise', $sections)) { ?>
    <section id="advertise" class="page front_content_block cntr page front_content_block_white front_content_block_advertise">
        <a name="advertise"></a>
        <h1 class="front_content_heading">
            Advertise With Us
        </h1>
        <p class="sub-heading">Promote your business with <span class="highlight">#FilmFans</span> website</p>
        <form class="form-advertise">
            <input type="text" placeholder="Your email" />
            <input type="submit" value="Send" />
        </form>
    </section>
	<?php } ?>
	<?php if (array_key_exists('support', $sections)) { ?>
    <section id="support" class="page front_content_block cntr half-bottom-padding front_content_block_white page front_content_block_support">
        <a name="support"></a>
        <h1 class="front_content_heading ">
            Support
        </h1>
	    <div class="row">
		    <div class="col-md-6 ">
			    <div class="ff-support-sections">
				    <h4 class="text-center">Call us</h4>
				    <table class="ff-support">
					    <tr>
						    <td>Los Angeles:</td>
						    <td>+1 310 295 0273</td>
					    </tr>
					    <tr>
						    <td>London:</td>
						    <td>+44 20 7637 9614</td>
					    </tr>
				    </table>
			    </div>
			    <div class="ff-support-sections">
				    <h4 class="text-center">Email us</h4>
				    <table class="ff-support">
					    <tr>
						    <td>New Business:</td>
						    <td><?php echo JHtml::_( 'email.cloak', 'business@filmfans.com' ); ?></td>
					    </tr>
					    <tr>
						    <td>Press inquiries:</td>
						    <td><?php echo JHtml::_( 'email.cloak', 'press@filmfans.com' ); ?></td>
					    </tr>
					    <tr>
						    <td>Website support:</td>
						    <td><?php echo JHtml::_( 'email.cloak', 'support@filmfans.com' ); ?></td>
					    </tr>
				    </table>
			    </div>
			    <div class="ff-support-sections">
				    <h4 class="text-center">Headquarters</h4>

				    <p class="text-center">
					    9200 Sunset Blvd<br>
					    West Hollywood, CA 90069<br>
					    United States
				    </p>
			    </div>
		    </div>
		    <div class="col-md-6">
			    - FORMS -
		    </div>
	    </div>
        <p class="text-center" style="display: none;"><img alt="megaphone" src="<?php echo $media; ?>icon_megaphone_white.png" /></p>
    </section>
	<?php } ?>
	<?php if (array_key_exists('faq', $sections) && count($this->faq)) { ?>
    <section id="faq" class="page front_content_block">
        <a name="faq"></a>
        <h1 class="front_content_heading cntr">
            F.A.Q.
        </h1>

        <div class="accordion-faq" role="tablist" aria-multiselectable="true">

            <?php foreach ($this->faq as $faq) { ?>

            <a data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $faq->id; ?>" aria-expanded="false" aria-controls="collapse<?php echo $faq->id; ?>">
                <div class="accordion-header" role="tab" id="heading<?php echo $faq->id; ?>">
                   <?php echo $faq->title; ?>
                </div>
            </a>
            <div id="collapse<?php echo $faq->id; ?>" class="accordion-panel panel-collapse collapse" role="tabpanel" aria-labelledby="heading<?php echo $faq->id; ?>">
                <?php echo $faq->introtext; ?>
            </div>

            <?php } ?>

        </div>
    </section>
	<?php } ?>
	<?php if (array_key_exists('report-abuse', $sections)) { ?>
    <section id="report-abuse" class="page front_content_block cntr half-bottom-padding">
        <a name="report-abuse"></a>
        <h1 class="front_content_heading">
            Report Abuse
        </h1>
        <p class="sub-heading">Our staff will review the content which may be in violation of our content guidelines</p>
        <form class="form-report-abuse">
            <input type="text" placeholder="paste page url, ie: http://www.filmfans.com/trailer-edge-of-tomorrow" />
            <input type="submit" value="Send" />
        </form>
        <p class="text-center"><img alt="mobiles" src="<?php echo $media; ?>icon_warning.png" /></p>
    </section>
	<?php } ?>
	<?php if (array_key_exists('terms', $sections)) { ?>
    <section id="terms" class="page front_content_block">
        <a name="terms"></a>
        <h1 class="front_content_heading">
            <?php if (!empty($this->terms)) echo $this->terms->title; ?>
        </h1>
         <?php if (!empty($this->terms)) echo $this->terms->introtext; ?>
    </section>
	<?php } ?>
	<?php if (array_key_exists('privacy-policy', $sections)) { ?>
    <section id="privacy-policy" class="page front_content_block">
        <a name="privacy-policy"></a>
        <h1 class="front_content_heading">
            <?php if (!empty($this->policy)) echo $this->policy->title; ?>
        </h1>
        <?php if (!empty($this->policy)) echo $this->policy->introtext; ?>
    </section>
	<?php } ?>
    <div class="side_links">
        <div class="inner_right">
            <ul class="nav">
	           	<?php if (array_key_exists('about', $sections)) { ?>
                <li><a class="scroll-to" href="#about"><b></b></a><div class="arrow_box"><span class="arrow_text">What is #FILMFANS</span></div></li>
	            <?php } ?>
	           	<?php if (array_key_exists('get-app', $sections)) { ?>
                <li><a class="scroll-to" href="#get-app"><b></b></a><div class="arrow_box"><span class="arrow_text">Get The App</span></div></li>
	            <?php } ?>
	           	<?php if (array_key_exists('browser-extention', $sections)) { ?>
                <li><a class="scroll-to" href="#browser-extention"><b></b></a><div class="arrow_box"><span class="arrow_text">Browse Extenstion</span></div></li>
	            <?php } ?>
	           	<?php if (array_key_exists('invite', $sections)) { ?>
                <li><a class="scroll-to" href="#invite"><b></b></a><div class="arrow_box"><span class="arrow_text">Invite Friends</span></div></li>
	            <?php } ?>
	           	<?php if (array_key_exists('advertise', $sections)) { ?>
                <li><a class="scroll-to" href="#advertise"><b></b></a><div class="arrow_box"><span class="arrow_text">Advertise With Us</span></div></li>
	            <?php } ?>
	           	<?php if (array_key_exists('support', $sections)) { ?>
                <li><a class="scroll-to" href="#support"><b></b></a><div class="arrow_box"><span class="arrow_text">Support</span></div></li>
	            <?php } ?>
	           	<?php if (array_key_exists('faq', $sections) && count($this->faq)) { ?>
                <li><a class="scroll-to" href="#faq"><b></b></a><div class="arrow_box"><span class="arrow_text">F.A.Q.</span></div></li>
	            <?php } ?>
	           	<?php if (array_key_exists('report-abuse', $sections)) { ?>
                <li><a class="scroll-to" href="#report-abuse"><b></b></a><div class="arrow_box"><span class="arrow_text">Report Abuse</span></div></li>
	            <?php } ?>
	           	<?php if (array_key_exists('terms', $sections)) { ?>
                <li><a class="scroll-to" href="#terms"><b></b></a><div class="arrow_box"><span class="arrow_text">Terms Of Services</span></div></li>
	            <?php } ?>
	           	<?php if (array_key_exists('privacy-policy', $sections)) { ?>
                <li><a class="scroll-to" href="#privacy-policy"><b></b></a><div class="arrow_box"><span class="arrow_text">Privacy Policy</span></div></li>
	            <?php } ?>
            </ul>
        </div>
    </div>
</div>