const path = require('path');

module.exports = {
    entry: './src/upload.js',
    mode: 'development',
    devtool: 'source-map',
    output: {
        path: path.resolve(__dirname, '../public/assets/js'),
        filename: 'upload.js',
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
