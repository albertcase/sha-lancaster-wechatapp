
function lotterydraw(){
    $.ajax({
        url:"/Request.php?model=lotterydraw",
        type:"post",
        dataType:"json",
        success:function(data){

            if(data.code==1){
                window.location="#congratulation";
            }else if(data.code==2){
                $(".regret_tips").show();
            }else{
                alert("没有抽奖机会了，请分享后获取更多机会")
            }

        }


    })
}

var sharval = true;


var SHAKE_THRESHOLD = 1000;  
var last_update = 0;  
var x = y = z = last_x = last_y = last_z = 0;  
function init() {  
    if (window.DeviceMotionEvent) {  
        window.addEventListener('devicemotion', deviceMotionHandler, false); 
    } else {  
        alert('not support mobile event');  
    }  
}  
function deviceMotionHandler(eventData) {  
    var acceleration = eventData.accelerationIncludingGravity;  
    var curTime = new Date().getTime();  
    if ((curTime - last_update) > 100) {  
        var diffTime = curTime - last_update;  
        last_update = curTime;  
        x = acceleration.x;  
        y = acceleration.y;  
        z = acceleration.z;  
        var speed = Math.abs(x + y + z - last_x - last_y - last_z) / diffTime * 10000;  

        if (speed > SHAKE_THRESHOLD) {  
            window.removeEventListener('devicemotion'); 
            if(sharval){
                lotterydraw();
                sharval=false;
            }else{

            }
        }  
        last_x = x;  
        last_y = y;  
        last_z = z;  
    }

}


$(document).on("pageinit","#index",function(){  
    init();
});   