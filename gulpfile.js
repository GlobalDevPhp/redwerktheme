/*
 * Installing
sudo apt install npm
npm install gulp --save-dev
npm install --save-dev gulp-concat
npm install --save-dev gulp-concat-css
npm install --save-dev gulp-minify-css
npm install --save-dev gulp-jsmin
npm install --save-dev gulp-sass
npm install --save-dev sass
npm install --save-dev gulp-rename
 */

var gulp         = require('gulp'), // Gulp
    concatCSS    = require('gulp-concat-css'), // Concat
    minifyCSS    = require('gulp-minify-css'); // miniCSS
    jsmin        = require('gulp-jsmin');
    sass         = require('gulp-sass')(require('sass'));
    concat       = require('gulp-concat');
    rename       = require('gulp-rename');
    
gulp.task('scripts', function() {
    return gulp.src([
        'www/wp-content/themes/twentytwenty-child/js/custom.js',
       ])
    .pipe(jsmin())
    .pipe(rename('custom.min.js'))
    .pipe(gulp.dest('./www/wp-content/themes/twentytwenty-child/js/'));
});

gulp.task('styles', function() {
    return gulp.src([ 'www/wp-content/themes/twentytwenty-child/css/form.scss'])
    .pipe(sass())
    .pipe(minifyCSS())
    .pipe(rename('form.min.css'))
    .pipe(gulp.dest('./www/wp-content/themes/twentytwenty-child/css/'));
});
