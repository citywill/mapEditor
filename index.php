<?php

$jsonPath = './data';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'draw.php';
    die();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <style type="text/css">
    body, html{width: 100%;height: 100%;margin:0;font-family:"微软雅黑";}
    #allmap {width: 100%; height:500px; overflow: hidden;}
    #result {width:100%;font-size:12px;}
    dl,dt,dd,ul,li{
        margin:0;
        padding:0;
        list-style:none;
    }
    p{font-size:12px;}
    dt{
        font-size:14px;
        font-family:"微软雅黑";
        font-weight:bold;
        border-bottom:1px dotted #000;
        padding:5px 0 5px 5px;
        margin:5px 0;
    }
    dd{
        padding:5px 0 0 5px;
    }
    li{
        line-height:28px;
    }
    </style>
    <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=584fa8af082f87b10783eab60748e804"></script>
    <!--加载鼠标绘制工具-->
    <script type="text/javascript" src="http://api.map.baidu.com/library/DrawingManager/1.4/src/DrawingManager_min.js"></script>
    <link rel="stylesheet" href="http://api.map.baidu.com/library/DrawingManager/1.4/src/DrawingManager_min.css" />
    <!--加载检索信息窗口-->
    <script type="text/javascript" src="http://api.map.baidu.com/library/SearchInfoWindow/1.4/src/SearchInfoWindow_min.js"></script>
    <link rel="stylesheet" href="http://api.map.baidu.com/library/SearchInfoWindow/1.4/src/SearchInfoWindow_min.css" />

    <link rel="stylesheet" href="./assets/.3.3.7@bootstrap/dist/css/bootstrap.min.css">

    <title>鼠标绘制工具</title>
</head>
<body>
<div class="container">

    <div class="page-header">

        <div id="nav" class="pull-right">
            <a class="goback btn btn-primary" style="display: none;" href="javascript:void(0)">
                返回<span class="target"></span>
            </a>
        </div>

        <h1>地图编辑器 <small></small></h1>

    </div>

    <div id="allmap" style="overflow:hidden;zoom:1;position:relative;">
        <div id="map" style="height:100%;-webkit-transition: all 0.5s ease-in-out;transition: all 0.5s ease-in-out;"></div>
    </div>

    <div class="bar" style="padding-top:20px;">

        <div id="status" class="pull-right"></div>

        <div id="tools" style="display: none;">
            <button class="btn btn-primary draw-create">绘制新区域</button>
        </div>

    </div>
</div>


<div class="modal modal-new-draw bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close draw-delete" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">新建区域</h4>
      </div>
      <div class="modal-body">
          <div class="form-group">
            <label for="form-area-name">区域名称</label>
            <input type="email" class="form-control" id="form-area-name" placeholder="">
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default draw-delete">取消</button>
        <button type="button" class="btn btn-primary draw-save">保存</button>
      </div>
    </div>
  </div>
</div>

</body>
</html>

<script src="./assets/.3.1.1@jquery/dist/jquery.min.js"></script>
<script src="./assets/.3.3.7@bootstrap/dist/js/bootstrap.min.js"></script>
<script src="./assets/jq-track-mouse.min.js"></script>

<script type="text/javascript">

var mapId = 220100;

// 百度地图API功能，关闭底图可点功能
var map = new BMap.Map('map', {enableMapClick:false});

//定位到长春
map.centerAndZoom("长春 人民广场");

//百度地图API功能
//var map = new BMap.Map("allmap");

//单击获取点击的经纬度
map.addEventListener("click",function(e){
    //console.log(e.point.lng + "," + e.point.lat);
});
/**/

//启用滚轮，禁用双击缩放地图
map.enableScrollWheelZoom();
map.disableDoubleClickZoom();

//绘制样式
var styleOptions = {
    strokeColor:"red", //边线颜色。
    fillColor:"red", //填充颜色。
    strokeWeight: 3, //边线的宽度，以像素为单位。
    strokeOpacity: 0.8, //边线透明度，取值范围0 - 1。
    fillOpacity: 0.6, //填充的透明度，取值范围0 - 1。
    strokeStyle: 'solid' //边线的样式，solid或dashed。
}

