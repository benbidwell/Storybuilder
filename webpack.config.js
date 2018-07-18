var webpack = require('webpack'),
    path = require('path'),
    ExtractTextPlugin = require("extract-text-webpack-plugin"),
    extractCSS = new ExtractTextPlugin({ filename: './[name].css', allChunks: true });

var OptimizeCssAssetsPlugin = require('optimize-css-assets-webpack-plugin');

var plugins = [
    extractCSS,
    new webpack.HotModuleReplacementPlugin(),
    new webpack.NoEmitOnErrorsPlugin(),
    new webpack.ProvidePlugin({
        $: "jquery",
        jQuery: "jquery"
    }),
    new webpack.optimize.CommonsChunkPlugin({ name: 'vendor', filename: 'vendor.bundle.js' }),
];

module.exports = {
    entry: {
        app: [
            './angular/index.js'
        ],
        vendor: [
            'jquery',
            'angular',
            'angular-ui-router',
            'angular-messages',
            'angular-jwt',
            'angular-ui-bootstrap',
            'ng-file-upload',
            'ng-infinite-scroll'
        ]
    },
    output: {
        path: path.join(__dirname, 'public/'),
        filename: '[name].bundle.js',
        chunkFilename: "[id].js"
    },
    resolve: {
        //modulesDirectories: ['node_modules', 'angular'],
        //extension: ['', '.js', '.css'],
        modules: [
            path.join(__dirname, "angular"),
            "node_modules"
        ]

    },
    module: {
        rules: [
            {
                test: /\.js$/,
                loader: 'babel-loader',
                query: {
                    presets: ['es2015'],
                    compact: false
                }
            },
            {
                test: /\.html$/,
                loader: "file-loader?name=./partials/[name]-[hash:6].[ext]!extract-loader!html-loader"
            },
            {
                test: /\.css$/,
                use: ExtractTextPlugin.extract({ fallback: 'style-loader', use: { loader: 'css-loader', options: { minimize: true } } })
            },
            {
                test: /\.(jpe?g|png|gif)$/,
                loader: "file-loader?name=./imgs/[name]-[hash:6].[ext]"
            },
            {
                test: /\.scss$/,
                use: ["style-loader", "css-loader", "sass-loader"]
            },
            {
                test: /\.svg$/,
                loader: 'file-loader?name=./imgs/[name]-[hash:6].[ext]'
            },
            {
                test: /\.(woff2?|ttf|eot)$/,
                loader: 'url-loader?limit=10000'
            }
        ]
    },
    plugins: plugins,
    devServer: {
        hot: true,
        port: 8002,
        proxy: {
            '*': 'http://localhost:8000'
        },
        //host:'192.168.10.50'
    }
};
