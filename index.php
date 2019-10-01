<?php
ini_set('log_errors', 'on');//ログを出力
ini_set('error_log', 'php.log');//出力先を設定
session_start();//セッション作成

//デバッグフラグ
$debug_flg = true;
//デバッグログ関数
function debug($str){
  global $debug_flg;
  if(!empty($debug_flg)){
    error_log('デバッグ: '.$str);
  }
}


//////////////////////////////////////////////////
//定数
//個性(クラス定数）
//(attack,mp,criticalに影響)
class Unique{
  const Trainee = 1;//muscle上がりやすい,
  const Humor = 2;//Ev発生率に影響
  const Inspiration = 3;//codingクリティカル率
}
//////////////////////////////////////////////////
//各種変数定義

//インスタンス格納用
$hero = array();
$enemys = array();

//各種フラグ（phase順）
$restartFlg = 0;//初めて画面に繊維してきたか、リスタートボタンを押した時
$settingFlg = 0;//キャラクター設定が終わった
$startFlg = 0;//スタートボタンを押した時
$actionFlg = 0;//毎ターンのアクションコマンドがある時
$productInfoFlg = 0;//amount,qualityが一定値値以上になるとtrue,出来栄えを告知してくれる
$eventFlg = 0;//アクションコマンドの後1/4の確率でtrue,次ターンの冒頭に発生
$resultFlg = 0;//amountが3000以上でtrue。SESSION内のattMin(筋肉度)、quality,日数でエンディング分岐).$_SESSION['turnCount']);
$_SESSION['enemyCount'] = 0;
$_SESSION['turnCount'] = 0;

//////////////////////////////////////////////////
//抽象クラス + 各種クラス設定

//抽象クラス(engineers,dreamkillersの共通オブジェクト）
abstract class Humans {
  protected $name;
  protected $img;
  protected $hp;//ハッスルポイント
  protected $attackMin;
  protected $critical;

  //セッター
  public function setHp($num){
    $this->hp = $num;
  }
  //ゲッター
  public function getName(){
    return $this->name;
  }
  public function getImg(){
    return $this->img;
  }
  public function getHp(){
    //protectされているhpプロパティを引っ張ってくるメソッド
    return $this->hp;
  }
  public function getAttMin(){
    //protectされているhpプロパティを引っ張ってくるメソッド
    return $this->attackMin;
  }

  public function attack($targetObj){
    $attackPoint = mt_rand($this->attackMin, 100);
    $judge = mt_rand($this->critical,10);
    if($judge !== false){
      //criticalポイントに応じて必殺確率が変わる
      $attackPoint = $attackPoint * 1.7;
      $attackPoint = (int)$attackPoint;
      History::set($this->name.'の会心の一撃！');
    } else {
      $targetObj->setHp($targetObj->getHp()-$attackPoint);
      History::set($attackPoint.'のダメージ！');
    }
  }
}


