<div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    <?= $_SESSION['flash_message'] ?>
    <?php unset($_SESSION['flash_message']); ?>
</div>