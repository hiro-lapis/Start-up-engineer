'use strict'

var gulp = require('gulp');
var sass = require('gulp-sass');

gulp.task('sass', function(){
  gulp.src('./_src/sass/**/*.scss')
    .pipe(sass({outputStyle: 'expanded'}))
    .pipe(gulp.dest('./css'));
});

gulp.task('watch', function(){
  gulp.watch('./_src/sass/**/*.scss', gulp.task('sass'));
})

gulp.task('default',gulp.series('watch'));