//主人公クラス
class Hero extends Humans{
  protected $unique;
  function __construct($name, $img, $hp, $attackMin, $critical, $unique){
    $this->name = $name;
    $this->img = $img;
    $this->hp = $hp;
    $this->attackMin = $attackMin;
    $this->critical = $critical;
    $this->unique = $unique;
  }
  //ゲッター
  public function getCritical(){
    return $this->critical;
  }
  public function getUnique(){
    return $this->unique;
  }
  //セッター
  public function setAttMin($str){
    $this->attackMin = $str;
  }
  public function setCritical($str){
    $this->attackMin = $str;
  }
  //actionコマンド①攻撃
  public function attack($targetObj){
    //Heroのattackオーバーライド
    $attackPoint = mt_rand($this->attackMin, 100);
    $criticalPoint = 9 - $this->critical;

    //クリティカル値がカンストした時の誤作動防止
    if(!mt_rand(0, $criticalPoint) || $criticalPoint <= 0){
      $attackPoint = $attackPoint * 1.7;
      $attackPoint = (int)$attackPoint;
      History::set('バリバリ仕事をこなした！');
    } else {
      $targetObj->setHp($targetObj->getHp() - $attackPoint);
      History::set('タスクが'.$attackPoint.'減った');
    }
  }
  //actionコマンド②コーディング
  public function coding(){
    $codingPoint = mt_rand($this->attackMin, 100);

    $criticalPoint = 9 - $this->critical;
    if(!mt_rand(0, $criticalPoint) || $criticalPoint <= 0){
      History::set('今日は頭のキレがいい！');
      Product::setQuality();
      $codingPoint = $codingPoint * 1.7;
      $codingPoint = (int)$codingPoint;
    }
    Product::setAmount($codingPoint);
    $damagePoint = mt_rand(10, 40);
    $this->hp -= $damagePoint;
    History::set('疲れで'.$damagePoint.'HPが減った');
    History::set($codingPoint.'行のコードを打った！'."\n".'疲れで'.$damagePoint.'HPが減った');
    debug(print_r($_SESSION['product']));
  }
  //actionコマンド③トレーニング
  public function training(){
    $menuNumber = round($this->attackMin / 20);
    debug('トレーニングNo:'.$menuNumber);
    switch ($menuNumber) {
      case 0://イベントで0以下になる可能性あり
      History::set('今日は近所をウォーキング');
      History::set('最近体力が落ちていたみたいで、思ったより疲れた');
      break;
      case 1:
      History::set('今日は近所をランニング');
      History::set('最初は息苦しいが、徐々に気持ちよくなってきた！');
      break;
      case 2:
      History::set('今日は家で体幹トレーニング');
      History::set('姿勢を維持する筋肉が鍛える！');
      History::set('プログラミングにも効果がありそうだ！');
      break;
      case 3:
      History::set('今日はジムでダンベルカール');
      History::set('見た目は地味なプランクだが、意外にしんどい！');
      break;
      case 4:
      History::set('今日はジムでベンチプレス');
      History::set('思いの丈をすべてバーベルにぶつける！');
      break;
      case 5:
      History::set('今日もジムでチンニング');
      History::set('鬼の背中を手に入れて見せる！');
      break;
      default:
      History::set('ベンチプレス、デッドリフト、スクワットのフルコース！');
      History::set('もはや向かうところ敵なしだ！');
    }
    $trainPoint = mt_rand(1, 3);
    $damagePoint = mt_rand(10, 30);
    $this->attackMin += $trainPoint;
    $this->hp -= $damagePoint;
    History::set('パワーが'.$trainPoint.'ポイント上がった');
    History::set('疲れで'.$damagePoint.'HPが減った');
  }
  //actionコマンド④レスト
  public function rest(){
    History::set('煮詰まったから休憩・・・');
    $this->setHp($this->hp + mt_rand(50, 100));
    History::set('体力が回復した！');

    $criticalPoint = 9 - $this->critical;
    if(!mt_rand(0, $criticalPoint) || $criticalPoint <= 0){
      History::set('急に詰まっていた箇所を解決するコードを閃いた！');
      Product::setAmount(30);
      Product::setQuality();
      Product::setQualityCount();
      History::set('30行だけコードを書くことができた！');
      History::set('休むのも勉強のうち♪');
    }
  }

  public function sayCry(){
    $heroHp = round($this->hp / 100);
    switch ($heroHp) {
      case 0:
      History::set('心が折れそうだ。とにかく休みたい・・・');
      break;
      case 1:
      History::set('疲れが溜まってかなり辛い！');
      break;
      case 2:
      History::set('ちょっとキツくなってきた');
      break;
      case 3:
      History::set('まだまだ元気！');
      break;
      default:
      History::set('まだまだ元気。ウチがんばる！');
      break;
    }
  }
}

