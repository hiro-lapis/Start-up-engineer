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
class Unique{
  const Trainee = 'train';
  const Humor = 'humor';
  const Inspiration = 'inspi';
}
//////////////////////////////////////////////////
//各種変数定義

//インスタンス格納用
$hero = array();
$enemys = array();

//各種フラグ（phase順）
$settingFlg = '';//キャラクター設定が終わった
$startFlg = '';//スタートボタンを押した時
$actionFlg = '';//毎ターンのアクションコマンドがある時
$resultFlg = '';//amountが一定値以上でtrue
$eventFlg = '';//アクションコマンドの後1/4の確率でtrue,次ターンの冒頭に発生

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
  public function setImg($str){
    $this->img = $str;
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
  function __construct($name, $img, $hp, $attackMin, $critical, $luck, $unique){
    $this->name = $name;
    $this->img = $img;
    $this->hp = $hp;
    $this->attackMin = $attackMin;
    $this->critical = $critical;
    $this->luck = $luck;
    $this->unique = $unique;
  }
  //ゲッター
  public function getCritical(){
    return $this->critical;
  }
  public function getUnique(){
    return $this->unique;
  }
  public function getLuck(){
    return $this->luck;
  }
  //セッター
  public function setAttMin($str){
    $this->attackMin = $str;
  }
  public function setCritical($str){
    $this->critical = $str;
  }
  //actionコマンド①攻撃
  public function attack($targetObj){
    //Heroのattackオーバーライド
    $attackPoint = mt_rand($this->attackMin, $this->attackMin + 50);
    $criticalPoint = 9 - $this->critical;

    //クリティカル値がカンストした時の誤作動防止
    if(!mt_rand(0, $criticalPoint)){
      $attackPoint = $attackPoint * 1.7;
      $attackPoint = (int)$attackPoint;
      History::set('バリバリ仕事をこなした！タスクが'.$attackPoint.'減った');
    } else {
      History::set('ふつうに仕事をした。タスクが'.$attackPoint.'減った');
    }
    $targetObj->setHp($targetObj->getHp() - $attackPoint);
  }

  //actionコマンド②コーディング
  public function coding(){
    $codingPoint = mt_rand($this->attackMin, $this->attackMin + 100);

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
    History::set($codingPoint.'行のコードを打った！'."\n".'疲れで'.$damagePoint.'HPが減った');
  }
  //actionコマンド③トレーニング
  public function training(){
    $menuNumber = round($this->attackMin / 20);
    debug('トレーニングNo:'.$menuNumber);
    switch ($menuNumber) {
      case 0://イベントで0以下になる可能性あり
      History::set('今日は近所をウォーキング。体力が落ちているのか、意外と疲れた');
      break;
      case 1:
      History::set('今日はランニング。走りはじめは苦しいが、慣れると気持ちいい！');
      break;
      case 2:
      History::set('今日は家で体幹トレーニング。姿勢維持に効果がありそうだ！');
      break;
      case 3:
      History::set('ジムに行ってダンベルカール。少し二の腕が引き締まった！');
      break;
      case 4:
      History::set('ジムに行ってベンチプレス');
      History::set('筋肉の焼けつくような感覚がたまらない！');
      break;
      case 5:
      History::set('ジムに行ってチンニング');
      History::set('オトコ顔負けの背中を手に入れてみせる！');
      break;
      default:
      History::set('ベンチプレス、デッドリフト、スクワットのフルコース！');
      History::set('向かうところ敵なしだ！');
    }
    $trainPoint = mt_rand(3, 6);
    $damagePoint = mt_rand(10, 30);
    $this->attackMin += $trainPoint;
    $this->hp -= $damagePoint;
    History::set('パワーが'.$trainPoint.'ポイント上がった');
    History::set('疲れで'.$damagePoint.'HPが減った');
  }
  //actionコマンド④レスト
  public function rest(){
    History::set('煮詰まったから休憩・・・');
    $recoverPoint = mt_rand(100, 150);
    $this->setHp($this->hp + $recoverPoint);
    History::set('HPが'.$recoverPoint.'回復した！');

    $criticalPoint = 9 - $this->critical;
    if(!mt_rand(0, $criticalPoint) || $criticalPoint <= 0){
      History::set('急にコードをひらめいた！');
      History::set('30行だけだがクオリティの高いコードを書くことができた！');
      Product::setAmount(30);
      Product::setQuality();
    }
  }

  public function sayCry(){
    $heroHp = round($this->hp / 100);
    switch ($heroHp) {
      case 0:
      History::set('心が折れそうだ。とにかく休みたい・・・');
      break;
      case 1:
      History::set('疲れが溜まってかなり辛い');
      break;
      case 2:
      History::set('ちょっとキツくなってきた');
      break;
      case 3:
      History::set('まだ元気！');
      break;
      default:
      History::set('余裕でこなせる。コードを書きたい！');
      break;
    }
  }
}

