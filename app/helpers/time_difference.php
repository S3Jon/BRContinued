<?php
/**
 * Calcula la diferencia entre un timestamp dado y la fecha y hora actuales.
 *
 * @param string $timestamp La fecha y hora en formato 'Y-m-d H:i:s'.
 * @return string La diferencia de tiempo en un formato legible.
 */
function timeDifference($timestamp) {
    // Crear un objeto DateTime para la fecha y hora específica
    $date1 = new DateTime($timestamp);

    // Crear un objeto DateTime para la fecha y hora actual
    $date2 = new DateTime();

    // Calcular la diferencia entre las dos fechas
    $interval = $date2->diff($date1);

    // Formatear la diferencia de una manera más amigable
    if ($interval->y > 0) {
        $difference = $interval->y . 'a.';
    } elseif ($interval->m > 0) {
        $difference = $interval->m . 'm.';
    } elseif ($interval->d > 0) {
        $difference = $interval->d . 'd.';
    } elseif ($interval->h > 0) {
        $difference = $interval->h . 'h.';
    } elseif ($interval->i > 0) {
        $difference = $interval->i . 'm.';
    } else {
        $difference = $interval->s . 's.';
    }

    return $difference;
}
?>
