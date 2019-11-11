<?php
require('system.php');
?>

  <!DOCTYPE html>
  <html lang="ja">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="description" content="駆け出しエンジニアシミュレーションゲーム">
    <!-- SEO -->
    <meta name="twitter:card" content="summary" />
    <meta name="twitter:site" content="@etBeEP5e7dwmw7P" />
    <meta property="og:url" content="http://startup-engineer.xyz/" />
    <meta property="og:title" content="StartupEngineer" />
    <meta property="og:description" content="駆け出しエンジニアシミュレーションゲーム" />
    <meta property="og:image" content="http://startup-engineer.xyz/img/twitter/card.png" />
    <!-- CDN -->
    <link href="https://fonts.googleapis.com/css?family=Kosugi+Maru|Manjari&display=swap" rel="stylesheet">
    <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script><!--シェアボタン用-->
    <!-- CSS -->
    <link rel="stylesheet" href="./css/reset.css">
    <link rel="stylesheet" href="./css/style.css">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.2/animate.min.css">
    <title>StartupEngineer</title>
  </head>
  <body>
    <?php if($restartFlg){ ?>

      <section class="l-window">

        <div class="c-main-display c-main-display--bg op">

          <h2 class="c-main-display__head c-title">Start up engineer</h2>

        </div>
        
        <div class="c-sub-display">
          <form class="c-btn-box" method="post">
                  <button class="animated bounce slower infinite c-btn--wide c-btn--input c-btn--config" type="submit" name="setting" value="setting">Play</button>
          </form>

            <div class="c-message-box">
              <p>エンジニアになろうと決心したアイちゃん！</p>
              <p>頑張ってプログラミングに取り組んでるけど,日常にはトラブルやジャマ者がいっぱい！</p>
              <p>障害を乗り越え、誘惑を振り切り、ハイクオリティなポートフォリオを作ろう！</p>
              <div class="c-message--supplyer">
                <p>画像提供：<a class="p-text-link" href="https://ai-catcher.com/">アイキャッチャー</a></p>
              </div>
            </div>


        </div>
      </section>

    <?php  } else if($settingFlg){ ?>

      <section class="l-window">

        <div class="js-unique-display c-main-display c-main-display--bg">
            <h2 class="c-main-display__head c-title">ユニークセレクト</h2>
        </div>
          <div class="l-sub-display">


            <form class="c-btn-box" method="post">
                  <label class="c-btn--wide  c-btn--label c-btn--humor"><input class="js-showbg c-btn--radio " data-unique="humor" type="radio" name="unique" value="humor">ユーモア</label>
                  <label class="c-btn--wide c-btn--label c-btn--inspi"><input class="js-showbg c-btn--radio " data-unique="inspi" type="radio" name="unique" value="inspiration">ひらめき</label>
                  <label class="c-btn--wide c-btn--label c-btn--trainee"><input class="js-showbg c-btn--radio" data-unique="trainee" type="radio" name="unique" value="trainee">筋トレ好き</label>
                  <input class="js-disable c-btn--wide  c-btn--input c-btn--config" type="submit" name="start" value="スタート" disabled="disabled">
            </form>

            <div class="c-message-box">
              <p class="js-message-box">ユニークセレクト<br>
                ユニークによって主人公の能力値が変わります<br>
                迷ったら筋トレを選ぶのがオススメ！</p>
            </div>

        </div>
      </section>

    <?php } elseif ($resultFlg) { ?>

      <section class="js-canvas__target l-window">

        <?php if($_SESSION['resultTitle'] === 'ハッピーエンド'){ ?>
          <canvas id="canvas" class="c-canvas" width="" height="">ハッピーエンドの紙吹雪</canvas>
        <?php } ?>

        <!-- メインディスプレイ -->
          <div class="js-canvas-target c-main-display c-main-display--bg <?php echo $_SESSION['resultImg']; ?>">
              <!-- リザルトタイトル -->
                <h2 class="js-result-type c-main-display__head c-title"><?php  echo $_SESSION['resultTitle']; ?></h2>
                <?php if($_SESSION['resultTitle'] === 'ハッピーエンド'){ ?><p class="c-message--cheer">Thanks for playing! It's your turn now!</p><?php } ?>
          </div><!-- ./main-display -->


          <!-- サブディスプレイ -->
          <div class="l-sub-display">
              <form class="c-btn-box" method="post">
                  <!-- <a href="https://twitter.com/share?ref_src=twsrc%5Etfw" class="twitter-share-button" data-show-count="false" data-text="#StartupEngineer #<?php echo $_SESSION['resultTitle'];?>" data-url="http://startup-engineer.xyz/">Tweet</a> -->
                  <input class="c-btn--config c-btn--wide" type="submit" name="restart" value="restart">
              </form>

            <div class="js-message-box c-message-box">
              <p><?php echo (!empty($_SESSION['history'])) ? $_SESSION['history'] : ''; ?></p>
            </div>
          </div><!-- ./l-sub-display  -->

      </section>

    <?php  } elseif ($startFlg || $actionFlg) { ?>

      <section class="l-window">

        <!-- モーダルカバー -->
        <div class="js-modal-cover c-modal-bg"></div>

            <!-- ヘルプ/リセットモーダル -->
            <div class="js-modal-help c-modal c-modal--info">
              <h2 class="c-title--modal">〜ゲームの流れ〜</h2>
              <p>主人公の行動を選択して、ポートフォリオを完成させましょう</p>
              <ol>
                <li>コマンドについて</li>
                <ul>
                  <li>アタック:がんばって仕事をします（同僚のHPダウン）同僚のHPを0にするとアイちゃんが成長しますが、仕事はもっと大変になります</li>
                  <li>コーディング:コードを打って成果物を作りましょう。HPが少し減ります</li>
                  <li>筋トレ:トレーニングしてパワーUP。やり過ぎると・・・？</li>
                  <li>レスト：休んでHPを回復します</li>
                  <li>イベント:不定期でアタックの代わりに選択できます。イイ事があるか、災難に会うかは運次第！</li>
                </ul>
                <li>ステータス</li>
                <ul>
                  <li>HP:0になるとゲームオーバー（敵を倒せる）です</li>
                  <li>パワー:高いほどアタック、コーディング力がアップ</li>
                </ul>
                <li>＊ヒント＊</li>
                <ul>
                  <li>目的は<strong>ポートフォリオを完成させること</strong>です</li>
                  <li>仕事をがんばるのは程々にしておきましょう</li>
                </ul>
              </ol>
              <form class="c-modal__bottom">
                <button class="js-cancel-btn c-btn--config c-btn--md" type="button" >閉じる</button>
                <button class="c-btn--reset c-btn--md" type="submit" name="restart" value="restart">リセット</button>
             </form>
          </div><!-- -./js-modal-info -->

        <!-- イベントモーダル -->
        <div class="js-modal-auto c-modal--small c-modal--event">

          <h2 class="c-modal--event__head c-title--modal"><?php echo (isset($_SESSION['eventTitle'])) ? $_SESSION['eventTitle'] : ''; ?></h2>
          <img class="c-modal--event__body c-img--event-modal" src="img/event/ev<?php if(!empty($_SESSION['eventNum'])){ echo $_SESSION['eventNum'];}?>.png" alt="イベント画像" >

          <div class="c-message-box--sm">
            <?php if(!empty($_SESSION['event'])){ ?>
                <p><?php echo $_SESSION['event']; ?></p>
            <?php } ?>
          </div>

        </div><!-- ./c-modal--event -->


        <!--/////////////////// 通常ゲーム画面 /////////////////-->

        <div class="js-main-display c-main-display c-main-display--bg--<?php echo $_SESSION['hero']->getUnique(); ?>">

          <div class="c-main-display__head p-text--day-count">
            <span class=""><?php echo $_SESSION['turnCount']; ?>日目</span>
          </div>

          <div class="c-main-display__body">

          <!-- 主人公イメージボックス -->
          <div class="p-character-box">
            <div class="p-character-box__head c-img__clip">
                <img class="js-hero-icon c-img--hero<?php echo $_SESSION['hero']->getUnique(); ?>" src="<?php echo $_SESSION['hero']->getImg(); ?>" alt="主人公アイコン">
            </div>
            <div class="p-character-box__body">
              <p class="p-text--name">Name: <?php echo $_SESSION['hero']->getName(); ?></p>
              <p class="p-text--hp--hero">HP:<span class="js-color-text"><?php echo $_SESSION['hero']->getHp();?></span> <br class="sp-br">パワー: <?php echo $_SESSION['hero']->getAttMin(); ?></p>
            </div>
          </div><!-- ./主人公 -->


          <!-- 同僚イメージボックス -->
          <div class="p-character-box">
              <div class="c-character-box__head c-img__clip">
                  <img class="p-character-box__head c-img--enemy" src="<?php echo $_SESSION['enemy']->getImg(); ?>" alt="敵アイコン">
              </div>

            <div class="p-character-box__body">
              <p class="p-text--name"> Name:<br class="sp-br"><?php echo $_SESSION['enemy']->getName(); ?></p>
              <p class="p-text--hp">タスク量:<?php echo $_SESSION['enemy']->getHp(); ?> <br class="sp-br">忙しさ: <?php echo $_SESSION['enemy']->getAttMin(); ?></p>
            </div>
          </div><!-- ./同僚 -->

        </div><!-- ./c-main-display__body  -->
        
      </div><!-- ./l-main-display -->

      <!-- sub-displayここから -->
    <div class="l-sub-display">

      <!-- コマンドボックス -->
      <form class="c-btn-box" name="action" method="post">
        <!-- アタックコマンドのみ、イベントフラグで表示切り替え-->
        <?php if($eventFlg){ echo '<button class="animated flash c-btn--wide c-btn--event" type="submit" name="action" value="event">イベント</button>';
          } else {
          echo '<button class="c-btn--wide c-btn--work" type="submit" name="action" value="attack">アタック</button>';} ?>

        <button class="c-btn--wide c-btn--coding"type="submit" name="action" value="coding">コード</button>
        <button class="c-btn--wide c-btn--training"type="submit" name="action" value="training">筋トレ</button>
        <button class="c-btn--wide c-btn--rest"type="submit" name="action" value="rest">レスト</button>
        <button class="c-btn--wide js-modal-on c-btn--config" type="button">遊び方/リセット</button>
      </form>

          <div class="js-messageBox c-message-box">
              <p class="js-auto-scroll"><?php echo (!empty($_SESSION['history'])) ? ($_SESSION['history']) : '';  ?></p>
          </div>
        </div><!-- ./l-sub-display -->

      </section>


      <!--/////////////////// ゲーム画面ここまで /////////////////-->

      <?php } ?>

      <!-- イベント時のみ、オートモーダルオン -->
      <script type="text/javascript">
      <?php if(isset($_SESSION['eventNum'] ) ){ ?>
        let autoModalFlg = true;
      <?php } else { ?>
        let autoModalFlg = false;
      <?php }; ?>
      </script>

      <script type="text/javascript" src="js/jquery-3.4.1.min.js"></script>
      <script type="text/javascript" src="js/script.js"></script>

</body>
</html>
