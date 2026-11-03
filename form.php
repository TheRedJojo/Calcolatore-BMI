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
$elaborato = false;

// Si attiva solo quando l'utente invia il form (richiesta POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $elaborato = true;
    $data = date("d/m/Y");
    $orario = date("H:i:s");
    $ip = $_SERVER['REMOTE_ADDR'];

    // Recupero e pulizia input
    $nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
    $peso_input = isset($_POST['peso']) ? str_replace(',', '.', trim($_POST['peso'])) : '';
    $altezza_input = isset($_POST['altezza']) ? str_replace(',', '.', trim($_POST['altezza'])) : '';

    // Validazione dei campi
    if (empty($nome) || empty($peso_input) || empty($altezza_input)) {
        $errore = 'Compila tutti i campi richiesti.';
        $nomeNV = "NOT_VALID";
        $pesoNV = "NOT_VALID";
        $altezzaNV = "NOT_VALID";
        $bmiNV = "NOT_VALID";
        $categoriaNV = "NOT_VALID";
        $riga_dati = "$data, $orario, $ip, $nomeNV, $pesoNV, $altezzaNV, $bmiNV, $categoriaNV\n";
        $logF = fopen("log.csv", "a");
        if ($logF) {
            fwrite($logF, $riga_dati);
            fclose($logF);
        }
    } elseif (!is_numeric($peso_input) || !is_numeric($altezza_input) || $peso_input <= 0 || $altezza_input <= 0) {
        $errore = 'Inserisci valori numerici validi per peso e altezza.';
    } else {
        $peso = floatval($peso_input);
        $altezza_cm = floatval($altezza_input);

        // Conversione altezza in metri e calcolo BMI
        $altezza_m = $altezza_cm / 100;
        $bmi = $peso / ($altezza_m * $altezza_m);
        $bmi_formattato = number_format($bmi, 1);

        // Determinazione della fascia BMI e dello stile cromatico
        if ($bmi < 18.5) {
            $categoria = 'Sottopeso';
            $badge_style = 'background-color: #e7f5ff; color: #1971c2; padding: 15px; border-radius: 8px; text-align: center;';
        } elseif ($bmi < 25) {
            $categoria = 'Normopeso';
            $badge_style = 'background-color: #ebfbee; color: #2b8a3e; padding: 15px; border-radius: 8px; text-align: center;';
        } elseif ($bmi < 30) {
            $categoria = 'Sovrappeso';
            $badge_style = 'background-color: #fff9db; color: #f59f00; padding: 15px; border-radius: 8px; text-align: center;';
        } else {
            $categoria = 'Obesità';
            $badge_style = 'background-color: #ffe3e3; color: #e03131; padding: 15px; border-radius: 8px; text-align: center;';
        }

        $riga_dati = "$data, $orario, $ip, $nome, $peso_input, $altezza_cm, $bmi_formattato, $categoria\n";
        $logF = fopen("log.csv", "a");
        if ($logF) {
            fwrite($logF, $riga_dati);
            fclose($logF);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calcolatore BMI</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="layout-container">
        <div class="card">
            <h1>Calcolatore BMI</h1>

            <?php if (!empty($errore)): ?>
                <div class="error-message" style="color: #e03131; margin-bottom: 15px;">
                    <?php echo htmlspecialchars($errore); ?>
                </div>
            <?php endif; ?>

            <?php if ($bmi === null): ?>
                <!-- MOSTRA IL FORM SOLO SE NON È STATO ANCORA CALCOLATO IL BMI -->
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="nome">Nome</label>
                        <input type="text" id="nome" name="nome" placeholder="Il tuo nome" value="<?php echo htmlspecialchars($nome); ?>">
                    </div>

                    <div class="form-group">
                        <label for="peso">Peso (kg)</label>
                        <input type="number" id="peso" name="peso" step="0.1" min="1" placeholder="es. 70" value="<?php echo htmlspecialchars($peso_input); ?>">
                    </div>

                    <div class="form-group">
                        <label for="altezza">Altezza (cm)</label>
                        <input type="number" id="altezza" name="altezza" step="1" min="1" placeholder="es. 175" value="<?php echo htmlspecialchars($altezza_input); ?>">
                    </div>

                    <button type="submit">Calcola</button>
                </form>
            <?php else: ?>
                <!-- SE IL RISULTATO È PRONTO, MOSTRA LA SCHEDA RISULTATO E NASCONDE IL FORM -->
                <div class="response">
                    <div class="result-card" style="<?php echo $badge_style; ?>">
                        <div class="result-title">Risultato per <?php echo htmlspecialchars($nome); ?></div>
                        <div class="result-score" style="font-size: 2.5rem; font-weight: bold; margin: 10px 0;"><?php echo $bmi_formattato; ?></div>
                        <div class="result-category" style="font-size: 1.2rem;"><?php echo htmlspecialchars($categoria); ?></div>
                    </div>
                    <div style="margin-top: 20px; text-align: center;">
                        <a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="btn-retry" style="display: inline-block; padding: 10px 20px; background-color: #228be6; color: white; text-decoration: none; border-radius: 5px;">Calcola di nuovo</a>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</body>
</html>