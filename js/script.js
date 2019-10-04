$(function(){

  //unique選択時に背景画像表示
  var $uniqueBtn = $('.js-showbg');
  $uniqueBtn.on('change', function(){
    console.log('selected');
    var $this = $(this);
    var selectedUnique = $this.data('unique');
    console.log(selectedUnique);
    $('#js-bg').removeClass();
    $('#js-bg').addClass(selectedUnique);
    $('.js-disable').prop('disabled', false);
  });

  //uniquボタンホバー時に吹き出し表示
  var $btnLabel = $('.js-hover__disp');
  $btnLabel.on('mouseover', function(){
    console.log('On');
    var $this = $(this);
    $this.children('.hidden').addClass("visible");
  });
  $btnLabel.on('mouseleave', function(){
    var $this = $(this);
    $this.children('.hidden').removeClass("visible");
  });

  //スタート時に自動でモーダル表示
  var modalBtn = $('.js-modal-btn');
  var modalBody = $('.js-modal-body');
  var modalCover = $('.js-modal-cover');
  var modalCancel = $('.js-cancel-btn');

  if(autoModalFlg){
    console.log('autoModalOn');//TODO　本番消去
    modalBody.add(modalCover).show();
    modalBody.add(modalCover).delay(10000).fadeOut(1000);
  };

  modalBtn.on('click', function() {
    console.log('On');//TODO　本番消去
    modalBody.fadeIn();
    modalCover.fadeIn();
  });
  modalCancel.add(modalCover).on('click', function() {
    //TODO　add()でイベントDOMを複数設定
    modalBody.fadeOut();
    modalCover.fadeOut();
  });

  //ピンチ判定、HPカラーリング、主人公のイメージ書き換え
  var hpRemain = $('.js-color-hp').text();
  console.log(hpRemain);
  var hpStr = parseInt(hpRemain, 10);
  console.log(typeof hpStr);
  if(hpStr < 100){
    console.log('change:Red');
    $('.js-color-hp').css('color', '#dc3545');
  };

  //自動スクロール
  var $historyWindow = $('.js-auto-scroll');
  $historyWindow.animate({scrollTop: $historyWindow[0].scrollHeight}, 'fast');
  height = $('.js-auto-scroll').scrollHeight;

  //ハッピーエンドで紙吹雪


  //リザルト画像表示
  var $result = $('.js-result-img');
  var resultType = $result.data('result');

  if(resultType　=== 'ハッピーエンド'){
    $result.addClass(resultType);
  } ;
  //TODO 終わった後にボタンをいいねオアイマイチボタン設置ajaxでアンケートとり、DBへ接続l
  //TODO 処理が終わったらリスタートボタンをsubmit();

  //TODO イベントコマンド
});
