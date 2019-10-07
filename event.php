<?php
//イベント機能の概要は、フラグ発生＞コマンド選択＞イベント処理＋モーダルフラグ処理＞モーダルで自動表示
//まず、PHP側でイベントフラグ発生
//せっかくイベント静的メンバがあるのだから、そこに発生処理を任せてしまいたい
//発生率は
$eventFlg = (!mt_rand(0,  9 - $_SESSION['hero']->getLuck()));//enFlgという静的メソッドとして定義完了

//で、フラグがtrueの時限定でコマンドを表示　以下のコードをHTML内に置く
//ちょっと考えたけど、アタックコマンドとtrue/falseで選択式にしたい。
<?php echo (!$eventFlg) ? } <button class="button attack"type="submit" name="action" value="attack">アタック</button> : <button class="button event"type="submit" name="action" value="event"><i class="fas fa-child"></i>イベント</button> ?>
 ?>
//ちょっと迷ったけど、イベントはactionコマンドとして使用
//で、POSTされた時の処理をaction内に書いていく
case 'event':
Event::actEv();
break;
//イベントで気をつけたいこととして、イベントコマンドを選択してゲームオーバーになるのは避けたい、ということ
//HPが減るようなアンラッキーなイベントに当たっら、そのことをちゃんと見て欲しい
//なので、HPが減るイベントには以下を追加
if($_SESSION['hero']->getHp() < 0){ $_SESSION['hero']->setHp(1); }

//また、イベントの演出としてオートモーダルを使用したい。
//actEvの冒頭に追加、HTML後半のstartFlgを改変
$modalFlg = true;

//さらにイベント時は敵の攻撃をなくす。攻撃の後に追加
  if(!$_POST['action'] === 'event'){ $_SESSION['enemy']->attack($_SESSION['hero']); }

//////////////////////////////////////////////////////
//ここまででPHP側の処理は終了、HTML、JS側の処理に移る
//モーダルの画像についても、PHPの変数に画像を参照するパスを持たせたり、PHPではイベントの種類だけ変数に入れて、
//jsでdataメソッドを使って画像のパスを書き換えたり、色々できる。
//ただ、jqueryはいかんせん重い・・・なので、js側ではモーダル表示、画像の判別・用意はPHP側にになってもらう

//eventにイベントNoを$eventNum
//まずはイベント用のモーダル用意
?>
<div class="js-modal__event">
  <img src="" alt="">
  <p>主人公の行動を選択して、ポートフォリオを完成させましょう</p>
  <ol>
    <li>コマンドについて</li>
    <ul>
      <li>アタック:がんばって仕事をします（同僚のHPダウン）<br>同僚のHPを0にするとアイちゃんが成長しますが、仕事はもっと大変になります</li>
      <li>コーディング:コードを打って成果物を作りましょう。HPが少し減ります</li>
      <li>筋トレ:トレーニングしてパワーがUP。HPが少し減ります</li>
      <li>レスト：休んでHPを回復します</li>
      <li>イベント:不定期で選択できます。イイ事があるか、災難に会うかは運次第！</li>
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
  <button class="js-cancel-btn">閉じる</button>
  <form method="post">
    <button class="btn btn-warning" type="submit" name="restart" value="restart">リセット</button>
  </form>
</div>
<script type="text/javascript">
//前提として,イベントフラグをPHP側で受け取っていること
<?php if($eventNum){ ?>　//$eventNumはグローバルなので使えるはず。また、SESSIONじゃないのでページをまたぐと使えまおはず。
  var autoModalFlg = true;
  <?php } else { ?>
    var autoModalFlg = false;
    <?php }; ?>
  </script>
<script type="text/javascript">
$(function(){

  //スタート時に自動でモーダル表示
  var modalBody = $('.js-modal__event');
  var modalCover = $('.js-modal-cover');
  var modalCancel = $('.js-cancel-btn');

  if(autoModalFlg){
    console.log('eventvModalOn');//TODO　本番消去
    modalBody.add(modalCover).show();
    modalBody.add(modalCover).delay(3000).fadeOut(1000);
  };
});
</script>
//可能なら、二重レンダリング防止を施したい