//継承クラス・マッスル
class HeroMuscle extends Hero{
  function __construct($name, $img, $hp, $attackMin, $critical, $unique){
    parent::__construct($name, $img, $hp, $attackMin, $critical, $unique);
  }
  //actionコマンド①攻撃
  public function attack($targetObj){
    //Heroのattackオーバーライド
    $attackPoint = mt_rand($this->attackMin);
    $criticalPoint = 9 - $this->critical;

    //クリティカル値がカンストした時の誤作動防止
    if(!mt_rand(0, $criticalPoint) || $criticalPoint <= 0){
      $attackPoint = $attackPoint * 1.7;
      $attackPoint = (int)$attackPoint;
      History::set('バリバリ仕事をこなした！');
    } else {
      $targetObj->setHp($targetObj->getHp() - $attackPoint);
      History::set('タスクが'.$attackPoint.'減った');
    }
  }
  //actionコマンド②コーディング
  public function coding(){
    $codingPoint = $this->attackMin;
    $missPoint = mt_rand(0, 1);
    if($missPoint === 0){
      History::set('ミス！');
      History::set('力が入りすぎてキーボードを壊してしまった！');
    }
    Product::setAmount($codingPoint);
    $damagePoint = mt_rand(10, 40);
    $this->hp -= $damagePoint;
    History::set($codingPoint.'行のコードを打った！'.$damagePoint.'ポイントが疲れた');
    debug(print_r($_SESSION['product']));
  }
}

//敵役クラス
class Enemy extends Humans{
  protected $attackMax;
  public function __construct($name, $img, $hp, $attackMin, $attackMax, $critical){
    $this->name = $name;
    $this->img = $img;
    $this->hp = $hp;
    $this->attackMin = $attackMin;
    $this->attackMax = $attackMax;
    $this->critical = $critical;
  }
  public function attack($targetObj){
    $attackPoint = mt_rand($this->attackMin, $this->attackMax);
    $judge = mt_rand($this->critical,10);
    if($judge !== false){
      //criticalポイントに応じて必殺確率が変わる
      $attackPoint = $attackPoint * 1.5;
      $attackPoint = (int)$attackPoint;
      History::set($this->name.'がやらかした！');
      $this->criticalVoice();
    } else {
      $this->actVoice();
    }
    $targetObj->setHp($targetObj->getHp()-$attackPoint);
    History::set($attackPoint.'ポイント疲れた');
  }
  public function actVoice(){
    $enemyName = $this->name;
    switch ($enemyName) {
      case 'お騒がせ上司':
        History::set('上司「言い忘れていたけど、来週までにこの仕事を頼みたいんだ」');
        break;
      case '食べ過ぎ同期':
      History::set('同期「クチャクチャクチャクチャ」');
      History::set('食べてばかりで全く手が動いていない！');
        break;
      case 'サボりグセ同期':
      History::set('あ〜仕事ダリぃ〜〜');
      History::set('眠くなるほどゆっくり仕事をしている・・・');
        break;
      case 'マウント上司':
      History::set('上司「私の若い頃はキミの何倍も働いたんだけどね〜」');
        break;
      case '弱気な自分':
      History::set('やっぱり私はダメなのかもしれない・・・');
        break;
      case '普通の上司':
      History::set('いまだにポートフォリオが完成していないことに不安が募る');
        break;
    }
  }
  public function criticalVoice(){
    $enemyName = $this->name;
    switch ($enemyName) {
      case 'お騒がせ上司':
        History::set('言い忘れていたけど、今日中にこの仕事を頼みたいんだ');
        break;
      case '食べ過ぎ同期':
      History::set('食い過ぎて腹が痛いから今日休むわ');
        break;
      case 'サボりグセ同期':
      History::set('カゼ引いたっぽいんで今日休みま〜す');
        break;
      case '自慢グセ上司':
      History::set('上司「私より先に帰るんじゃない！」');
        break;
      case '弱気な自分':
      History::set('仕事大変だし、プログラミングは難しいし、無茶なのよ');
        break;
      case '普通の上司':
      History::set('いまだにポートフォリオが完成していないことに焦ってしまう');
        break;
  }
}
}



//////////////////////////////////////////////////
//interface + 静的メンバ

