<?php
// no direct access
defined('_JEXEC') or die;

$mainframe = JFactory::getApplication();
$document = JFactory::getDocument();

$mainframe->input->set('tmpl', 'short');

$user = JFactory::getUser();

if (empty($this->user->id)) $this->user = &$user;

if (empty($this->user->id)) {
	include 'user_error.php';
	return;
}

$model = K2Model::getInstance('Itemlist', 'K2Model');
$this->K2User = $model->getUserProfile($this->user->id);

if (empty($this->user->id)) {
	include 'user_error.php';
	return;
}

$this->K2User->plugins = json_decode($this->K2User->plugins, true);

//FilmFansHelper::_sd($this);

if (!empty($this->K2User->plugins['ffcover'])) {
	$cover = FilmFansHelper::getCover($this->K2User->plugins['ffcover'], 0);
    if ($cover) JFactory::getDocument()->addStyleDeclaration('body { background-image: url("'.$cover.'"); }');
}

if (empty($this->K2User->plugins['ffuser']['lastname']) && empty($this->K2User->plugins['ffuser']['firstname'])) {
	$_tmp = array_filter(explode(' ', $this->user->get('name')));
	$this->K2User->plugins['ffuser']['lastname'] = array_pop($_tmp);
	$this->K2User->plugins['ffuser']['firstname'] = count($_tmp) ? implode(' ', $_tmp) : $this->K2User->plugins['ffuser']['lastname'];
}

$document->setTitle($this->user->get('name'));

?>

<?php if (!$mainframe->input->get('ffajax')) { ?>

<div class="row">
	<div class="container-fluid ffprofile">

		<?php if ($user->get('id') && $user->get('id') == $this->user->get('id')) {
			include dirname(__DIR__) . '/profile.php';
		} else {
			$avatar = FilmFansHelper::getAvatar($this->K2User->image, $this->user);
		?>

		<div class="container container-white-not">

			<div class="profile-avatar">
				<div class="user-title">
					<a class="avatar">
						<b><img src="<?php echo $avatar[0]; ?>" alt="" /></b>
						<i></i>
					</a>
					<div class="componentheading">
						<?php echo $this->user->name; ?>
						<small><?php echo FilmFansHelper::getDD('country', @$this->K2User->plugins['ffuser']['country']); ?></small>
					</div>
					<div class="clearfix"></div>
				</div>
			</div>
		</div>

		<?php } ?>

	</div>
</div>

<?php } ?>

<div class="row container-black">

	<div class="container ffcontainer">

		<h1 class="filter_heading"><?php echo JText::_('PLG_K2_FILMFANS_USER_FEED'); ?></h1>
		<div class="clearfix"></div>

		<form action="<?php echo JUri::current(); ?>" method="get" class="fffilter">
            <input type="hidden" value="0" name="fffeatured">
            <input type="hidden" value="0" name="ffpopular">
            <input type="hidden" value="feed" name="fftmpl">
            <input type="hidden" value="" name="term">
            <input type="hidden" value="filmfans" name="service">
	    </form>

		<?php
			if (!empty($this->items)) {
				$this->fftmpl = 'feed';
				$this->ffnofiterform = 1;
				$this->leading = &$this->items;
				include 'category.php';
			} else { ?>

			<?php if ($this->user->get('id') == $user->get('id')) { ?>
			<div class="ffwarning ffinfo">
				<a href="javascript:void(0)" class="ffwarning-close"><span class="glyphicon glyphicon-remove"></span></a>
				<div class="row">
					<div class="ufe1 col-md-8 col-lg-8">
						<?php echo JText::sprintf('PLG_K2_FILMFANS_USER_FEED_EMPTY1', $this->K2User->plugins['ffuser']['firstname']); ?>
					</div>
					<div class="ufe2 col-md-4 col-lg-4">
						<?php echo JText::sprintf('PLG_K2_FILMFANS_USER_FEED_EMPTY2', FilmFansHelper::routeMenuItem('miFAQ')); ?>
					</div>
				</div>
			</div>
			<?php } else { ?>
			<div class="ffwarning">
				<a href="javascript:void(0)" class="ffwarning-close"><span class="glyphicon glyphicon-remove"></span></a>
				<div class="row">
					<div class="ufe1 col-md-8 col-lg-8">
						<?php echo JText::_('PLG_K2_FILMFANS_CATEGORY_EMPTY'); ?>
					</div>
					<div class="ufe2 col-md-4 col-lg-4">
						<?php echo JText::sprintf('PLG_K2_FILMFANS_USER_FEED_EMPTY2', FilmFansHelper::routeMenuItem('miFAQ')); ?>
					</div>
				</div>
			</div>
			<?php } ?>
		<?php } ?>

	</div>
</div>