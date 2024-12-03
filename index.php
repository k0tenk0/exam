<?php

function atbashCipher($text, $encrypt = true) {
    $result = '';
    for ($i = 0; $i < mb_strlen($text, 'UTF-8'); $i++) {
        $char = mb_substr($text, $i, 1, 'UTF-8');
        $char_code = ord($char);// Получение ASCII кода символа.

        if ($char_code >= ord('A') && $char_code <= ord('Z')) {
            $result .= chr(ord('A') + ord('Z') - $char_code);
        } elseif ($char_code >= ord('a') && $char_code <= ord('z')) {
            $result .= chr(ord('a') + ord('z') - $char_code);
        } else {
            $result .= $char;
        }
    }
    return $result;
}

function atbashDecrypt($text) {
    return atbashCipher($text, false);
}

$result = '';
$error = null;
$logFile = 'log.txt';

if (!is_dir(dirname($logFile))) {
    mkdir(dirname($logFile), 0777, true);
}

if (!is_writable(dirname($logFile))) {
    $error = "Ошибка: Директория для лог-файла не доступна для записи.";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $operation = $_POST['operation'] ?? null;
    $message = $_POST['encryptText'] ?? null;

    if (isset($message) && isset($operation)) {
        try {
            $result = atbashCipher($message, $operation === "encrypt");
            $logEntry = [
                'timestamp' => date('Y-m-d H:i:s'),
                'message' => $message,
                'operation' => $operation,
                'result' => $result,
                'ip' => $_SERVER['REMOTE_ADDR']
            ];

            $logData = file_exists($logFile) ? json_decode(file_get_contents($logFile), true) : [];

            $logData[] = $logEntry;

            $logDataJson = json_encode($logData, JSON_PRETTY_PRINT);
            if (file_put_contents($logFile, $logDataJson . PHP_EOL) === false) { 
                $error = "Ошибка: Не удалось записать в лог-файл.";
            }
        } catch (Exception $e) {
            $error = "Ошибка: " . $e->getMessage();
        }
    } else {
        $error = "Ошибка: Необходимо ввести текст и выбрать операцию.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Atbash's cipher</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="main-container">
        <h1>Atbash's cipher</h1>
    <div class="container">
        <div class="image">
                <img src="content/atbash_logo.png" alt="logo_atbash">
        </div>
        <div class="text">
        The Atbash cipher is a simple substitution cipher for alphabetic writing, in which each nth letter 
        of the alphabet is replaced by the letter m - n +1, where m is the total number of letters in the 
        alphabet. In other words, the first letter is replaced by the last, the second by the penultimate, 
        and so on. The service allows you to perform encryption and decryption for the Latin alphabet.
        </div>
        <div class="image">
            <img src="content/atbash_logo.png" alt="logo_atbash">
        </div>
    </div>
    </div>
    
    <form method="post">
        <label for="encryptText">Text to encode:</label><br>
        <textarea id="message" name="encryptText" rows="10" cols="30" placeholder="Enter the text"></textarea><br><br>
        <label for="operation">Type of operation:</label><br>
        <select id="operation" name="operation">
            <option value="encrypt">Encrypt</option>
            <option value="decrypt">Decrypt</option>
        </select>
        <br><br>
        <input type="submit" value="Perfom">
    </form>


<?php
if (isset($result)) {
    echo "<p>Result: " . htmlspecialchars($result) . "</p>";
}

if (isset($error)){
    echo "<p style='color:red;'>" . htmlspecialchars($error) . "</p>";
}
?>

</body>
</html>