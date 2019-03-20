const { CheckerPlugin } = require('awesome-typescript-loader');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const TsconfigPathsPlugin = require('tsconfig-paths-webpack-plugin');
const Path = require('path');


class Without {
    constructor(patterns) {
        this.patterns = patterns;
    }

    apply(compiler) {
        compiler.hooks.emit.tapAsync("Without", (compilation, callback) => {
            Object.keys(compilation.assets)
                .filter(asset => {
                    let match = false,
                        i = this.patterns.length
                    ;
                    while (i--) {
                        if (this.patterns[i].test(asset)) {
                            match = true;
                        }
                    }
                    return match;
                }).forEach(asset => {
                    delete compilation.assets[asset];
                });

            callback();
        });
    }
}

module.exports = {
    mode: process.env.NODE_ENV || 'development',
    resolve: {
        extensions: ['.ts', '.js', '.scss', '.css'],
        plugins: [
            new TsconfigPathsPlugin(),
        ],
    },
    devtool: 'source-map',
    entry: {
        'bundle': './js/bundle.ts',
        'bundle.css': './resources/css/bundle.scss',
    },
    output: {
        path: Path.resolve(__dirname, 'public'),
        publicPath: '/assets/',
    },
    externals: {
        jquery: 'jQuery',
        'materialize-css': 'M',
        'vue': 'Vue',
    },
    module: {
        rules: [
            {
                test: /\.scss$/,
                use: [
                    MiniCssExtractPlugin.loader,
                    {
                        loader: 'css-loader',
                        options: {
                            sourceMap: true
                        }
                    },
                    {
                        loader: 'sass-loader',
                        options: {
                            sourceMap: true
                        }
                    },
                ],
            },
            {
                test: /\.ts$/,
                loader: 'awesome-typescript-loader',
            },
            {
                test: /\.html$/,
                loader: 'vue-template-loader',
            }
        ],
    },
    plugins: [
        new CheckerPlugin(),
        new MiniCssExtractPlugin({
            filename: '[name]',
        }),
        new Without([/\.css\.js(\.map)?$/]),
    ],
};
