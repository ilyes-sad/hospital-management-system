<?php
// ============================================================
//  app/Views/patients/edit.php
// ============================================================
$baseUrl = '/medapp2/public';
?>

<div class="card" style="max-width:700px">
    <form method="POST" action="<?= $baseUrl ?>/patients/update/<?= $patient['id'] ?>" novalidate>
        <?php require ROOT_PATH . 'app/Views/patients/form_fields.php'; ?>
        <div class="form-actions">
            <a href="<?= $baseUrl ?>/patients" class="btn btn-outline">Annuler</a>
            <button type="submit" class="btn btn-primary">Enregistrer</button>
        </div>
    </form>
</div>
