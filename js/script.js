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

  //イベント時に自動でモーダル表示
  var eventModal = $('.js-modal-auto');
  var modalCover = $('.js-modal-cover');//ヘルプ用モーダルと共用

  if(autoModalFlg){
    console.log('autoModalOn');//TODO　本番消去
    console.log(autoModalFlg);
    eventModal.add(modalCover).show();
    eventModal.add(modalCover).delay(5000).fadeOut(1000);
  };
  //ヘルプ用モーダル
  var modalOn = $('.js-modal-on');
  var modalBody = $('.js-modal-help');
  var modalOff = $('.js-cancel-btn');
  modalOn.on('click', function() {
    console.log('On');//TODO　本番消去
    modalBody.fadeIn();
    modalCover.fadeIn();
  });
  modalOff.add(modalCover).on('click', function() {
    //TODO　add()でイベントDOMを複数設定
    modalBody.fadeOut();
    modalCover.fadeOut();
  });

  //ピンチ判定、HPカラーリング、主人公のイメージ書き換え
  var hpRemain = $('.js-color-hp').text();
  var hpStr = parseInt(hpRemain, 10);
  console.log(typeof hpStr);
  if(hpStr < 100){
    console.log('change:Red');
    $('.js-color-hp').css('color', '#dc3545');
  };


  //紙吹雪
  var resultType = $('.js-flake').text();
  console.log(resultType);
  if(resultType === 'ハッピーエンド'){
    console.log('flakeOn');//TODO 本番削除
    //canvas init
    var canvas = $("#canvas");
    var ctx = canvas[0].getContext("2d");

    //canvas dimensions
    var W = $('#js-canvas__target').innerWidth();//windowにすると、画面いっぱいになる
    var H = $('#js-canvas__target').innerHeight();

    canvas.attr("width", W);
    canvas.attr("height", H);
    canvas.width = W;
    canvas.height = H;

    //snowflake particles
    var mp = 100; //max particles
    var particles = [];//配列
    for (var i = 0; i < mp; i++) {
      particles.push({
        x: Math.random() * W, //x-coordinate Math.random()は0~1の乱数を返すメソッド
        y: Math.random() * H, //y-coordinate
        r: Math.random() * 15 + 1, //radius
        d: Math.random() * mp, //density
        color: "rgba(" + Math.floor((Math.random() * 255)) + ", " + Math.floor((Math.random() * 255)) + ", " + Math.floor((Math.random() * 255)) + ", 0.8)",
        tilt: Math.floor(Math.random() * 5) - 5
      });
    }

    //Lets draw the flakes
    function draw() {
      ctx.clearRect(0, 0, W, H);
      for (var i = 0; i < mp; i++) {
        var p = particles[i];
        ctx.beginPath();
        ctx.lineWidth = p.r;
        ctx.strokeStyle = p.color; // Green path
        ctx.moveTo(p.x, p.y);
        ctx.lineTo(p.x + p.tilt + p.r / 2, p.y + p.tilt);
        ctx.stroke(); // Draw it
      }
      update();
    }

    var angle = 0;
    function update() {
      angle += 0.01;
      for (var i = 0; i < mp; i++) {
        var p = particles[i];
        p.y += Math.cos(angle + p.d) + 1 + p.r / 2;
        p.x += Math.sin(angle) * 2;

        if (p.x > W + 5 || p.x < -5 || p.y > H) {
          if (i % 3 > 0) {
            particles[i] = {
              x: Math.random() * W,
              y: -10,
              r: p.r,
              d: p.d,
              color: p.color,
              tilt: p.tilt
            };
          } else {
            if (Math.sin(angle) > 0) {
              //Enter from the left
              particles[i] = {
                x: -5,
                y: Math.random() * H,
                r: p.r,
                d: p.d,
                color: p.color,
                tilt: p.tilt
              };
            } else {
              //Enter from the right
              particles[i] = {
                x: W + 5,
                y: Math.random() * H,
                r: p.r,
                d: p.d,
                color: p.color,
                tilt: p.tilt
              };
            }
          }
        }
      }
    }
    setInterval(draw, 20);
  } ;

  //自動スクロール
  var $historyWindow = $('.js-auto-scroll');
  $historyWindow.animate({scrollTop: $historyWindow[0].scrollHeight}, 'fast');
  height = $('.js-auto-scroll').scrollHeight;
  //TODO 終わった後にボタンをいいねオアイマイチボタン設置ajaxでアンケートとり、DBへ接続l
  //TODO 処理が終わったらリスタートボタンをsubmit();

  //TODO イベントコマンド
});
