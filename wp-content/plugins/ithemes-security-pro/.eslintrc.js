const path = require( 'path' );

const eslintConfig = {
	root: true,
	parser: '@babel/eslint-parser',
	extends: [ 'plugin:@wordpress/eslint-plugin/recommended-with-formatting' ],
	settings: {
		'import/resolver': {
			node: {},
			webpack: {
				config: path.join( __dirname, '/webpack.config.js' ),
			},
		},
		jsdoc: {
			mode: 'typescript',
		},
	},
};

module.exports = eslintConfig;
