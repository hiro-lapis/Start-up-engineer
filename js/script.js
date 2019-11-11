$(function() {
  //unique選択時に背景画像表示
  let $uniqueBtn = $(".js-showbg"),
    $mainDisplay = $(".js-unique-display"),
    $messageBox = $(".js-message-box");
  $uniqueBtn.on("change", function() {
    let $this = $(this),
      infoText,
      selectedUnique = $this.data("unique");

    switch (selectedUnique) {
      case "humor":
        infoText =
          "面白きことはよきことなり！楽観的なのでイイ事が起きやすいです";
        break;
      case "inspi":
        infoText =
          "ねぼすけで休みがち・・・だけど、センスがよく、やればできる子です";
        break;
      case "trainee":
        infoText = "お願いマッチョ！めっちゃモテた〜い馬力があります";
        break;
    }
    $mainDisplay.removeClass("humor inspi trainee");
    $mainDisplay.addClass(selectedUnique);
    $messageBox.text(infoText);
    $(".js-disable").prop("disabled", false);
  });

  //イベント時に自動でモーダル表示
  const modalCover = $(".js-modal-cover"); //ヘルプ用モーダルと共用
  const eventModal = $(".js-modal-auto");
  const these = eventModal.add(modalCover);

  if (autoModalFlg) {
    let winWidth = $(".l-window").width(),
      modalWidth = eventModal.innerWidth();
    eventModal.css("margin-left", winWidth / 2 - modalWidth / 2);
    these.show();
    setTimeout(function() {
      these.fadeOut(1000);
    }, 5000);
    these.on("click", function() {
      these.fadeOut(1000);
    });
  }

  //自動スクロール
  const $historyWindow = $(".js-messageBox");
  $(window).on("load", function() {
    const bottomHeight = $(".js-auto-scroll").height();
    $historyWindow.animate({ scrollTop: bottomHeight }, "fast");
  });

  //ヘルプ用モーダル
  var modalOn = $(".js-modal-on");
  var modalBody = $(".js-modal-help");
  var modalOff = $(".js-cancel-btn");
  modalOn.on("click", function() {
    let winWidth = $(".l-window").width(),
      modalWidth = modalBody.innerWidth();

    modalBody.css("margin-left", winWidth / 2 - modalWidth / 2);
    modalBody.fadeIn();
    modalCover.fadeIn();
  });

  modalOff.add(modalCover).on("click", function() {
    //TODO　add()でイベントDOMを複数設定
    modalBody.fadeOut();
    modalCover.fadeOut();
  });

  //ピンチ判定、HPカラーリング、主人公のイメージ書き換え
  const hpRemain = $(".js-color-").text();
  const hpStr = parseInt(hpRemain, 10);
  if (hpStr < 100) {
    $(".js-color-text").css("color", "#dc3545");
  }

  //紙吹雪
  let resultType = $(".js-result-type").text();
  if (resultType === "ハッピーエンド") {
    //canvas init
    const canvas = $("#canvas");
    let ctx = canvas[0].getContext("2d");

    //canvas dimensions
    let W = $(".js-canvas-target").innerWidth(); //windowにすると、画面いっぱいになる
    let H = $(".js-canvas-target").innerHeight();

    canvas.attr("width", W);
    canvas.attr("height", H);
    canvas.width = W;
    canvas.height = H;

    //snowflake particles
    let mp = 100; //max particles
    let particles = []; //配列
    for (var i = 0; i < mp; i++) {
      particles.push({
        x: Math.random() * W, //x-coordinate Math.random()は0~1の乱数を返すメソッド
        y: Math.random() * H, //y-coordinate
        r: Math.random() * 15 + 1, //radius
        d: Math.random() * mp, //density
        color:
          "rgba(" +
          Math.floor(Math.random() * 255) +
          ", " +
          Math.floor(Math.random() * 255) +
          ", " +
          Math.floor(Math.random() * 255) +
          ", 0.8)",
        tilt: Math.floor(Math.random() * 5) - 5
      });
    }

    //Lets draw the flakes
    function draw() {
      ctx.clearRect(0, 0, W, H);
      for (var i = 0; i < mp; i++) {
        let p = particles[i];
        ctx.beginPath();
        ctx.lineWidth = p.r;
        ctx.strokeStyle = p.color; // Green path
        ctx.moveTo(p.x, p.y);
        ctx.lineTo(p.x + p.tilt + p.r / 2, p.y + p.tilt);
        ctx.stroke(); // Draw it
      }
      update();
    }

    let angle = 0;
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
  }

  //リサイズに合わせてcanvas調整
  if (resultType === "ハッピーエンド") {
    $(window).on("resize", function() {
      let W = $(".js-canvas-target").innerWidth(); //windowにすると、画面いっぱいになる
      let H = $(".js-canvas-target").innerHeight();
      let canvas = $("#canvas");

      canvas.attr("width", W);
      canvas.attr("height", H);
      canvas.width = W;
      canvas.height = H;
    });
  }
});
