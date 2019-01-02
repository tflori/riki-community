const { CheckerPlugin } = require('awesome-typescript-loader');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const Path = require('path');


class Without {
    constructor(patterns) {
        this.patterns = patterns;
    }

    apply(compiler) {
        compiler.hooks.emit.tapAsync("MiniCssExtractPluginCleanup", (compilation, callback) => {
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
        extensions: ['.ts', '.js', '.scss', '.css']
    },
    devtool: 'source-map',
    entry: {
        'bundle': './resources/js/bundle.ts',
        'bundle.css': './resources/css/bundle.scss',
    },
    output: {
        path: Path.resolve(__dirname, 'public'),
        publicPath: '/assets/',
    },
    externals: {
        jquery: 'jQuery'
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