//継承クラス・マッスル
class HeroMuscle extends Hero{
  function __construct($name, $img, $hp, $attackMin, $critical, $luck, $unique){
    parent::__construct($name, $img, $hp, $attackMin, $critical, $luck, $unique);
  }
  //actionコマンド①攻撃
  public function attack($targetObj){
    //オーバーライド
    $attackPoint = $this->attackMin;
    History::set('筋肉が脈うち、みるみる仕事が片付いていく！');
    $targetObj->setHp($targetObj->getHp() - $attackPoint);
    History::set('タスクが'.$attackPoint.'減った');
  }
  //actionコマンド②コーディング
  public function coding(){
    $missPoint = mt_rand(0, 1);
    if($missPoint === 0){
      History::set('ミス！');
      History::set('力が入りすぎてキーボードを壊してしまった！');
    } else {
      $codingPoint = $this->attackMin;
      Product::setAmount($codingPoint);
      $damagePoint = mt_rand(10, 40);
      $this->hp -= $damagePoint;
      History::set($codingPoint.'行のコードを打った！'.$damagePoint.'ポイント疲れた');
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
    $this->attackMin = $attackMin;
    $this->attackMax = $attackMax;
    $this->critical = $critical;
  }
  public function attack($targetObj){
    $attackPoint = mt_rand($this->attackMin, $this->attackMax);
    $judge = mt_rand($this->critical,10);
    if($judge === 10){
      //criticalポイントに応じて必殺確率が変わる
      $attackPoint = $attackPoint * 1.5;
      $attackPoint = (int)$attackPoint;
      History::set($this->name.'がやらかした！');
      $this->criticalVoice();
    } else {
      $this->actVoice();
    }
    $targetObj->setHp($targetObj->getHp()-$attackPoint);
    $targetObj->setHp($targetObj->getHp()-$attackPoint);
    History::set($attackPoint.'ポイント疲れた');
    History::set($targetObj->sayCry());
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
      case 'サボりグセ後輩':
      History::set('後輩「あ〜仕事ダリぃ〜〜」');
      History::set('眠くなるほどゆっくり仕事をしている・・・');
      break;
      case 'マウント上司':
      History::set('上司「私の若い頃はキミの何倍も働いたんだけどね〜」');
      break;
      case '弱気な自分':
      History::set('やっぱり私はダメなのかもしれない・・・');
      History::set('いまだにポートフォリオが完成していないことに不安が募る');
      break;
      case '普通の上司':
      History::set('今日も忙しいね。よろしく頼むよ。');
      break;
    }
  }
  public function criticalVoice(){
    $enemyName = $this->name;
    switch ($enemyName) {
      case 'お騒がせ上司':
      History::set('上司「言い忘れていたけど、今日中にこの仕事を頼みたいんだ」');
      break;
      case '食べ過ぎ同期':
      History::set('同期「食い過ぎて腹が痛いから今日休むわ」');
      break;
      case 'サボりグセ後輩':
      History::set('後輩「カゼ引いたっぽいんで今日休みま〜す」');
      break;
      case 'マウント上司':
      History::set('上司「私より先に帰るんじゃない！」');
      break;
      case '弱気な自分':
      History::set('仕事大変だし、プログラミングは難しいし、無茶なのよ');
      break;
      case '普通の上司':
      History::set('本社直々の業務をやってもらいたいんだ');
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
      History::red('200行以上のコードを書いた');
      History::set('私の挑戦は、まだ始まったばかりだ！');
      self::setAmountCount();
    }
    if($_SESSION['product']['amount'] > 500 && $_SESSION['product']['amountCount'] === 1){
      History::red('ログイン・ログアウト機能ができた！');
      History::set('まだまだ道は長い けど、コードを書くのに慣れてきた気がする！');
      self::setAmountCount();
    }
    if($_SESSION['product']['amount'] > 1000 && $_SESSION['product']['amountCount'] === 2){
      History::red('アカウント登録・編集機能ができた！');
      History::set('コードの量的には半分くらいだろうか');
      History::set('仕事帰りのプログラミングは大変だが、充実している！');
      self::setAmountCount();
    }
    if($_SESSION['product']['amount'] > 1800 && $_SESSION['product']['amountCount'] === 3){
      History::red('情報登録・編集・表示機能など、主要な機能は実装できた！');
      History::set('だが、動作テストしてみたら、バグが見つかった・・・');
      History::set('シンドくて、最初の頃のような楽しさがない・・・');
      History::set('でも、楽しさなんて後回し まずはやり切ろう！');
      self::setAmountCount();
    }
    if($_SESSION['product']['amount'] > 2300 && $_SESSION['product']['amountCount'] === 4){
      History::red('バグは取り終えた！あとはサーバーにアップするだけだ');
      History::set('サーバーのことはこれまで調べて来なかったので、どうすればいいのかよくわからない');
      History::set('ここまできたら、やるしかない！、デプロイ作業に取り掛かった');
      self::setAmountCount();
    }
  }

