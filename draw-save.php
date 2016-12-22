<?php
//var_dump($_POST);die();

$mapId = $_POST['id'];
$data = $_POST['data'];

foreach ($data as $key => $value) {
    $data[$key] = array($value['lng'], $value['lat']);
}

$jsonFile = $jsonPath . '/' . $mapId . '.json';

$jsonData = file_get_contents($jsonFile);

$jsonData = json_decode($jsonData, true);

$jsonData['regins'][] = array(

    'properties' => array(
        'name' => '新社区',
        'id' => '23232323',
    ),

    'geometry' => array(
        'type' => 'MultiPolygon',
        'coordinates' => array(array($data)),
    ),
);

file_put_contents($jsonFile, json_encode($jsonData));

//var_dump($jsonData);
