const mason = require('@joomlatools/mason-tools-v1');
const path = require('path');
const fs = require('fs').promises;

const frameworkFolder = process.cwd();
const libraryAssetsPath = `${frameworkFolder}/code/resources/assets`;
const KUIPath = `${path.resolve(frameworkFolder, '..')}/kodekit-ui/dist`;

async function files() {
    await mason.fs.copyFolderContents(`${KUIPath}/css`, `${libraryAssetsPath}/css`);
    await mason.fs.copyFolderContents(`${KUIPath}/fonts`, `${libraryAssetsPath}/fonts`);
    await mason.fs.copyFolderContents(`${KUIPath}/img`, `${libraryAssetsPath}/img`);
    await mason.fs.copyFolderContents(`${KUIPath}/js`, `${libraryAssetsPath}/js`, {
        rename: (targetName) => targetName.replace(/koowa\./, "kodekit.")
    });
}

async function js() {
    await fs.rename(`${libraryAssetsPath}/js/admin.kodekit.js`, `${libraryAssetsPath}/js/admin.js`);
    await fs.rename(`${libraryAssetsPath}/js/admin.kodekit.min.js`, `${libraryAssetsPath}/js/admin.min.js`);
    const append = [
        `${libraryAssetsPath}/js/kodekit.js`,
        `${libraryAssetsPath}/js/kodekit.min.js`,
        `${libraryAssetsPath}/js/kodekit.select2.js`,
        `${libraryAssetsPath}/js/kodekit.select2.min.js`
    ];

    for (let file of append) {
        let contents = await fs.readFile(file);

        contents += "\nif(typeof Kodekit === 'undefined') { var Kodekit = Koowa; }\n";

        await fs.writeFile(file, contents);
    }
}

module.exports = {
    version: '1.0',
    tasks: {
        files,
        js,
        watch: {
            path: [`${libraryAssetsPath}/scss`, `${libraryAssetsPath}/js`],
            callback: async (path) => {
                if (path.endsWith('.scss')) {
                    await css();
                }
                else if (path.endsWith('.js')) {
                    await js();
                }
            },
        },
        default: ['files', 'js'],
    },
};
