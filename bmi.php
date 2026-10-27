<?php
// --- LOGICA ED ELABORAZIONE PHP ---

$nome = '';
$peso_input = '';
$altezza_input = '';
$bmi = null;
$bmi_formattato = '';
$categoria = '';
$badge_style = '';
$errore = '';

// Se la pagina viene aperta direttamente senza POST, reindirizza a index.html
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.html');
    exit();
}

// Recupero e pulizia input
$nome = trim($_POST['nome'] ?? '');
$peso_input = str_replace(',', '.', trim($_POST['peso'] ?? ''));
$altezza_input = str_replace(',', '.', trim($_POST['altezza'] ?? ''));

// Validazione dei campi
if (empty($nome) || empty($peso_input) || empty($altezza_input)) {
    $errore = 'Compila tutti i campi richiesti.';
} elseif (!is_numeric($peso_input) || !is_numeric($altezza_input) || $peso_input <= 0 || $altezza_input <= 0) {
    $errore = 'Inserisci valori numerici validi per peso e altezza.';
} else {
    $peso = floatval($peso_input);
    $altezza_cm = floatval($altezza_input);

    // Conversione altezza in metri e calcolo BMI
    $altezza_m = $altezza_cm / 100;
    $bmi = $peso / ($altezza_m * $altezza_m);
    $bmi_formattato = number_format($bmi, 1, ',', '.');

    // Determinazione della fascia BMI e dello stile cromatico della card
    if ($bmi < 18.5) {
        $categoria = 'Sottopeso';
        $badge_style = 'background-color: #e7f5ff; color: #1971c2;';
    } elseif ($bmi < 25) {
        $categoria = 'Normopeso';
        $badge_style = 'background-color: #ebfbee; color: #2b8a3e;';
    } elseif ($bmi < 30) {
        $categoria = 'Sovrappeso';
        $badge_style = 'background-color: #fff9db; color: #f59f00;';
    } else {
        $categoria = 'Obesità';
        $badge_style = 'background-color: #ffe3e3; color: #e03131;';
    }
}
?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calcolatore BMI - Risultato</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="layout-container">
        <div class="card">
            <h1>Calcolatore BMI</h1>

            <?php if (!empty($errore)): ?>
                <!-- Card di Errore -->
                <div class="error-card">
                    <?= htmlspecialchars($errore) ?>
                </div>
            <?php else: ?>
                <!-- Card del Risultato -->
                <div class="result-card" style="<?= $badge_style ?>">
                    <div class="result-title">Risultato per <?= htmlspecialchars($nome) ?></div>
                    <div class="result-score"><?= $bmi_formattato ?></div>
                    <div class="result-category"><?= htmlspecialchars($categoria) ?></div>
                </div>
            <?php endif; ?>

            <!-- Bottone per nuovo calcolo -->
            <a href="index.html" class="btn-back">Calcola di nuovo</a>
        </div>
    </div>
</body>
</html>