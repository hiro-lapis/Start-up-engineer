<?php require('index.php');
// ゲームシステム
if(!empty($_POST)){
  debug('POST送信を各フラグに変換します');
  $restartFlg = (!empty($_POST['restart'])) ? true : false ;
  $settingFlg = (!empty($_POST['setting'])) ? true : false ;
  $startFlg = (!empty($_POST['start'])) ? true : false ;
  $actionFlg = (!empty($_POST['action'])) ? true : false ;
  $resultFlg = (PRODUCT::$amount > 2500 ) ? true : false ;//PRODUCTが一定以上になったらエンディングへ

  debug($restartFlg);
  debug($settingFlg);
  debug($startFlg);
  debug($actionFlg);
  debug($eventFlg );
  debug($resultFlg);

  debug('現在のターン:'.$_SESSION['turnCount']);
  debug('倒した敵の数:'.$_SESSION['enemyCount']);

}

/////////////////////////////////////////////////
//初期設定
if(empty($_POST){
  debug('ページに遷移してきたので、restartフラグをONにします');
  $restartFlg = 1;
} elseif ($_POST['restart']){
  debug('リスタートボタンが押されたので、startフラグをONにします');
  $restartFlg = 1;

  debug('他のフラグを初期化します');
  $settingFlg = 0;//OP→セッティング
  $startFlg = 0;//セッティング画面→通常画面
  $actionFlg = 0;//通常画面でボタンを押した時
  $eventFlg = 0;//イベント発生の判定。処理の最後に日数＋１
  $SESSION['turnCount'] = 0;
  debug('OP画面を出力します');
}

/////////////////////////////////////////////////
//再スタート:$_SESSIONを全て空にする
if($restartFlg){
  gameover();

} else {

  //セッテイング画面
  if($settingFlg){
    debug('スタートボタンが押されました');
    debug('settingへ移行します');
  }
}

/////////////////////////////////////////////////
//ゲームスタート（選択画面から遷移してきた初回のみ）
if($startFlg){
  init();//0~2の値が入り、$_SESSION['hero']にインスタンスを格納

} elseif ($resultFlg){
  debug('エンディング判定を行います');
  debug(PRODUCT::amount)

}

//スタート移行、毎ターンの開始ポイント
  if($actionFlg){
    switch ($actionFlg) {
      case 'attack':
        $_SESSION['hero']->attack();
        break;
      case 'coding':
        $_SESSION['hero']->coding();
        break;
      case 'trainning':
        $_SESSION['hero']->trainning()
        break;
      case 'rest':
        $_SESSION['hero']->rest()
        break;
    }
    //PRODUCTのプロパティをアナウンスするかどうかの判定を行う
    PRODUCT::sayAmount;
    PRODUCT::sayQuality;
}

if($_SESSION['turnCount'] % 3 == 0 && empty($_SESSION['enemy'])){
  createEnemy($_SESSION['enemyCount']);
}




?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="description" content="駆け出しエンジニアシミュレーションゲーム">
  <meta property=og:"site_name" content="StartupEngineer">
  <meta property=og:"type" content="website">
  <meta property=og:"title" content="StartupEngineer">
  <meta property=og:"description" content="">
  <meta property=og:"url" content="">
  <meta property=og:"image" content="img/top-baner.jpg">
  <meta name="twitter:card" content="summary">
  <link href="https://fonts.googleapis.com/css?family=Enriqueta&display=swap" rel="stylesheet">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
  <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
  <link rel="icon" href="img/favicon.ico" type="image/x-icon">
  <link rel="stylesheet" href="style.css">
  <title>StartupEngineer</title>
</head>
<body>
  <!-- SESSIONやフラグが空の場合ここから -->
  <section id="contents" class="site-width">
  <?php if(!empty($startFlg)){ ?>
    <h1>StartupEngineer</h1>

    <p>エンジニアになろうと決心したアイちゃん！</p>
    <p>頑張ってプログラミングに取り組んでるけど,日常にはトラブルやジャマ者がいっぱい！</p>
    <p>障害を乗り越え、誘惑を振り切り、ハイクオリティなポートフォリオを作ろう！</p>

    <form post="post">
      <input type="submit" name="setting" value="スタート">
    </form>
  <?php } ?>

  <?php  if($settingFlg){ ?>
    <h1>キャラクターせれくと！</h1>
    <p>アイちゃんの個性を付けてあげよう！</p>
    <p>ストレスへの耐性やコードを書く能力、その他アレやコレやに影響します。</p>
    <p>迷ったら、とりあえずマッスルを選ぶのがおすすめ！</p>
    <form method="post">
      <label for="unique">Unique</label>
        <div class="unique-trainee">
      <input type="radio" name="unique" value="1">トレーニー
      <i class="far fa-hand-rock"></i>
      <p>お願いマッチョ！めっちゃモテた〜い。攻撃力と、なぜか書けるコードの量も増えます。</p>
    </div>
    <div class="unique-humor">
    <i class="far fa-grin-squint"></i>
      <input type="radio" name="unique" value="2">ユーモア
      <p>面白きことは良きことなり！ちょっぴりイベントに遭遇しやすくなります</p>
    </div>

    <div class="unique-inspiration">
      <i class="fas fa-bolt"></i>
      <input type="radio" name="unique" value="3">インスピレーション
      <p>しかし、アイに電流走る！閃きやすくなります。</p>
    </div>

    </form>

  <?php } ?>


  </section>

</body>
</html>
