//===================================================================================================================
// Dado un string buscamos donde esta incrustado el json de la lista de productos y lo sustituimos por una lista en formato MarkDown
//===================================================================================================================
function extraerYParsearYReemplazarJSON(texto) {
    let regex = /```json\s*({[\s\S]*?})\s*```/;
    let match = texto.match(regex);

    if (match && match[1]) {
        try {
            // Parsear el JSON extraído
            let data = JSON.parse(match[1]);

            // Verificar si data y listadoConLosComponentes existen
            if (data && data.listadoConLosComponentes) {
                // Generar la lista en formato Markdown
                let markdownList = "**Lista de Componentes:**\n";
                data.listadoConLosComponentes.forEach(componente => {
                    markdownList += `- **${componente.nombre}**: ${componente.precio}\n`;
                });

                // Reemplazar el bloque JSON en el texto original por la lista Markdown
                let textoModificado = texto.replace(regex, markdownList);
                return textoModificado;
            } else {
                // Manejar el caso donde el JSON no tiene la estructura esperada
                console.error("El JSON no tiene la estructura esperada.");
                return texto; // Retorna el texto original
            }
        } catch (error) {
            console.error("Error al parsear JSON:", error);
            return texto; // Retorna el texto original si hay un error
        }
    } else {
        console.log("No se encontró JSON en el texto.");
        return texto; // Retorna el texto original si no se encuentra JSON
    }
}

