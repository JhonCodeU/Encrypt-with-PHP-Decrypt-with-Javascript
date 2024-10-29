<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    handlePost();
} else {
    echo json_encode(['message' => 'Método no permitido. Usa POST.']);
}

// Función para descifrar datos encriptados en formato JSON
function cryptoJsAesDecrypt($passphrase, $encryptedData) {
    // Dividir la cadena en sus componentes: ct, iv, s
    list($ct, $iv, $salt) = explode(":", $encryptedData);
    $salt = hex2bin($salt);
    $ct = base64_decode($ct);
    $iv = hex2bin($iv);
    $concatedPassphrase = $passphrase . $salt;
    $md5 = [];
    $md5[0] = md5($concatedPassphrase, true);
    $result = $md5[0];
    for ($i = 1; $i < 3; $i++) {
        $md5[$i] = md5($md5[$i - 1] . $concatedPassphrase, true);
        $result .= $md5[$i];
    }
    $key = substr($result, 0, 32);
    $data = openssl_decrypt($ct, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
    return json_decode($data, true);
}

function handlePost() {
    $secretKey = "mySecretKey123";

    $input = json_decode(file_get_contents("php://input"), true);

    if (isset($input["data"])) {
        $encryptedData = $input["data"];
        $decryptedData = cryptoJsAesDecrypt($secretKey, $encryptedData);

        if ($decryptedData) {
            echo json_encode([
                "status" => "success",
                "data" => $decryptedData,
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Error al descifrar los datos",
            ]);
        }
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "No se recibieron datos",
        ]);
    }
}
