<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
$this->inc('elements/header.php');
?>

<?php
$a = new Area('Main');
$a->enableGridContainer();
$a->display($c);

$a = new Area('Page Footer');
$a->enableGridContainer();
$a->display($c);
?>

<?php $this->inc('elements/footer.php') ?>