  public static function sayQuality(){
    if($_SESSION['product']['quality'] > 2 && $_SESSION['product']['qualityCount'] === 0){
      History::aqua('徐々にではあるが、自分の頭で考えてコードを打てるようになってきた！');
      History::aqua('とりあえず及第点くらいの成果物にはなりそうだ');
      self::setQualityCount();
    }
    if($_SESSION['product']['quality'] > 4 && $_SESSION['product']['qualityCount'] === 1){
      History::aqua('アプリのデザインもかなり凝った作りにできた！');
      History::aqua('かなりイケてるアプリになりそうだ！');
      self::setQualityCount();
    }
    if($_SESSION['product']['quality'] > 6 && $_SESSION['product']['qualityCount'] === 2){
      History::aqua('ビジネスになりそうな機能を実装！');
      History::aqua('このままいくと、とんでもないアプリを作れるかもしれない！');
      self::setQualityCount();
    }
  }
}

//Eventクラス
interface EventInterface{
  public static function actEv();
  public static function eventInit();
  public static function resultCheck();
}

class Event implements EventInterface{
  public static function eventInit(){
    $_SESSION['event'] ='';
    $_SESSION['eventNum'] = null;
    $_SESSION['eventTitle'] = '';
  }
  public static function eventSet($str){
    if(empty($_SESSION['event'])) $_SESSION['event'] = '';
    $_SESSION['event'] .= $str.'<br>';
  }

