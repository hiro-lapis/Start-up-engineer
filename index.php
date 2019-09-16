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
    error_log('デバッグ'.$str);
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
$resultFlg = 0;//amountが3000以上でtrue。SESSION内のattMin(筋肉度)、quality,日数でエンディング分岐)

//////////////////////////////////////////////////
//抽象クラス + 各種クラス設定

//抽象クラス(engineers,dreamkillersの共通オブジェクト）
abstract class Humans {
  protected $name;
  protected $img;
  protected $hp;//ハッスルポイント
  protected $attackMin;
  protected $critical;

  public function actVoice();

  //セッター
  public function setName($str){
    $this->name = $str;
  }
  public function setHp($num){
    $this->hp = $num;
  }
  //ゲッター
  public function getName(){
    return $this->name;
  }

  public function getImg(){
    return $this->personal;
  }
  public function getHp(){
    //protectされているhpプロパティを引っ張ってくるメソッド
    return $this->hp;
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
      private $unique;
      function __construct($name, $img, $hp, $attackMin, $critical, $unique){
      $this->name = $name;
      $this->img = $img;
      $this->hp = $hp;
      $this->attMin = $attackMin;
      $this->critical = $critical;
      $this->unique = $unique;
      }
      //ゲッター
      public function getAttMin($str){
        return $this->attMin;
      }
      public function getCritical(){
        return $this->critical;
      }
      public function getUnique(){
        return $this->unique;
      }
      //セッター
      //HPはHumanにある。攻撃力
      public function setAttMin($str){
        $this->attMin = $str;
      }
//actionコマンド①攻撃
      public function attack($targetObj){
        //Heroのattackオーバーライド
        $attackPoint = mt_rand($this->attackMin, 100);

        $judge = mt_rand($this->critical,10);
        if($judge !== false){
          $attackPoint = $attackPoint * 1.7;
          $attackPoint = (int)$attackPoint;
          History::set($this->name.'の会心の一撃！');
        } else {
          $targetObj->setHp($targetObj->getHp()-$attackPoint);
          History::set($attackPoint.'のダメージ！');
        }
      }
//actionコマンド②コーディング
      public function coding($targetObj){
        $codingPoint = mt_rand($this->attackMin, 100);
        $judge = mt_rand($this->critical,10);
     if($judge !== false){
          Product::setQuality();
          $codingPoint = $codingPoint * 1.7;
          $codingPoint = (int)$codingPoint;
          History::set('今日は頭のキレがいい！');
          }
          Product::setAmount(PRODUCT::getAmount() + $codingPoint);
          History::set($codingPoint.'行のコードを打った！');
      }
//actionコマンド③トレーニング
      public function trainning(){
        History::set('今日は筋トレだ！');
        $menu = mt_rand(0, 6);
        $this->setMp(mt_rand(3, 5));

        $trainPoint = mt_rand(1, 3);
        $this->setAttMin($trainPoint);
        History::set($trainPoint.'0gの筋肉がついた！');
        }
//actionコマンド④レスト
      public function rest(){
        History::set('煮詰まったから休憩・・・');
        $this->setHp(mt_rand(50, 100));
        History::set('体力が回復した！');

        if(!mt_rand($this->critical, 9)){
          History::set('休んでいたら、問題を解決するコードを閃いた！');
          Product::setAmount(getAmount() + 20);
          Product::setQuality(getQuality() +1);
          History::set('少しだけコードを書くことができた！');
          History::set('休むのも勉強のうちって、こういうことだったのか！と納得した');
        }
      }
      public function actVoice($action){
        switch ($action) {
          case 'attack':
            History::set('アイちゃんは心の中でいい返した。');
            History::set('挑戦しなければ成功もありません！');
          break;
          case 'coding':
            History::set('気合いの入ったコーディング');
            History::set('この１タイプ１タイプに命を込める！');
          break;
          case 'trainning':
            History::set('いいコードはいい筋肉に宿る！');
          break;
          case 'rest':
            History::set('たまにはリフレッシュしなくちゃね');
          break;
        }
      }
      public function sayCry($enemys){
        switch ($enemys) {
          case '嫌がらせ上司':
            History::set('やめて！顔を近づけて話さないでください！');
          break;
          case '冷やかし同期':
            History::set('人生分かってます的な気取りっぷりがメンタルに響く！');
          break;
          case 'マウント上司':
            History::set('事あるごとに上から目線の会話が辛い！');
          break;
          case '口だけ上司':
            History::set('いい事を言っているけど、行動がアレ過ぎる・・・');
          break;
          case '弱気な自分':
            History::set('どんどん過ぎていく日々に気が滅入る');
          break;
        }
      }
    }




//敵役クラス
    class Enemy extends Humans{
      protected $attackMax;
      public function __construct($name, $img, $hp, $attackMin, $attackMax, $critical){
      $this->name = $name;
      $this->img = $img;
      $this->hp = $hp;
      $this->attMin = $attackMin;
      $this->attMin = $attackMax;
      $this->critical = $critical;
    }
    public function actVoice(){
      History::set($this->name.'の嫌がらせ！');
    }
    public function sayCry(){
      History::set('小癪な！');
    }
    public function actVoice(){
      History::set($this->name.'が口を挟んでくる！');
    }//攻撃は抽象クラスにあるものをそのまま使う
  }

//////////////////////////////////////////////////
//interface + 静的メンバ

//productクラス
interface ProductInterface{
  public function setAmount();
  public function getAmount();
  public function setQuality();
  public function getQuality();
  public function setamountCount();
  public function getamountCount();
}
  class Product implements ProductInterface{
  public static $amount = 0;
  public static $quality = 0;
  public static $amountCount = 0;
  public static $qualityCount = 0;
    //セッター
    public static function setAmount($str){
      self::$amount += $str;
    }
    public static function setQuality(){
      self::$quality += 1;
    }
    public static function setAmountCount(){
      self::$amountCount += 1;
    }
    public static function setQualityCount(){
      self::$qualityCount += 1;
    }

