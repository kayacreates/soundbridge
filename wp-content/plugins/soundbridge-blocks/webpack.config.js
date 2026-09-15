const path = require('path');
const defaultConfig = require('@wordpress/scripts/config/webpack.config');

module.exports = {
    ...defaultConfig,
    entry: async () => ({
        ...(await defaultConfig.entry()),
        'program-archive-settings/index': path.resolve(process.cwd(), 'src/program-archive-settings/index.js'),
        'directory-archive-settings/index': path.resolve(process.cwd(), 'src/directory-archive-settings/index.js'),
        'event-archive-settings/index': path.resolve(process.cwd(), 'src/event-archive-settings/index.js'),
        'faculty-archive-settings/index': path.resolve(process.cwd(), 'src/faculty-archive-settings/index.js'),
    }),
};
