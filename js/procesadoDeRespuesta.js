//===================================================================================================================
// Dado un string buscamos donde esta incrustado el json de la lista de productos
//===================================================================================================================
function procesadoDeRespuesta(respuesta) {
    let regex = /```json([\s\S]*?)```/; // Esta expresión regular busca el patrón que delimita el JSON
    let match = respuesta.match(regex);
    if (match && match[1]) {
        // Intenta parsear el JSON extraído
        try {
            let jsonExtraido = JSON.parse(match[1]);
            return jsonExtraido;
        } catch (error) {
            console.error("Error al parsear el JSON:", error);
            return null;
        }
    } else {
        console.log("No se encontró un JSON válido en el texto.");
        return null;
    }
}