//productクラス
interface ProductInterface{
  public static function setAmount($str);
  public static function setQuality();
  public static function setAmountCount();
  public static function setQualityCount();
}
class Product implements ProductInterface{
  public static function init(){
    $_SESSION['product'] = array('amount' => 0, 'quality' => 0, 'amountCount' => 0, 'qualityCount' => 0);
  }
  //セッター
  public static function setAmount($str){
    $_SESSION['product']['amount'] += $str;
  }
  public static function setQuality(){
    $_SESSION['product']['quality'] += 1;
  }
  public static function setAmountCount(){
    $_SESSION['product']['amountCount'] += 1;
  }
  public static function setQualityCount(){
    $_SESSION['product']['qualityCount'] += 1;
  }

  public static function sayAmount(){
    if($_SESSION['product']['amount'] > 200 && $_SESSION['product']['amountCount'] === 0){
      History::set('200行のコードを書き終えた');
      History::set('私の挑戦は、まだ始まったばかりだ！');
      self::setAmountCount();
    }
    if($_SESSION['product']['amount'] > 500 && $_SESSION['product']['amountCount'] === 1){
      History::set('ログイン・ログアウト機能ができた！');
      History::set('まだまだ道は長い けど、コードを書くのに慣れてきた気がする！');
      self::setAmountCount();
    }
    if($_SESSION['product']['amount'] > 1000 && $_SESSION['product']['amountCount'] === 2){
      History::set('アカウント登録・編集機能ができた！');
      History::set('コードの量的には半分くらいだろうか');
      History::set('仕事帰りのプログラミングは大変だが、充実している！');
      self::setAmountCount();
    }
    if($_SESSION['product']['amount'] > 1800 && $_SESSION['product']['amountCount'] === 3){
      History::set('情報登録・編集・表示機能など、主要な機能は実装できた！');
      History::set('だが、動作テストしてみたら、バグが見つかった・・・');
      History::set('シンドくて、最初の頃のような楽しさがない・・・');
      History::set('でも、楽しさなんて後回し まずはやり切ろう！');
      self::setAmountCount();
    }
    if($_SESSION['product']['amount'] > 2500 && $_SESSION['product']['amountCount'] === 4){
      History::set('バグは取り終えた！後はサーバーにアップするだけだ');
      History::set('私がこのサービスを公開しなければ、地球に隕石が落ちる');
      History::set('眠たい目をこすりつつ、デプロイ作業に取り掛かった！');
      self::setAmountCount();
    }
  }

  public static function sayQuality(){
    if($_SESSION['product']['quality'] > 3 && $_SESSION['product']['qualityCount'] === 0){
      History::set('とりあえず及第点くらいの成果物にはなりそうだ');
      self::setQualityCount();
    }
    if($_SESSION['product']['quality'] > 5 && $_SESSION['product']['qualityCount'] === 1){
      History::set('かなりイケてるアプリになりそうだ！');
      self::setQualityCount();
    }
    if($_SESSION['product']['quality'] > 8 && $_SESSION['product']['qualityCount'] === 2){
      History::set('とんでもないWebサービスを作ってしまったかもしれない！');
      self::setQualityCount();
    }
  }
}

