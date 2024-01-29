module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    
    uglify: {
      
      options: {
        manage: false
      },
      
      bluejs: {
        files:{
           'js/usaepaytransapi.min.js' : [ 'js/usaepaytransapi.js' ]
        }
      },
      adminjs: {
        files:{
           'js/admin-usaepaytransapi.min.js' : [ 'js/admin-usaepaytransapi.js' ],
           'js/admin-item-order.min.js' : [ 'js/admin-item-order.js' ]
        }
      },
      oadminjs: {
        files:{
           'js/admin-usaepay.min.js' : [ 'js/admin-usaepay.js' ]
        }
      },
    },

    cssmin:{
      receipt : {
        files: [{
          expand:true,
          cwd: 'css/',
          src: ['*.css', '!*.min.css'],
          dest: 'css/',
          ext: '.min.css'
        }]
      }
    }

  });

  // Load the plugin that provides the "uglify" task.
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-cssmin');

  // Default task(s).
  grunt.registerTask('default', ['uglify']);

};