  public static function actEv(){
    $luckPoint = ($_SESSION['hero']->getLuck());
    $eventNum = mt_rand($luckPoint, 8);
    $_SESSION['eventNum'] = $eventNum;
    switch ($eventNum) {
      case '0':
      $_SESSION['eventTitle'] = 'アイちゃんがやらかした！';
      self::eventSet('夜遅くまでコードを書いてたら寝坊した!');
      self::eventSet('自責の念で、少しだけ仕事がしづらくなってしまった');
      self::eventSet('HPが100ポイントダウン、パワーが5ダウン！');
      $_SESSION['hero']->setHp($_SESSION['hero']->getHp() - 100);
      $_SESSION['hero']->setAttMin($_SESSION['hero']->getAttMin() - 5);
      Product::setAmount(100);
      break;
      case '1':
      $_SESSION['eventTitle'] = 'ツイッターショック';
      self::eventSet('ツイッターをしていたら、自分より短期間でアプリを作った人を発見');
      self::eventSet('ショックで眠れない数日間、眠れず夜を過ごした・・・');
      self::eventSet('HPが100ダウン');
      $_SESSION['hero']->setHp($_SESSION['hero']->getHp() - 100);
      Product::setQuality();
      break;
      case '2':
      $_SESSION['eventTitle'] = '早くおわんないかな';
      self::eventSet('昼休憩でカフェにいったら、');
      self::eventSet('グチっぽい同僚と偶然会ってしまった');
      self::eventSet('せっかくの休憩時間が台無しになってしまった・・・');
      self::eventSet('HPが150ダウン');
      $_SESSION['hero']->setHp($_SESSION['hero']->getHp() - 150);
      break;
      case '3':
      $_SESSION['eventTitle'] = '寝る子は育つ';
      self::eventSet('会社で爆睡したがバレなかった。ラッキー！');
      self::eventSet('HPが250回復');
      $_SESSION['hero']->setHp($_SESSION['hero']->getHp() + 250);
      break;
      case '4':
      $_SESSION['eventTitle'] = 'まっくろくろすけ';
      self::eventSet('残業で深夜まで仕事するハメになった');
      self::eventSet('HPが100ダウン！このままじゃいけないというキモチでパワーが5アップ！');
      $_SESSION['hero']->setHp($_SESSION['hero']->getHp() - 100);
      $_SESSION['hero']->setAttMin($_SESSION['hero']->getAttMin() + 10);
      break;
      case '5':
      $_SESSION['eventTitle'] = '集中してたね';
      self::eventSet('休日にコードを打っていたら、あっという間に夜になっていた！');
      self::eventSet('ホントはショッピングに行くつもりだったんだけど・・・まぁいっか');
      self::eventSet('200行のコードをなかなかのクオリティで書けた！');
      Product::setAmount(200);
      Product::setQuality();
      break;
      case '6':
      $_SESSION['eventTitle'] = 'ピンチはチャンス';
      self::eventSet('上司担当のプレゼンを、自分がやることになった！');
      self::eventSet('ヤケクソになってやって見たら、かなり上手くいった！');
      self::eventSet('仕事が200片付いた！パワーが10上がった！');
      $_SESSION['enemy']->setHp($_SESSION['enemy']->getHp() - 200);
      $_SESSION['hero']->setAttMin($_SESSION['hero']->getAttMin() + 10);
      break;
      case '7':
      $_SESSION['eventTitle'] = '';
      self::eventSet('嫌いな上司に誘われて、イヤイヤ飲みに行くことに');
      self::eventSet('酒が入った上司の話は、意外とタメになった');
      self::eventSet('上司とのわだかまりが溶け、前向きな気持ちになれた!');
      Product::setQuality();
      self::eventSet('HPが250回復、タスクが150減った');
      $_SESSION['enemy']->setHp($_SESSION['enemy']->getHp() - 150);
      $_SESSION['enemy']->setHp($_SESSION['hero']->getHp() + 250);
      break;
      case '8':
      $_SESSION['eventTitle'] = 'いいセンスだ';
      self::eventSet('プログラミングで気づいたことをツイートしたら少しバズった！');
      self::eventSet('気分よくなったおかげで、その後のプログラミングも上手くいった！');
      self::eventSet('400行のコードを、かなりのクオリティで打てた！');
      Product::setAmount(400);
      Product::setQuality();
      break;
    }
    //イベントでゲームオーバーにはならないようにする
    if($_SESSION['hero']->getHp() < 0){
      $_SESSION['hero']->setHp(1);
    }
  }