//Eventクラス
interface EventInterface{
  public static function actEv();
}
class Event implements EventInterface{
  public static function actEv(){
     global $eventNum;
    $eventNum = mt_rand(0, 5);
      switch ($eventNum) {
        case '0':
          $hero->setHp(getHp() - 30);
          History::set('夜遅くまでコードを書いたら寝坊した!');
          History::set('よく眠れたが少し仕事がしづらくなってしまった');
          History::set('HPとコードが100アップ、パワーが5ダウンした！');
          $hero->setHp(getHp() + 100);
          $hero->setAttMin(getAttMin() - 5);
          Product::setAmount(100);
          return $eventNum;
          break;
        case '1':
          $hero->setHp(getHp() - 50);
        History::set('ツイッターでやり過ぎで寝不足だ・・・');
        History::set('HPが50ダウン、しかしポートフォリオのヒントを見つけた!');
        $hero->setHp(getHp() - 50);
        Product::setQuality();
        return $eventNum;
          break;
        case '2':
        History::set('会社でこっそり仮眠をとった');
        History::set('HPが200回復！');
        $hero->setHp(getHp() + 100);
        return $eventNum;
          break;
        case '3':
        History::set('残業で深夜まで仕事するハメになった');
        History::set('HP-50ポイントダウン！しかし仕事をこなしたことでパワーが5アップ！');
        $hero->setHp(getHp() - 50);
        $hero->setAttMin(getAttMin() + 5);
          return $eventNum;
          break;
        case '4':
        History::set('休日にコードを打っていたら、あっという間に夜になっていた！');
        History::set('200行のコードをなかなかのクオリティで書けた！');
          return $eventNum;
          break;
        case '5':
        History::set('ヤケクソになってめちゃくちゃ仕事をした！');
        History::set('仕事が300片付いた！');
        $enemy->setHp(getHp() - 300);
          return $eventNum;
          break;
      }
    }

  public static function endJudge(){
  //エンディングの優先順位//バッド > マッスル > ハッピー > グッド
    if($_SESSION['hero']->getHp() <= 0 ){
      global $resultTitle;
      $resultTitle = 'バッドエンド';
    } elseif($_SESSION['hero']->getName() === 'Muscle AI'){
      $_SESSION['resultTitle'] = 'マッスルエンド';
      History::set('おめでとう！アイちゃんは立派なボディビルダーになった！');
    } elseif ($_SESSION['product']['quality'] > 7 && $_SESSION['product']['amount']){
      $resultTitle = 'ハッピーエンド!';
      History::set('おめでとう！');
      History::set('素晴らしいポートフォリオで第一志望から内定をもらえた！');
      History::set('新たにWebサービスを作りたいし、新しい技術も学びたい');
      History::set('輝かしいエンジニア人生が、これから始まる！');
    } elseif ($_SESSION['product']['quality'] > 4){
      $_SESSION['resultTitle'] = 'グッドエンド';
      History::set('おめでとう、ポートフォリオのお陰でなんとか内定をもらえた');
      History::set('初めての個人開発には苦労もあったが、今となってはいい思い出だ');
      History::set('これから先も大変だろうけど、がんばろう');
    }
    return $resultTitle;
  }
}



//Historyクラス
interface HistoryInterface{
  public static function set($str);
  public static function clear();
}
class History implements HistoryInterface {

  public static function set($str){
    if(empty($_SESSION['history'])) $_SESSION['history'] = '';
    $_SESSION['history'] = $str.'<br>';
  }
  public static function clear(){
    unset($_SESSION['history']);
  }
}


//////////////////////////////////////////////////
//インスタンス生成

$heros[] = new Hero ('アイ', 'img/bustup/ai01.png', 800, 50, 1, Unique::Trainee);
$heros[] = new Hero ('アイ', 'img/bustup/ai02.png', 500, 40, 2, Unique::Humor);
$heros[] = new Hero ('アイ', 'img/bustup/cut110.png', 400, 30, 3, Unique::Inspiration);
$heros[] = new HeroMuscle ('Muscle AI', 'img/bustup/muscle.png', 5000, 500, 3, Unique::Trainee);

$enemys[] = new Enemy ('お騒がせ上司', 'img/bustup/enemy01.png', 200, 0, 30, 50, 0);
$enemys[] = new Enemy ('食べ過ぎ同期', 'img/bustup/enemy02.png', 500, 0, 40, 50, 0);
$enemys[] = new Enemy ('サボりグセ上司', 'img/bustup/enemy03.png', 300, 10, 30, 60, 1);
$enemys[] = new Enemy ('マウント上司', 'img/bustup/enemy04.png', 500, 20, 40, 80, 2);
$enemys[] = new Enemy ('弱気な自分', 'img/bustup/ai04.png', 1000, 50, 50, 150, 3);
$enemys[] = new Enemy ('普通の上司', 'img/bustup/enemy05.png', 10000, 0, 10, 150, 0);

