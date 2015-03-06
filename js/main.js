$(function(){
  $("input").focus(function(){
    $(".formTips").hide();
  });  
})


islogin();
//屏幕方向标识，0横屏，其他值竖屏
var orientation=0;
//转屏事件，内部功能可以自定义
var henghtml = "<div id='heng'></div>";
function screenOrientationEvent(){
    if(orientation == 0){
      var heng = document.getElementById('heng');
      heng.parentNode.removeChild(heng);
    }else {
      console.log(henghtml)
      var container = document.getElementById('container');
      container.innerHTML += henghtml;
    }
}
var innerWidthTmp = window.innerWidth;
//横竖屏事件监听方法
function screenOrientationListener(){
    try{
        var iw = window.innerWidth;     
        //屏幕方向改变处理
        if(iw != innerWidthTmp){
            if(iw>window.innerHeight)orientation = 90;
            else orientation = 0;
            //调用转屏事件
            screenOrientationEvent();
            innerWidthTmp = iw;
        }
    } catch(e){//alert(e);
    };
    //间隔固定事件检查是否转屏，默认500毫秒
    setTimeout("screenOrientationListener()",500);
}
//启动横竖屏事件监听
screenOrientationListener();

function islogin(){
    $.ajax({
        url:"/Request.php?model=islogin",
        type:"post",
        dataType:"json",
        success:function(data){
            if(data.code==0){
                window.location="/"
            }else if(data.msg.lotterystatus!=0){
                //已经中过奖
                if(data.msg.name==""){
                    window.location="#congratulation";
                    return false;
                }
                $(".lottery_tips").show();
            }
            

        }


    })
}
function lotterydraw(){
    $.ajax({
        url:"/Request.php?model=lotterydraw",
        type:"post",
        dataType:"json",
        success:function(data){

            if(data.code==1){
                $("#lotname").html(data.msg);
                window.location="#congratulation";
            }
            else if(data.code==2){
                $(".mask").show();
                $(".regret_tips").show();
            }else{
                $(".mask").show();
                $(".jihui_tips").show();
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



var errtxt = {
            "name" : "请输入您的姓名",
            "mobile" : "请输入您的手机号",
            "check" : "请填写正确手机号",
            "address" : "请输入您的地址",
            

        }
function submitform(){

    var name = $("#name").val();
    var mobile = $("#mobile").val();
    var address = $("#address").val();

    if(name==""){
        $("#name").addClass("errorTips");
        $("#name").val("").attr("placeholder",errtxt.name);
        $(".formTips").show().html("请填写您的姓名信息");
        return false;

    }
    if(address==""){
        $("#address").addClass("errorTips");
        $("#address").val("").attr("placeholder",errtxt.address);
        $(".formTips").show().html("请填写您的地址信息");
        return false;
    }
    if(mobile==""){
        $("#mobile").addClass("errorTips");
        $("#mobile").val("").attr("placeholder",errtxt.mobile);
        $(".formTips").show().html("请填写您的手机信息");
        return false;
    }
    if(!mobile.match(/13[0-9]{9}|14[0-9]{9}|15[0-9]{9}|18[0-9]{9}/)){
        $("#mobile").addClass("errorTips");
        $("#mobile").val("").attr("placeholder",errtxt.check);
        $(".formTips").show().html("请填写您的手机信息");
        return false;
    }
    
        //$( ".tips" ).css( 'display', 'block');
    $.ajax({
        type: "POST",
        url: "/Request.php?model=finish",
        data: {
          "name": name,
          "mobile":mobile,
          "address":address
        },
        dataType:"json",
        success: function(data){
           if(data.code==1){
                $(".mask").show();
           		$(".success_tips").show();
                $(".formTips").hide();

                //返回首页
                window.history.length = 0;
                window.history.pushState( {} , 'lancaster', '/' );
                
           }
           else if(data.code==0){
                window.location="/";
           }
           else{
                alert(data.msg);
           }
           //window.location.href="question.html"

        }
      }); 
}




/* ----------- 分享js ----------- */

$.ajax({  
        type: "GET",  
        url: "/access_token/access_token.php", //orderModifyStatus 
        data:{url:window.location.href},
        dataType:"json",  
        async:false,  
        cache:false,  
        success: function(data){  
       wechatShare(data.time,data.sign);
        },  
        error: function(json){  
            //alert("数据获取异常，请刷新后重试...");  
        }  
 });  



function wechatShare(timestamp_val,signature_val){
  
  var SHARE_TITLE = '兰嘉丝汀美白大作战';
  var SHARE_LINK = 'http://lancasterld.samesamechina.com';
  var SHARE_IMG = 'http://lancasterld.samesamechina.com/images/share.jpg';
  var SHARE_DESC = '摇出千元大礼，摇出美白好运，快来帮我成为那个幸运儿吧';
  wx.config({
      debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
      appId: 'wx77e36122ac152d47', // 必填，公众号的唯一标识
      timestamp: timestamp_val, // 必填，生成签名的时间戳
      nonceStr: 'asdkhaedhqwui', // 必填，生成签名的随机串
      signature: signature_val,// 必填，签名，见附录1
      jsApiList: [
        'checkJsApi',
        'onMenuShareTimeline',
        'onMenuShareAppMessage',
        'onMenuShareQQ',
        'onMenuShareWeibo',
        'hideMenuItems',
        'showMenuItems',
        'hideAllNonBaseMenuItem',
        'showAllNonBaseMenuItem',
        'translateVoice',
        'startRecord',
        'stopRecord',
        'onRecordEnd',
        'playVoice',
        'pauseVoice',
        'stopVoice',
        'uploadVoice',
        'downloadVoice',
        'chooseImage',
        'previewImage',
        'uploadImage',
        'downloadImage',
        'getNetworkType',
        'openLocation',
        'getLocation',
        'hideOptionMenu',
        'showOptionMenu',
        'closeWindow',
        'scanQRCode',
        'chooseWXPay',
        'openProductSpecificView',
        'addCard',
        'chooseCard',
        'openCard'
      ] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
  });

  wx.ready(function(){
    //朋友圈
    // config信息验证后会执行ready方法，所有接口调用都必须在config接口获得结果之后，config是一个客户端的异步操作，所以如果需要在页面加载时就调用相关接口，则须把相关接口放在ready函数中调用来确保正确执行。对于用户触发时才调用的接口，则可以直接调用，不需要放在ready函数中。
    wx.onMenuShareTimeline({
        title: SHARE_DESC, // 分享标题
        link: SHARE_LINK, // 分享链接
        imgUrl: SHARE_IMG, // 分享图标
        success: function () {
            $.ajax({  
		        type: "GET",  
		        url: "/Request.php?model=share", //orderModifyStatus 
		        data:{source:"timeline"},
		        dataType:"json",  
		        async:false,  
		        cache:false,  
		        success: function(data){ 

                    ga('send','event','auction', 'timeline' );
                    
                    window.location="/";


		        },  
		        error: function(json){  
		            //alert("数据获取异常，请刷新后重试...");  
		        }  
		 	});  
        },
        cancel: function () { 
            // 用户取消分享后执行的回调函数
            // alert("分享失败")
        }
    });
    
    //好友
    wx.onMenuShareAppMessage({
        title: SHARE_TITLE, // 分享标题
        link: SHARE_LINK, // 分享链接
        imgUrl: SHARE_IMG, // 分享图标
        desc: SHARE_DESC,
        success: function () { 
            $.ajax({  
		        type: "GET",  
		        url: "/Request.php?model=share", //orderModifyStatus 
		        data:{source:"message"},
		        dataType:"json",  
		        async:false,  
		        cache:false,  
		        success: function(data){  
                    ga('send','event','auction', 'message' );
                    window.location="/";


		        },  
		        error: function(json){  
		            //alert("数据获取异常，请刷新后重试...");  
		        }  
		 	}); 
        },
        cancel: function () { 
            // 用户取消分享后执行的回调函数
           // alert("分享失败")
        }
    });
      
  });

  wx.error(function(res){
    //alert("无法使用微信JS")
      // config信息验证失败会执行error函数，如签名过期导致验证失败，具体错误信息可以打开config的debug模式查看，也可以在返回的res参数中查看，对于SPA可以在这里更新签名。

  });




}























