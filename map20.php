<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
</head>
<body>

<!-- 为ECharts准备一个具备大小（宽高）的Dom -->
<div id="main" style="height:400px;"></div>

</body>
</html>

<script src="//cdn.bootcss.com/jquery/3.1.1/jquery.min.js"></script>

<script src="http://echarts.baidu.com/build/dist/echarts-all.js"></script>

<script>

var myChart = echarts.init(document.getElementById('main'));

var cityMap = {
    "长春市": "220100",
};

var curIndx = 0;

var mapType = [];

var mapGeoData = echarts.util.mapData.params;

var myChart = echarts.init(document.getElementById('main'));

console.log(mapGeoData)
for (var city in cityMap) {
    mapType.push(city);
    // 自定义扩展图表类型
    mapGeoData.params[city] = {
        getGeoJson: (function (c) {
            var geoJsonName = cityMap[c];
            return function (callback) {
                $.getJSON('./geoJson/china-main-city/' + geoJsonName + '.json', callback);
            }
        })(city)
    }
}

var ecConfig = echarts.config;
var zrEvent = zrender.tool.event;

/*
干掉滚轮切换
document.getElementById('main').onmousewheel = function (e){
    var event = e || window.event;
    curIndx += zrEvent.getDelta(event) > 0 ? (-1) : 1;
    if (curIndx < 0) {
        curIndx = mapType.length - 1;
    }
    var mt = mapType[curIndx % mapType.length];
    option.series[0].mapType = mt;
    option.title.subtext = mt + ' （滚轮或点击切换）';
    myChart.setOption(option, true);
    zrEvent.stop(event);
};*/

myChart.on(ecConfig.EVENT.MAP_SELECTED, function (param){
    curIndx++;
    var mt = mapType[curIndx % mapType.length];
    option.series[0].mapType = mt;
    option.title.subtext = mt + ' （点击进入）';
    myChart.setOption(option, true);
    alert(param.target);
    console.log(param);
});

option = {
    title: {
        text : '长春'
    },
    tooltip : {
        trigger: 'item',
        formatter: '点击进入<br/>{b}'
    },
    series : [
        {
            name: '长春',
            type: 'map',
            mapType: '长春市',
            selectedMode : 'single',
            itemStyle:{
                normal:{label:{show:true}},
                emphasis:{label:{show:true}}
            },
            data:[]
        }
    ]
};

myChart.setOption(option);
</script>
