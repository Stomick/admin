const helpers = require('./helpers');
const webpackMerge = require('webpack-merge');
const commonConfig = require('./webpack.common.js');

const DefinePlugin = require('webpack/lib/DefinePlugin');
const UglifyJsPlugin = require('webpack/lib/optimize/UglifyJsPlugin');
const WebpackMd5Hash = require('webpack-md5-hash');

const ENV = process.env.NODE_ENV = process.env.ENV = 'production';
const HOST = process.env.HOST || 'localhost';
const PORT = process.env.PORT || 8080;
const API_PATH =  'http://api.weev.ru/';
const METADATA = webpackMerge(commonConfig.metadata, {
  host: HOST,
  port: PORT,
  ENV: ENV,
  API_PATH: API_PATH
});


module.exports = webpackMerge(commonConfig, {

  devtool: 'source-map',

  output: {

    path: helpers.root('../prod'),

    filename: '[name].[chunkhash].bundle.js',

    sourceMapFilename: '[name].[chunkhash].bundle.map',

    chunkFilename: '[id].[chunkhash].chunk.js'

  },


  plugins: [

    new WebpackMd5Hash(),

    new DefinePlugin({
      'ENV': JSON.stringify(METADATA.ENV),
      'API_PATH': JSON.stringify(METADATA.API_PATH),
      'process.env': {
        'ENV': JSON.stringify(METADATA.ENV),
        'NODE_ENV': JSON.stringify(METADATA.ENV),
        'API_PATH': JSON.stringify(METADATA.API_PATH)
      }
    }),

    new UglifyJsPlugin({
        compress:{
            warnings: false,
            drop_console: true,
            unsafe: true
        }
    })

  ],

   node: {
    global: true,
    crypto: 'empty',
    process: false,
    module: false,
    clearImmediate: false,
    setImmediate: false
  }

});
