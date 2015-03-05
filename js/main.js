
;(function($){
    $(function(){
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
                	
                    alert("摇动了");  
                }  
                last_x = x;  
                last_y = y;  
                last_z = z;  
            }

        }


        $(document).on("pageinit","#index",function(){  
  			init()
		});   
    })
})(jQuery)