<?php

use Slim\Http\Response;
use Proyecto\Conexion\MyFirebase as MyFire;

require "vendor/autoload.php";
require_once __DIR__ . '/conexion.php';

$app = new Slim\App();

//Conexion a base de datos
$objeto = new MyFire('practica6-ffc02-default-rtdb');

$app->get("/", function ( $request, $response, $args) {
    $response-> write("Hola Mundo Slim!!!");
    return $response;
});

//Funcion get categoria
$app->get("/productos[/{categoria}]", function( $request, $response, $args){
    // $response->write("Categoria: " . $args["categoria"]);
    $categoria = strtolower($args["categoria"]);
    //Armamos la respuesta
    $res["code"] = "999";
    $res["message"] = $GLOBALS["objeto"]->obtainMessage(999);
    $res["data"] = "";
    $res["status"] ="error";
    
    //DATOS DE LOGIN OBTENIDOS DE LOS HEADER
    $user = implode($request->getHeader('user'));
    $pass = implode($request->getHeader('pass'));
    // return $response->write(implode($user));
    if ( ($GLOBALS["objeto"]->isUserInDB($user) == true) and ($user != "") ) {
        if ( $GLOBALS["objeto"]->obtainPassword($user) === md5($pass)) {
            if ( $GLOBALS["objeto"]->isCategoryInDB($categoria) != false ) {
                $aux = json_decode($GLOBALS["objeto"]->obtainProducts($categoria));
                $res['code'] = 200;
                $res['message'] = $GLOBALS["objeto"]->obtainMessage(200);
                $res['status'] = 'success';
                $res['data'] = json_decode($GLOBALS["objeto"]->obtainProducts($categoria));
            }
            else {
                $res['code'] = 300;
                $res['message'] = $GLOBALS["objeto"]->obtainMessage('300');
            }
        }
        else {
            $res['code'] = 501;
            $res['message'] = $GLOBALS["objeto"]->obtainMessage('501');
        }
    }
    else {
        $res['code'] = 500;
        $res['message'] = $GLOBALS["objeto"]->obtainMessage('500');
    }
    // return $response->write(json_encode($res, JSON_PRETTY_PRINT));
    $response->write(json_encode($res, JSON_FORCE_OBJECT));
});

//Funcion get detalles 
$app->get("/detalles[/{clave}]", function( $request, $response, $args){
    // $response->write("Clave: " . $args["clave"]);
    
    $isbn = strtoupper($args["clave"]);
    //Armamos la respuesta
    $res["code"] = "999";
    $res["message"] = $GLOBALS["objeto"]->obtainMessage(999);
    $res["data"] = "";
    $res["status"] ="error";
    
   //DATOS DE LOGIN OBTENIDOS DE LOS HEADER
   $user = implode($request->getHeader('user'));
   $pass = implode($request->getHeader('pass'));
            
    if ( ($GLOBALS["objeto"]->isUserInDB($user) == true) and ( $user != '' ) ) { //checar si existe el user
        if ($GLOBALS["objeto"]->obtainPassword($user) === md5($pass)) { //si es la misma password
            if ( $GLOBALS["objeto"]->isIsbnInDB($isbn) != false ) { 

                $aux = json_decode($GLOBALS["objeto"]->obtainDetails($isbn));
                if($aux->Descuento > 0){
                    $res['oferta'] = true;
                }
                else
                {
                    $res['oferta'] = false;
                }
                $res['isbn'] = $isbn;
                $res['code'] = 201;
                $res['message'] = $GLOBALS["objeto"]->obtainMessage(201);
                $res['status'] = 'success';
                $res['data'] = json_decode( $GLOBALS["objeto"]->obtainDetails($isbn));             
            }
            else {
                $res['code'] = 301;
                $res['message'] = $GLOBALS["objeto"]->obtainMessage('301');
            }
        }
        else {
            $res['code'] = 501;
            $res['message'] = $GLOBALS["objeto"]->obtainMessage('501');
        }
    }
    else {
        $res['code'] = 500;
        $res['message'] = $GLOBALS["objeto"]->obtainMessage('500');
    }
    $response->write(json_encode($res, JSON_PRETTY_PRINT));
});


