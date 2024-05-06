//===================================================================================================================
// Convertir de markdown a HTML
//===================================================================================================================

function markdownHTML(text) {
    // Convertir encabezados
    text = text.replace(/^### (.*$)/gim, '<h3>$1</h3>');
    text = text.replace(/^## (.*$)/gim, '<h2>$1</h2>');
    text = text.replace(/^# (.*$)/gim, '<h1>$1</h1>');

    // Convertir negritas e itálicas
    text = text.replace(/\*\*(.*)\*\*/gim, '<strong>$1</strong>');
    text = text.replace(/\*(.*)\*/gim, '<em>$1</em>');

    // Convertir enlaces
    text = text.replace(/\[([^\]]+)\]\(([^)]+)\)/gim, '<a href="$2">$1</a>');

    // Convertir saltos de línea a etiquetas <br>
    text = text.replace(/\n/gim, '<br>');

    return text;
}

//Utilizado para los test
module.exports = markdownHTML;