function createHero($num){
  global $heros;
  $_SESSION['hero'] = $heros[$num];
  debug('初期ステータス:'.print_r($_SESSION['hero']));
}

function createEnemy($count = 0){
  global $enemys;
  $_SESSION['enemy'] = $enemys[$count];
  debug('エネミーのステータス:'.print_r($_SESSION['enemy']));
  History::set($_SESSION['enemy']->getName().'と一緒に仕事をすることになった！');
  if($_SESSION['enemy']->getName() === 'パワハラ上司'){
    History::set('この人は悪い人じゃないが単純に仕事が終わらなさそうだ・・・');
  }
}

function gameover(){
  $_SESSION = array();
}

function heroCheck($ai){
  if($ai->getHp() <= 0){
    $resultFlg = true;
  } elseif ($ai->getAttMin() > 100){
    createHero(4);
  }
}
function enemyCheck($enemy){
  if($enemy->getHp() <= 0){

    if($_SESSION['enemyCount'] === 4 ){
      History::set($enemy->getName().'に打ち勝った！');
      History::set('迷いを断ち切った今なら、いいコードを書ける気がする！');
      $_SESSION['hero']->setCritical(3);
      $_SESSION['enemyCount']++ ;
      createEnemy($_SESSION['enemyCount']);
    } else {
      History::set($enemy->getName().'との仕事がひと段落した！');
      $_SESSION['enemyCount']++ ;
      createEnemy($_SESSION['enemyCount']);
    }
  }
}
function init(){
  debug('初期化します！');
  //静的メンバの初期化
  History::clear();
  Product::init();
  $_SESSION['turnCount'] = 0;
  $_SESSION['enemyCount'] = 0;
  createHero($_SESSION['unique']);
  createEnemy($_SESSION['enemyCount']);
}


//クラス設定ここまで
/////////////////////////////////////////////////
/////////////////////////////////////////////////
// ゲームシステム
if(empty($_POST || $_POST['restart'])){
  debug('ページに遷移、もしくはリスタートボタンが押されたので、restartフラグをONにします');
  $restartFlg = 1;

  debug('他のフラグを初期化します');
  $settingFlg = 0;//OP→セッティング
  $startFlg = 0;//セッティング画面→通常画面
  $actionFlg = 0;//通常画面でボタンを押した時
  $eventFlg = 0;//イベント発生の判定。処理の最後に日数＋１
  $_SESSION['turnCount'] = 0;
  $_SESSION['enemyCount'] = 0;
  debug('OP画面を出力します');
} else {

  //毎ターン行う処理
  //本番前に削除
  debug('POSTを展開します：'.print_r($_POST));
  debug('POST送信を各フラグに変換します');
  $restartFlg = (!empty($_POST['restart'])) ? true : false ;
  $settingFlg = (!empty($_POST['setting'])) ? true : false ;
  $startFlg = (!empty($_POST['start'])) ? true : false ;
  $actionFlg = (!empty($_POST['action'])) ? true : false ;
  $resultFlg = ($_SESSION['product']['amount'] > 2500 ) ? true : false ;//Productが一定以上になったらエンディングへ

  debug('restartフラグ:'.$restartFlg);
  debug('settingフラグ:'.$settingFlg);
  debug('startフラグ:'.$startFlg);
  debug('actionフラグ:'.$actionFlg);
  debug('eventフラグ:'.$eventFlg);
  debug('resultフラグ:'.$resultFlg);

  debug('現在のターン:'.$_SESSION['turnCount']);
  debug('倒した敵の数:'.$_SESSION['enemyCount']);
}

/////////////////////////////////////////////////
//再スタート:$_SESSIONを全て空にする
if($restartFlg){
  debug('restartフラグ:'.$restartFlg);
  debug('settingフラグ:'.$settingFlg);

  debug('SESSIONを空にします');
  gameover();

} else {
  /////////////////////////////////////////////////

  //セッテイング画面
  if($settingFlg){
    debug('startボタンが押されました');
    debug('settingへ移行します');
  }
}

