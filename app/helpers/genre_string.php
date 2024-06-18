<?php
// genre_string.php

/**
 * Convierte una cadena de géneros en una cadena formateada separada por comas.
 *
 * @param string $genresString La cadena de géneros.
 * @return string La cadena de géneros separada por comas.
 */
function genreString($genresString) {
    // Eliminar caracteres innecesarios de la cadena (corchetes y comillas)
    $cleanString = trim($genresString, '[]"');
    // Reemplazar comillas seguidas de comas por comas
    $cleanString = str_replace('","', ', ', $cleanString);
    // Reemplazar comillas simples seguidas de comas por comas
    $cleanString = str_replace("','", ", ", $cleanString);
    // Reemplazar comillas simples o dobles al principio y al final por nada
    $cleanString = str_replace(['"', "'"], '', $cleanString);
    return $cleanString;
}
?>
