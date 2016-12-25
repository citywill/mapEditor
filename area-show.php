<?php
/**
 * 保存区域信息
 */

include 'config.php';

$jsonFile = $jsonPath . '/' . $_GET['area'] . '.json';

/*if (!file_exists($jsonFile)) {
$json = array(

);
}*/

// 返回已有json
echo file_get_contents($jsonFile);