/////////////////////////////////////////////////
//ゲームスタート（選択画面から遷移してきた初回のみ）
if($startFlg){
  debug('セッテイング処理を始めます');
  switch($_POST['unique']){
    case 'trainee':
    $_SESSION['unique'] = 1;
    break;
    case 'humor':
    $_SESSION['unique'] = 2;
    break;
    case 'inspi':
    $_SESSION['unique'] = 3;
    break;
  }
  init();

} elseif ($resultFlg){
  debug('エンディング条件を満たしたので分岐判断します');
  Event::endJudge();
  debug('エンディング：'.$resultTitle);


  //スタート移行、毎ターンの開始ポイント
} elseif ($actionFlg){
  debug($_POST['action']);

  switch ($_POST['action']) {
    //アタック
    case 'attack':
    $_SESSION['hero']->attack($_SESSION['enemy']);
    enemyCheck($_SESSION['enemy']);
    break;
    //コーディング
    case 'coding':
    $_SESSION['hero']->coding();
    heroCheck($_SESSION['hero']);
    debug('ヒストリーの管理:'.var_dump($_SESSION['history']));
    Product::sayAmount();
    Product::sayQuality();
    var_dump($_SESSION['history']);
    break;
    //トレーニング
    case 'training':
    $_SESSION['hero']->training();
    heroCheck($_SESSION['hero']);
    break;
    //レスト
    case 'rest':
    $_SESSION['hero']->rest();
    Product::sayAmount();
    Product::sayQuality();
    break;
  }
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
  <link href="https://fonts.googleapis.com/css?family=Enriqueta&display=swap" rel="stylesheet">
  <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
  <link rel="stylesheet" href="sass/_src/css/style.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  <link href="https://fonts.googleapis.com/css?family=Kosugi+Maru|Manjari&display=swap" rel="stylesheet">
  <title>StartupEngineer</title>
</head>
<body>
  <?php if($restartFlg){ ?>

    <section  class="container">
      <div class="site-width op-img">
        <form class="btn-wrap" method="post">
          <button class="btn-setting" type="submit" name="setting" value="setting">StartupEngineer</button>
        </form>
        <div class="message message-opening message-opening__large">
          <p>エンジニアになろうと決心したアイちゃん！</p>
          <p>頑張ってプログラミングに取り組んでるけど,日常にはトラブルやジャマ者がいっぱい！</p>
          <p>障害を乗り越え、誘惑を振り切り、ハイクオリティなポートフォリオを作ろう！</p>
        </div>
      </div>

    </section>
  <?php  } else if($settingFlg){ ?>

    <section class="site-width container-setting">
      <div id="js-bg" class="">


        <div class="message message-setting message-setting__middle">
          <h2 class="setting-title">ユニークセレクト</h2>
          <p>ユニークによって主人公の能力値が変わります</p>
          <p>迷ったら、とりあえずを筋トレをしておきましょう！</p>
        </div>

        <form class="form-unique"method="post">
          <div class="wrap-btn__setting">
            <div class="wrap-radio">

              <div class="js-hover__disp">
                <label class="button button-radio__trainee">
                  <input class="js-showbg input-radio__hide" data-unique="trainee" type="radio" name="unique" value="trainee" checked="checked"><i class="far fa-hand-rock"></i>筋トレ好き
                </label>
                <span class="hidden train">お願いマッチョ！めっちゃモテた〜い<br>攻撃力と書けるコードの量がアップ</span>
              </div>

              <div class="js-hover__disp">
                <label class="button button-radio__humor">
                  <input class="js-showbg input-radio__hide" data-unique="humor" type="radio" name="unique" value="humor"><i class="far fa-grin-squint"></i>ユーモア
                </label>
                <span class="hidden humor">面白きことは良きことなり！<br>イベントが起きやすくなります</span>
              </div>

              <div class="js-hover__disp">
                <label class="button button-radio__inspiration">
                  <input class="js-showbg input-radio__hide" data-unique="inspi" type="radio" name="unique" value="inspiration"><i class="fas fa-bolt"></i>ひらめき
                </label>
                <span class="hidden inspiration">ねぼすけ(HP低)   だけど、やればできる子です</span>
              </div>

            </div>
          </div>
          <div class="wrap-btn__submit">
            <span class="errmsg"></span>
            <input class="js-check btn btn-outline-success btn-lg btn-block" type="submit" name="start" value="スタート">
          </div>
        </form>
      </div>
    </section>
  <?php  } elseif ($resultFlg) { ?>

    <section  class="site-width <?php echo $resultTitle; ?>">
      <h1 > <?php echo $resultTitle; ?></h1>
      <div class="message-box bottom">
        <p><?php echo (!empty($_SESSION['history'])) ? $_SESSION['history'] : ''; ?></p>
      </div>

    </section>


  <?php  } elseif ($startFlg || $actionFlg) { ?>

    <section class="site-width action">

    <div class="js-modal-body">
        <h2>〜ゲームの流れ〜</h2>
        <p>主人公の行動を選択して、ポートフォリオを完成させましょう</p>
        <ol>
            <li>コマンドについて</li>
              <ul>
                <li>アタック:上司に対抗しHPを減らします</li>
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
            <li>＊注意＊</li>
              <ul>
                <li>目的は<strong>ポートフォリオを完成させること</strong>です</li>
                <li>職場でのバトルはほどほどにしましょう</li>
              </ul>
          </ol>
          <button class="js-cancel-btn">閉じる</button>
      </div>
      <div class="js-modal-cover"></div>

      <div class="main-display">

        <div class="charcters-box">


        <div class="character-box">
          <div class="character-img__box">
            <img class="character-img__hero" src="<?php echo $_SESSION['hero']->getImg(); ?>" alt="主人公アイコン">
          </div>
          <div class="character-status">
            <p class=""> Name: <?php echo $_SESSION['hero']->getName(); ?></p>
            <p class="hp">HP:<?php echo $_SESSION['hero']->getHp(); ?> パワー: <?php echo $_SESSION['hero']->getAttMin(); ?></p>
          </div>
        </div>

        <div class="character-box">
          <div class="character-img__box">
            <img class="character-img__enemy" src="<?php echo $_SESSION['enemy']->getImg(); ?>" alt="敵アイコン">
          </div>
          <div class="character-status">
            <p class="state"> Name: <?php echo $_SESSION['enemy']->getName(); ?></p>
            <p class="state">HP:<?php echo $_SESSION['enemy']->getHp(); ?> </p>
            <p class="state no-top">パワー: <?php echo $_SESSION['enemy']->getAttMin(); ?></p>
          </div>
        </div>
      </div>

       <div class="system-box">

        <div class="command-box">
          <form class="command-body"class="btn" name="action" method="post">
            <button class="button attack"type="submit" name="action" value="attack">アタック</button>
            <button class="button coding"type="submit" name="action" value="coding">コード</button>
            <button class="button training"type="submit" name="action" value="training">筋トレ</button>
            <button class="button rest"type="submit" name="action" value="rest">レスト</button>
          </form>
          <button class="js-modal-btn">HELP</button>
        </div>

        <div class="message-box">
          <p><?php echo (!empty($_SESSION['history'])) ? $_SESSION['history'] : '';  ?></p>
        </div>
      </div>

      </div>
    </section>


  <?php } ?>
<!-- ゲームスタート時のみ、オートモーダルオン -->

<script type="text/javascript">
<?php if($startFlg){ ?>
  var autoModalFlg = true;
<?php } else { ?>
  var autoModalFlg = false;
<?php }; ?>
</script>

<script type="text/javascript" src="js/jquery-3.4.1.min.js"></script>
<script type="text/javascript" src="js/script.js"></script>
</body>
</html>
