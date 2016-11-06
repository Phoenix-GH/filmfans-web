<?php
// no direct access
defined('_JEXEC') or die;

$mainframe = JFactory::getApplication();

$orig = $mainframe->input->getString('term');
$term = urlencode($orig);
$iframe = $mainframe->input->getString('_iframe');

if (!function_exists('get_inner_html')) {

    function get_inner_html( $node ) {
        $innerHTML = '';
        $children = $node->childNodes;
        foreach ($children as $child) {
            $innerHTML .= $child->ownerDocument->saveHTML( $child );
        }
        return $innerHTML;
    }
}

$url = '';
$msg = 'Services is unavailable. Please try other search options.';
switch ($mainframe->input->get('service')) {
    case 'wiki':
        $url = 'http://en.wikipedia.org/wiki/'.$term;
        break;
    case 'bing':
        $url = 'http://www.bing.com/search?q='.$term;
        break;
    case 'google':
        $url = 'http://www.google.com/custom?q='.$term;
        break;
    case 'imdb':

        $html = file_get_contents('http://m.imdb.com/find?q=w'.$term);
        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new DOMXpath($dom);
        $elements = $xpath->query("//div[contains(@class,'poster')]");

        $found = array();

        foreach ($elements as $element) {

            $class = array_filter(explode(' ', trim($element->getAttribute('class'))));
            if (array_search('poster', $class) === false) continue;

            $text = get_inner_html($element);

            $text = preg_replace('/href="\//im', 'target="_blank" href="http://www.imdb.com/', $text);
            $text = preg_replace('/src="\//im', 'target="_blank" src="http://www.imdb.com/', $text);
            $text = preg_replace('/style="[^"]+"/im', '', $text);
            $text = preg_replace('/(class="\s*)label(\s*")/im', '$1item-info$2', $text);

            $text = preg_replace_callback('/src="([^"]+)"/', function ($s) {
                return 'src="'.JUri::base(true).'?ffaction=img&amp;url='.urlencode($s[1]).'"';
            }, $text);

            $found[] = '<div class="imdb-item">'.$text.'</div>';
        }

        $this->found = $found;
        echo $this->loadTemplate('imdb');
        return;

        break;
    case 'yahoo':

        //$url = 'http://uk.yhs4.search.yahoo.com/yhs/search?p='.$term.'&hspart=wintermike&hsimp=yhs-selfserve_5494f0dffb81e628';
        //break;

        ?>
        <div class="row">
            <div class="content yahoo-search">
                <div class="row" id="yahoosearch"></div>
            </div>
        </div>

        <script type="text/javascript" src="http://d.yimg.com/rj/v1/yhs.js"></script>
        <script type="text/javascript">
            jQuery(function($) {
                YHS.init({
                    hspart: 'wintermike', //will be provided in the code snippet
                    hsimp: "yhs-selfserve_5494f0dffb81e628", //will be provided in the code snippet
                    market: 'uk', //will be provided in the code snippet
                    dom_id: "yahoosearch", //id of div tag under which BOSS Hosted Search iframe will be rendered
                    searchbox_id: '', /*Optional. If you have turned off the Yahoo Search Box, only then
                     enter the id of your text box here - this is recommended to enable advanced functions such as auto-update
                     when a user clicks on a Related Search/Also Try query from within the BOSS Hosted Search iframe */
                    debug: false //Optional. set to true only in development or testing environment
                });
                YHS.search('<?php echo addslashes($orig) ?>');
            });
        </script>

        <?php

        return;

        break;

    case 'youtube':

        JHtml::_('script', 'media/filmfans/js/ytembed.js');

        ?>

        <script type="text/javascript">
        ytEmbed.init({'block':'youtubesearch','type':'search', 'results': 4, 'q' : '<?php echo addslashes($mainframe->input->getString('term')); ?>'});
        </script>
        <div class="row">
            <div class="content youtube-search">
                <h4>
                    <a href="http://www.youtube.com/"><img src="<?php echo JUri::base(true); ?>/media/filmfans/images/youtube-logo.png" alt="YouTube" /></a>
                </h4>
                <div class="row" id="youtubesearch"></div>
            </div>
        </div>

        <?php
        return;
        break;
}

if ($url) {
?>
<div class="row">
    <iframe src="<?php echo $url; ?>" class="auto-height" style="width: 100%; min-height: 800px; border: none;"></iframe>
</div>
<?php } else { ?>
<div class="ffwarning">
    <a href="javascript:void(0)" class="ffwarning-close"><span class="glyphicon glyphicon-remove"></span></a>
    <div class="row">
        <div class="ufe1 col-md-8 col-lg-8">
            <?php echo $msg; ?>
        </div>
        <div class="ufe2 col-md-4 col-lg-4">
            <?php echo JText::sprintf('PLG_K2_FILMFANS_USER_FEED_EMPTY2', FilmFansHelper::routeMenuItem('miFAQ')); ?>
        </div>
    </div>
</div>
<?php } ?>
