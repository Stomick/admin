const webpack = require('webpack');
const helpers = require('./helpers');

const CopyWebpackPlugin = require('copy-webpack-plugin');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const ForkCheckerPlugin = require('awesome-typescript-loader').ForkCheckerPlugin;
const ContextReplacementPlugin = require('webpack/lib/ContextReplacementPlugin');
const ExtractTextPlugin = require('extract-text-webpack-plugin');

const METADATA = {
  title: 'LetMeSport',
  description: '',
  baseUrl: './'
};

module.exports = {
    
    entry: {
        'polyfills': './src/polyfills.ts',
        'vendor': './src/vendor.ts',
        'main': './src/main.ts'
    },

    resolve:{
        extensions: ['.js', '.ts', '.css', '.scss', '.json'],
		modules: [helpers.root('src'), 'node_modules']
    },

    module: {       
        loaders:[ 
             {
                test: /\.ts$/,
                loaders: [
                    'awesome-typescript-loader',
                    'angular2-template-loader'
                ],
                exclude: [/\.(spec|e2e)\.ts$/]
             },
             {
                test: /\.json$/,
                loader: 'json-loader'
             },
             {
                test: /\.css$/,
                loader: 'raw-loader'
             },
             {
                test: /\.scss$/,
                loaders: ['raw-loader', 'sass-loader']
             },
             {
                test: /initial\.scss$/,                
                loader: ExtractTextPlugin.extract({
                    fallbackLoader: 'style-loader',
                    loader: 'css-loader!sass-loader?sourceMap'
                })
             },
     
             {
                test: /\.woff(2)?(\?v=.+)?$/,
                loader: 'url-loader?limit=10000&mimetype=application/font-woff&name=fonts/[name].[ext]'
             },

             {
                test: /\.(ttf|eot|svg)(\?v=.+)?$/,
                loader: 'file-loader?&name=fonts/[name].[ext]'
             },
             {
                test: /bootstrap\/dist\/js\/umd\//,
                loader: 'imports?jQuery=jquery'
             },
             {
                test: /\.html$/,
                loader: 'raw-loader',
                exclude: [helpers.root('src/index.html')]
             },  
             { 
                 test: /\.(png|jpg|gif)$/,
                 loader: 'file-loader?limit=8192&name=assets/img/[name].[ext]'
             }           
        ]
    },

    plugins:[
        new ExtractTextPlugin({filename: 'initial.css', allChunks: true}),
        new ForkCheckerPlugin(),

        new ContextReplacementPlugin(
            /angular(\\|\/)core(\\|\/)(esm(\\|\/)src|src)(\\|\/)linker/,
            helpers.root('src')
        ),
      
     
        new webpack.optimize.CommonsChunkPlugin({
            name: ['polyfills', 'vendor'].reverse()
        }),
        new CopyWebpackPlugin([{
            from: 'src/assets',
            to: 'assets'
        }]),
        new HtmlWebpackPlugin({
			title: METADATA.title,
            template: 'src/index.html',
            chunksSortMode: 'dependency',
            metadata: METADATA
        }), 
        new webpack.ProvidePlugin({
            jQuery: "jquery",
            $: 'jquery',
            jquery: 'jquery',
            'window.jQuery': 'jquery',
            'Tether': 'tether',
            'window.Tether': 'tether',
            Datamap:'datamaps',
            Skycons: 'skycons/skycons',
            Dropzone: 'dropzone',
            moment: 'moment',
            fullCalendar: 'fullcalendar',
            _: 'lodash'
        })

    ],

     node: {
        global: true,
        crypto: 'empty',
        process: true,
        module: false,
        clearImmediate: false,
        setImmediate: false
    }

}