<?php
// ============================================================
//  app/Views/rendezvous/edit.php
// ============================================================
$baseUrl = '/medapp2/public';
?>

<div class="card" style="max-width:700px">
    <form method="POST" action="<?= $baseUrl ?>/rendezvous/update/<?= $rdv['id'] ?>" novalidate>
        <?php require ROOT_PATH . 'app/Views/rendezvous/form_fields.php'; ?>
        <div class="form-actions">
            <a href="<?= $baseUrl ?>/rendezvous" class="btn btn-outline">Annuler</a>
            <button type="submit" class="btn btn-primary">Enregistrer</button>
        </div>
    </form>
</div>