$app->patch("/producto", function($request, $response, $args )
{
    parse_str(file_get_contents("php://input"), $repPost);
    // $isbn = $reqPost['clave'];
    // $isbn = strtoupper($isbn[0]);
    
    // $repPost = $request->getParsedBody();
    $categoria = strtolower($repPost["categoria"]);
    $isbn = strtoupper($repPost["isbn"]);
    $autor = $repPost["autor"];
    $nombre = $repPost["nombre"];
    $editorial = $repPost["editorial"];
    $fecha = $repPost["fecha"];
    $precio = $repPost["precio"];
    $descuento = $repPost["descuento"];
 
    $url = 'https://practica6-ffc02-default-rtdb.firebaseio.com/pruebascategoria/'.$categoria.'.json';
    $set = curl_init();
    curl_setopt($set, CURLOPT_URL, $url);
    curl_setopt($set, CURLOPT_CUSTOMREQUEST, "PATCH");
    curl_setopt($set, CURLOPT_RETURNTRANSFER, 1);
    //FORMAMOS LOS DATOS
    curl_setopt($set, CURLOPT_POSTFIELDS, "{ \"$isbn\":\"$nombre\" }");
    $data = curl_exec($set);
    // curl_close($set); 
    // $set = curl_init();
    // curl_setopt($set, CURLOPT_URL, $url);
    // curl_setopt($set, CURLOPT_POST, 1);
    // // $data["COM003"] = "comic3nuevo";
    // $data['COM003'] = 'comic3nuevo';
    // curl_setopt($set, CURLOPT_POSTFIELDS, json_encode('{"COM003":{"COM003":"COMICEE33"}}'));
    // $response = curl_exec($set);
    // echo $response;
    // curl_close($set);   


    // $data = '{"id": "6"}'; 
    // $ch = curl_init(); 
    // curl_setopt($ch, CURLOPT_URL, $url);                                
    // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
    // curl_setopt($ch, CURLOPT_POST, 1); 
    // curl_setopt($ch, CURLOPT_POSTFIELDS, $data); 
    // curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded')); 
    // $jsonResponse = curl_exec($ch); 
    // if(curl_errno($ch)) 
    // { 
    //     echo 'Curl error: ' . curl_error($ch); 
    // } 
    // curl_close($ch); 

    $url = 'https://practica6-ffc02-default-rtdb.firebaseio.com/pruebasdetalles/'.$isbn.'.json';
    $set = curl_init();
    curl_setopt($set, CURLOPT_URL, $url);
    curl_setopt($set, CURLOPT_CUSTOMREQUEST, "PATCH");
    curl_setopt($set, CURLOPT_RETURNTRANSFER, 1);
    //FORMAMOS LOS DATOS
    curl_setopt($set, CURLOPT_POSTFIELDS, "{ \"Autor\":\"$autor\", \"Nombre\":\"$nombre\", \"Editorial\":\"$editorial\",
    \"Fecha\":\"$fecha\", \"Precio\":\"$precio\", \"Descuento\":\"$descuento\" }");
    $data = curl_exec($set);
    // curl_close($set2); 

    //Armamos la respuesta
    $res["code"] = "999";
    $res["message"] = $GLOBALS["objeto"]->obtainMessage(999);
    $res["data"] = "";
    $res["status"] ="error";
    
    //DATOS DE LOGIN OBTENIDOS DE LOS HEADER
    $user = implode($request->getHeader('user'));
    $pass = implode($request->getHeader('pass'));
            
    if ( ($GLOBALS["objeto"]->isUserInDB($user) == true) and ( $user != '' ) ) { //checar si existe el user
        if ($GLOBALS["objeto"]->obtainPassword($user) === md5($pass)) { //si es la misma password
            if ( $categoria != false ) { 
                $res['code'] = 202;
                $res['message'] = $GLOBALS["objeto"]->obtainMessage(202);
                $res['status'] = 'success';
                $res['data'] = date('y-m-d h:i:s');       
            }
            else {
                $res['code'] = 301;
                $res['message'] = $GLOBALS["objeto"]->obtainMessage('301');
            }
        }
        else {
            $res['code'] = 501;
            $res['message'] = $GLOBALS["objeto"]->obtainMessage('501');
        }
    }
    else {
        $res['code'] = 500;
        $res['message'] = $GLOBALS["objeto"]->obtainMessage('500');
    }

    return json_encode($res, JSON_FORCE_OBJECT);

    // $response->write(json_encode($res, JSON_PRETTY_PRINT));
    // return $response;
});


