const pluginData = {
    'name': 'WooCommerce Bulk Variations',
    'libNamespace': 'Barn2\\WBV_Lib',
    'libExcludes': ['Admin/Settings_API_Helper.php', 'class-html-data-table.php', 'class-wp-scoped-hooks.php']
};

const { src, dest, watch, series, parallel } = require( 'gulp' );

const fs = require( 'fs' ),
    barn2build = getBarn2Build();

function getBarn2Build() {
    var build;

    if ( fs.existsSync( '../barn2-lib/build' ) ) {
        build = require( '../barn2-lib/build/gulpfile-common' );
    } else if ( process.env.BARN2_LIB ) {
        build = require( process.env.BARN2_LIB + '/build/gulpfile-common' );
    } else {
        throw new Error( "Error: please set the BARN2_LIB environment variable to path of Barn2 Library project" );
    }
    build.setupBuild( pluginData );
    return build;
}

function test( cb ) {
    console.log( 'All looks good.' );
    cb();
}

module.exports = {
    default: test,
    build: barn2build.buildPlugin,
    assets: barn2build.buildAssets,
    library: barn2build.updateLibrary,
    zip: barn2build.createZipFile,
    release: barn2build.releasePlugin,
    pluginPlayground: barn2build.updatePluginPlayground,
    pluginTesting: barn2build.updatePluginTesting,
    pluginDemo: barn2build.updatePluginDemo,
	doc: barn2build.updateDoc,
    watch: () => {
        watch( 'assets/scss/*.scss', barn2build.compileSass );
    }
};
