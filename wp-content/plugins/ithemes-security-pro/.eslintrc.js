const eslintConfig = {
	root: true,
	extends: [ 'plugin:@wordpress/eslint-plugin/recommended' ],
	settings: {
		'import/resolver': {
			webpack: {
				config: __dirname + '/webpack.config.js',
			},
		},
	},
};

module.exports = eslintConfig;
