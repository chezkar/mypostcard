<?php $view->extend('::base.html.php') ?>

<?php $view['slots']->start('title') ?>
    Thumbnail MyPost
<?php $view['slots']->stop() ?>

<?php $view['slots']->start('stylesheets') ?>
    <link rel="stylesheet" type="text/css" href="<?= $view['assets']->getUrl('/bundles/product/css/album.css') ?>"/>
    <link rel="stylesheet" type="text/css" href="<?= $view['assets']->getUrl('/bundles/product/css/custom.css') ?>"/>
<?php $view['slots']->stop() ?>

<?php $view['slots']->start('body') ?>
<div class="make_table table-responsive">
    <table class="table">
        <?php for ($i=0; $i<=$num_rows; $i++): ?>
            <?php if(isset($thmls[$k])): ?>
                <tr class="tr_<?php echo $i; ?>">
                <?php for ($j=1; $j<=1; $j++): ?>
                        <td align="center"> 
                            <div class="wrapper">
                                <div class="box"> 
                                    <a href="<?= $thmls[$k]['img_full'] ?>" data-toggle="lightbox" data-title="<?= $thmls[$k]['title'] ?>" data-footer=' <a target="_blank" href="<?= $view['router']->path('pdf_created', ['id' => $thmls[$k]['id']]) ?>">Crear PDF <i class="far fa-file-pdf"></i></a>'> 
                                        <img class="img-fluid" src="<?= $thmls[$k]['img_thumb'] ?>?image=450" alt="<?= $thmls[$k]['title'] ?>" width="200">
                                    </a>
                                    <div class="data">
                                        <p><strong><?= $thmls[$k]['title'] ?> &euro; <?= $thmls[$k]['price'] ?></strong></p>
                                        <div class="container">
                                            <div class="row">
                                                <?php foreach($thmls[$k]['option'] as $key => $size): ?>
                                                    <div class="col-3">
                                                        <p><?= $key ?> &euro; <?= $size ?></p>
                                                    </div>
                                                <?php endforeach ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <?php $k=$k+1; ?>
                <?php endfor ?>
                </tr>
            <?php endif ?>
        <?php endfor ?>
    </table>
</div>
<div class="col-sm-12">
    <nav aria-label="Page navigation example">
        <ul class="pagination justify-content-center">
            <li class="page-item"><a class="page-link" href="#">1</a></li>
            <li class="page-item"><a class="page-link" href="#">2</a></li>
            <li class="page-item"><a class="page-link" href="#">3</a></li>
        </ul>
    </nav>
</div>
<?php $view['slots']->stop() ?>


<?php $view['slots']->start('javascripts') ?>
<script>
    $(document).on('click', '[data-toggle="lightbox"]', function(event) {
        event.preventDefault();
        $(this).ekkoLightbox();
    });
    $(document).ready(function(){
        $('nav.navigation li.page-item a.page-link').click(function(e){
            var pl = $(this);
            e.prevenDefault();
            $.ajax({
                path: "<?= $view['router']->path('more') ?>",
                method: "POST",
                data: { pl: pl.text() }
            })
        })
    })
</script>
<?php $view['slots']->stop() ?>

