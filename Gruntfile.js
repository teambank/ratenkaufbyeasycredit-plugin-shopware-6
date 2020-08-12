module.exports = function(grunt) {

    var baseDir = 'src/woocommerce-gateway-ratenkaufbyeasycredit/assets';

    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-copy');

    grunt.initConfig({
      uglify: {
        easycredit: {
          options: {
            sourceMap: true,
            sourceMapName: baseDir+'/js/easycredit.min.js.map'
          },
          files: {
            [baseDir+'/js/easycredit.min.js']: [
                baseDir+'/js/src/easycredit-modal.js',
                baseDir+'/js/src/easycredit-widget.js',
                baseDir+'/js/src/easycredit-frontend.js'
            ],
            [baseDir+'/js/easycredit-backend.min.js'] : [
               './merchant-interface/dist/js/app.js'
            ]
          },
        },
        easycreditBackend: {
          options: {
//            sourceMap: true,
//            sourceMapName: baseDir+'/js/easycredit-backend.min.js.map',

          },
          files: {
            [baseDir+'/js/easycredit-backend.min.js'] : [
               './merchant-interface/dist/js/app.js'
            ]
          },

        }
      },
      cssmin: {
          options: {
            mergeIntoShorthands: false,
            roundingPrecision: -1
          },
          easycredit: {
            files: {
              [baseDir+'/css/easycredit.min.css']: [
                baseDir+'/css/src/easycredit-modal.css',
                baseDir+'/css/src/easycredit-widget.css',
                baseDir+'/css/src/easycredit-frontend.css'
              ],
              [baseDir+'/css/easycredit-backend.min.css'] : [
                baseDir+'/css/src/easycredit-backend.css',
                './merchant-interface/dist/css/app.css'
              ]
            }
          }
      },

      copy: {
          easycredit: {
            files: [
                {expand: true, cwd: 'merchant-interface/dist/img', src: ['**'], dest: baseDir+'/img/', filter: 'isFile'}
            ]
          }
      }
    });
    grunt.registerTask('default', ['uglify','cssmin','copy']);
}
