<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\modules\orders\models\Orders;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);
$moduleName = 'orders';

?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<nav class="navbar navbar-fixed-top navbar-default">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>
        <div class="collapse navbar-collapse" id="bs-navbar-collapse">
            <ul class="nav navbar-nav">
                <li class="active"><a href="#">Orders</a></li>
            </ul>
        </div>
    </div>
</nav>
<ul id="status_navbar" class="nav nav-tabs p-b">
    <li class=<?= Yii::$app->request->url == sprintf("/%s/", $moduleName) ? 'active' : '' ?>>
        <a href=<?php echo sprintf("/%s/", $moduleName) ?>>
            <?php echo Yii::t($moduleName, 'All orders')?>
        </a>
    </li>
    <?php foreach (Orders::STATUS_LIST as $status) { ?>
        <li class=<?= strpos(Yii::$app->request->url, $status) !== false ? 'active' : '' ?>>
            <a href=<?php echo sprintf("/%s/%s", $moduleName, $status)?>>
                <?php echo Yii::t($moduleName, $status)?>
            </a>
        </li>
    <?php } ?>
    <li>
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <?= Yii::t($moduleName, 'Language') ?> <b class="caret"></b>
        </a>
        <ul class="dropdown-menu">
            <li><a href="<?= Yii::$app->urlManager->createUrl(['/orders/change-language', 'lang' => 'en']) ?>">English</a></li>
            <li><a href="<?= Yii::$app->urlManager->createUrl(['/orders/change-language', 'lang' => 'ru']) ?>">Русский</a></li>
        </ul>
    </li>

    <li class="pull-right custom-search">
        <form class="form-inline" action=<?php echo $_SERVER['REQUEST_URI']?>>
            <div class="input-group">
                <input type="text" name="search" class="form-control" value="" placeholder="Search orders" required>
                <span class="input-group-btn search-select-wrap">

            <select class="form-control search-select" name="search-type">
              <option value=<?php echo Orders::SCENARIO_SEARCH_ID?> selected="">
                  <?php echo Yii::t($moduleName, 'Order ID')?>
              </option>
              <option value=<?php echo Orders::SCENARIO_SEARCH_LINK?>>
                  <?php echo Yii::t($moduleName, 'Link')?>
              </option>
              <option value=<?php echo Orders::SCENARIO_SEARCH_USER?>>
                    <?php echo Yii::t($moduleName, 'Username')?>
              </option>
            </select>

            <button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
            </span>
            </div>
        </form>
    </li>
</ul>

<?php if (!empty($this->params['breadcrumbs'])): ?>
    <?= Breadcrumbs::widget(['links' => $this->params['breadcrumbs']]) ?>
<?php endif ?>
<?= Alert::widget() ?>
<?= $content ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
