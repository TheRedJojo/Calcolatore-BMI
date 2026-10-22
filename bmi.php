<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calcolatore BMI</title>
</head>
<body>
    <?php
        // --- RAGIONAMENTO E LOGICA IN PHP ---

        $nome = '';
        $peso_input = '';
        $altezza_input = '';
        $bmi = null;
        $categoria = '';
        $badge_style = '';
        $errore = '';

        // Verifica se il form è stato inviato tramite POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

                // Determinazione della fascia BMI
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
        }
    ?>
</body>
</html>