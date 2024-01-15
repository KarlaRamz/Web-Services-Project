<?php
namespace Proyecto\Conexion;

class MyFirebase
{

    private $proyect;
    // public $coleccion;

    public function __construct($project)
    {
        $this->proyect = $project;   
    }

    private function runCurl($collection, $document)
    {
        // $this->coleccion = $collection;
        //$document = '';

        $url = 'https://'.$this->proyect.'.firebaseio.com/'.$collection.'/'.$document.'.json';

        $ch =  curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
        $response = curl_exec($ch);
    
        curl_close($ch);
    
        // Se convierte a Object o NULL
        return json_decode($response);
    }

    private function runCurlPOST($collection, $document){
        $url = 'https://'.$this->proyect.'.firebaseio.com/pruebascategoria/'.$document.'.json';

        $set = curl_init();
        curl_setopt($set, CURLOPT_URL, $url);
        curl_setopt($set, CURLOPT_CUSTOMREQUEST, "POST");
        $data["COM003"] = "comic3nuevo";
        curl_setopt($set, CURLOPT_POSTFIELDS, json_encode($data));
        $response = curl_exec($set);
        echo $response;
        curl_close($set);   
        return json_decode($response);
    }
    public function obtainMessage($code){
        $res = $this->runCurl( 'respuestas', $code);
        if(!is_null($res)) {
            // echo '<br>'.'Se encontro mensaje de clave '. $code . ' con: '.json_encode($res).'<br>';
            return json_encode($res);
        } else {
            return '<br>No se encontraron resultados<br>';
        }
    }

    public function isUserInDB($name){
        $res = $this->runCurl('usuarios', $name);
        if(!is_null($res)) {
            // echo '<br>'.'Se encontro el usuario: '.json_encode($res).'<br>'; //Mandar bool con 1
            return true;
        } else {
            // echo '<br>No se encontraron resultados<br>'; //Mandar bool con 0
            return false;
        }
    }

    public function isIsbnInDB($clave){
        $res = $this->runCurl( 'detalles', $clave);
        if(!is_null($res)) {
            // echo '<br>'.'Se encontro los detalles del producto de clave '. $clave . ' con: '.json_encode($res).'<br>';//Mandar bool con 1
            return true;
        } else {
            // echo '<br>No se encontraron resultados<br>'; //Mandar bool con 0
            return false;
        }
    }
    
    public function obtainPassword($user){
        $res = $this->runCurl('usuarios', $user);
        if(!is_null($res)) {
            // echo '<br>'.'Se encontro la contraseña: '.json_encode($res).'<br>';
            // echo json_encode($res);
            return $res;
        } else {
            return false;
        }
    }

    public function isCategoryInDB($name){
        $res = $this->runCurl('productos', $name);
        if(!is_null($res)) {
            // echo '<br>'.'Se encontro la categoria '. $name . ' con: '.json_encode($res).'<br>';//Mandar bool con 1
            return true;
        } else {
            // echo '<br>No se encontraron resultados<br>'; //Mandar bool con 0
            return false;
        }
    }

    public function obtainProducts($category)
    {
        $res = $this->runCurl('productos', $category);
        if(!is_null($res)) {
            // echo '<br>'.'Se encontro la categoria '. $category . ' con: '.json_encode($res).'<br>';
            // echo json_encode($res);
            return json_encode($res , JSON_PRETTY_PRINT , JSON_UNESCAPED_UNICODE);             
        } else {
            return '<br>No se encontraron resultados<br>';
        }
    }

    public function obtainDetails($isbn){
        $res = $this->runCurl( 'detalles', $isbn);
        if(!is_null($res)) {
            // echo '<br>'.'Se encontro los detalles del producto de clave '. $isbn . ' con: '.json_encode($res).'<br>';
            return json_encode($res, JSON_PRETTY_PRINT , JSON_UNESCAPED_UNICODE);          
        } else {
            return '<br>No se encontraron resultados<br>';
        }
    }

    public function createProduct($categoria, $isbn){
        $res = $this->runCurlPOST( $categoria, $isbn);
        if(!is_null($res)) {
            // echo '<br>'.'Se encontro los detalles del producto de clave '. $isbn . ' con: '.json_encode($res).'<br>';
            return json_encode($res, JSON_PRETTY_PRINT , JSON_UNESCAPED_UNICODE);          
        } else {
            return '<br>No se encontraron resultados<br>';
        }
    }

}