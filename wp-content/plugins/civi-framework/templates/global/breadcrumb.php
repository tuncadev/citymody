<?php
// DON'T render breadcrumb if the current page is the front latest posts.
if (is_home() && is_front_page()) {
    return;
}
?>
<div id="page-breadcrumb" class="page-breadcrumb">
    <?php echo Civi_Breadcrumb::breadcrumb(); ?>
</div>
<?php
