<div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    <ul>
        <?php foreach($errors as $row) { ?>
            <li><?= $row ?></li>
        <?php } ?>
    </ul>
</div>

