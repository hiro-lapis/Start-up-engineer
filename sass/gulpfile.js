//plug-in
var gulp = require('gulp');
var cleanCSS = require('gulp-clean-css');
var sass = require('gulp-sass');
var changed  = require('gulp-changed');
var imagemin = require('gulp-imagemin');

// gulpタスクの作成
// gulp.task()を使う
// 第一引数に任意のタスク名、第二引数に実行したい処理をfunction関数で渡してあげる
// 関数内ではpipe()で処理を繋ぐ（jQueryのメソッドチェーンと同じ）

//CSS圧縮
gulp.task('minify-css', function() {
  return gulp.src("src/css/*.css")//元のファイル
    .pipe(cleanCSS())
    .pipe(gulp.dest('dist/css/'));//出力先
});

// defaultで動かすタスクを指定
// defaultに設定しておくとgulpコマンドだけでタスクが実行される
// 書き方は第二引数に配列でタスクを指定する
// gulp.task(‘default’,[‘タスク名’,’タスク名’,’タスク名’,…]);
gulp.task('default',['minify-css']);


// sassをコンパイル
gulp.task('sass', function(){
  gulp.src('./_src/scss/*.scss')
    .pipe(sass())
    .pipe(gulp.dest('dist/css'));
});

// 画像圧縮
// 圧縮前と圧縮後のディレクトリを定義
var paths = {
  srcDir : 'src',
  dstDir : 'dist'
};
// jpg,png,gif画像の圧縮タスク
gulp.task('imagemin', function(){
  var srcGlob = paths.srcDir + '/**/*.+(jpg|jpeg|png|gif)';
  var dstGlob = paths.dstDir;
  gulp.src( srcGlob ) //srcディレクトリ以降を対象にする
    .pipe(changed( dstGlob )) //変換したことがある画像は加工対象外にする
    .pipe(imagemin([
      imagemin.gifsicle({interlaced: true}),
      imagemin.jpegtran({progressive: true}),
      imagemin.optipng({optimizationLevel: 5})
      ]
    ))
    .pipe(gulp.dest( dstGlob ));
});


// Gulpを使ったファイルの監視
// watch()を使う
// 第一引数は監視したいディレクトリ配下
// 第二引数に変更があった場合に実行するタスクを配列形式で指定
gulp.task('watch', function(){
  gulp.watch(paths.srcDir + '/**/*', ['imagemin']);
});
