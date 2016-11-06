<?php
// no direct access
defined('_JEXEC') or die;

?>

<ul class="itemRatingList">
    <li class="itemCurrentRating" id="itemCurrentRating<?php echo $this->item->id; ?>" style="width:<?php echo $this->item->_rate * 20; ?>%;"></li>
    <li><a href="#" data-id="<?php echo $this->item->id; ?>" title="<?php echo JText::sprintf('PLG_K2_FILMFANS_RATE_STARS', 1, 10); ?>" class="one-star-minus">0.5</a></li>
    <li><a href="#" data-id="<?php echo $this->item->id; ?>" title="<?php echo JText::sprintf('PLG_K2_FILMFANS_RATE_STARS', 2, 10); ?>" class="one-star">1</a></li>
    <li><a href="#" data-id="<?php echo $this->item->id; ?>" title="<?php echo JText::sprintf('PLG_K2_FILMFANS_RATE_STARS', 3, 10); ?>" class="one-star-plus">1.5</a></li>
    <li><a href="#" data-id="<?php echo $this->item->id; ?>" title="<?php echo JText::sprintf('PLG_K2_FILMFANS_RATE_STARS', 4, 10); ?>" class="two-stars">2</a></li>
    <li><a href="#" data-id="<?php echo $this->item->id; ?>" title="<?php echo JText::sprintf('PLG_K2_FILMFANS_RATE_STARS', 5, 10); ?>" class="two-stars-plus">2.5</a></li>
    <li><a href="#" data-id="<?php echo $this->item->id; ?>" title="<?php echo JText::sprintf('PLG_K2_FILMFANS_RATE_STARS', 6, 10); ?>" class="three-stars">3</a></li>
    <li><a href="#" data-id="<?php echo $this->item->id; ?>" title="<?php echo JText::sprintf('PLG_K2_FILMFANS_RATE_STARS', 7, 10); ?>" class="three-stars-plus">3.5</a></li>
    <li><a href="#" data-id="<?php echo $this->item->id; ?>" title="<?php echo JText::sprintf('PLG_K2_FILMFANS_RATE_STARS', 8, 10); ?>" class="four-stars">4</a></li>
    <li><a href="#" data-id="<?php echo $this->item->id; ?>" title="<?php echo JText::sprintf('PLG_K2_FILMFANS_RATE_STARS', 9, 10); ?>" class="four-stars-plus">4.5</a></li>
    <li><a href="#" data-id="<?php echo $this->item->id; ?>" title="<?php echo JText::sprintf('PLG_K2_FILMFANS_RATE_STARS', 10, 10); ?>" class="five-stars">5</a></li>
</ul>
<div class="clearfix"></div>