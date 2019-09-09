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
)クラス(attack,mp,criticalに影響)
class Unique{
  const Trainee = 1;//muscle上がりやすい,
  const Humor = 2;//Ev発生率に影響
  const Inspiration = 3;//codingクリティカル率
}
//行動属性フラグ(クラス定数）setCry,actVoiceに影響
class Hobby{
  const Game = 1;
  const Comic = 2;
  const Book = 3;
}
//////////////////////////////////////////////////
//各種変数定義

//インスタンス格納用
$engineers = array();
$products = array();
$sevenOfSins = array();

//各種フラグ（phase順）
$restartFlg = 0;//リスタートボタンを押した時
$startFlg = 0;//スタートボタンを押した時
$playerSelectFlg = 0;//キャラクター設定が終わった
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
  protected $mp;//モチベーション
  protected $attackMin;
  protected $critical;

  abstract public function actVoice();
  abstract public function sayCry();

  //セッター
  public function setName($str){
    $this->name = $str;
  }
  public function setHp($num){
    $this->hp = $num;
  }
  public function setMp($num){
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
  public function getMp(){
    //protectされているhpプロパティを引っ張ってくるメソッド
    return $this->hp;
  }

  public function attack($targetObj){
    $attackPoint = mt_rand($this->attackMin, 100);
    $judge = mt_rand($this->critical,10)
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
      private $personal;

      function __construct($unique, $personal){
      parent::__construct($name, $img, $hp, $mp, $attackMin, , $critical);
      $this->unique = $unique;
      $this->personal = $personal;
      }
      //ゲッター
      public function getCritical(){
        return $this->critical;
      }

      public function getPersonal(){
        return $this->personal;
      }

      public function getUnique(){
        return $this->unique;
      }
      //セッター
      //HPとMPはHumanにある。攻撃力
      public function setAttMin($str){
        return $this->attMin = $str;
      }
//actionコマンド①攻撃
      public function attack($targetObj){
        //Heroのattackオーバーライド
        $attackPoint = if($this->unique === Unique::Trainee ){
                                 mt_rand($this->attackMin, 100) + 20;
                        } else { mt_rand($this->attackMin, 100); }

        $judge = mt_rand($this->critical,10)
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
//actionコマンド②コーディング
      public function coding($targetObj){
        $codingPoint = mt_rand($this->attackMin, 100);
        $criticalPoint = if($this->unique === Unique::Inspiration ){ $this->critical + 2;
                                                            } else { $this->critical; }
        $judge = mt_rand($criticalPoint,10);
     if($judge !== false){
          $targetObj->setQuality($targetObj->getQuality()++);
          //criticalポイントに応じて必殺確率が変わる
          $codingPoint = $codingPoint * 1.7;
          $codingPoint = (int)$codingPoint;
          History::set('今日は頭のキレがいい！');
          }
          $targetObj->setAmount($targetObj->getAmount() + $codingPoint);
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
        $this->setMp(mt_rand(20, 40));
        History::set('HPとMPが回復した！');
        if(!mt_rand(0, 5)){
          History::set('閃いた！');
          Product::setAmoun
          History::set('頭がすごく冴えていたので、少しだけコードを書いた！');


        }

      }
      }




//敵役クラス
    class Enemy extends Human{
      protected $karma;
      public function __construct($karma) {
      parent::__construct($name, $img, $hp, $mp, $attackMin, $attackMax, $critical);
      $this->karma = $karma;
    public function actVoice(){
      History::set($this->name.'の嫌がらせ！');
    }
    public function sayCry(){
      History::set('小癪な！');
    }
    public function actVoice(){
      History::set($this->name.'が口を挟んでくる！');
    }
  }
}


//////////////////////////////////////////////////
//interface + 静的メンバ

//productクラス
interface ProductInterface{
  public function setAmount();
  public function getAmount();
  public function setQuality();
  public function getQuality();
}
  class Product implements ProductInterface{
  public static $amount = 0;
  public static $quality = 0;
  public static $infoCount = 0;
    //セッター
    public static function setAmount($str){
      self->amount = $str;
    }
    public static function setQuality($str){
      self->quality = $str;
    }
    //ゲッター（主人公のcodingアクションでamountとqualityを更新する）
    public static function getAmount (){
      return self->amount;
    }
    public static function getQuality (){
      return self->quality;
    }
}
    public static function sayAmount(){//呼び出す時は、Product::sayMount()
      //$productInfoFlg =true の時に実行
      if(self->amount > 200){
        History::set('200行のコードを書けた！私の挑戦は、まだ始まったばかりだ！');
        ++self->infoCount;
      }
      if(self->amount > 500 && self->infoCount === 1){
        History::set('ログイン・ログアウト機能ができた！');
        History::set('まだまだ道は長い。けど、コードを書くのに慣れてきた気がする！');
        ++self->infoCount;
      }
      if(self->amount > 1000 && self->infoCount === 2){
        History::set('アカウント登録・編集機能ができた！');
        History::set('コードの量的には半分くらいだろうか');
        History::set('仕事帰りのプログラミングは大変だが、充実している！');
        ++$this->infoCount;
      }
      if(self->amount > 1800 && self->infoCount === 3){
        History::set('情報登録・編集・表示機能など、主要な機能は実装できた！');
        History::set('だが、動作テストしてみたら、バグが見つかった・・・');
        History::set('正直シンドくて、最初の頃のような楽しさがない・・・');
        History::set('でも、楽しさなんて後回し。まずはやり切ろう！');
        ++$this->infoCount;
      }
      if(self->amount > 2500 && self->infoCount === 4){
        History::set('バグは取り終えた！後はサーバーにアップするだけだ');
        History::set('私がこのサービスを公開しなければ、地球に隕石が落ちる');
        History::set('眠たい目をこすりつつ、デプロイ作業に取り掛かった！');
        ++self->infoCount;
      }
    }
  }
//Eventクラス
interface EventInterface{
  public function setEv();
}




//Historyクラス
interface HistoryInterface{
  public function set();
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

$hero = new Hero ('')




 ?>
