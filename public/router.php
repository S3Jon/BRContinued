<?php

$request_uri = $_SERVER['REQUEST_URI'];

// Parseamos la URL para obtener el path y los query parameters
$url_components = parse_url($request_uri);
$path = $url_components['path'];
$query_params = isset($url_components['query']) ? $url_components['query'] : '';

switch ($path) {
    //TODO Se modificara esto para mejorar la navegacion
    case '/BookReaders/':
        require '../app/views/landing.php';
        break;
    case '/BookReaders/list.php':
	case '/BookReaders/list_edit.php':
        if (!empty($query_params)) {
            // Parseamos los parámetros de la query
            parse_str($query_params, $params);
            // Verificamos si se proporcionó el parámetro 'id'
            if(isset($params['id'])) {
                // Accedemos al valor del parámetro 'id'
                $id_list = $params['id'];
                // Luego puedes hacer algo con el ID, como cargar la vista correspondiente o procesar la información
                require '../app/views/list.php';
                // Importante: Finalizamos el switch para evitar que se ejecute el case default
                exit();
            }
        }
        break;
	case '/BookReaders/legal.php':
		if (!empty($query_params)) {
			parse_str($query_params, $params);
			if(isset($params['section'])) {
				$section = $params['section'];
				require '../app/views/legal.php';
				exit();
			}
		}
		break;
	case '/BookReaders/book.php':
		if (!empty($query_params)) {
			parse_str($query_params, $params);
			if(isset($params['isbn'])) {
				$isbn = $params['isbn'];
				require '../app/views/book.php';
				exit();
			}
		}
		break;

	case 'Bookreaders/profile':
		if (!empty($query_params)) {
			parse_str($query_params, $params);
			if(isset($params['id'])) {
				$id = $params['id'];
				require '../app/views/profile.php';
				exit();
			}
		}
		break;
	
	case 'Bookreaders/lists':
		if (!empty($query_params)) {
			parse_str($query_params, $params);
			if(isset($params['search'])) {
				$search = $params['search'];
				require '../app/views/lists.php';
				exit();
			}
		}
		break;

	case 'Bookreaders/profile_search':
		if (!empty($query_params)) {
			parse_str($query_params, $params);
			if(isset($params['id'])) {
				$id = $params['id'];
				require '../app/views/profile_search.php';
				exit();
			}
		}
		break;
	
    default:
        $request = str_replace('/BookReaders/', '', $path);
        $filename = '../app/views/' . $request . '.php';

        if (file_exists($filename)) {
            require_once $filename;
            break;
        } 

        http_response_code(404);
        require '../app/views/404.php';        
        break;
}