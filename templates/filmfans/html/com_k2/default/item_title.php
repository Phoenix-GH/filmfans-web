<?php
// no direct access
defined('_JEXEC') or die;
?>

<h1><?php echo $this->item->title_html; ?></h1>

<div class="item-rating">
    <div class="ffitemRatingForm">
        <?php include('item_rating.php'); ?>
        <div class="clearfix"></div>
        <div id="itemRatingLog<?php echo $this->item->id; ?>" class="itemRatingLog"><?php echo number_format($this->item->_rate, 2); ?><span>&nbsp;&nbsp;<?php echo $this->item->numOfvotes; ?></span></div>
    </div>
    <div class="clearfix"></div>
</div>