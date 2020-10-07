<?php defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\User\User;
use Concrete5\AssetLibrary\Results\Formatter\Asset;

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
$url = Concrete\Core\Support\Facade\Url::to('/search');

$defaultQuery = array_filter([
    'filter' => $filter ?? null,
    'keywords' => $qkeywords ?? null,
    'ipp' => (int) $items_per_page ?? null,
    'sortBy' => $sortBy ?? null
]);
$searchUrl = function($data = []) use ($url, $defaultQuery) {
    $query = $url->getQuery();
    $query->set($data + $defaultQuery);
    return $url->setQuery($query);
};
?>

<div class="collection-container container search-results">
    <div class="row">
        <div class="col-sm-8">
            <h1><?= $input_search?> <small>Results <strong><?=t2('%s item', '%s items', $count_results)?>.</strong></small></h1>
        </div>

        <div class="modal fade" id="search-filter-popup" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-search modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form action="<?php echo $searchUrl(); ?>" method="get">
                        <?php echo $form->hidden("keywords"); ?>

                        <div class="modal-header mb-2 mt-3">
                            <h4 class="modal-title">
                                <?php echo t("Sort and Filter"); ?>
                            </h4>
                        </div>

                        <div class="modal-body ml-5 pl-5 mr-5 pr-5">
                            <div class="row">
                                <div class="col-sm-7">
                                    <div class="form-group">
                                        <h5>
                                            <?php echo t("Sort By"); ?>
                                        </h5>

                                        <div class="form-check">
                                            <?php echo $form->radio("sortBy", "recent", $sort_radio_value); ?>
                                            <label for="sortBy1" class="form-check-label">
                                                <?php echo t("Recent First"); ?>
                                            </label>
                                        </div>

                                        <div class="form-check">
                                            <?php echo $form->radio("sortBy", "oldest", $sort_radio_value); ?>
                                            <label for="sortBy2" class="form-check-label">
                                                <?php echo t("Oldest First"); ?>
                                            </label>
                                        </div>

                                        <div class="form-check">
                                            <?php echo $form->radio("sortBy", "name", $sort_radio_value); ?>
                                            <label for="sortBy3" class="form-check-label">
                                                <?php echo t("Name"); ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-5">
                                    <div class="form-group">
                                        <h5>
                                            <?php echo t("Filter By"); ?>
                                        </h5>

                                        <div class="form-check">
                                            <?php echo $form->radio("filter", "all", $filter_radio_value); ?>
                                            <label for="filter4" class="form-check-label">
                                                <?php echo t("Show All"); ?>
                                            </label>
                                        </div>

                                        <div class="form-check">
                                            <?php echo $form->radio("filter", "photo", $filter_radio_value); ?>
                                            <label for="filter5" class="form-check-label">
                                                <?php echo t("Photos"); ?>
                                            </label>
                                        </div>

                                        <div class="form-check">
                                            <?php echo $form->radio("filter", "logo", $filter_radio_value); ?>
                                            <label for="filter6" class="form-check-label">
                                                <?php echo t("Logos"); ?>
                                            </label>
                                        </div>

                                        <div class="form-check">
                                            <?php echo $form->radio("filter", "template", $filter_radio_value); ?>
                                            <label for="filter7" class="form-check-label">
                                                <?php echo t("Templates"); ?>
                                            </label>
                                        </div>

                                        <div class="form-check">
                                            <?php echo $form->radio("filter", "video", $filter_radio_value); ?>
                                            <label for="filter8" class="form-check-label">
                                                <?php echo t("Videos"); ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-round" data-dismiss="modal">
                                <?php echo t("Cancel"); ?>
                            </button>

                            <button type="submit" class="btn btn-submit">
                                <?php echo t("Update"); ?>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-sm-4 text-right">
            <div class="btn-group search-filter-dropdown">
                <button class="btn btn-default" id="toggle-search-filter-popup"  data-toggle="modal" data-target="#search-filter-popup">
                    <?php echo t("Sort Results"); ?> <img src="<?=$view->getThemePath()?>/images/dropdown_filter.png"></button>

                <ul class="switch-view search">
                    <li>
                        <a href="javascript:void(0);" data-tooltip="regular-grid" data-grid-view="regular" title="<?php echo h(t("Regular Grid")); ?>">
                            <i class="fa fa-th-large"></i>
                        </a>
                    </li>

                    <li>
                        <a href="javascript:void(0);" data-tooltip="masonry-grid" data-grid-view="masonry" title="<?php echo h(t("Masonry Grid")); ?>">
                            <i class="fa fa-th"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <?php if(count($keywords)>0) { ?>
        <div class="row">
            <div class="col-12">
                <div class="search-keywords">
                    <?php foreach ($keywords as $keyword) {
                        $tagUrl = $view->controller->getRemoveTagUrl($keyword);
                        ?>
                        <span><?= h($keyword) ?> <a href="<?=$tagUrl?>"><i class="fa fa-close"></i></a></span>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php } ?>

    <?php if ($search_assets) { ?>
        <div class="grid-view grid-view-regular d-none row assets">
            <?php foreach($search_assets as $asset) {
                $asset = new \Concrete5\AssetLibrary\Results\Formatter\Asset($asset);
                ?>
                <div class="col-12 col-sm-6 col-lg-4 thumbnail-container">
                    <a href="<?= \URL::to('/assets', $asset->getId()) ?>" class="thumbnail">
                        <img src="<?=$asset->getThumbnailImageURL()?>" class="<?= $asset->getAssetType() ?>"/>
                    </a>
                    <div class="thumbnail-caption">
                        <h3>
                            <span class="pull-right">
                                <?php
                                $u = new User();
                                if ($u->isRegistered()) {
                                    ?>
                                    <a href="#" class="add-to-lightbox" data-tooltip="lightbox" title="<?=t('Add to Lightbox')?>" data-asset="<?= $asset->getId() ?>"><i class="fa fa-plus"></i></a>
                                <?php } ?>
                                <a href="<?=$asset->getDownloadURL()?>" data-tooltip="download" title="<?=t('Download')?>"><i class="fa fa-download"></i></a>
                            </span>
                            <a href="<?= \URL::to('/assets', $asset->getId()) ?>" class="asset-link"><?= trim(h($asset->getAssetName())) ?: '&nbsp;' ?></a>
                        </h3>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="grid-view grid-view-masonry d-none row assets">
                <div class="grid">
                    <?php foreach($search_assets as $asset) { ?>
                        <?php $asset = new Asset($asset); ?>

                        <div class="grid-item">
                            <a href="<?php echo Url::to('/assets', $asset->getId()) ?>" class="thumbnail">
                                <img src="<?php echo h($asset->getThumbnailImageURL()) ?>" class="<?php echo h($asset->getAssetType()) ?>"/>
                            </a>

                            <div class="overlay">
                                <a href="<?php echo Url::to('/assets', $asset->getId()) ?>" class="description">
                                        <span class="title">
                                            <?php echo $asset->getAssetName()?>
                                        </span>

                                    <?php echo $asset->getAssetDescription(); ?>
                                </a>

                                <?php $u = new User(); ?>

                                <?php if ($u->isRegistered()) { ?>
                                    <a href="#" class="add-to-lightbox" data-tooltip="lightbox" title="<?php echo h(t('Add to Lightbox')) ?>" data-asset="<?php echo h($asset->getId()) ?>">
                                        <i class="fa fa-plus"></i>
                                    </a>
                                <?php } ?>

                                <a href="<?php echo h($asset->getDownloadURL()) ?>" data-tooltip="download" title="<?php echo h(t('Download')) ?>" class="download">
                                    <i class="fa fa-download"></i>
                                </a>
                            </div>
                        </div>
                    <?php } ?>
            </div>
        </div>
    <?php } ?>

<?php
        if (isset($pagination) && $pagination->getTotalPages() > 1) { ?>

            <div class="row">
                <div class="col-sm-4">
                    <div class="dropdown search-page-results" style="">
                        <button data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="current-option"><?= h($items_per_page) ?> </span> <span class="caret"></span></button>
                        <ul class="dropdown-menu">
                            <li><a href="<?= $searchUrl(['ipp' => 12]) ?>">12</a></li>
                            <li><a href="<?= $searchUrl(['ipp' => 24]) ?>">24</a></li>
                            <li><a href="<?= $searchUrl(['ipp' => 48]) ?>">48</a></li>
                            <li><a href="<?= $searchUrl(['ipp' => 96]) ?>">96</a></li>
                        </ul>
                    </div>
                    Per Page
                </div>
                <div class="col-sm-8">
                    <?php print $pagination->renderDefaultView(); ?>
                </div>
            </div>

        <?php } ?>

        <?php if ($showCollectionResults) { ?>
            <h3><?=t('Also check out...')?></h3>
            <?php
            View::element('all_collection_grid', ['collections' => $collectionResults], 'brand_central');
            ?>
        <?php } ?>
        <div style="min-height: 200px;"></div>
</div>
