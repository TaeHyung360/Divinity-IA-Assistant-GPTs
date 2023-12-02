<?php

function verificarCreacionDelTxt() {
    return file_exists(plugin_dir_path(__FILE__) . 'productos.txt');
}