//实例化绘制对象
var drawingManager = new BMapLib.DrawingManager(map, {
    isOpen: false, //是否开启绘制模式
    enableDrawingTool: false, //是否显示工具栏
    polygonOptions: styleOptions, //多边形的样式
});

//初始化模态框
$('.modal-new-draw').modal({
  keyboard: false,
  show: false
});

//绘制区域
var areaDraw;

//添加鼠标绘制工具监听事件，用于获取绘制结果
drawingManager.addEventListener('overlaycomplete', function(e){
    areaDraw = e.overlay;

    //显示模态框
    $('.modal-new-draw').modal('show');
});

/**
 * 新增绘制区域
 */
$('.draw-create').on('click', function() {
    drawingManager.open();
    drawingManager.setDrawingMode('polygon');
    $(this).attr('disabled','disabled');
});


/**
 * 删除绘制区域
 */
var deleteDraw = function() {
    map.removeOverlay(areaDraw);
    areaDraw = null;
    $('.draw-create').removeAttr('disabled');
    $('.modal-new-draw').modal('hide');
}

$('.draw-delete').on('click', deleteDraw);

/**
 * 保存绘制区域
 */
$('.draw-save').on('click', function() {
    $.ajax({
        "method": "POST",
        "url": "index.php",
        "data": {
            "id": mapId,
            "name": $('#form-area-name').val(),
            "data": areaDraw.ro
        },
        "success": function(e){
            showMap(mapId);
            deleteDraw();
            $('.draw-create').removeAttr('disabled');
            $('.modal-new-draw').modal('hide');
            $('#form-area-name').val('');
        }
    });
});


//已有区域
var areas = [];

/**
 * 删除所有区域
 */
var clearMap = function() {
    for(var i = 0; i < areas.length; i++){
        map.removeOverlay(areas[i]);
    }
    areas = []
}

/**
 * 显示地图区域（from json）
 */
var showMap = function(mapDataId){

    //显示已经存在的覆盖数据
    $.getJSON('./data/' + mapDataId + '.json', function(data){

        clearMap();

        //定位到坐标
        var poi = new BMap.Point(data.poi[0],data.poi[1]);
        map.centerAndZoom(poi, data.zoom);

        //标题和提示内容
        $('h1 small').html(data.name);
        $('#status').html(data.name);

        //遍历区域
        $.each(data.regins, function(i, regin){

            //坐标点
            var points = []

            //遍历每个区域的地块
            $.each(regin.geometry.coordinates, function(j, coordinates){

                //遍历每个地块的坐标点
                $.each(coordinates[0], function(k, coordinate){

                    //创建多边形的坐标点数组
                    points.push(new BMap.Point(coordinate[0],coordinate[1]));
                });
            });

            //创建多边形区域
            areas[i] = new BMap.Polygon(points, {strokeColor:"blue", strokeWeight:2, strokeOpacity:0.5});

            points = null;

            //将多边形区域添加到地图上
            map.addOverlay(areas[i]);

            //对多边形定义鼠标提示事件
            areas[i].addEventListener("mouseover",function(){
                $.trackMouse({
                    "text":regin.properties.name,
                });
                //$.trackMouse().reset();
                //$(this).removeEventListener();
                $('#status').html(
                    data.name +
                    regin.properties.name
                );
            });

            //点击一个区域
            areas[i].addEventListener("click",function(){
                if(data.type=='regin'){
                    return false;
                } else {
                    //todo:如果该区域没有json数据，则ajax创建一个
                    mapId = regin.properties.id;
                    showMap(mapId);
                }
            });
        });

        //如果有上级则显示导航，否则关闭
        if(data.parent) {
            $('#nav .goback .target').html(data.parent.name);
            $('#nav .goback').show();
            $('#nav .goback').on('click', function(){
                showMap(data.parent.id);
                //location.hash = '#' + data.parent.id;
                $(this).hide();
            });
        } else {
            $('#nav .goback').hide();
        }

        //如果地图类型为区则显示社区绘制工具
        if(data.parent) {
            $('#tools').show();
        } else {
            $('#tools').hide();
        }
    });
}

showMap(mapId);
</script>
