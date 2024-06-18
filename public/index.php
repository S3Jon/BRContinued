<?php

require_once "../database/querys.php";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="icon" type="image/x-icon" href="img/BR.ico" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Source+Serif+4:ital,opsz,wght@0,8..60,200..900;1,8..60,200..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/habibmhamadi/multi-select-tag@3.0.1/dist/css/multi-select-tag.css">
    <link href="https://cdn.datatables.net/v/dt/dt-2.0.6/datatables.min.css" rel="stylesheet">
    <title>BookReaders</title>
</head>
<body class="bg-background font-inter">
    <?php require_once "router.php"; ?>    
    <script src="https://cdn.jsdelivr.net/gh/habibmhamadi/multi-select-tag@3.0.1/dist/js/multi-select-tag.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/v/dt/dt-2.0.6/datatables.min.js"></script>
    <script>
        new DataTable('#books',
            {
                pageLength: 5,
                lengthMenu: [5, 10, 25,],
                responsive: true,
            }
        );
        $('#books_wrapper').addClass('w-full overflow-auto');
        document.addEventListener('DOMContentLoaded', function() {
            if (document.querySelector('#genre'))
            new MultiSelectTag('genre', {
                placeholder: 'Selecciona uno o varios géneros',
                shadow: true,
            });
        });
    </script>
</body>
</html>