$app->patch('/producto/detalles', function ($request, $response, array $args) {
    // $repPut = $request->getParsedBody('clave');
    $repPut = $request->getParsedBody();
    // $contents = json_decode(file_get_contents('php://input'), true);
    // if (json_last_error() === JSON_ERROR_NONE) {
    //     $request = $request->withParsedBody($contents);

    // parse_str(file_get_contents("php://input"), $reqPost);
    // $isbn = $reqPost['clave'];
    // $isbn = strtoupper($isbn[0]);

    $isbn = strtoupper($repPut["isbn"]);
    $autor = $repPut["autor"];
    $categoria = $repPut["categoria"];
    // $categoria = substr($isbn, 0, 3);
    // if($categoria == "COM"){
    //     $categoria = "comics";
    // }
    // else{
    //     if($categoria == "LIB"){
    //         $categoria = "libros";
    //     }
    //     else{
    //         if($categoria == "MAN"){
    //             $categoria = "mangas";
    //         }
    //     }
    // }
    $nombre = $repPut["nombre"];
    $editorial = $repPut["editorial"];
    $fecha = $repPut["fecha"];
    $precio = $repPut["precio"];
    $descuento = $repPut["descuento"];
    
    $url = "https://practica6-ffc02-default-rtdb.firebaseio.com/pruebasdetalles/$isbn.json";
    $set = curl_init();
    curl_setopt($set, CURLOPT_URL, $url);
    curl_setopt($set, CURLOPT_CUSTOMREQUEST, "PATCH");
    curl_setopt($set, CURLOPT_RETURNTRANSFER, 1);
    //FORMAMOS LOS DATOS
    curl_setopt($set, CURLOPT_POSTFIELDS, "{ \"Autor\":\"$autor\", \"Nombre\":\"$nombre\", \"Editorial\":\"$editorial\",
    \"Fecha\":\"$fecha\", \"Precio\":\"$precio\", \"Descuento\":\"$descuento\" }");
    $data = curl_exec($set);

    //AGREGAR EN PRODUCTOS
    $url = "https://practica6-ffc02-default-rtdb.firebaseio.com/pruebascategoria/$categoria.json";
    $set = curl_init();
    curl_setopt($set, CURLOPT_URL, $url);
    curl_setopt($set, CURLOPT_CUSTOMREQUEST, "PATCH");
    //FORMAMOS LOS DATOS
    $aux["$isbn"] = $nombre;
    curl_setopt($set, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($set, CURLOPT_POSTFIELDS, json_encode($aux));
    $data = curl_exec($set);
    

    //DATOS DE BODY
    // $categoria = implode($request->getParsedBody('user'));
    // $producto = implode($request->getHeader('pass'));

    //Armamos la respuesta
    $res["code"] = "999";
    $res["message"] = $GLOBALS["objeto"]->obtainMessage(999);
    $res["data"] = "";
    $res["status"] ="error";
    
    //DATOS DE LOGIN OBTENIDOS DE LOS HEADER
    $user = implode($request->getHeader('user'));
    $pass = implode($request->getHeader('pass'));
             
    if ( ($GLOBALS["objeto"]->isUserInDB($user) == true) and ( $user != '' ) ) { //checar si existe el user
        if ($GLOBALS["objeto"]->obtainPassword($user) === md5($pass)) { //si es la misma password
            if ( $isbn != "" ) { 
                $res['code'] = 203;
                $res['message'] = $GLOBALS["objeto"]->obtainMessage(203);
                $res['status'] = 'success';
                $res['data'] = date('y-m-d h:i:s');             
            }
            else {
                $res['code'] = 301;
                $res['message'] = $GLOBALS["objeto"]->obtainMessage('301');
            }
        }
        else {
            $res['code'] = 501;
            $res['message'] = $GLOBALS["objeto"]->obtainMessage('501');
        }
    }
    else {
        $res['code'] = 500;
        $res['message'] = $GLOBALS["objeto"]->obtainMessage('500');
    }
    $response->write(json_encode($res, JSON_PRETTY_PRINT));
    return $response;
});

$app->delete('/producto/delete', function ($request, $response, array $args) {
    // $repDelete = $request->getParsedBody('clave');
    // parse_str(file_get_contents("php://input"), $reqPost);
    // $isbn = $reqPost['clave'];
    // $isbn = strtoupper($isbn[0]);
    // $isbn = $repDelete["clave"];
    // $categoria = substr($isbn, 0, 3);
    parse_str(file_get_contents("php://input"), $repDelete);
    $isbn = strtoupper($repDelete["clave"]);
    $categoria = substr($isbn, 0, 3);

    if($categoria == "COM" && $repDelete != ""){
        $categoria = "comics";
    }
    else{
        if($categoria == "LIB" && $repDelete != ""){
            $categoria = "libros";
        }
        else{
            if($categoria == "MAN" && $repDelete != ""){
                $categoria = "mangas";
            }
        }
    }
    //Armamos la respuesta
    $res["code"] = "999";
    $res["message"] = $GLOBALS["objeto"]->obtainMessage(999);
    $res["data"] = "";
    $res["status"] ="error";
    // $val1 = "";
    // $response->write(json_encode($categoria, JSON_PRETTY_PRINT));
    //BORRAMOS EL PRODUCTO EN CATEGORIA
    if($isbn != ""){
        $url = 'https://practica6-ffc02-default-rtdb.firebaseio.com/pruebascategoria/'.$categoria.'/'.$isbn.'.json';
        $set = curl_init();
        curl_setopt($set, CURLOPT_URL, $url);
        curl_setopt($set, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($set, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_exec($set);
       
    }
    // //BORRAR PRODUCTO DENTRO DE LOS DETALLES
    if($isbn != ""){
        $url = 'https://practica6-ffc02-default-rtdb.firebaseio.com/pruebasdetalles/'.$isbn.'.json';
        $set = curl_init();
        curl_setopt($set, CURLOPT_URL, $url);
        curl_setopt($set, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($set, CURLOPT_CUSTOMREQUEST, "DELETE");
        $response = curl_exec($set);   
    }
    //DATOS DE LOGIN OBTENIDOS DE LOS HEADER
    $user = implode($request->getHeader('user'));
    $pass = implode($request->getHeader('pass'));

    if ( ($GLOBALS["objeto"]->isUserInDB($user) == true) and ( $user != '' ) ) { //checar si existe el user
        if ($GLOBALS["objeto"]->obtainPassword($user) === md5($pass)) { //si es la misma password
            if ( $isbn != "" ) { 
                $res['code'] = 204;
                $res['message'] = $GLOBALS["objeto"]->obtainMessage(204);
                $res['status'] = 'success';
                $res['data'] = date('y-m-d h:i:s');             
            }
            else {
                $res['code'] = 301;
                $res['message'] = $GLOBALS["objeto"]->obtainMessage('301');
            }
        }
        else {
            $res['code'] = 501;
            $res['message'] = $GLOBALS["objeto"]->obtainMessage('501');
        }
    }
    else {
        $res['code'] = 500;
        $res['message'] = $GLOBALS["objeto"]->obtainMessage('500');
    }

    //$response->write(json_encode($res, JSON_PRETTY_PRINT));
    // return $res;
    return json_encode($res, JSON_FORCE_OBJECT);
});

$app->patch('/userlogin', function ($request, $response, array $args) {
   
    $repPut = $request->getParsedBody();
  
    // $isbn = $repPut["user"];
    // $contraseña = $repPut["contraseña"];
    //Armamos la respuesta
    $res["code"] = "999";
    $res["message"] = $GLOBALS["objeto"]->obtainMessage(999);
    $res["data"] = "";
    $res["status"] ="error";
    
    //DATOS DE LOGIN OBTENIDOS DE LOS HEADER
    $user = implode($request->getHeader('user'));
    $pass = implode($request->getHeader('pass'));
             
    if ( ($GLOBALS["objeto"]->isUserInDB($user) == true) and ( $user != '' ) ) { //checar si existe el user
        if ($GLOBALS["objeto"]->obtainPassword($user) === md5($pass)) { //si es la misma password
            $res['status'] = 'success';
            $res['code'] = 203;
            $res['message'] = "Usuario y contraseña correctos";
            $res['data'] = date('y-m-d h:i:s');             
        }
        else {
            $res['code'] = 501;
            $res['message'] = $GLOBALS["objeto"]->obtainMessage('501');
        }
    }
    else {
        $res['code'] = 500;
        $res['message'] = $GLOBALS["objeto"]->obtainMessage('500');
    }
    $response->write(json_encode($res, JSON_PRETTY_PRINT));
    return $response;
});


// $app->post("/pruebapost", function($request, $response, $args )
// {
//     $repPost = $request->getParsedBody();
//     $val1 = $repPost["val1"];
//     $val2 = $repPost["val2"];
//     $response->write("Valores: " . $val1 . " " . $val2);
//     return $response;
// });

$app->get("/testjson", function($request, $response, $args) {
    $data[0]["nombre"]="Sergio";
    $data[0]["apellidos"]="Rojas Espino";
    $data[1]["nombre"]="Pedro";
    $data[1]["apellidos"]="Perez Lopez";
    $response->write(json_encode($data, JSON_PRETTY_PRINT));
    return $response;
});

$app->run();
?>