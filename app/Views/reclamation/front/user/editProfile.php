<style>
    .profile-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 90vh;
    background: linear-gradient(135deg, #eef6ff, #dbeafe);
}

.profile-card {
    background: #fff;
    padding: 40px;
    width: 420px;
    border-radius: 18px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.profile-title {
    margin: 0;
    font-size: 24px;
    font-weight: bold;
    color: #1e293b;
}

.profile-subtitle {
    margin-bottom: 25px;
    color: #64748b;
    font-size: 14px;
}

.form-group {
    margin-bottom: 15px;
}

.form-row {
    display: flex;
    gap: 10px;
}

.form-row .form-group {
    flex: 1;
}

.form-group label {
    display: block;
    font-size: 13px;
    margin-bottom: 5px;
    color: #475569;
}

.form-group input {
    width: 100%;
    padding: 10px;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    background: #f8fafc;
    transition: 0.2s;
}

.form-group input:focus {
    outline: none;
    border-color: #3b82f6;
    background: #fff;
}

.profile-actions {
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
}

.btn-save {
    background: #3b82f6;
    color: white;
    padding: 10px 18px;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    transition: 0.2s;
}

.btn-save:hover {
    background: #2563eb;
}

.btn-cancel {
    text-decoration: none;
    padding: 10px 18px;
    border-radius: 10px;
    background: #e5e7eb;
    color: #1e293b;
    transition: 0.2s;
}

.btn-cancel:hover {
    background: #d1d5db;
}

.alert {
    background: #fee2e2;
    color: #991b1b;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 15px;
}
</style>
<div class="profile-container">
    <div class="profile-card">

        <h2 class="profile-title">Modifier mon profil</h2>
        <p class="profile-subtitle">Mettez à jour vos informations personnelles</p>

        <?php if (!empty($errors['general'])): ?>
            <div class="alert">
                <?= htmlspecialchars($errors['general']) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="index.php?action=updateProfile">

            <div class="form-row">
                <div class="form-group">
                    <label>Nom</label>
                    <input type="text" name="nom" value="<?= htmlspecialchars($old['nom'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label>Prénom</label>
                    <input type="text" name="prenom" value="<?= htmlspecialchars($old['prenom'] ?? '') ?>">
                </div>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Téléphone</label>
                <input type="text" name="telephone" value="<?= htmlspecialchars($old['telephone'] ?? '') ?>">
            </div>

            <div class="profile-actions">
                <button type="submit" class="btn-save">Enregistrer</button>
                <a href="index.php?action=profile" class="btn-cancel">Annuler</a>
            </div>

        </form>
    </div>
</div>