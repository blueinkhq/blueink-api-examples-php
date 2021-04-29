const path = require('path');

module.exports = {
    entry: './src/index.js',
    output: {
        path: path.resolve(__dirname, 'dist'),
        filename: 'blueink-embed.js',
        library: 'BlueInkEmbed',
        libraryExport: 'default',
        libraryTarget: 'umd',
    },
    resolve: {
        modules: [
            path.resolve(__dirname, 'src'),
            'node_modules',
        ],
    },
    module: {
        rules: [
            { test: /\.js$/, exclude: /node_modules/, use: 'babel-loader' }
        ],
    }
};
