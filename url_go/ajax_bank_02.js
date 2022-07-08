
//============== 銀行名前検索 ============
$(function(){

    //$('#ajax_show_02').on('click', function(){
    
        $('#bank_code_kensaku').on('input', function(event) {
     //   $('#id_name').keyup(function(event) {
    
        $.ajax({
            // リクエスト方法
            type: "GET",
            // 送信先ファイル名
            url: "ajax_bank_02.php",
            // 受け取りデータの種類
            datatype: "json",
            // 送信データ
            data: {
                "bank_name" : $('#bank_code_kensaku').val()
            },
          
                /*
                success: function(data) {
    
                    $('#result').html(
                        "<p>銀行code::::::" + data[0].bank_code + "銀行名::::::::" + data[0].bank_name + "</p>"
                        );
                */
    
        success :function(data) { 

            $.each(data, function(num, data){
            
              // テキストボックスが空だったら　子要素を削除
              if($('#bank_code_kensaku').val() == "") {
          //      $('#list').empty();
                 　 
                  
               $('#bank_kensaku_val').empty();

               $('#bank_kensaku_val').append('<option value="" class="jsc-default-selected">検索結果が入ります</option>');
          
              } else {
              
              // 空じゃなかったら ajax で　php にデータを投げて　検索する 

              $('#bank_kensaku_val').append('<option  class="jsc-default-selected" value="' + 
                    data.bank_code + ':' + data.bank_name + 
                    '"' + ">" + data.bank_code + ':' + data.bank_name + "</option>"

                 );

               }
                    

               /*
                $('#list').append("<li>" +
    
                '<input class="ajax_form" type="text" value=' + '"' + data.bank_code + '"' + '>' +
                '<input class="ajax_form" type="text" value=' + '"' + data.bank_name + '"' + '>' +
                
                    "コード:::" + data.bank_code +
                    "銀行名:::" + data.bank_name + "</li>"
                
                );

                console.log(data.bank_code);
                console.log(data.bank_name);
                console.log(num);

              }
              */

                });
    
            },
    
            error: function(data) {
                console.log("通信失敗");
                console.log(data);
            },
    
        });
    
        return false;
    
        });
    
    });
    