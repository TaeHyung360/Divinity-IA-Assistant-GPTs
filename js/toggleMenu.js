

function toggleMenu() {
    var menu = document.querySelector('.divinity-ia-products-column');
    if (menu.style.left === '-100%') {
        menu.style.left = '0';
    } else {
        menu.style.left = '-100%';
    }
}