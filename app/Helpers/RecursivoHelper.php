<?php


if (!function_exists('array_search_id')) {

    // Function to recursively search for a given value
    function array_search_id($search_value, $array, $id_path) {
        
        if(is_array($array) && count($array) > 0) {
            
            foreach($array as $key => $value) {
    
                $temp_path = $id_path;
                
                if ($key == '0' || $key=='parents_sectors_rec' || $key =='id') {
                   
                    // Adding current key to search path
                    array_push($temp_path, $key);
        
                    // Check if this value is an array
                    // with atleast one element
                    if(is_array($value) && count($value) > 0) {
                        $res_path = array_search_id(
                                $search_value, $value, $temp_path);
        
                        if ($res_path != null) {
                            return $res_path;
                        }
                    }
                    else if($value == $search_value) {
                        return join(" --> ", $temp_path);
                    }
                }
            }
        }
        
        return null;
        }
    }

    if (!function_exists('organismo_chart')) {

        function organismo_chart($organismo_nodo, $sectores) {

            // se almacena en $organismo_chart el primer nodo del organigrama, que es el organismo
            $organismo_chart = [$organismo_nodo];

            if (count($sectores) > 0) {

                // se recorre la coleccion de sectores consultadas en el controlador y se van cargando en un array que utiliza el siguiente formato:
                // 1. el nombre del nodo (debe ser UNICO)
                // 2. la relacion con el nodo padre (se utiliza el nombre del nodo padre)
                // 3. un titulo para el nodo (se mostrará cuando se haga hover sobre el nodo, puede ser vacio)
                foreach ($sectores as $key => $sector) {

                    // se consulta si el sector tiene un padre, si no lo tiene, significa que su ancestro es el organismo y se pasa como referencia el nombre del organismo
                    // del cual depende en la segunda posición del array $sector_nodo
                    if ($sector->parentSector == NULL) {
                        
                        // si el sector tiene estado "inactivo", se coloca una leyenda de referencia
                        if ($sector->activo == 0) {
                            $sector_nodo = [['v' => $sector['organismossector'], 'f' => $sector['organismossector'] .'<div style="color:red; font-style:italic">Inactivo</div>'],
                                            $organismo_nodo[0],
                                            'Sector'];
                        }
                        else {
                            $sector_nodo = [$sector['organismossector'], $organismo_nodo[0], 'Sector'];
                        }
                        
                        array_push($organismo_chart, $sector_nodo); // se carga el array $organismo_chart con los datos de los sectores y relaciones
                    }
                    // si el sector tiene un padre, se pasa como referencia el nombre del sector del cual depende en la segunda posición del array $sector_nodo
                    else if ($sector->parentSector !== NULL) {

                        if ($sector->activo == 0) {
                            $sector_nodo = [['v' => $sector['organismossector'], 'f' => $sector['organismossector'] .'<div style="color:red; font-style:italic">Inactivo</div>'],
                                            $sector->parentSector->organismossector,
                                            'Sector'];
                        }
                        else {
                            $sector_nodo = [$sector['organismossector'], $sector->parentSector->organismossector, 'Sector'];
                        }

                        array_push($organismo_chart, $sector_nodo); // se carga el array $organismo_chart con los datos de los sectores y relaciones
                    }
                }
            }

            return $organismo_chart;
        }
    }

