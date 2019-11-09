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
    <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Kosugi+Maru|Manjari&display=swap" rel="stylesheet">
    <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script><!--シェアボタン用-->
    <!-- CSS -->
    <link rel="stylesheet" href="./css/style.css">
    <title>StartupEngineer</title>
  </head>
  <body>
    <?php if($restartFlg){ ?>

      <section class="l-window">
        <div class="l-main-display">
        </div>

          <div class="l-sub-display">

          <form class="c-command-box" method="post">
              <input class="c-btn--to-setting" type="submit" name="setting" value="setting">Start<br>up<br>Engineer</button>
          </form>

          <div class="c-message-box">
            <p>エンジニアになろうと決心したアイちゃん！</p>
            <p>頑張ってプログラミングに取り組んでるけど,日常にはトラブルやジャマ者がいっぱい！</p>
            <p>障害を乗り越え、誘惑を振り切り、ハイクオリティなポートフォリオを作ろう！</p>
            <p>画像提供：<a href="https://ai-catcher.com/">アイキャッチャー</a></p>
          </div>

        </div>
      </section>

    <?php  } else if($settingFlg){ ?>

      <section class="l-window">

        <div class="js-main-display l-main-display"></div>

          <div class="l-sub-display">


              <form class="c-command" method="post">
                  <input class="js-showbg c-command__btn c-command--unique-humor" data-unique="humor" type="radio" name="unique" value="humor">
                  <input class="js-showbg c-command__btn c-command--unique-inspi" data-unique="inspi" type="radio" name="unique" value="inspiration">
                  <input class="js-showbg c-command__btn c-command--unique-train" data-unique="trainee" type="radio" name="unique" value="trainee">
                  <input class="js-disable c-command__btn c-command--submit" type="submit" name="start" value="スタート" disabled="disabled">
              </form>

            <div class="js-message-box c-message-box">
              <p>ユニークセレクト<br>
                ユニークによって主人公の能力値が変わります<br>
                迷ったら筋トレを選ぶのがオススメ！</p>
            </div>

        </div>
      </section>

    <?php } elseif ($resultFlg) { ?>

      <section class="js-canvas__target l-window">

        <?php if($_SESSION['resultTitle'] === 'ハッピーエンド'){ ?>
          <canvas id="canvas" width="" height="">ハッピーエンドの紙吹雪</canvas>
        <?php } ?>

          <div class="js-main-display l-main-display">
            <img class="c-img--result" src="img/result/<?php echo $_SESSION['resultImg']; ?>.png" alt="">
            <p class="c-text--result"><?php echo $_SESSION['resultTitle']; ?></p>

            <?php if($_SESSION['resultTitle'] === 'ハッピーエンド'){ ?><p class="c-text--bottom">Thanks for playing! It's your turn now!</p><?php } ?>
          </div>

          <div class="l-sub-display">

              <form class="btn-wrap" method="post">
                  <input class="js-disable c-command__btn c-command--submit" type="submit" name="start" value="スタート" disabled="disabled">
                  <a href="https://twitter.com/share?ref_src=twsrc%5Etfw" class="twitter-share-button" data-show-count="false" data-text="#StartupEngineer #<?php echo $_SESSION['resultTitle'];?>" data-url="http://startup-engineer.xyz/">Tweet</a>
                  <input class="c-btn--submit c-btn--restart" type="submit" name="restart" value="restart">
              </form>

            <div class="js-message-box c-message-box">
              <p><?php echo (!empty($_SESSION['history'])) ? $_SESSION['history'] : ''; ?></p>
            </div>
          </div><!-- ./l-sub-display  -->

      </section>

    <?php  } elseif ($startFlg || $actionFlg) { ?>

      <section class="l-window">

        <div class="js-main-display l-main-display">
          <!-- モーダルカバー -->
          <div class="js-modal-cover modal-cover"></div>

            <!-- ヘルプ/リセットモーダル -->
            <div class="js-modal-help c-modal__info">
              <h2>〜ゲームの流れ〜</h2>
              <p>主人公の行動を選択して、ポートフォリオを完成させましょう</p>
              <ol>
                <li>コマンドについて</li>
                <ul>
                  <li>アタック:がんばって仕事をします（同僚のHPダウン）<br>同僚のHPを0にするとアイちゃんが成長しますが、仕事はもっと大変になります</li>
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
                <button class="js-cancel-btn btn btn-outline-secondary" type="button" >閉じる</button>
                <button class="c-btn--submit c-btn" type="submit" name="restart" value="restart">リセット</button>
             </form>
            </div><!-- -./js-modal-info -->

        <!-- イベントモーダル -->
        <div class="js-modal-auto c-modal--event">

          <h2 class="c-modal--event__head c-title--event"><?php echo (isset($_SESSION['eventTitle'])) ? $_SESSION['eventTitle'] : ''; ?></h2>
          <img class="c-modal--event__body c-img--event-modal" src="img/event/ev<?php if(!empty($_SESSION['eventNum'])){ echo $_SESSION['eventNum'];}?>.png" alt="イベント画像" >

          <div class="c-message-box--sm">
            <?php if(!empty($_SESSION['event'])){ ?>
                <p><?php echo $_SESSION['event']; ?></p>
            <?php } ?>
          </div>

        </div><!-- ./c-modal--event -->


        <!--/////////////////// 通常ゲーム画面 /////////////////-->

        <div class="p-main-display">
          <span class="p-main-display__head"><?php echo $_SESSION['turnCount']; ?>日目</span>
          <div class="p-main-display__body">

          <!-- 主人公イメージボックス -->
          <div class="p-character-box">
            <img class="js-hero-icon c-character-box__head c-img--hero <?php echo $_SESSION['hero']->getUnique(); ?>" src="<?php echo $_SESSION['hero']->getImg(); ?>" alt="主人公アイコン">
            
            <div class="p-character-box__body">
              <p class="p-text--name">Name: <?php echo $_SESSION['hero']->getName(); ?></p>
              <p class="p-text--hp--hero">HP:<span class="js-color-hp"><?php echo $_SESSION['hero']->getHp();?></span> <br class="sp-br">パワー: <?php echo $_SESSION['hero']->getAttMin(); ?></p>
            </div>

          </div><!-- ./主人公 -->

          <!-- 同僚イメージボックス -->
          <div class="p-character-box">
              <img class="p-character-box__head c-img--enemy" src="<?php echo $_SESSION['enemy']->getImg(); ?>" alt="敵アイコン">
            <div class="p-character-box__body">
              <p class="p-text--name"> Name:<br class="sp-br"><?php echo $_SESSION['enemy']->getName(); ?></p>
              <p class="p-text--hp">タスク:<?php echo $_SESSION['enemy']->getHp(); ?> <br class="sp-br">パワー: <?php echo $_SESSION['enemy']->getAttMin(); ?></p>
            </div>
          </div><!-- ./同僚 -->

          </div><!-- ./l-main-display -->


        <div class="l-sub-display">

                <form class="c-command" name="action" method="post">

                  <!-- アタックコマンドのみ、イベントフラグで表示切り替え-->
                  <?php if($eventFlg){ echo '<button class="c-button--event" type="submit" name="action" value="event">イベント</button>';
                    } else {
                    echo '<button class="c-button--attack" type="submit" name="action" value="attack">アタック</button>';} ?>

                  <button class="c-button--coding"type="submit" name="action" value="coding">コード</button>
                  <button class="c-button--training"type="submit" name="action" value="training">筋トレ</button>
                  <button class="c-button--rest"type="submit" name="action" value="rest">レスト</button>
                  <button class="js-modal-on c-button--help" type="button">遊び方/リセット</button>
                </form>
        </div>

              <div class="js-message-box c-message-box">
                  <p><?php echo (!empty($_SESSION['history'])) ? ($_SESSION['history']) : '';  ?></p>
              </div>

          </div><!-- ./l-sub-display -->
        </div><!-- ./l-main-display -->
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
