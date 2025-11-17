<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['judul']; ?></title>
    <link rel="stylesheet" href="<?= BASEURL; ?>/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container navbar-flex">
            <div class="navbar-brand">
                <img src="<?= BASEURL; ?>/images/logo.gif" alt="Logo" class="logo">
                <span class="brand-title">UMKM Cerdas</span>
            </div>
            <div class="navbar-links">
                <a href="<?= BASEURL; ?>">Home</a>
                <a href="<?= BASEURL; ?>/kasir">Kasir</a>
                <a href="<?= BASEURL; ?>/analisis">Ai</a>
            </div>
        </div>
    </nav>