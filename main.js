// Nueva versión de CryptoJSAesJson
var CryptoJSAesJson = {
  stringify: function (cipherParams) {
      // Concatenar iv y salt junto con ct para hacerlo más compacto
      var j = cipherParams.ciphertext.toString(CryptoJS.enc.Base64) +
              ":" + cipherParams.iv.toString() +
              ":" + cipherParams.salt.toString();
      return j;
  },
  parse: function (jsonStr) {
      // Dividir la cadena para recuperar ct, iv y salt
      var parts = jsonStr.split(':');
      var cipherParams = CryptoJS.lib.CipherParams.create({
          ciphertext: CryptoJS.enc.Base64.parse(parts[0])
      });
      cipherParams.iv = CryptoJS.enc.Hex.parse(parts[1]);
      cipherParams.salt = CryptoJS.enc.Hex.parse(parts[2]);
      return cipherParams;
  }
};

// Función para cifrar los datos
function encryptData(data, passphrase) {
  return CryptoJS.AES.encrypt(JSON.stringify(data), passphrase, {
      format: CryptoJSAesJson
  }).toString();
}

// Función para enviar los datos
async function sendData() {
  const data = {
      name: "prueba",
      price: 4.99,
      description: "High quality aluminum foil, perfect for wrapping food."
  };
  const secretKey = "mySecretKey123"; // Llave secreta de cifrado
  const encryptedData = encryptData(data, secretKey);

  console.log("Datos cifrados:", encryptedData);

  try {
      const response = await fetch("http://localhost/crytop_back/backend.php", {
          method: "POST",
          headers: {
              "Content-Type": "application/json"
          },
          body: JSON.stringify({ data: encryptedData })
      });

      const result = await response.json();
      console.log("Respuesta del servidor:", result);
  } catch (error) {
      console.log("Error al enviar los datos:", error);
  }
}