  public static function resultCheck(){
    global $resultFlg;
    //実質ゲームオーバー
    if($_SESSION['turnCount'] > 49 ){
      //時間切れエンド
      $resultFlg = true;
      $_SESSION['resultTitle'] = 'バッドエンド';
      $_SESSION['resultImg'] = 'bad-end1';
      History::clear();
      History::set('仕事の忙しさで、プログラミングのことを忘れてしまった・・・');
      History::set('エンジニアになる日はいつになることやら・・・');
    } elseif($_SESSION['hero']->getHp() < 0 ){
      //挫折エンド
      $resultFlg = true;
      $_SESSION['resultTitle'] = 'バッドエンド';
      $_SESSION['resultImg'] = 'bad-end2';
      History::clear();
      History::set('アイちゃんは疲れ果て、挫折してしまった・・・');
      ////////ここからクリア分岐//////////////////////////
    }  elseif ($_SESSION['product']['amount'] > 2500){
      if($_SESSION['hero']->getName() === 'Muscle AI'){
        //マッスルエンド
        $resultFlg = true;
        $_SESSION['resultTitle'] = 'マッスルエンド';
        $_SESSION['resultImg'] = 'muscle-end';
        History::clear();
        History::set('おめでとう！アイちゃんは立派なボディビルダーになった！');
      } elseif ($_SESSION['product']['quality'] > 6) {
        //ハッピーエンド
        $resultFlg = true;
        $_SESSION['resultTitle'] = 'ハッピーエンド';
        $_SESSION['resultImg'] = 'happy-end';
        History::clear();
        History::set('おめでとう！');
        History::set('素晴らしいポートフォリオのお陰で、憧れの会社から内定をもらえた');
        History::set('新たにWebサービスを作りたいし、新しい技術も学びたい！');
        History::set('輝かしいエンジニアキャリアが、これから始まる！');
      } else {
        //グッドエンド
        $resultFlg = true;
        $_SESSION['resultTitle'] = 'グッドエンド';
        $_SESSION['resultImg'] = 'good-end';
        History::clear();
        History::set('おめでとう、ポートフォリオのお陰で面接のアポをとれた！');
        History::set('これから先もがんばっていこう！');
      }
      debug('リザルト判定:'.$resultFlg);
      debug('リザルト種類:'.$_SESSION['resultTitle']);
    }
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
    $_SESSION['history'] .= $str.'<br>';
  }
  public static function red($str){
    $_SESSION['history'] .= '<span class="amount-red">'.$str.'</span><br>';
  }
  public static function aqua($str){
    $_SESSION['history'] .= '<span class="quality-aqua">'.$str.'</span><br>';
  }
  public static function clear(){
    unset($_SESSION['history']);
  }
}


//////////////////////////////////////////////////
//インスタンス生成

$heros[] = new Hero ('アイ', 'img/bustup/hero-humor.png', 1000, 40, 2, 2, Unique::Humor);
$heros[] = new Hero ('アイ', 'img/bustup/hero-inspi.png', 500, 50, 5, 1, Unique::Inspiration);
$heros[] = new Hero ('アイ', 'img/bustup/hero-trainee.png', 800, 70, 1, 0, Unique::Trainee);
$heros[] = new HeroMuscle ('Muscle AI', 'img/bustup/muscle.png', 5000, 500, 3, 0, Unique::Trainee);
$enemys[] = new Enemy ('お騒がせ上司', 'img/bustup/enemy01.png', 200, 20, 30, 1);
$enemys[] = new Enemy ('食べ過ぎ同期', 'img/bustup/enemy02.png', 300, 20, 40, 1);
$enemys[] = new Enemy ('サボりグセ後輩', 'img/bustup/enemy03.png', 400, 10, 30, 3);
$enemys[] = new Enemy ('マウント上司', 'img/bustup/enemy04.png', 400, 30, 40, 3);
$enemys[] = new Enemy ('弱気な自分', 'img/bustup/enemy05.png', 600, 40, 80, 2);
$enemys[] = new Enemy ('普通の上司', 'img/bustup/enemy06.png', 10000, 0, 10, 0);

//////////////////////////////////////////////////
//Heroインスタンス作成
function createHero($num){
  global $heros;
  $_SESSION['hero'] = $heros[$num];
}

