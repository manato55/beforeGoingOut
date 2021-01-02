<div class="">
   <span>登録項目数
       <span class="note">（最大9個、20文字以内）</span>
   </span>
   <span class="delete_link">
       <?= $this->Html->link('削除ページ',[
           'action' => 'deletepage',
       ])
       ?>
   </span>
        <?= $this->Form->submit('追加',[
            'id' => 'add'
        ])
        ?>
        <?= $this->Form->create($check,[
            'type' => 'post',
            'url' => [
                'controller' => 'checks',
                'action' => 'register',
            ]
        ])?>
        <p class="regi_name">登録名<span class="title_count"></span></p>
        <?= $this->Form->control('登録名',[
            'type' => 'text',
            'name' => 'title',
            'id' => 'title',
            'label' => ''
        ])
        ?>
        <?= $this->Form->error('title') ?>
        <div class="sub_container"></div>
        <?= $this->Form->button('送信',[
             'id'=>'sendBtn'
         ])
        ?>
        <?= $this->Form->end()?>
</div>

<script>
    'use strict';
    {
        //defalutでは送信ボタン非表示
        $('#sendBtn').hide();

        //登録名の文字数カウント
        $('#title').keyup(function() {
            let count = $(this).val().length;
            $('.title_count').text(count);
        })

        let box = $('.sub_container');
        let num = 0;

        $('#add').on('click', function() {
            //追加ボタンを押す度にカウントアップ
            $(this).data('click', ++num);

            let input = $('<input>', {
                type: 'text',
                class: 'added_input_box_'+ num,
                name: 'num_' + num,
            });
            //numの数と比較するために作成
            let hidden = $('<input>', {
                type: 'hidden',
                class: 'hidden_'+ num,
                value: num
            });
            //文字カウントのためのタグ
            let span = $("<span></span>", {"class": "count_" + num});
            //チェック項目を削除するボタン
            let delInput = $(`<span class=del_${num}>☓</span>`);
            //先頭の番号とピリオドを入れるたのタグ
            let tag = $(`<span class=tag_${num}>${num}.</span>`);

            box.append($(`<div class=inputBox_${num}>`).append(tag,delInput,span,input,hidden));

            //新規追加されたtextボックス以外の削除ボタンは削除する
            for(let j=1;j<=num;j++) {
                $('.hidden_'+j).val() != num ?  $('.del_'+j).remove(): false;
            }

            //追加ボタンによって後から追加された要素のクリックイベントの処理の仕方
            $('.inputBox_'+num).on('click','.del_'+num,function() {
                //削除ボタン「☓」を押すとそのtextボックスを削除する
                $('.inputBox_'+num).remove();

                //削除したら、残ったtextボックスに削除ボタン「☓」を加えるためにnumから−１
                num--;

                //numを−１したため、再度、削除ボタンを定義
                delInput = $(`<span class=del_${num}>☓</span>`);

                //hiddenの値とnumの値が一致するば、そのtextボックスが一番下ということなので、そのtextボックスに削除ボタン「☓」を加える
                for(let j=1;j<=num;j++) {
                    $('.hidden_'+j).val() != num ? $('.del_'+j).remove():  $('.tag_'+num).after(delInput);
                }
                
                //9回クリックしたらクリックボタンを無効にする
                num == 9 ? $('#add').prop('disabled', true): $('#add').prop('disabled', false);
               
                //numの値が１以上であれば送信ボタンを表示する
                num != 0 ? $('#sendBtn').show(): $('#sendBtn').hide(); 

            })

            //9回クリックしたらクリックボタン無効。追加ボタンを押したときと、削除ボタンを押したときの２つ処理があるため二回同じ処理を記載
            if(num == 9) {
                $('#add').prop('disabled', true);
            } else {
                $('#add').prop('disabled', false);
            }

            //numの値が１以上であれば送信ボタンを表示する。追加ボタンを押したときと、削除ボタンを押したときの２つ処理があるため二回同じ処理を記載
            num != 0 ? $('#sendBtn').show(): $('#sendBtn').hide(); 

            //チェック項目の文字数カウント
            for(let i = 1; i <= num; i++) {
                $('.added_input_box_'+i).keyup(function() {
                    let count = $(this).val().length;
                    $('.count_'+i).text(count);
                });
            }
        });   

        //送信ボタンをクリックしたときの処理
        $('#sendBtn').on('click',function(e) {
            //カウントの初期化
            let cnt = 0;

            //セレクトボックスで選択した数値分だけテキストボックスの内容を取得
            for(let i = 1; i <= num; i++) {
                let input_val = $('.added_input_box_'+i).val();

                //テキストボックスに一つでも未入力の項目があればアラートして処理を停止
                if(!input_val || !$('#title').val()) {
                    alert('未入力の項目があります');
                    e.preventDefault();
                    break;
                }

                //文字数20文字以内に制御
                if($('#title').val().length > 20 || input_val.length > 20) {
                    alert('登録名および登録項目は20文字以内にしてください');
                    e.preventDefault();
                    break;
                }

                //テキストボックスに内容が入力されていれば変数cntに＋１加える
                input_val !== '' ? cnt++ : false;

                //cntの数とセレクトボックスで入力した数値が一致すれば送信可能とする
                if(cnt == num && !confirm('送信しますか？')) {
                    return false;
                }            
            }
        });



    }








</script>