    //ゲッター（主人公のcodingアクションでamountとqualityを更新する）
    public static function getAmount(){
      return self::$amount;
    }
    public static function getQuality(){
      return self::$quality;
    }
    public static function getAmountCount(){
      return self::$amountCount;
    }
    public static function getQualityCount(){
      return self::$qualityCount;
    }
    public static function sayAmount(){//呼び出す時は、Product::sayMount()
      //$productInfoFlg =true の時に実行
      if(self::$amount > 200){
        History::set('200行のコードを書けた！私の挑戦は、まだ始まったばかりだ！');
        self::setAmountCount();
      }
      if(self::$amount > 500 && self::$amountCount === 1){
        History::set('ログイン・ログアウト機能ができた！');
        History::set('まだまだ道は長い。けど、コードを書くのに慣れてきた気がする！');
        self::setAmountCount();
      }
      if(self::$amount > 1000 && self::$amountCount === 2){
        History::set('アカウント登録・編集機能ができた！');
        History::set('コードの量的には半分くらいだろうか');
        History::set('仕事帰りのプログラミングは大変だが、充実している！');
        self::setAmountCount();
      }
      if(self::$amount > 1800 && self::$amountCount === 3){
        History::set('情報登録・編集・表示機能など、主要な機能は実装できた！');
        History::set('だが、動作テストしてみたら、バグが見つかった・・・');
        History::set('シンドくて、最初の頃のような楽しさがない・・・');
        History::set('でも、楽しさなんて後回し。まずはやり切ろう！');
        sself::etAmountCount();
      }
      if(self::$amount > 2500 && self::$amountCount === 4){
        History::set('バグは取り終えた！後はサーバーにアップするだけだ');
        History::set('私がこのサービスを公開しなければ、地球に隕石が落ちる');
        History::set('眠たい目をこすりつつ、デプロイ作業に取り掛かった！');
        self::setAmountCount();
      }
    }

    public static function sayQuality(){
     if(self::$quality > 3){
        History::set('とりあえず及第点くらいの成果物にはなりそうだ');
        self::setQualityCount();
      }
      if(self::$amount > 5 && self::$qualityCount === 1){
        History::set('かなりイケてるアプリになりそうだ！');
        self::setQualityCount();
      }
      if(self::$amount > 8 && self::$qualityCount === 2){
        History::set('とんでもないWebサービスを作ってしまったかもしれない！');
      } self::setQualityCount();
    }
  }
//Eventクラス
interface EventInterface{
  public function setEv();
}




//Historyクラス
interface HistoryInterface{
  public function set($str);
  public function clear();
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

$hero = new Hero ('アイ', img/bustup/ai01.png, 700, 30, 1, Unique::Trainee);
$hero = new Hero ('アイ', img/bustup/ai01.png, 500, 30, 2, Unique::Humor);
$hero = new Hero ('アイ', img/bustup/ai01.png, 500, 30, 3, Unique::Inspiration);

$enemys = new Enemy ('お騒がせ上司', img/bustup/enemy01.png, 100, 0, 30, 50, 0);
$enemys = new Enemy ('冷やかし同期', img/bustup/enemy02.png, 200, 0, 40, 50, 0);
$enemys = new Enemy ('マウント上司', img/bustup/enemy03.png, 300, 10, 30, 60, 0);
$enemys = new Enemy ('口だけ上司', img/bustup/enemy04.png, 400, 20, 40, 80, 1);
$enemys = new Enemy ('弱気な自分', img/bustup/ai04.png, 1000, 50, 50, 150, 0);
$enemys = new Enemy ('パワハラ上司', img/bustup/enemy05.png, 10000, 50, 150, 150, 0);


function createHero($num){
  global $hero;
  $_SESSION['hero'] = $hero[$num];//0~8
}
function createEnemy($count){
  global $enemys;
  $enemy = $enemys[0 + $count];
  Hisotry::set($enemy->getName.'と一緒に仕事をすることになった！');
  $_SESSION['enemy'] = $enemy;
}

function gameover(){
  $_SESSION = array();
}
function init(){
  debug('初期化します！');
  History::clear();
  $_SESSION['turnCount'] = 0;
  $_SESSION['enemyCount'] = 0;
  Product::getAmount(0);
  Product::getQuality(0);
  Product::getamountCount(0);
}

//クラス設定ここまで
/////////////////////////////////////////////////
/////////////////////////////////////////////////
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
if(empty($_POST)){
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
  debug(PRODUCT::amount);

}

//スタート移行、毎ターンの開始ポイント
  if($actionFlg){
    switch ($_POST['action']) {
      case 'attack':
        $_SESSION['hero']->attack();
        break;
      case 'coding':
        $_SESSION['hero']->coding();
        break;
      case 'trainning':
        $_SESSION['hero']->trainning();
        break;
      case 'rest':
        $_SESSION['hero']->rest();
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
