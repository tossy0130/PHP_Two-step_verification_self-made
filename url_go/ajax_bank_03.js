$(function(){

    $('#bank_siten_code').on('input', function(event){

        $.ajax({
            // リクエスト方法
            type:"GET",
            // 送信先ファイル名
            url:"ajax_bank_03.php",
            // 受け取りデータの種類
            datatype: "json",
            // 送信データ
            data: {
                "bank_code": $('#bank_code').val(), 
                "bank_num": $('#bank_siten_code').val()
            },

            // 通信が成功した時
            success: function(data) {

                $('#bank_siten_name').val(data[0].bank_name_02);
                    
                    console.log("通信成功");
                    console.log(data);
            },

            error: function(data) {

                console.log("通信失敗");
                console.log(data);
            },

        });

        return false;
    });


});