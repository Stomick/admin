const helpers = require('./helpers');
const webpackMerge = require('webpack-merge');
const commonConfig = require('./webpack.common.js');

const DefinePlugin = require('webpack/lib/DefinePlugin');

const ENV = process.env.ENV = process.env.NODE_ENV = 'development';
const HOST = process.env.HOST || 'localhost';
const PORT = process.env.PORT || 3000;
// const API_PATH =  'http://letmesport.dev.rgt.by/api/';
// const API_PATH =  'http://weev.ru/api/';
// const API_PATH =  'http://localhost:8080/api/';
const API_PATH =  'http://dovnar.ewd.pw/';
const METADATA = webpackMerge(commonConfig.metadata, {
  host: HOST,
  port: PORT,
  ENV: ENV,
  API_PATH: API_PATH
});


module.exports = webpackMerge(commonConfig, {

  devtool: 'cheap-module-eval-source-map',

  output: {

    path: helpers.root('../web'),

    filename: '[name].bundle.js',

    sourceMapFilename: '[name].map',

    chunkFilename: '[id].chunk.js'

  },

  plugins: [

    new DefinePlugin({
      'ENV': JSON.stringify(METADATA.ENV),
      'API_PATH': JSON.stringify(METADATA.API_PATH),
      'process.env': {
        'ENV': JSON.stringify(METADATA.ENV),
        'NODE_ENV': JSON.stringify(METADATA.ENV),
        'API_PATH': JSON.stringify(METADATA.API_PATH)
      }
    })
  ],

  devServer: {
    port: METADATA.port,
    host: METADATA.host,
    historyApiFallback: true,
    watchOptions: {
      aggregateTimeout: 300,
      poll: 1000
    }
  },

  node: {
    global: true,
    crypto: 'empty',
    process: true,
    module: false,
    clearImmediate: false,
    setImmediate: false
  }

});
