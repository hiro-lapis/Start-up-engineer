$(function(){

  //unique選択時に背景画像表示
  var $uniquebtn = $('.js-showbg');
  $uniquebtn.on('change', function(){
    console.log('selected');
    var $this = $(this);
    var selectedunique = $this.data('unique');
    console.log(selectedunique);
    $('#js-bg').removeClass();
    $('#js-bg').addClass(selectedunique);
  });

  //uniquボタンホバー時に吹き出し表示
  var $btnlabel = $('.js-hover__disp');
  $btnlabel.on('mouseover', function(){
    console.log('On');
    var $this = $(this);
    $this.children('.hidden').addClass("visible");
  });
   $btnlabel.on('mouseleave', function(){
    var $this = $(this);
    $this.children('.hidden').removeClass("visible");
  } );

  //スタート時に自動でモーダル表示
      var modalbtn = $('.js-modal-btn');
      var modalbody = $('.js-modal-body');
      var modalcover = $('.js-modal-cover');
      var modalcancel = $('.js-cancel-btn');

      if(autoModalFlg){
          console.log('autoModalOn');//TODO　本番消去
          modalbody.add(modalcover).show();
          modalbody.add(modalcover).delay(10000).fadeOut(1000);
        } else {
      return false;
     };

      modalbtn.on('click', () => {
          console.log('On');//TODO　本番消去
          var modalwidth = $('.js-modal-body').width();//モーダルの大きさ
          var windowwidth = $(window).width();//ウィンドウの大きさ
          modalbody.attr('style',
           'margin-left: ' + (windowwidth/2 - modalwidth/2) + 'px' );
          modalbody.fadeIn();
          modalcover.fadeIn();
      });
      modalcancel.add(modalcover).on('click', () => {
          //TODO　add()でイベントDOMを複数設定
          modalbody.fadeOut();
          modalcover.fadeOut();
      });







//ピンチの時、HP赤く、主人公のイメージパス書き換え
//data属性を取得して使う

//ハッピーエンドで紙吹雪

//エンディングのトグルメッセージで一口アドバイス
//:hiddenフィルタを使う
//スマホの場合はスライドでヒント画像表示

//終わった後にボタンをいいねオアイマイチボタン設置ajaxでアンケートとり、DBへ接続l
//処理終了後はオープニングへ。ラジオボタンでchange発火
//処理が終わったらリスタートボタンをsbumit();

//イベントコマンドじに
});
