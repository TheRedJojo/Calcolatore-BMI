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
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="nome">Nome</label>
                    <input type="text" id="nome" name="nome" placeholder="Il tuo nome" required>
                </div>

                <div class="form-group">
                    <label for="peso">Peso (kg)</label>
                    <input type="number" id="peso" name="peso" step="0.1" min="1" placeholder="es. 70" required>
                </div>

                <div class="form-group">
                    <label for="altezza">Altezza (cm)</label>
                    <input type="number" id="altezza" name="altezza" step="1" min="1" placeholder="es. 175" required>
                </div>

                <button type="submit">Calcola</button>
            </form>

            <div class="response">
                <?php
                    $nome = '';
                    $peso_input = '';
                    $altezza_input = '';
                    $bmi = null;
                    $bmi_formattato = '';
                    $categoria = '';
                    $badge_style = '';
                    $errore = '';
                    $elaborato = false;

                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        $elaborato = true;
                        $data = date("d/m/Y");
                        $orario = date("H:i:s");
                        $ip = $_SERVER['REMOTE_ADDR'];

                        $nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
                        $peso_input = isset($_POST['peso']) ? str_replace(',', '.', trim($_POST['peso'])) : '';
                        $altezza_input = isset($_POST['altezza']) ? str_replace(',', '.', trim($_POST['altezza'])) : '';

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
                            $altezza_m = $altezza_cm / 100;
                            
                            $bmi = $peso / ($altezza_m * $altezza_m);
                            $bmi_formattato = number_format($bmi, 1);

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

                            echo '<div class="result-card" style="' . $badge_style . '">';
                            echo '    <div class="result-title">Risultato per ' . htmlspecialchars($nome) . '</div>';
                            echo '    <div class="result-score">' . $bmi_formattato . '</div>';
                            echo '    <div class="result-category">' . htmlspecialchars($categoria) . '</div>';
                            echo '</div>';

                            $riga_dati = "$data, $orario, $ip, $nome, $peso_input, $altezza_cm, $bmi_formattato, $categoria\n";
                            $logF = fopen("log.csv", "a");
                            if ($logF) {
                                fwrite($logF, $riga_dati);
                                fclose($logF);
                            }
                        }
                    }
                ?>
            </div>
        </div>
    </div>
</body>
</html>