$(function () {
    
    var d;
    
    //   $('#ajax_show').on('click', function(){
    // blur
    //    $('#bank_code').on('blur', function(event){
    //    $('#bank_code').on('change', function (event) {
    //    $("#bank_code").change(function () {
    $('#bank_code').on('input', function (event) {
        
        $.ajax({
            // リクエスト方法
            type: "GET",
            // 送信先ファイル名
            url: "ajax_bank_01.php",
            // 受け取りデータの種類
            datatype: "json",
            // 送信データ
            data: {
                "bank_code": $('#bank_code').val()
            },
            // 通信が成功した時
            success: function (data) {
                
                $('#bank_name').val(data[0].bank_name);
                

                d = data[0].bank_name;

                // #bank_name を　クリックしたことにする
               // $('#bank_name').trigger("click");
                
                // フォーカス移動
            //    $('#bank_name').focus();
                
                /*
                if ($('#bank_name') != "") {
                    $('#bank_name').focus();
                }
                */

                console.log("通信成功");
                console.log(data);
                
            },

            error: function (data) {
            
                console.log("通信失敗");
                console.log(data);
            },

        });

        return false;

    });

   // $('#bank_name').val(d.bank_name);
    $('#bank_name').text(d);
    
   
   
});
