var gulp = require('gulp'),
    sass = require('gulp-ruby-sass'),
    imagemin = require('gulp-imagemin'),
    jshint = require('gulp-jshint'),
    uglify = require('gulp-uglify'),
    rimraf = require('gulp-rimraf'),
    concat = require('gulp-concat'),
    cache = require('gulp-cache'),
    livereload = require('gulp-livereload');

gulp.task('sass', function () {
    return gulp.src('app/assets/stylesheets/application.scss')
        .pipe(sass({
            style: 'compressed',
            loadPath: [
                'bower_components/bourbon/dist/',
                'bower_components/neat/app/assets/stylesheets/',
                'app/assets/stylesheets/vendor/'
            ]
        }))
        .pipe(gulp.dest('public/assets/css'))
});

gulp.task('imagemin', function () {
    return gulp.src('app/assets/images/**/*')
        .pipe(imagemin({}))
        .pipe(gulp.dest('public/assets/images'));
});

gulp.task('fonts', function () {
    return gulp.src('app/assets/fonts/**/*')
        .pipe(gulp.dest('public/assets/fonts'));
});

gulp.task('clean', function () {
    return gulp.src(['css'], {read: false})
        .pipe(rimraf());
});

gulp.task('watch', function () {
    // Watch .scss files
    gulp.watch('app/assets/sass/**/*.scss', ['sass']);

    // Create LiveReload server
    var server = livereload();

    // Watch any files in public/assets/, reload on change
    gulp.watch(['public/assets/**/*']).on('change', function (file) {
        server.changed(file.path);
    });
});

// Default task
gulp.task('default', ['clean'], function () {
    gulp.start('sass');
    gulp.start('imagemin');
    gulp.start('fonts');
});