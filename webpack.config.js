var path = require('path');
var resolvePath = dir => { return path.resolve(__dirname, dir); }

const customStyleLoader = {
    loader: 'style-loader',
    options: {
      insert: function (linkTag) {
        function waitTillReady() {
            if (document.querySelector('#app')) {
                const parent = document.querySelector('#shadow');

                if (! parent.shadowRoot) {
                    parent.attachShadow({ mode: 'open' });
                }

                linkTag.innerHTML = linkTag.innerHTML.replaceAll('root', 'host');
				linkTag.dataset.shadow = `1`;

                parent.shadowRoot.appendChild(linkTag);
            } else {
                window.setTimeout(waitTillReady, 1);
            }
        }

        waitTillReady();
      },
    },
};

module.exports = env => {
    return [
        {
            entry:
            {
                taopix: './webroot/utils/src/index.tsx',
            },
            output: {
                path: resolvePath('./webroot/utils/'),
                filename: '[name]/[name].js',
                library: '[name]',
                libraryTarget: "umd",
                pathinfo: process.env.NODE_ENV !== 'production'
            },
            module: {
                rules: [
                {
                    test: /\.tsx?$/,
                    exclude: /node_modules/,
                    use: [{
                        loader: 'ts-loader',
                        options:
                        {
                            configFile : 'tsconfig.json'
                        }
                    }],
                },
                {
                    test: /\.css$/,
                    use: [
                    customStyleLoader,  // to inject the result into the DOM as a style block
					{ loader: "css-loader" }  // to convert the resulting CSS to Javascript to be bundled (modules:true to rename CSS classes in output to cryptic identifiers, except if wrapped in a :global(...) pseudo class)
                    ]
                },
                ]
            },
            resolve: {
                extensions: [".tsx", ".ts", ".js", ".css"],
                alias: {
                }
            },
            mode: process.env.NODE_ENV,
        }
    ];
};