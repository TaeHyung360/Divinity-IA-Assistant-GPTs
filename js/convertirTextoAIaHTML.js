

function convertirTextoAIaHTML(texto){
    // Convertir negritas: **texto** a <strong>texto</strong>
    let html = texto.replace(/\*\*(.*?)\*\*/g, "<strong>$1</strong>");

    // Dividir el texto en líneas
    let lineas = html.split('\n');
    let htmlFinal = '';
    let enLista = false;

    lineas.forEach((linea) => {
        if (linea.startsWith('- ')) {
        // Comprobar si empezamos una nueva lista
        if (!enLista) {
            htmlFinal += '<ul>';
            enLista = true;
        }
        // Agregar elemento de lista
        htmlFinal += `<li>${linea.slice(2)}</li>`;
        } else {
        // Si no es parte de una lista y hay una lista abierta, cerrarla
        if (enLista) {
            htmlFinal += '</ul>';
            enLista = false;
        }
        // Agregar párrafo si la línea no está vacía
        if (linea.trim() !== '') {
            htmlFinal += `<p>${linea}</p>`;
        }
        }
    });

    // Cerrar la lista si el texto termina en una lista
    if (enLista) {
        htmlFinal += '</ul>';
    }

    return htmlFinal;
}