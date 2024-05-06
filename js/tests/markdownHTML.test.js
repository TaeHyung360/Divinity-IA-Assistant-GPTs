const markdownHTML = require('../markdownHTML.js');

test('La función markdownHTML debería convertir los encabezados de Markdown a HTML.', () => {
    expect(markdownHTML('# Header')).toBe('<h1>Header</h1>');
    expect(markdownHTML('## Header')).toBe('<h2>Header</h2>');
    expect(markdownHTML('### Header')).toBe('<h3>Header</h3>');
});

test('La función markdownHTML debería convertir el texto en negrita y cursiva de Markdown a HTML.', () => {
    expect(markdownHTML('**bold**')).toBe('<strong>bold</strong>');
    expect(markdownHTML('*italic*')).toBe('<em>italic</em>');
});

test('La función markdownHTML debería convertir los enlaces de Markdown a HTML.', () => {
    expect(markdownHTML('[link](http://example.com)')).toBe('<a href="http://example.com">link</a>');
});

test('La función markdownHTML debería convertir los saltos de línea en etiquetas <br>.', () => {
    expect(markdownHTML('line1\nline2\n')).toBe('line1<br>line2<br>');
});

test('La función markdownHTML debería manejar Markdown mezclado.', () => {
    expect(markdownHTML('# Title\n**bold** text and *italic* text\n[link](http://example.com)'))
        .toBe('<h1>Title</h1><br><strong>bold</strong> text and <em>italic</em> text<br><a href="http://example.com">link</a>');
});