function heroCheck(){
  debug($_SESSION['hero']->getImg());
  debug($_SESSION['hero']->getAttMin());
  switch ($_SESSION['hero']) {
    case $_SESSION['hero']->getHp() < 0:
    $resultFlg = true;
    break;
    case $_SESSION['hero']->getAttMin() >= 150:
    if($_SESSION['hero']->getName() !== 'Muscle AI'){
      createHero(3);
      History::set('アイちゃんはついに限界を超え、最強のカラダを手にした！');
    }
    break;
    case $_SESSION['hero']->getHp() < 100:
    if($_SESSION['hero']->getImg() !== 'img/bustup/pinch.png'){
      debug('主人公のimgを書きかえます');
      $_SESSION['hero']->setImg('img/bustup/hero-pinch.png');}
      debug($_SESSION['hero']->getImg());
      break;
      case $_SESSION['hero']->getHp() > 100:
      if($_SESSION['hero']->getImg() === 'img/bustup/hero-pinch.png'){ $_SESSION['hero']->setImg('img/bustup/hero-'.$_SESSION['imgPath'].'.png');}
      break;
    }
  }


  //////////////////////////////////////////////////
  //Enemyインスタンス作成
  function createEnemy($count = 0){
    global $enemys;
    $_SESSION['enemy'] = $enemys[$count];
    switch ($_SESSION['enemy']->getName()) {
      case '弱気な自分':
      History::set('今度は'.$_SESSION['enemy']->getName().'と向き合うことになった！');
      break;
      case '普通の上司':
      History::set($_SESSION['enemy']->getName().'と一緒に仕事をすることになった！');
      History::set('この人は悪い人じゃないが、仕事が大変そうだ・・・');
      break;
      default:
      History::set($_SESSION['enemy']->getName().'と一緒に仕事をすることになった！');
      break;
    }
  }


  //////////////////////////////////////////////////
  //ゲーム進行関数
  function gameover(){
    $_SESSION = array();
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


  //設定ここまで
  /////////////////////////////////////////////////
  /////////////////////////////////////////////////
  //ここから実行処理

  // ゲームシステム
  if(empty($_POST || $_POST['restart'])){
    debug('ページに遷移、もしくはリスタートボタンが押されたので、restartフラグをONにします');
    $restartFlg = 1;
    $settingFlg = 0;//OP→セッティング
    $startFlg = 0;//セッティング画面→通常画面
    $actionFlg = 0;//通常画面でボタンを押した時
    $eventFlg = 0;//イベント発生の判定
    debug('OP画面を出力します');
  } else {

    //POSTがあった場合
    debug('POST送信を各フラグに変換します');
    $restartFlg = (!empty($_POST['restart'])) ? true : false ;
    $settingFlg = (!empty($_POST['setting'])) ? true : false ;
    $startFlg = (!empty($_POST['start'])) ? true : false ;
    $actionFlg = (!empty($_POST['action'])) ? true : false ;
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
      case 'humor':
      $_SESSION['imgPath'] = 'humor';
      $_SESSION['unique'] = 0;
      break;
      case 'inspiration':
      $_SESSION['unique'] = 1;
      $_SESSION['imgPath'] = 'inspi';
      break;
      case 'trainee':
      $_SESSION['unique'] = 2;
      $_SESSION['imgPath'] = 'trainee';
      break;
    }
    init();


    //スタート以降、毎ターンの開始ポイント
  } elseif ($actionFlg){
    Event::eventInit();

    //①Heroのアクション
    switch ($_POST['action']) {
      //アタック
      case 'attack':
      $_SESSION['hero']->attack($_SESSION['enemy']);
      break;
      //コーディング
      case 'coding':
      $_SESSION['hero']->coding();
      break;
      //トレーニング
      case 'training':
      $_SESSION['hero']->training();
      break;
      //レスト
      case 'rest':
      $_SESSION['hero']->rest();
      break;
      //イベント
      case 'event':
      Event::actEv();
      break;
    }

    //②heroとPRODUCTの状態,eventFlgのチェック
    heroCheck();
    Product::sayAmount();
    Product::sayQuality();

    //③enemyの状態チェック、倒した時
    if($_SESSION['enemy']->getHp() <= 0){
      History::clear();
      if($_SESSION['enemyCount'] === 4 ){
        History::set($_SESSION['enemy']->getName().'に打ち勝った！');
        History::set('迷いを断ち切った今なら、いいコードを書ける気がする！');
        $_SESSION['hero']->setCritical($_SESSION['hero']->getCritical() + 3);
        createEnemy(++$_SESSION['enemyCount']);
      } else {
        History::set($_SESSION['enemy']->getName().'との仕事がひと段落した！');
        $_SESSION['hero']->setAttMin($_SESSION['hero']->getAttMin() + 10);
        createEnemy(++$_SESSION['enemyCount']);
      }
      //④enemyのHPが残っていて、かつイベントコマンドでない場合に攻撃
    } elseif ($_POST['action'] !== 'event') {
      $_SESSION['enemy']->attack($_SESSION['hero']);
    }
    debug('主人公の状態を確認します');
    heroCheck();
    $_SESSION['turnCount']++;

    //毎ターンイベント発生の判定をするために、最初にfalseにする
    if(!mt_rand(0, 6 - $_SESSION['hero']->getLuck())){
      $eventFlg = true;
    }
    Event::resultCheck();//$resultFlg判定

    /////////////////////////////////////////////////
    //エンディング判定
    if($resultFlg){
      debug('エンディング条件を満たしました');
      debug('エンディング：'.$resultTitle);
    }
  }

  ?>